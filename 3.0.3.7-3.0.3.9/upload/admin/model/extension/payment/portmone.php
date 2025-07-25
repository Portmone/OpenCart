<?php
class ModelExtensionPaymentPortmone extends Model {
    public function updateTableStatusOrders() {
        $this->db->query("ALTER TABLE `" . DB_PREFIX . "order_status` MODIFY `name` VARCHAR(300) NOT NULL ");
    }

    public function addOrderStatus($data) {
        $this->load->model('setting/setting');
        foreach ($data as $id => $value) {
            $this->db->query("DELETE FROM " . DB_PREFIX . "order_status WHERE name = '" . $value['name'] . "'");
            $this->db->query("INSERT INTO " . DB_PREFIX . "order_status SET language_id = '" . (int)$value['language_id'] . "', name = '" . $value['name'] . "'");
            $order_status_id = $this->db->getLastId();
            $this->createSetting('payment_portmone', [$id => $order_status_id]);
        }

        $this->cache->delete('order_status');
        return $order_status_id;
    }

    public function deleteOrderStatus($data) {
        foreach ($data as $id => $value) {
            $this->db->query("DELETE FROM " . DB_PREFIX . "order_status WHERE name = '" . $value['name'] . "'");
        }
    }

    public function createSetting($code, $data, $store_id = 0) {
        foreach ($data as $key => $value) {
            if (substr($key, 0, strlen($code)) == $code) {
                if (!is_array($value)) {
                    $this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '" . (int)$store_id . "', `code` = '" . $this->db->escape($code) . "', `key` = '" . $this->db->escape($key) . "', `value` = '" . $this->db->escape($value) . "'");
                } else {
                    $this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '" . (int)$store_id . "', `code` = '" . $this->db->escape($code) . "', `key` = '" . $this->db->escape($key) . "', `value` = '" . $this->db->escape(json_encode($value, true)) . "', serialized = '1'");
                }
            }
        }
    }

}