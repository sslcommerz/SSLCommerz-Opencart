<?php
/**
 * catalog/model/extension/payment/SSLCommerce.php
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
class ModelExtensionPaymentSSLCommerce extends Model {
  	public function getMethod($address, $total) {
		$this->load->language('extension/payment/SSLCommerce');
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('SSLCommerce_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");
		
		if ($this->config->get('SSLCommerce_total') > $total) {
			$status = false;
		} elseif (!$this->config->get('SSLCommerce_geo_zone_id')) {
			$status = true;
		} elseif ($query->num_rows) {
			$status = true; 
		} else {
			$status = false;
		}	
		
		$method_data = array();
	
		if ($status) {  
      		$method_data = array( 
        		'code'       => 'SSLCommerce',
			'terms'      => '',
        		'title'      => $this->language->get('text_title'),
			'sort_order' => $this->config->get('SSLCommerce_sort_order')
      		);
    	}
   
    	return $method_data;
  	}
}
?>
