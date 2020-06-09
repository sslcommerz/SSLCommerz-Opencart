<?php
/**
 * 
 * PHP4 und PHP5
 *
 * @version 1.1
 * @author JM Redwan <me@jmredwan.com>
 * @copyright 2012 https://www.sslcommerz.com.bd
 * Free Payment Module for OpenCart.com
 */
class ControllerPaymentSSLCommerce extends Controller {
	protected function index() {
    	$this->data['button_confirm'] = $this->language->get('button_confirm');

		$this->load->model('checkout/order');

		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

		$this->data['merchant'] = $this->config->get('SSLCommerce_merchant');
                                        $this->data['SSLCommerce_test'] = $this->config->get('SSLCommerce_test');
              //  print_r($this->data['SSLCommerce_test']);exit;successful
		$this->data['trans_id'] = $this->session->data['order_id'];
		$this->data['amount'] = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);
		
		if ($this->config->get('SSLCommerce_password')) {
			$this->data['SSLCommerce_password'] = $this->config->get('SSLCommerce_password');
		} else {
			$this->data['digest'] = '';
		}		
		
		$this->data['bill_name'] = $order_info['payment_firstname'] . ' ' . $order_info['payment_lastname'];
		$this->data['bill_addr_1'] = $order_info['payment_address_1'];
		$this->data['bill_addr_2'] = $order_info['payment_address_2'];
		$this->data['bill_city'] = $order_info['payment_city'];
		$this->data['bill_state'] = $order_info['payment_zone'];
		$this->data['bill_post_code'] = $order_info['payment_postcode'];
		$this->data['bill_country'] = $order_info['payment_country'];
		$this->data['bill_tel'] = $order_info['telephone'];
		$this->data['bill_email'] = $order_info['email'];

		if ($this->cart->hasShipping()) {
			$this->data['ship_name'] = $order_info['shipping_firstname'] . ' ' . $order_info['shipping_lastname'];
			$this->data['ship_addr_1'] = $order_info['shipping_address_1'];
			$this->data['ship_addr_2'] = $order_info['shipping_address_2'];
			$this->data['ship_city'] = $order_info['shipping_city'];
			$this->data['ship_state'] = $order_info['shipping_zone'];
			$this->data['ship_post_code'] = $order_info['shipping_postcode'];
			$this->data['ship_country'] = $order_info['shipping_country'];
		} else {
			$this->data['ship_name'] = '';
			$this->data['ship_addr_1'] = '';
			$this->data['ship_addr_2'] = '';
			$this->data['ship_city'] = '';
			$this->data['ship_state'] = '';
			$this->data['ship_post_code'] = '';
			$this->data['ship_country'] = '';
		}

		$this->data['currency'] = $this->currency->getCode();
		$this->data['callback'] = $this->url->link('payment/SSLCommerce/callback', '', 'SSL');
        $this->data['cancel'] = $this->url->link('checkout/cart', '', 'SSL');
		$products = '';
		
		foreach ($this->cart->getProducts() as $product) {
    		$products .= $product['quantity'] . ' x ' . $product['name'] . ', ';
    	}		
		
		$this->data['detail1_text'] = $products;

		if($this->config->get('SSLCommerce_test')=='live') {
				$this->data['URL'] = 'https://securepay.sslcommerz.com/gwprocess/v3/process.php';
			}
		else {
				$this->data['URL'] = 'https://sandbox.sslcommerz.com/gwprocess/v3/process.php';
			}
			
			
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/SSLCommerce.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/payment/SSLCommerce.tpl';
		} else {
			$this->template = 'default/template/payment/SSLCommerce.tpl';
		}

		$this->render();
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
//echo $order_id;

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
                     
					              
                $this->load->model('checkout/order');
				$order_info = $this->model_checkout_order->getOrder($order_id);
                $amount = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);
                $handle = curl_init();
curl_setopt($handle, CURLOPT_URL, $requested_url);
curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);

$result = curl_exec($handle);

$code = curl_getinfo($handle, CURLINFO_HTTP_CODE);


//echo $requested_url;
		
							//echo '<pre>';
						 // print_r($result);
						  //echo '</pre>';			  
					  
				//exit;	 

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
            
