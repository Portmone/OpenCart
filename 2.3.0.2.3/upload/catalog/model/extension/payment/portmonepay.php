<?php
class ModelExtensionPaymentPortmonepay extends Model {
    public function getMethod($address, $total) {
        $this->load->language('extension/payment/portmonepay');
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('portmonepay_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");

        if (!$this->config->get('portmonepay_geo_zone_id')) {
            $status = true;
        } elseif ($query->num_rows) {
            $status = true;
        } else {
            $status = false;
        }

        $method_data = array();
        if ($status) {
            if ($this->config->get('portmonepay_entry_showlogo') == '1') {
                $portmone_title = $this->language->get('img_portmonepay');
                $portmone_img = $this->config->get('portmonepay_name');
            } else {
                $portmone_title = $this->config->get('portmonepay_name');
                $portmone_img = '';
            }
            $method_data = array(
                'code'       => 'portmonepay',
                'title'      => $portmone_title,
                'terms'      => $portmone_img,
                'sort_order' => $this->config->get('portmonepay_sort_order')
            );
        }
    return $method_data;
    }
}