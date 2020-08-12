Theteller payment gateway for Opencart

== Introduction ==

This is a Theteller payment gateway for Opencart. Theteller is an integrated and secure online payment and collection platform designed to enable individuals, businesses and institutions to make or receive payments online from their homes or offices. Theteller Opencart Payment Gateway allows you to accept local and International payment on your Opencart store via the use of a Visa/ MasterCard, Gh-link enabled card, mobile wallets. To get a Theteller merchant account visit Theteller website by clicking here With this Theteller Opencart Payment Gateway plugin, you will be able to accept the following payment methods in your shop: •	MasterCard •	Visa •	Mobile Wallet(MTN, TIGO-AIRTEL, VODAFONE)

= NOTE = This plugin is only for Opencart 3+ .

Add this line into you .htaccess RewriteRule ^response/theteller/?$ index.php?route=extension/payment/theteller/callback [L]

== Installation ==

1.Download the plugin zip file 2.Extract the zip file.

3.Locate the root OpenCart directory of your shop via FTP connection.

4.Copy the admin, catalog, and system folders into your OpenCart's root folder.

5.In your OpenCart admin area, enable the Theteller Payment Gateway and insert your merchant details (Merchant ID , API key , API user and Merchant name or Shop Name Or Company Name).

6.Select the environment you want to test for test transaction and live for live transaction.

7.Set Total order to 1 to enable gateway, you can set to more..this ensure that gateway is only available when order reached 1


= SMS Configuration = 

Theteller SMS allows you to send SMS to your customer as receipt after successfull purchase. • SMS Status  – select Disabled or Active to  enable Theteller SMS. • SMS Api User  – Your API USER. will be given upon registration. • SMS Api Password  – YOUR API PASSWORD. will be given upon registration. • SMS SenderID  – This control what user sees as sender. Must be register before use. 11 characters Maximum.


8.Select status either Enabled or Disabled to enable the Theteller Plugin on checkout

9.Click on the save disk on top to save details.

You are Done
