<?php
/**
 * admin/controller/extension/payment/SSLCommerce.php
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
	private $error = array();

	public function index() {
		$this->load->language('extension/payment/SSLCommerce');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('SSLCommerce', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			//$this->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
                        $this->response->redirect($this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=payment', true));
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_all_zones'] = $this->language->get('text_all_zones');
		$data['text_yes'] = $this->language->get('text_yes');
		$data['text_no'] = $this->language->get('text_no');
		$data['text_live'] = $this->language->get('text_live');
		$data['text_successful'] = $this->language->get('text_successful');
		$data['text_fail'] = $this->language->get('text_fail');

		$data['enter_store_id'] = $this->language->get('enter_store_id');
		$data['entry_store_password'] = $this->language->get('entry_store_password');
		$data['entry_test'] = $this->language->get('entry_test');
		$data['entry_total'] = $this->language->get('entry_total');
		$data['entry_order_status'] = $this->language->get('entry_order_status');
		$data['entry_order_fail_status'] = $this->language->get('entry_order_fail_status');
		$data['entry_order_risk_status'] = $this->language->get('entry_order_risk_status');
		$data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_sort_order'] = $this->language->get('entry_sort_order');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

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

  		$data['breadcrumbs'] = array();

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_payment'),
			'href'      => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('extension/payment/SSLCommerce', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);

		$data['action'] = $this->url->link('extension/payment/SSLCommerce', 'token=' . $this->session->data['token'], 'SSL');

		$data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');

		if (isset($this->request->post['SSLCommerce_merchant'])) {
			$data['SSLCommerce_merchant'] = $this->request->post['SSLCommerce_merchant'];
		} else {
			$data['SSLCommerce_merchant'] = $this->config->get('SSLCommerce_merchant');
		}

		if (isset($this->request->post['SSLCommerce_password'])) {
			$data['SSLCommerce_password'] = $this->request->post['SSLCommerce_password'];
		} else {
			$data['SSLCommerce_password'] = $this->config->get('SSLCommerce_password');
		}

		if (isset($this->request->post['SSLCommerce_test'])) {
			$data['SSLCommerce_test'] = $this->request->post['SSLCommerce_test'];
		} else {
			$data['SSLCommerce_test'] = $this->config->get('SSLCommerce_test');
		}

		if (isset($this->request->post['SSLCommerce_total'])) {
			$data['SSLCommerce_total'] = $this->request->post['SSLCommerce_total'];
		} else {
			$data['SSLCommerce_total'] = $this->config->get('SSLCommerce_total');
		}

		if (isset($this->request->post['SSLCommerce_order_status_id'])) {
			$data['SSLCommerce_order_status_id'] = $this->request->post['SSLCommerce_order_status_id'];
		} else {
			$data['SSLCommerce_order_status_id'] = $this->config->get('SSLCommerce_order_status_id');
		}
        if (isset($this->request->post['SSLCommerce_order_fail_id'])) {
			$data['SSLCommerce_order_fail_id'] = $this->request->post['SSLCommerce_order_fail_id'];
		} else {
			$data['SSLCommerce_order_fail_id'] = $this->config->get('SSLCommerce_order_fail_id');
		}
		
		if (isset($this->request->post['SSLCommerce_order_risk_id'])) {
			$data['SSLCommerce_order_risk_id'] = $this->request->post['SSLCommerce_order_risk_id'];
		} else {
			$data['SSLCommerce_order_risk_id'] = $this->config->get('SSLCommerce_order_risk_id');
		}
                
		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		if (isset($this->request->post['SSLCommerce_geo_zone_id'])) {
			$data['SSLCommerce_geo_zone_id'] = $this->request->post['SSLCommerce_geo_zone_id'];
		} else {
			$data['SSLCommerce_geo_zone_id'] = $this->config->get('SSLCommerce_geo_zone_id');
		}

		$this->load->model('localisation/geo_zone');

		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		if (isset($this->request->post['SSLCommerce_status'])) {
			$data['SSLCommerce_status'] = $this->request->post['SSLCommerce_status'];
		} else {
			$data['SSLCommerce_status'] = $this->config->get('SSLCommerce_status');
		}

		if (isset($this->request->post['SSLCommerce_sort_order'])) {
			$data['SSLCommerce_sort_order'] = $this->request->post['SSLCommerce_sort_order'];
		} else {
			$data['SSLCommerce_sort_order'] = $this->config->get('SSLCommerce_sort_order');
		}
$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/payment/SSLCommerce.tpl', $data));
		
	}

	private function validate() {
		if (!$this->user->hasPermission('modify', 'extension/payment/SSLCommerce')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['SSLCommerce_merchant']) {
			$this->error['merchant'] = $this->language->get('error_merchant');
		}

		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}
}
?>