//print_r($status);
		//exit;							
		if ($order_info && isset($status)) {
			$this->language->load('payment/SSLCommerce');
	
			$this->data['title'] = sprintf($this->language->get('heading_title'), $this->config->get('config_name'));
	
			if (!isset($this->request->server['HTTPS']) || ($this->request->server['HTTPS'] != 'on')) {
				$this->data['base'] = HTTP_SERVER;
			} else {
				$this->data['base'] = HTTPS_SERVER;
			}
	
			$this->data['language'] = $this->language->get('code');
			$this->data['direction'] = $this->language->get('direction');
	
			$this->data['heading_title'] = sprintf($this->language->get('heading_title'), $this->config->get('config_name'));
	
			$this->data['text_response'] = $this->language->get('text_response');
			$this->data['text_success'] = $this->language->get('text_success');
			$this->data['text_success_wait'] = sprintf($this->language->get('text_success_wait'), $this->url->link('checkout/success'));
			$this->data['text_failure'] = $this->language->get('text_failure');
			$this->data['text_failure_wait'] = sprintf($this->language->get('text_failure_wait'), $this->url->link('checkout/cart'));
	
			if (isset($status) && $status == 'success') {
				$this->load->model('checkout/order');
	
				$this->model_checkout_order->confirm($tran_id, $this->config->get('config_order_status_id'));
	
				$message = '';
	
				
					$message .= 'Payment Status = ' . $status . "\n";
				    
					$message .= 'Bank txnid = ' . $bank_tran_id . "\n";
				   
					$message .= 'Your Oder id = ' . $tran_id . "\n";
					
					$message .= 'Payment Date = ' . $tran_date . "\n";  
				   
					$message .= 'Card Number = ' .$card_no . "\n"; 
				   
					$message .= 'Card Type = ' .$card_brand .'-'. $card_type . "\n"; 
				    
					$message .= 'Transaction Risk Level = ' .$risk_level . "\n"; 
				   
					$message .= 'Transaction Risk Description = ' .$risk_title . "\n"; 
				   
					
	
				$this->model_checkout_order->update($order_id, $this->config->get('SSLCommerce_order_status_id'), $message, false);  
	
				$this->data['continue'] = $this->url->link('checkout/success');
	
				if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/success.tpl')) {
					$this->template = $this->config->get('config_template') . '/template/payment/success.tpl';
				} else {
					$this->template = 'default/template/payment/success.tpl';
				}
	
				$this->children = array(  
					'common/column_left',
					'common/column_right',
					'common/content_top',
					'common/content_bottom',
					'common/footer',
					'common/header'
				);
	
				$this->response->setOutput($this->render());
			}
			else if (isset($status) && $status == 'risk') {
				$this->load->model('checkout/order');
	
				$this->model_checkout_order->confirm($tran_id, $this->config->get('config_order_status_id'));
	
				$message = '';
	
				
					$message .= 'Payment Status = ' . $status . "\n";
				    
					$message .= 'Bank txnid = ' . $bank_tran_id . "\n";
				   
					$message .= 'Your Oder id = ' . $tran_id . "\n";
					
					$message .= 'Payment Date = ' . $tran_date . "\n";  
				   
					$message .= 'Card Number = ' .$card_no . "\n"; 
				   
					$message .= 'Card Type = ' .$card_brand .'-'. $card_type . "\n"; 
				    
					$message .= 'Transaction Risk Level = ' .$risk_level . "\n"; 
				   
					$message .= 'Transaction Risk Description = ' .$risk_title . "\n"; 
				   
	
				$this->model_checkout_order->update($order_id, $this->config->get('SSLCommerce_order_risk_id'), $message, false);
	
				$this->data['continue'] = $this->url->link('checkout/success');
	
				if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/Commerce_risk.tpl')) {
					$this->template = $this->config->get('config_template') . '/template/payment/Commerce_risk.tpl';
				} else {
					$this->template = 'default/template/payment/Commerce_failure.tpl';
				}
	
				$this->children = array(
					'common/column_left',
					'common/column_right',
					'common/content_top',
					'common/content_bottom',
					'common/footer',
					'common/header'
				);
	
				$this->response->setOutput($this->render());
			} else {
				$this->load->model('checkout/order');
	
				$this->model_checkout_order->confirm($tran_id, $this->config->get('config_order_status_id'));
	
				$message = '';
	
				
					$message .= 'Payment Status = ' . $status . "\n";
				    
					$message .= 'Bank txnid = ' . $bank_tran_id . "\n";
				   
					$message .= 'Your Oder id = ' . $tran_id . "\n";
					
					$message .= 'Payment Date = ' . $tran_date . "\n";  
				   
					$message .= 'Card Number = ' .$card_no . "\n"; 
				   
					$message .= 'Card Type = ' .$card_brand .'-'. $card_type . "\n"; 
				    
					$message .= 'Transaction Risk Level = ' .$risk_level . "\n"; 
				   
					$message .= 'Transaction Risk Description = ' .$risk_title . "\n"; 
				   
	
				$this->model_checkout_order->update($order_id, $this->config->get('SSLCommerce_order_fail_id'), $message, false);
	
				$this->data['continue'] = $this->url->link('checkout/checkout');
	
				if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/Commerce_failure.tpl')) {
					$this->template = $this->config->get('config_template') . '/template/payment/Commerce_failure.tpl';
				} else {
					$this->template = 'default/template/payment/Commerce_failure.tpl';
				}
	
				$this->children = array(
					'common/column_left',
					'common/column_right',
					'common/content_top',
					'common/content_bottom',
					'common/footer',
					'common/header'
				);
	
				$this->response->setOutput($this->render());
			}
		}
	}
}
?>
