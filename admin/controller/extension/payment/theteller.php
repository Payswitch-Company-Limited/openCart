<?php
class ControllerExtensionPaymentTheteller extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/payment/theteller');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {

			$this->model_setting_setting->editSetting('payment_theteller', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true));
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['merchant_id'])) {
			$data['error_merchant_id'] = $this->error['merchant_id'];
		} else {
			$data['error_merchant_id'] = '';
		}

		if (isset($this->error['api_user'])) {
			$data['error_api_user'] = $this->error['api_user'];
		} else {
			$data['error_api_user'] = '';
		}

		if (isset($this->error['api_key'])) {
			$data['error_api_key'] = $this->error['api_key'];
		} else {
			$data['error_api_key'] = '';
		}


		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/payment/theteller', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/payment/theteller', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true);

		if (isset($this->request->post['payment_theteller_merchant_id'])) {
			$data['payment_theteller_merchant_id'] = $this->request->post['payment_theteller_merchant_id'];
		} else {
			$data['payment_theteller_merchant_id'] = $this->config->get('payment_theteller_merchant_id');
		}

		if (isset($this->request->post['payment_theteller_merchant_name'])) {
			$data['payment_theteller_merchant_name'] = $this->request->post['payment_theteller_merchant_name'];
		} else {
			$data['payment_theteller_merchant_name'] = $this->config->get('payment_theteller_merchant_name');
		}

		if (isset($this->request->post['payment_theteller_api_user'])) {
			$data['payment_theteller_api_user'] = $this->request->post['payment_theteller_api_user'];
		} else {
			$data['payment_theteller_api_user'] = $this->config->get('payment_theteller_api_user');
		}


		if (isset($this->request->post['payment_theteller_api_key'])) {
			$data['payment_theteller_api_key'] = $this->request->post['payment_theteller_api_key'];
		} else {
			$data['payment_theteller_api_key'] = $this->config->get('payment_theteller_api_key');
		}


		if (isset($this->request->post['payment_theteller_callback'])) {
			$data['payment_theteller_callback'] = $this->request->post['payment_theteller_callback'];
		} else {
			$data['payment_theteller_callback'] = $this->config->get('payment_theteller_callback');
		}



		$data['callback'] = HTTP_CATALOG . 'response/theteller/';
		//$callback_url = HTTP_CATALOG . 'index.php?route=extension/payment/theteller/callback';
		//http://localhost/opencart/index.php?route=extension/payment/theteller/callback

		if (isset($this->request->post['payment_theteller_total'])) {
			$data['payment_theteller_total'] = $this->request->post['payment_theteller_total'];
		} else {
			$data['payment_theteller_total'] = $this->config->get('payment_theteller_total');
		}

		if (isset($this->request->post['payment_theteller_order_status_id'])) {
			$data['payment_theteller_order_status_id'] = $this->request->post['payment_theteller_order_status_id'];
		} else {
			$data['payment_theteller_order_status_id'] = $this->config->get('payment_theteller_order_status_id');
		}

		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		if (isset($this->request->post['payment_theteller_geo_zone_id'])) {
			$data['payment_theteller_geo_zone_id'] = $this->request->post['payment_theteller_geo_zone_id'];
		} else {
			$data['payment_theteller_geo_zone_id'] = $this->config->get('payment_theteller_geo_zone_id');
		}

		$this->load->model('localisation/geo_zone');

		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		if (isset($this->request->post['payment_theteller_status'])) {
			$data['payment_theteller_status'] = $this->request->post['payment_theteller_status'];
		} else {
			$data['payment_theteller_status'] = $this->config->get('payment_theteller_status');
		}

		if (isset($this->request->post['payment_theteller_sort_order'])) {
			$data['payment_theteller_sort_order'] = $this->request->post['payment_theteller_sort_order'];
		} else {
			$data['payment_theteller_sort_order'] = $this->config->get('payment_theteller_sort_order');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/payment/theteller', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/payment/theteller')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['payment_theteller_merchant_id']) {
			$this->error['merchant_id'] = $this->language->get('error_merchant_id');
		}

		if (!$this->request->post['payment_theteller_merchant_name']) {
			$this->error['merchant_name'] = $this->language->get('error_merchant_name');
		}


		if (!$this->request->post['payment_theteller_api_user']) {
			$this->error['api_user'] = $this->language->get('error_api_user');
		}


		if (!$this->request->post['payment_theteller_api_key']) {
			$this->error['api_key'] = $this->language->get('error_api_key');
		}


		return !$this->error;
	}
}