<?php
/**
 * catalog/controller/extension/payment/SSLCommerce.php
 *
 * Copyright (c) 2009-2019 Software Shop Limited
 *
 * LICENSE:
 *
 * This payment module is free software; you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation; either version 3 of the License, or (at
 * your option) any later version.
 *
 * This payment module is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
 * or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public
 * License for more details.
 *
 * @author     Prabal Mallick(prabal.mallick@sslwireless.com), C.M.Sayedur Rahman(cmsayed@gmail.com)
 * @copyright  2009-2019 SSLCommerz
 * @license    http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @version    3.0.0
 */

class ControllerExtensionPaymentSSLCommerce extends Controller {
	
	public function index() {
		$data['button_confirm'] = $this->language->get('button_confirm');

		$this->load->model('checkout/order');

		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

		$data['store_id'] = $this->config->get('payment_SSLCommerce_merchant');
		$data['tran_id'] = $this->session->data['order_id'];
		$data['total_amount'] = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);
		
		$data['SSLCommerce_password'] = $this->config->get('payment_SSLCommerce_password');

		$data['cus_name'] = $order_info['payment_firstname'] . ' ' . $order_info['payment_lastname'];
		$data['cus_add1'] = $order_info['payment_address_1'];
		$data['cus_add2'] = $order_info['payment_address_2'];
		$data['cus_city'] = $order_info['payment_city'];
		$data['cus_state'] = $order_info['payment_zone'];
		$data['cus_postcode'] = $order_info['payment_postcode'];
		$data['cus_country'] = $order_info['payment_country'];
		$data['cus_phone'] = $order_info['telephone'];
		$data['cus_email'] = $order_info['email'];

		if ($this->cart->hasShipping()) {
			$data['ship_name'] = $order_info['shipping_firstname'] . ' ' . $order_info['shipping_lastname'];
			$data['ship_add1'] = $order_info['shipping_address_1'];
			$data['ship_add2'] = $order_info['shipping_address_2'];
			$data['ship_city'] = $order_info['shipping_city'];
			$data['ship_state'] = $order_info['shipping_zone'];
			$data['ship_postcode'] = $order_info['shipping_postcode'];
			$data['ship_country'] = $order_info['shipping_country'];
		} else {
			$data['ship_name'] = '';
			$data['ship_add1'] = '';
			$data['ship_add2'] = '';
			$data['ship_city'] = '';
			$data['ship_state'] = '';
			$data['ship_postcode'] = '';
			$data['ship_country'] = '';
		}
              
		$data['currency'] = $order_info['currency_code'];
		$data['success_url'] = $this->url->link('extension/payment/SSLCommerce/callback', '', 'SSL');
        $data['fail_url'] = $this->url->link('extension/payment/SSLCommerce/Failed', '', 'SSL');
        $data['cancel_url'] = $this->url->link('extension/payment/SSLCommerce/Cancelled', '', 'SSL');
		
		////Hash Key Gernarate For SSL
		$security_key = $this->sslcommerz_hash_key($this->config->get('payment_SSLCommerce_password'), $data);
		
		$data['verify_sign'] = $security_key['verify_sign'];
        $data['verify_key'] = $security_key['verify_key'];

		
		
		$products = '';
		
		
		foreach ($this->cart->getProducts() as $product) {
    		$products .= $product['quantity'] . ' x ' . $product['name'] . ', ';
    	}		
		
