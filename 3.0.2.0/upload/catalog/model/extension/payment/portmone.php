<?php
class ModelExtensionPaymentPortmone extends Model {
    public function getMethod($address, $total) {
        $this->load->language('extension/payment/portmone');
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('payment_portmone_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");

        if (!$this->config->get('payment_portmone_geo_zone_id')) {
            $status = true;
        } elseif ($query->num_rows) {
            $status = true;
        } else {
            $status = false;
        }

        $method_data = array();
        if ($status) {
            if ($this->config->get('payment_portmone_entry_showlogo') == '1') {
                $portmone_title = $this->language->get('img_portmone');
                $portmone_img = '';
            } else {
                $portmone_title = $this->config->get('payment_portmone_name');
                $portmone_img = '';
            }
            $method_data = array(
                'code'       => 'portmone',
                'title'      => $portmone_title,
                'terms'      => $portmone_img,
                'sort_order' => $this->config->get('payment_portmone_sort_order')
            );
        }
    return $method_data;
    }
}