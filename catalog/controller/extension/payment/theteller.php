<?php
class ControllerExtensionPaymentTheteller extends Controller {
	public function index() {
		$data['button_confirm'] = $this->language->get('button_confirm');

		$this->load->model('checkout/order');

		
		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

		
		 $this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('payment_theteller_order_status_id'), "Thank you for shopping with us. Kindly click Confirm order button to proceed payment ", true);

		
		$environment = $this->config->get('payment_theteller_environment');

		if($environment == "Live")
		{	
			$api_base_url = "https://prod.theteller.net/checkout/initiate";
		}

		else
		{	
			$api_base_url = "https://test.theteller.net/checkout/initiate";
		}
		
		

		$data['ap_merchant_name'] = $this->config->get('payment_theteller_merchant_name');

		$data['ap_merchant_id'] = $this->config->get('payment_theteller_merchant_id');

		$apiuser = $this->config->get('payment_theteller_api_user');
		$data['ap_api_user'] = $this->config->get('payment_theteller_api_user');

		$apikey = $this->config->get('payment_theteller_api_key');
		$data['ap_api_key'] = $this->config->get('payment_theteller_api_key');

		$data['ap_environment'] = $this->config->get('payment_theteller_environment');

		$data['ap_amount'] = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);
		$data['ap_currency'] = $order_info['currency_code'];
		$data['ap_purchasetype'] = 'Item';
		$data['ap_itemname'] = $this->config->get('config_name') . ' - #' . $this->session->data['order_id'];
		$data['ap_itemcode'] = $order_info['order_id'];
		$this->session->data['order_id'] = $order_info['order_id'];
		
		$data['ap_returnurl'] = $this->url->link('checkout/success');
		$data['ap_cancelurl'] = $this->url->link('checkout/checkout', '', true);

		$amount = $order_info['total'];
		 //Convert amount to minor float..
         $minor='';
          if(is_float((float)$amount) || is_double((double)$amount)) {
        $number = $amount * 100;
        
        $zeros = 12 - strlen($number);
        $padding = '';
        //Log::info('The number of zeros to use is '.$zeros);
        for($i=0; $i<$zeros; $i++) {
            $padding .= '0';
        }
        //Log::info('Padding is '.$padding);
        $minor = $padding.$number;
    }
    if(strlen($amount)==12) {
        //Received an actual minor unit
        $minor = $amount;

    }


//Generating 12 unique random transaction id...
$transaction_id='';
$allowed_characters = array(1,2,3,4,5,6,7,8,9,0); 
for($i = 1;$i <= 12; $i++){ 
    $transaction_id .= $allowed_characters[rand(0, count($allowed_characters) - 1)];
   $_SESSION['theteller_transaction_id'] = $transaction_id;
   $this->session->data['theteller_transaction_id'] = $transaction_id;
 
} 


//Theteller Checkout Api Payload...
    $data = array(
    "merchant_id" => $this->config->get('payment_theteller_merchant_id'),
    "transaction_id" => $transaction_id,
    "desc" => "Payment  to ".$this->config->get('payment_theteller_merchant_name')."",
    "amount" => $minor,
    "email" =>$order_info['email'],
    "redirect_url" => $this->config->get('payment_theteller_callback')
);


//Encoding playload...
$json_data = json_encode($data);

//Api base URL...
 $url = $api_base_url;                                                                                                            
// Initialization of the request
$curl = curl_init();

// Definition of request's headers
curl_setopt_array($curl, array(
  CURLOPT_URL => $url,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_SSL_VERIFYHOST => false,
  CURLOPT_SSL_VERIFYPEER => false,
  CURLOPT_ENCODING => "json",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_HTTPHEADER => array(
    "Authorization: Basic ".base64_encode($apiuser.':'.$apikey)."",
    "cache-control: no-cache",
    "content-type: application/json; charset=UTF-8",
    
  ),
   CURLOPT_POSTFIELDS => $json_data,
));

