<?php
/**
 * SSLCommerz
 * @version 4.0
 * @author Leton Miah <letoncse7@gmail.com>
 * @copyright 2018 https://www.sslcommerz.com.bd
 * Opencat Payment Module V.3.x
 */

class ControllerExtensionPaymentSSLCommerce extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/payment/SSLCommerce');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('payment_SSLCommerce', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', 'SSL'));
		}


 		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['merchant'])) {
			$data['error_merchant'] = $this->error['merchant'];
		} else {
			$data['error_merchant'] = '';
		}
		if (isset($this->error['password'])) {
			$data['error_password'] = $this->error['password'];
		} else {
			$data['error_password'] = '';
		}

  		$data['breadcrumbs'] = array();

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href' => $this->url->link('common/home', 'user_token=' . $this->session->data['user_token'], 'SSL'),
      		'separator' => false
   		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', 'SSL'),
			'separator' => ' :: '
		);

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('extension/payment/SSLCommerce', 'user_token=' . $this->session->data['user_token'], 'SSL'),
      		'separator' => ' :: '
   		);

		$data['action'] = $this->url->link('extension/payment/SSLCommerce', 'user_token=' . $this->session->data['user_token'], 'SSL');

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', 'SSL');
		

		if (isset($this->request->post['payment_SSLCommerce_merchant'])) {
			$data['payment_SSLCommerce_merchant'] = $this->request->post['payment_SSLCommerce_merchant'];
		} else {
			$data['payment_SSLCommerce_merchant'] = $this->config->get('payment_SSLCommerce_merchant');
		}

		if (isset($this->request->post['payment_SSLCommerce_password'])) {
			$data['payment_SSLCommerce_password'] = $this->request->post['payment_SSLCommerce_password'];
		} else {
			$data['payment_SSLCommerce_password'] = $this->config->get('payment_SSLCommerce_password');
		}

		if (isset($this->request->post['payment_SSLCommerce_test'])) {
			$data['payment_SSLCommerce_test'] = $this->request->post['payment_SSLCommerce_test'];
		} else {
			$data['payment_SSLCommerce_test'] = $this->config->get('payment_SSLCommerce_test');
		}

		if (isset($this->request->post['payment_SSLCommerce_total'])) {
			$data['payment_SSLCommerce_total'] = $this->request->post['payment_SSLCommerce_total'];
		} else {
			$data['payment_SSLCommerce_total'] = $this->config->get('payment_SSLCommerce_total');
		}

		if (isset($this->request->post['payment_SSLCommerce_order_status_id'])) {
			$data['payment_SSLCommerce_order_status_id'] = $this->request->post['payment_SSLCommerce_order_status_id'];
		} else {
			$data['payment_SSLCommerce_order_status_id'] = $this->config->get('payment_SSLCommerce_order_status_id');
		}
        if (isset($this->request->post['payment_SSLCommerce_order_fail_id'])) {
			$data['payment_SSLCommerce_order_fail_id'] = $this->request->post['payment_SSLCommerce_order_fail_id'];
		} else {
			$data['payment_SSLCommerce_order_fail_id'] = $this->config->get('payment_SSLCommerce_order_fail_id');
		}
		
		if (isset($this->request->post['payment_SSLCommerce_order_risk_id'])) {
			$data['payment_SSLCommerce_order_risk_id'] = $this->request->post['payment_SSLCommerce_order_risk_id'];
		} else {
			$data['payment_SSLCommerce_order_risk_id'] = $this->config->get('payment_SSLCommerce_order_risk_id');
		}
                
		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		if (isset($this->request->post['payment_SSLCommerce_geo_zone_id'])) {
			$data['payment_SSLCommerce_geo_zone_id'] = $this->request->post['payment_SSLCommerce_geo_zone_id'];
		} else {
			$data['payment_SSLCommerce_geo_zone_id'] = $this->config->get('payment_SSLCommerce_geo_zone_id');
		}

		
		$this->load->model('localisation/geo_zone');

		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		if (isset($this->request->post['payment_SSLCommerce_status'])) {
			$data['payment_SSLCommerce_status'] = $this->request->post['payment_SSLCommerce_status'];
		} else {
			$data['payment_SSLCommerce_status'] = $this->config->get('payment_SSLCommerce_status');
		}

		if (isset($this->request->post['payment_SSLCommerce_sort_order'])) {
			$data['payment_SSLCommerce_sort_order'] = $this->request->post['payment_SSLCommerce_sort_order'];
		} else {
			$data['payment_SSLCommerce_sort_order'] = $this->config->get('payment_SSLCommerce_sort_order');
		}
		
        $string = $this->url->link('extension/payment/SSLCommerce/sslcommerz_ipn', '', 'SSL');
		$data['payment_SSLCommerce_ipn_url'] = preg_replace('~/admin+~', '', $string, 1);
		
        $data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
  
             /* admin/view/template/extension/payment/SSLCommerce.twig */
             
        $string = $this->url->link('extension/payment/SSLCommerce/sslcommerz_ipn', '', 'SSL');
		// $data['payment_SSLCommerce_ipn_url'] = preg_replace('~/admin+~', '', $string, 1);

    	$search    = '/admin';
    	$replace   = '';
    	$str = $this->str_replace_last( $search , $replace , $string );
		$data['payment_SSLCommerce_ipn_url'] = $str;
		
        $data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
  
             /* admin/view/template/extension/payment/SSLCommerce.twig */
  
		$this->response->setOutput($this->load->view('extension/payment/SSLCommerce', $data));
  
		$this->response->setOutput($this->load->view('extension/payment/SSLCommerce', $data));
		
	}
	
	private function str_replace_last( $search , $replace , $str ) {
	    if( ( $pos = strrpos( $str , $search ) ) !== false ) {
	        $search_length  = strlen( $search );
	        $str    = substr_replace( $str , $replace , $pos , $search_length );
	    }
	    return $str;
	}

	private function validate() {
		if (!$this->user->hasPermission('modify', 'extension/payment/SSLCommerce')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['payment_SSLCommerce_merchant']) {
			$this->error['merchant'] = $this->language->get('error_merchant');
		}
		if (!$this->request->post['payment_SSLCommerce_password']) {
			$this->error['password'] = $this->language->get('error_password');
		}

		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}
}
?>