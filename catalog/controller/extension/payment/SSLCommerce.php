<?php
/**
 * catalog/controller/extension/payment/SSLCommerce.php
 *
 * Copyright (c) 2009-2016 Software Shop Limited
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
 * @author     JM Redwan
 * @copyright  2009-2016 SSLCommerz
 * @license    http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @version    2.0.0
 */
class ControllerExtensionPaymentSSLCommerce extends Controller {
	// Payment Process Funciton 
	public function index() {
$this->language->load( 'extension/payment/SSLCommerce' );
		$data['button_confirm'] = $this->language->get('button_confirm');

		$this->load->model('checkout/order');

		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

		$data['store_id'] = $this->config->get('SSLCommerce_merchant');
		$data['tran_id'] = $this->session->data['order_id'];
		$data['total_amount'] = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);
		
			$data['SSLCommerce_password'] = $this->config->get('SSLCommerce_password');

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
              
		$data['currency'] = $this->session->data['currency'];
		$data['success_url'] = $this->url->link('extension/payment/SSLCommerce/callback', '', 'SSL');
        $data['fail_url'] = $this->url->link('extension/payment/SSLCommerce/failure', '', 'SSL');
        $data['cancel_url'] = $this->url->link('checkout/cart', '', 'SSL');
		
		////Hash Key Gernarate For SSL
		$security_key = $this->sslcommerz_hash_key($this->config->get('SSLCommerce_password'), $data);
		
		$data['verify_sign'] = $security_key['verify_sign'];
        $data['verify_key'] = $security_key['verify_key'];

		
		
		$products = '';
		
		
		foreach ($this->cart->getProducts() as $product) {
    		$products .= $product['quantity'] . ' x ' . $product['name'] . ', ';
    	}		
		
		$data['detail1_text'] = $products;
		
		
		if($this->config->get('SSLCommerce_test')=='live') {
				$data['process_url'] = 'https://securepay.sslcommerz.com/gwprocess/v3/process.php';
			}
		else {
				$data['process_url'] = 'https://sandbox.sslcommerz.com/gwprocess/v3/process.php';
			}


		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . 'extension/payment/SSLCommerce.tpl')) {
			return $this->load->view($this->config->get('config_template') . 'extension/payment/SSLCommerce.tpl', $data);
		} else {
			return $this->load->view('extension/payment/SSLCommerce.tpl', $data);
		}
	}
	
	
	public function callback() {
                
				$SSLCommerce_test = $this->config->get('SSLCommerce_test');
                $store_id = urldecode($this->config->get('SSLCommerce_merchant'));
                $store_passwd = urldecode($this->config->get('SSLCommerce_password'));
               if (isset($_POST['tran_id'])) {
					$order_id = $_POST['tran_id'];
							       											 
				} else {
					$order_id = 0;
				}
                if (isset($_POST['amount'])) {
                    $total=$_POST['amount'];
					
				}else
                	{
                    $total='';	
                   
                }
				if(isset($_POST['val_id'])){
					$val_id = urldecode($_POST['val_id']); 
					}
				else {
					 $val_id = ''; 
					}
                
					
					
		$this->load->model('checkout/order');
		$order_info = $this->model_checkout_order->getOrder($order_id);
        $amount = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);
                                       
        
if(empty($val_id)){
						if($this->config->get('SSLCommerce_test')=='live') {
						  $valid_url_own = ("https://securepay.sslcommerz.com/validator/api/merchantTransIDvalidationAPI.php?tran_id=".$order_id."&Store_Id=".$store_id."&Store_Passwd=".$store_passwd."&v=1&format=json"); 
						 
						  } else{
							 $valid_url_own = ("https://sandbox.sslcommerz.com/validator/api/merchantTransIDvalidationAPI.php?tran_id=".$order_id."&Store_Id=".$store_id."&Store_Passwd=".$store_passwd."&v=1&format=json");  
						  }
						 
			$ownvalid = curl_init();
			curl_setopt($ownvalid, CURLOPT_URL, $valid_url_own);
			curl_setopt($ownvalid, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ownvalid, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($ownvalid, CURLOPT_SSL_VERIFYPEER, false);
			
			$ownvalid_result = curl_exec($ownvalid);
			
			$ownvalid_code = curl_getinfo($ownvalid, CURLINFO_HTTP_CODE);
			
			if($ownvalid_code == 200 && !( curl_errno($ownvalid)))
			{
				$result_own = json_decode($ownvalid_result, true);
				$lastupdate_no = $result_own['no_of_trans_found']-1;	
				$own_data = $result_own['element']; 
				$val_id = $own_data[$lastupdate_no]['val_id'];
				//echo $own_data[0]['val_id'];
			}
						 
					 
						 
}


			
                if($this->config->get('SSLCommerce_test')=='live') {
                $requested_url = ("https://securepay.sslcommerz.com/validator/api/validationserverAPI.php?val_id=".$val_id."&Store_Id=".$store_id."&Store_Passwd=".$store_passwd."&v=1&format=json");
                } else{
               $requested_url = ("https://sandbox.sslcommerz.com/validator/api/validationserverAPI.php?val_id=".$val_id."&Store_Id=".$store_id."&Store_Passwd=".$store_passwd."&v=1&format=json");  
                }  
				
                $amount = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);
                $handle = curl_init();
curl_setopt($handle, CURLOPT_URL, $requested_url);
curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);

$result = curl_exec($handle);

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
	

	$orderAmount_SITE = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);
	
	if($amount < $orderAmount_SITE){
		$risk_level==1;
	}

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
			$this->language->load( 'extension/payment/SSLCommerce' );
	
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
	
	
	
			if (isset($status) && $status == 'success') {
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
	
                   $this->model_checkout_order->addOrderHistory($order_id, $this->config->get('SSLCommerce_order_status_id'), $message, false);
	$error='';
                    $data['text_message'] = sprintf('your payment was successfully received', $error, $this->url->link('information/contact'));
			$data['continue'] = $this->url->link('checkout/success');
            $data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . 'extension/payment/success.tpl')) {
				$this->response->setOutput($this->load->view($this->config->get('config_template') . 'extension/payment/success.tpl', $data));
			} else {
				$this->response->setOutput($this->load->view('extension/payment/success.tpl', $data));
			}

			}
			else if (isset($status) && $status == 'risk') {
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
					
	            $this->model_checkout_order->addOrderHistory($order_id, $this->config->get('SSLCommerce_order_risk_id'), $message, false);
				$this->model_checkout_order->update($order_id, $this->config->get('SSLCommerce_order_risk_id'), $message, false);
	
				$data['continue'] = $this->url->link('checkout/checkout');
            $data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

				if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . 'extension/payment/Commerce_risk.tpl')) {
					$this->response->setOutput($this->load->view($this->config->get('config_template') . 'extension/payment/Commerce_risk.tpl', $data));
				} else {
					$this->response->setOutput($this->load->view('extension/payment/Commerce_risk.tpl', $data));
				}

			} else {
				
				
				
				
				
			$data['continue'] = $this->url->link('checkout/cart');
            $data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . 'extension/payment/Commerce_failure.tpl')) {
				$this->response->setOutput($this->load->view($this->config->get('config_template') . 'extension/payment/Commerce_failure.tpl', $data));
			} else {
				$this->response->setOutput($this->load->view('extension/payment/Commerce_failure.tpl', $data));
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
		/// END

}