// Send request and show response
$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  //echo "API Error #:" . $err;
    //Api error if any...
     return  $err;
} else {

  
    $response = json_decode($response, true);
    
    //Getting Response...
    //$status = $response['status'];
    //$code = $response['code'];
    // $reason = $response['reason'];
    // $token = $response['token'];
    // $checkout_url = $response['checkout_url'];

     if (!isset($response['status'])) {
         $status = null;
        }

        else
        {
           $status = $response['status'];
        }

          if (!isset($response['code'])) {
         $code = null;
        }

        else
        {
           $code = $response['code'];
        }

        if (!isset($response['reason'])) {
         $reason = null;
        }

        else
        {
           $reason = $response['reason'];
        }

         if (!isset($response['token'])) {
         $token = null;
        }

        else
        {
           $token = $response['token'];
        }

        if (!isset($response['checkout_url'])) {
         $checkout_url = null;
        }

        else
        {
           $checkout_url = $response['checkout_url'];
        }



    if($status == "success" && $code == "200" && $token !="")
    { 
      //Redirect to checkout page...
    $data['checkout_url'] = $checkout_url;
 
       // return $checkout_url;
      
  }
    else
    {   
       // die($reason);
    
        return json_encode($response, true);
    }
  }

		return $this->load->view('extension/payment/theteller', $data);
	}

	public function callback() {
			

		//Loading order model...
		$this->load->model('checkout/order');

		//Getting orderid from session..
		if (!isset($this->session->data['order_id'])) {
    		$this->session->data['order_id'] = null;
        }

        else
        {
            $order_id = $this->session->data['order_id'];
        }

		//Getting transaction from session...
		if (!isset($this->session->data['theteller_transaction_id'])) {
    		$this->session->data['theteller_transaction_id'] = null;
        }

        else
        {
            $transaction_id = $this->session->data['theteller_transaction_id'];
        }


		if(empty($transaction_id) && empty($order_id))
		{
			die("<h2 style=color:red>Invalid Request ! </h2>");
		}

		//Page request...
		$request =  $_SERVER['REQUEST_URI'];

		$url_form = ltrim(strstr($request, '?'), '?');

		$url_decoded = explode('&', $url_form) ;

		//Check if status is set...
		 if (!isset($url_decoded[0])) {
    		$url_decoded[0] = null;
        }

        else
        {
            if($url_decoded[0] == "code=100")
            {	
            	 $code = $url_decoded[0];
            	 $status = $url_decoded[1];
            	 $theteller_transaction_id = $url_decoded[2];
            	 $trans_num = explode('=', $theteller_transaction_id);
       			$trans_num = $trans_num[1];
            }
            else
            {
            	 $status = $url_decoded[0];
            	 $code = $url_decoded[1];
            	 $reason = $url_decoded[2];
            	 $theteller_transaction_id = $url_decoded[3];
            	 $trans_num = explode('=', $theteller_transaction_id);
         		 $trans_num = $trans_num[1];
            }
           


        }


        //Getting Reason detail..
        $reason_detail = explode('=', $reason);
        $reason_detail = $reason_detail[1];
        

        if($trans_num != $transaction_id)
		{
			die("<h2 style=color:red>Code 0001 : Data has been tampered ! </h2>");
		}

        //checking if transaction is successful
		if($code == "code=000" && $status == "status=approved")
		{	

			 $this->model_checkout_order->addOrderHistory($this->session->data['order_id'], 5, "Thank you for shopping with us. Payment Received ", true);
			 
			 $this->response->redirect($this->url->link('checkout/success'), 301);
			

			
		}

		//checking if transaction failed..
		if($code =="code=900")
		{
			$this->model_checkout_order->addOrderHistory($this->session->data['order_id'], 10, "Thank you for shopping with us. However your transaction has been failed or declined ", true);
			 $this->response->redirect($this->url->link('checkout/checkout', '', true));
		}

		if($code =="code=100")
		{	


			 $this->model_checkout_order->addOrderHistory($this->session->data['order_id'], 10, "Thank you for shopping with us. However your transaction has been failed or declined ", true);
			 $this->response->redirect($this->url->link('checkout/checkout', '', true));
			
		}


		
			} // end of callback...
			
		
		
	
}