		$data['detail1_text'] = $products;
		
		
		if($this->config->get('payment_SSLCommerce_test')=='live') {
				$data['process_url'] = $this->url->link('extension/payment/SSLCommerce/sendrequest', '', 'SSL');
				$data['api_type'] = "NO";
			}
		else {
				$data['process_url'] = $this->url->link('extension/payment/SSLCommerce/sendrequest', '', 'SSL');
				$data['api_type'] = "YES";
			}


		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/extension/payment/SSLCommerce')) {
			return $this->load->view($this->config->get('config_template') . '/template/extension/payment/SSLCommerce', $data);
		} else {
			return $this->load->view('extension/payment/SSLCommerce', $data);
		}
	}

	public function sendrequest()
	{
		$this->load->model('checkout/order');

		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);// update order status as pending
		
		foreach ($this->cart->getProducts() as $product) {
    		$products = $product['name'] . ', ';
    	}
    	$quantity=0;
    	foreach ($this->cart->getProducts() as $product) {
    		$quantity++;
    	}
    	
		$data['store_id'] = $this->config->get('payment_SSLCommerce_merchant');
		$data['tran_id'] = $_REQUEST['order'];
		$data['total_amount'] = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);
		
		$data['store_passwd'] = $this->config->get('payment_SSLCommerce_password');

		$data['cus_name'] = $order_info['payment_firstname'] . ' ' . $order_info['payment_lastname'];
		$data['cus_add1'] = $order_info['payment_address_1'];
		$data['cus_add2'] = $order_info['payment_address_2'];
		$data['cus_city'] = $order_info['payment_city'];
		$data['cus_state'] = $order_info['payment_zone'];
		$data['cus_postcode'] = $order_info['payment_postcode'];
		$data['cus_country'] = $order_info['payment_country'];
		$data['cus_phone'] = $order_info['telephone'];
		$data['cus_email'] = $order_info['email'];
		if ($this->cart->hasShipping()) {
			$data['ship_name'] = $order_info['shipping_firstname'] . ' ' . $order_info['shipping_lastname'];
			$data['ship_add1'] = $order_info['shipping_address_1'];
			$data['ship_add2'] = $order_info['shipping_address_2'];
			$data['ship_city'] = $order_info['shipping_city'];
			$data['ship_state'] = $order_info['shipping_zone'];
			$data['ship_postcode'] = $order_info['shipping_postcode'];
			$data['ship_country'] = $order_info['shipping_country'];
			$ship = "YES";
		} else {
			$data['ship_name'] = $order_info['payment_firstname'] . ' ' . $order_info['payment_lastname'];
			$data['ship_add1'] = $order_info['payment_address_1'];
			$data['ship_add2'] = $order_info['payment_address_2'];
			$data['ship_city'] = $order_info['payment_city'];
			$data['ship_state'] = $order_info['payment_zone'];
			$data['ship_postcode'] = $order_info['payment_postcode'];
			$data['ship_country'] = $order_info['payment_country'];
			$ship = "NO";
		}
		$data['currency']       = $order_info['currency_code'];
		$data['success_url'] = $this->url->link('extension/payment/SSLCommerce/callback', '', 'SSL');
        $data['fail_url'] = $this->url->link('extension/payment/SSLCommerce/Failed', '', 'SSL');
        $data['cancel_url'] = $this->url->link('extension/payment/SSLCommerce/Cancelled', '', 'SSL');
        $data['ipn_url'] = $this->url->link('extension/payment/SSLCommerce/sslcommerz_ipn', '', 'SSL');
		$data['shipping_method']   = $ship;
    	$data['num_of_item']       = $quantity;
    	$data['product_name']      = $products;
    	$data['product_category']  = 'Ecommerce';
    	$data['product_profile']   = 'general';
    	
		$security_key = $this->sslcommerz_hash_key($this->config->get('payment_SSLCommerce_password'), $data);
		
		$data['verify_sign'] = $security_key['verify_sign'];
        $data['verify_key'] = $security_key['verify_key'];


        if($this->config->get('payment_SSLCommerce_test')=='live') 
		{
			$redirect_url = 'https://securepay.sslcommerz.com/gwprocess/v4/api.php';
			$api_type = "NO";
		}
		else 
		{
			$redirect_url = 'https://sandbox.sslcommerz.com/gwprocess/v4/api.php';
			$api_type = "YES";
		}

		$handle = curl_init();
		curl_setopt($handle, CURLOPT_URL, $redirect_url);
		curl_setopt($handle, CURLOPT_TIMEOUT, 10);
		curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($handle, CURLOPT_POST, 1 );
		curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
		curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
		$content = curl_exec($handle );
		$code = curl_getinfo($handle, CURLINFO_HTTP_CODE);
		
		if($code == 200 && !( curl_errno($handle))) 
		{
		  	curl_close( $handle);
		  	$sslcommerzResponse = $content;

		  	$sslcz = json_decode($sslcommerzResponse, true );
		  	
		  //	print_r($sslcz);exit;
		  	
		  	if($sslcz['status']=='SUCCESS')
		  	{
		  	    $tran_id = $this->session->data['order_id'];
		  	    // update order status to 1 from 0.
				$this->model_checkout_order->addOrderHistory($tran_id, $this->config->get('config_order_status_id'), 'Order Initiated');
				
                if(isset($sslcz['GatewayPageURL']) && $sslcz['GatewayPageURL']!="") 
                {
            		if($api_type == "NO")
            		{
            			echo json_encode(['status' => 'SUCCESS', 'data' => $sslcz['GatewayPageURL'], 'logo' => $sslcz['storeLogo'] ]);
            			
            			exit;
            		}
            		else if($api_type == "YES")
            		{
            			echo json_encode(['status' => 'success', 'data' => $sslcz['GatewayPageURL'], 'logo' => $sslcz['storeLogo'] ]);
            			exit;
            		}
            		
            	   //return json_encode(['status' => 'SUCCESS', 'data' => $sslcz['GatewayPageURL'], 'logo' => $sslcz['storeLogo'] ]);
            	} 
            }
            else {
        	   echo json_encode(['status' => 'FAILED', 'data' => null, 'message' => "JSON Data parsing error!"]);
        	}
		}
		else
		{
			echo "CURL not activate!";
		}
	}
	
	public function Failed() 
	{
	    $this->load->model('checkout/order');
	    if (isset($_POST['tran_id'])) 
        {
			$order_id = $_POST['tran_id'];
		} 
	    if(isset($_POST['status']) && $_POST['status'] == 'FAILED')
	    {
	        $this->model_checkout_order->addOrderHistory($order_id, $this->config->get('payment_SSLCommerce_order_fail_id'), "Order Failed By User", false);
	        echo "
                <script>
                    window.location.href = '" . $this->url->link('checkout/failure', '', 'SSL') . "';
                </script>
            ";
            exit;
	    }
	}
	
	public function Cancelled() 
	{
	    $this->load->model('checkout/order');
	    if (isset($_POST['tran_id'])) 
        {
			$order_id = $_POST['tran_id'];
		} 
	    if(isset($_POST['status']) && $_POST['status'] == 'CANCELLED')
	    {
	        $this->model_checkout_order->addOrderHistory($order_id, $this->config->get('payment_SSLCommerce_order_risk_id'), "Order Cancelled By User", false);
	        echo "
                <script>
                    window.location.href = '" . $this->url->link('checkout/cart', '', 'SSL') . "';
                </script>
            ";
            exit;
	    }
	}
	
	
	public function callback() 
	{
		$SSLCommerce_test = $this->config->get('payment_SSLCommerce_test');
        $store_id = urldecode($this->config->get('payment_SSLCommerce_merchant'));
        $store_passwd = urldecode($this->config->get('payment_SSLCommerce_password'));
        if (isset($_POST['tran_id'])) 
        {
			$order_id = $_POST['tran_id'];
		} 
		else 
		{
			$order_id = 0;
		}
        if (isset($_POST['amount'])) 
        {
            $total=$_POST['amount'];
		}
		else
        {
            $total='';	
        }
		if(isset($_POST['val_id']))
		{
			$val_id = urldecode($_POST['val_id']); 
		}
		else 
		{
			 $val_id = ''; 
		}
		if(!isset($_POST['tran_id']) || !isset($_POST['val_id']) || !isset($_POST['amount']))
		{
		    echo "Invalid Information";
		    exit;
		}
    
					
		$this->load->model('checkout/order');
		$order_info = $this->model_checkout_order->getOrder($order_id);
        $amount = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);
			
        if($this->config->get('payment_SSLCommerce_test')=='live')
        {
            $requested_url = ("https://securepay.sslcommerz.com/validator/api/validationserverAPI.php?val_id=".$val_id."&Store_Id=".$store_id."&Store_Passwd=".$store_passwd."&v=1&format=json");
        } 
        else
        {
            $requested_url = ("https://sandbox.sslcommerz.com/validator/api/validationserverAPI.php?val_id=".$val_id."&Store_Id=".$store_id."&Store_Passwd=".$store_passwd."&v=1&format=json");  
        }  
				
        $amount = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);
        $handle = curl_init();
        curl_setopt($handle, CURLOPT_URL, $requested_url);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
        
        $result = curl_exec($handle);

        // echo "<pre>";
        // print_r($result);exit;

        $code = curl_getinfo($handle, CURLINFO_HTTP_CODE);

        if($code == 200 && !( curl_errno($handle)))
        {	
        	# TO CONVERT AS ARRAY
        	# $result = json_decode($result, true);
        	# $status = $result['status'];	
        	
        	# TO CONVERT AS OBJECT
        	$result = json_decode($result);
        		//print_r($result);
        	# TRANSACTION INFO
        	$status = $result->status;	
        	$tran_date = $result->tran_date;
        	$tran_id = $result->tran_id;
        	$val_id = $result->val_id;
        	$amount = $result->amount;
        	$store_amount = $result->store_amount;
        	$bank_tran_id = $result->bank_tran_id;
        	$card_type = $result->card_type;
        	
        	# ISSUER INFO
        	$card_no = $result->card_no;
        	$card_issuer = $result->card_issuer;
        	$card_brand = $result->card_brand;
        	$card_issuer_country = $result->card_issuer_country;
        	$card_issuer_country_code = $result->card_issuer_country_code;   
        	
        	//Payment Risk Status
        	$risk_level = $result->risk_level;
        	$risk_title = $result->risk_title;
        
            if($status=='VALID')
            {
                if($risk_level==0){ $status = 'success';}
                if($risk_level==1){ $status = 'risk';} 
            }
            elseif($status=='VALIDATED'){
                if($risk_level==0){ $status = 'success';}
                if($risk_level==1){ $status = 'risk';} 
            }
            else
            {
                 $status = 'failed';
            }
        }
        //print_r($result);
        //exit;

        $data['breadcrumbs'] = array();

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_home'),
				'href' => $this->url->link('common/home')
			);

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_basket'),
				'href' => $this->url->link('checkout/cart')
			);

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_checkout'),
				'href' => $this->url->link('checkout/checkout', '', 'SSL')
			);

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_failed'),
				'href' => $this->url->link('checkout/success')
			);

			$data['heading_title'] = $this->language->get('text_failed');

			
			$data['button_continue'] = $this->language->get('button_continue');
						
		    if ($order_info && $status) {
			$this->language->load('extension/payment/SSLCommerce');
	
			$data['title'] = sprintf($this->language->get('heading_title'), $this->config->get('config_name'));
	
			if (!isset($this->request->server['HTTPS']) || ($this->request->server['HTTPS'] != 'on')) {
				$data['base'] = HTTP_SERVER;
			} else {
				$data['base'] = HTTPS_SERVER;
			}
	
			$data['language'] = $this->language->get('code');
			$data['direction'] = $this->language->get('direction');
	
			$data['heading_title'] = sprintf($this->language->get('heading_title'), $this->config->get('config_name'));
	
			$data['text_response'] = $this->language->get('text_response');
			$data['text_success'] = $this->language->get('text_success');
			$data['text_success_wait'] = sprintf($this->language->get('text_success_wait'), $this->url->link('checkout/success'));
			$data['text_failure'] = $this->language->get('text_failure');
			$data['text_failure_wait'] = sprintf($this->language->get('text_failure_wait'), $this->url->link('checkout/cart'));
	
	        $msg='';
	
			if (isset($status) && $status == 'success') 
			{
				    $this->load->model('checkout/order');
                    $order_status = $order_info['order_status'];
		    	    $amount_rat = $_POST['amount'];
		    	    if($order_status == 'Pending')
					{
					    $message = '';
    					$message .= 'Payment Status = ' . $status . "\n";
    					$message .= 'Bank txnid = ' . $bank_tran_id . "\n";
    					$message .= 'Your Oder id = ' . $tran_id . "\n";
    					$message .= 'Payment Date = ' . $tran_date . "\n";  
    					$message .= 'Card Number = ' .$card_no . "\n"; 
    					$message .= 'Card Type = ' .$card_brand .'-'. $card_type . "\n"; 
    					$message .= 'Transaction Risk Level = ' .$risk_level . "\n"; 
    					$message .= 'Transaction Risk Description = ' .$risk_title . "\n";
    				    if ($_POST['currency_amount'] == $result->currency_amount) 
    					{
							if($_POST['card_type'] != "")
							{
				                $this->model_checkout_order->addOrderHistory($_POST['tran_id'], $this->config->get('config_order_status_id'));
							}
							else
        					{
        						$msg= "Invalid Card Type!";
        					}
						}
						else
    					{
    						$msg= "Your Paid Amount is Mismatched!";
    					}
					}
					elseif($order_status == 'Processing' || $order_status == 'Complete' || $order_status == 'Processed')
					{
					    $message = '';
					    $message .= 'Transaction Done By IPN: '. $order_status. "\n";
					    $message .= 'Payment Status = ' . $status . "\n";
    					$message .= 'Bank txnid = ' . $bank_tran_id . "\n";
    					$message .= 'Your Oder id = ' . $tran_id . "\n";
    					$message .= 'Payment Date = ' . $tran_date . "\n";  
    					$message .= 'Card Number = ' .$card_no . "\n"; 
    					$message .= 'Card Type = ' .$card_brand .'-'. $card_type . "\n"; 
    					$message .= 'Transaction Risk Level = ' .$risk_level . "\n"; 
    					$message .= 'Transaction Risk Description = ' .$risk_title . "\n";
					}
					else
					{
						$msg= "Order Status Not Pending!";
					}

					
	
                    $this->model_checkout_order->addOrderHistory($order_id, $this->config->get('payment_SSLCommerce_order_status_id'), $message, false);
	                $error='';
                    $data['text_message'] = sprintf('your payment was successfully received', $error, $this->url->link('information/contact'));
        			$data['continue'] = $this->url->link('checkout/success');
                    $data['column_left'] = $this->load->controller('common/column_left');
        			$data['column_right'] = $this->load->controller('common/column_right');
        			$data['content_top'] = $this->load->controller('common/content_top');
        			$data['content_bottom'] = $this->load->controller('common/content_bottom');
        			$data['footer'] = $this->load->controller('common/footer');
        			$data['header'] = $this->load->controller('common/header');
                    //echo $msg;
        			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/extension/payment/success')) 
        			{
        				$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/extension/payment/success', $data));
        			} 
        			else 
        			{
        				$this->response->setOutput($this->load->view('extension/payment/success', $data));
        			}

			}
			else if (isset($status) && $status == 'risk') 
			{
			    $msg = '';
				$this->load->model('checkout/order');
				$this->model_checkout_order->addOrderHistory($_POST['tran_id'], $this->config->get('config_order_status_id'));
				
	
				$message = '';
	
				
					$message .= 'Payment Status = ' . $status . "\n";
				    
					$message .= 'Bank txnid = ' . $bank_tran_id . "\n";
				   
					$message .= 'Your Oder id = ' . $tran_id . "\n";
					
					$message .= 'Payment Date = ' . $tran_date . "\n";  
				   
					$message .= 'Card Number = ' .$card_no . "\n"; 
				   
					$message .= 'Card Type = ' .$card_brand .'-'. $card_type . "\n"; 
				    
					$message .= 'Transaction Risk Level = ' .$risk_level . "\n"; 
				   
					$message .= 'Transaction Risk Description = ' .$risk_title . "\n"; 
					
	            $this->model_checkout_order->addOrderHistory($order_id, $this->config->get('payment_SSLCommerce_order_risk_id'), $message, false);
				//$this->model_checkout_order->update($order_id, $this->config->get('payment_SSLCommerce_order_risk_id'), $message, false);
	
				$data['continue'] = $this->url->link('checkout/checkout');
                $data['column_left'] = $this->load->controller('common/column_left');
    			$data['column_right'] = $this->load->controller('common/column_right');
    			$data['content_top'] = $this->load->controller('common/content_top');
    			$data['content_bottom'] = $this->load->controller('common/content_bottom');
    			$data['footer'] = $this->load->controller('common/footer');
    			$data['header'] = $this->load->controller('common/header');

				if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/extension/payment/Commerce_risk')) {
					$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/extension/payment/Commerce_risk', $data));
				} else {
					$this->response->setOutput($this->load->view('extension/payment/Commerce_risk', $data));
				}

			} else {
				
			$data['continue'] = $this->url->link('checkout/cart');
            $data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/extension/payment/Commerce_failure')) {
				$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/extension/payment/Commerce_failure', $data));
			} else {
				$this->response->setOutput($this->load->view('extension/payment/Commerce_failure', $data));
			}
	

			}
		}
	}
	
	
   // Hash Key Gernate For SSL Commerz
	   public function sslcommerz_hash_key($store_passwd="", $parameters=array()) {
	
			$return_key = array(
				"verify_sign"	=>	"",
				"verify_key"	=>	""
			);
			if(!empty($parameters)) {
				# ADD THE PASSWORD
		
				$parameters['store_passwd'] = md5($store_passwd);
		
				# SORTING THE ARRAY KEY
		
				ksort($parameters);	
		
				# CREATE HASH DATA
			
				$hash_string="";
				$verify_key = "";	# VARIFY SIGN
				foreach($parameters as $key=>$value) {
					$hash_string .= $key.'='.($value).'&'; 
					if($key!='store_passwd') {
						$verify_key .= "{$key},";
					}
				}
				$hash_string = rtrim($hash_string,'&');	
				$verify_key = rtrim($verify_key,',');
		
				# THAN MD5 TO VALIDATE THE DATA
		
				$verify_sign = md5($hash_string);
				$return_key['verify_sign'] = $verify_sign;
				$return_key['verify_key'] = $verify_key;
			}
			return $return_key;
		}
		
		
		public function sslcommerz_ipn()
		{
			$this->load->model('checkout/order');
			$order_id = $_POST['tran_id'];
			$val_id = $_POST['val_id'];
			$status = $_POST['status'];
			
			$order_info = $this->model_checkout_order->getOrder($order_id);
			$store_passwd = $this->config->get('payment_SSLCommerce_password');
			$store_id = $this->config->get('payment_SSLCommerce_merchant');

			$order_status = $order_info['order_status'];
			$amount_rat = $_POST['amount'];
			
			if($status == 'FAILED')
			{
			    $this->load->model('checkout/order');
                $order_id = $_POST['tran_id'];
                $this->model_checkout_order->addOrderHistory($order_id, $this->config->get('payment_SSLCommerce_order_fail_id'), "Order Failed By IPN", false);
			    echo "Order ".$status." By IPN";
			}
			elseif($status == 'CANCELLED')
			{
			    $this->load->model('checkout/order');
                $order_id = $_POST['tran_id'];
                $this->model_checkout_order->addOrderHistory($order_id, $this->config->get('payment_SSLCommerce_order_risk_id'), "Order Cancelled By IPN", false);
			    echo "Order ".$status." By IPN";
			}
			elseif($status == 'VALID' || $status == 'VALIDATED')
			{
    			if($this->config->get('payment_SSLCommerce_test')=='live')
                {
                    $valid_url_own = ("https://securepay.sslcommerz.com/validator/api/validationserverAPI.php?val_id=".$val_id."&Store_Id=".$store_id."&Store_Passwd=".$store_passwd."&v=1&format=json");
                } 
                else
                {
                    $valid_url_own = ("https://sandbox.sslcommerz.com/validator/api/validationserverAPI.php?val_id=".$val_id."&Store_Id=".$store_id."&Store_Passwd=".$store_passwd."&v=1&format=json");  
                }
    
    			$handle = curl_init();
    			curl_setopt($handle, CURLOPT_URL, $valid_url_own);
    			curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
    			curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);
    			curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
    				
    			$result = curl_exec($handle);
    			  	
    			
    			$code = curl_getinfo($handle, CURLINFO_HTTP_CODE);
    				
    			if($code == 200 && !( curl_errno($handle)))
    			{	
    				$result = json_decode($result);
    
    				if($this->sslcommerz_hash_key($store_passwd, $_POST))
    				{
    					if ($_POST['currency_amount'] == $result->currency_amount) 
    					{
    						if($result->status=='VALIDATED' || $result->status=='VALID') 
    						{
    							if($order_status == 'Pending')
    							//if($order_status == '')
    							{
    								if($_POST['card_type'] != "")
    								{
    									//$this->load->model('checkout/order');
    									$this->model_checkout_order->addOrderHistory($order_id, $this->config->get('payment_SSLCommerce_order_status_id'), 'IPN Triggerd', false);
    									$msg =  "Hash validation success.";
    								}
    								else
    								{
    								    $msg=  "Card Type Empty or Mismatched";
    								}
    							}
    							else
    							{
    								$msg=  "Order already in processing Status";
    							}
    						}
    						else
    						{
    							$msg=  "Your Validation id could not be Verified";
    						}
    					}
    					else
    					{
    						$msg= "Your Paid Amount is Mismatched";
    					}	
    				}
    				else
    				{
    					$msg =  "Hash validation failed.";              		
    				}
    				echo $msg;
    			}
			}
			
			else
			{
			    echo "Invalid Status!";
			}
		}
		/// END

}
