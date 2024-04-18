<?php
class ControllerExtensionPaymentPortmone extends Controller {
    public $version = '4.0.3';
    public $requires_OC_at_least = '3.0.3.9';
    public $tested_OC_up_to = '3.0.3.9';
    private $error = array();
    private $text_data = array(
        'heading_title'             ,
        'text_portmone'             ,
        'text_edit'                 ,
        'text_enabled'              ,
        'text_disabled'             ,
        'text_all_zones'            ,
        'text_pay'                  ,
        'text_card'                 ,
        'tab_general'               ,
        'tab_order_status'          ,
        'entry_status'              ,
        'h_entry_status'            ,
        'entry_payee_id'            ,
        'h_entry_payee_id'          ,
        'entry_login'               ,
        'h_entry_login'             ,
        'entry_pass'                ,
        'h_entry_pass'              ,
        'entry_order_confirm_stat'  ,
        'h_entry_order_confirm_stat',
        'entry_order_stat'          ,
        'h_entry_order_stat'        ,
        'entry_order_stat_fa'       ,
        'h_entry_order_stat_fa'     ,
        'entry_order_stat_preauth'  ,
        'h_entry_order_stat_preauth',
        'entry_order_stat_completed',
        'h_entry_order_stat_completed',
        'entry_geo_zone'            ,
        'entry_preauth_flag'        ,
        'h_entry_preauth_flag'      ,
        'entry_showlogo'            ,
        'h_entry_showlogo'          ,
        'entry_sort_order'          ,
        'h_entry_sort_order'        ,
        'OP_version'                ,
        'Plugin_version'            ,
        'help_total'                ,
        'button_save'               ,
        'button_cancel'             ,
        'h_entry_name'              ,
        'entry_name'                ,
        'entry_key'           		,
        'h_entry_key'          		,
        'entry_exp_time'           	,
        'h_entry_exp_time'          ,
    );
    private $error_data = array(
        'warning'   ,
        'payee_id'  ,
        'login'     ,
        'pass'      ,
        'type'      ,
        'key'		,
    );
    private $post_data = array(
        'status'                    ,
        'name'                      ,
        'payee_id'                  ,
        'login'                     ,
        'pass'                      ,
        'order_stat_id'             ,
        'order_stat_fal_id'         ,
        'order_stat_not_verified_id',
        'order_stat_preauth_id'     ,
        'order_stat_completed_id'   ,
        'entry_preauth_flag'        ,
        'entry_showlogo'            ,
        'sort_order'                ,
        'geo_zone_id'               ,
        'key'						,
        'exp_time'					,
    );
    private $currency_add_uan = array (
        'title'         => 'Гривна',
        'code'          => 'UAN',
        'symbol_left'   => '₴' ,
        'symbol_right'  => 'грн' ,
        'decimal_place' => '2' ,
        'value'         => '0.00000000' ,
        'status'        => '0',
    );
    public $statuses = [
        'payment_portmone_order_stat_id'                => ['language_id' => 1,'name' => '<b style="color:#2abb1a;">(Portmone.com) Оплачен</b>'],
        'payment_portmone_order_stat_not_verified_id'   => ['language_id' => 1,'name' => '<b style="color:#13580b;">(Portmone.com) Оплачено (но не проверено)</b>'],
        'payment_portmone_order_stat_fal_id'            => ['language_id' => 1,'name' => '<b style="color:#ef0c0c;">(Portmone.com) Оплата не прошла</b>'],
        'payment_portmone_order_stat_preauth_id'        => ['language_id' => 1,'name' => '<b style="color:#ffd400;">(Portmone.com) Оплачено (блокировка средств)</b>'],
        'payment_portmone_order_stat_completed_id'      => ['language_id' => 1,'name' => '<b style="color:#0000ff;">(Portmone.com) Завершён</b>']
    ];

    public function __construct($registry) {
        parent::__construct($registry);
        $this->load->language('extension/payment/portmone');
        $this->document->setTitle($this->language->get('heading_title'));
    }

    public function portmone_notice($post) {
        $payment_portmone_view_error = $this->config->get('payment_portmone_view_error');

        if(!empty($payment_portmone_view_error)) {
            $post['payment_portmone_view_error'] = $payment_portmone_view_error;
            $this->error['warning'] = $payment_portmone_view_error . ' ' . $this->language->get('notice_description');
        }
        return $post;
    }

    function OC_actual() {
        if (VERSION >= $this->requires_OC_at_least && VERSION <= $this->tested_OC_up_to) {
            return '<span style="color: green">('.$this->language->get('plagin_status_success').')</span>';
        } else {
            return '<span style="color: #e7a511;">('.$this->language->get('plagin_status_warning').')</span>';
        }
    }

    public function index() {
        $this->load->language('extension/payment/portmone');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('setting/setting');
        $this->load->model('localisation/currency');
        $post = $this->portmone_notice($this->request->post);

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('payment_portmone', $post);
            $this->session->data['success'] = $this->language->get('text_success');
            $this->response->redirect($this->makeUrl('extension/payment/portmone'));
        }

        $data['entry_OP_version'] = VERSION;
        $data['OC_actual'] = $this->OC_actual();
        $data['entry_Plugin_version'] = $this->version;

        foreach ($this->text_data as $value) {
            $data[$value] = $this->language->get($value);
        }

        $currency_uan = $this->model_localisation_currency->getCurrencyByCode('UAN');
        if(empty($currency_uan)){
            $this->currency_add_uan();
        }

        foreach ($this->error_data as $value) {
            if (isset($this->error[$value])) {
                $data['error_'.$value] = $this->error[$value];
            } else {
                $data['error_'.$value] = '';
            }
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->makeUrl('common/dashboard')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_payment'),
            'href' => $this->makeUrl('marketplace/extension')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->makeUrl('extension/payment/portmone')
        );

        if(!empty($this->session->data['success'])){
            $data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        }

        $data['action'] = $this->makeUrl('extension/payment/portmone');
        $data['cancel'] = $this->makeUrl('marketplace/extension');
        $this->load->model('localisation/order_status');
        $this->load->model('localisation/geo_zone');

        $data['order_statuses']                 = $this->model_localisation_order_status->getOrderStatuses();
        $data['geo_zones']                      = $this->model_localisation_geo_zone->getGeoZones();

        foreach ($this->post_data as $value) {
            if (isset($this->request->post['payment_portmone_'.$value])) {
                $data['payment_portmone_'.$value] = $this->request->post['payment_portmone_'.$value];
            } else {
                $data['payment_portmone_'.$value] = $this->config->get('payment_portmone_'.$value);
            }
        }
        $data['payment_portmone_order_confirm_id'] = 1;

        $payment_portmone_name_val = $this->config->get('payment_portmone_name');
        if(!isset($payment_portmone_name_val)){
            $data['payment_portmone_name'] = 'Portmone.com';
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $this->response->setOutput($this->load->view('extension/payment/portmone', $data));
    }

    private function createOrderStatusesPortmone() {
        $this->model_extension_payment_portmone->updateTableStatusOrders();
        $this->model_extension_payment_portmone->addOrderStatus($this->statuses);
    }

    private function deleteOrderStatusesPortmone() {
        $this->model_extension_payment_portmone->deleteOrderStatus($this->statuses);
    }

    private function currency_add_uan() {
        $this->model_localisation_currency->addCurrency($this->currency_add_uan);
    }

    public function install() {
        $this->load->model('extension/payment/portmone');
        $this->createOrderStatusesPortmone();
    }

    public function uninstall() {
        $this->load->model('extension/payment/portmone');
        $this->deleteOrderStatusesPortmone();
    }

    protected function makeUrl($route, $url = '') {
        return str_replace('&amp;', '&', $this->url->link($route, $url . '&user_token=' . $this->session->data['user_token'], 'SSL'));
    }

    protected function validate() {
        unset($this->error['warning']);

        if (!$this->user->hasPermission('modify', 'extension/payment/portmone')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }
        if (!$this->request->post['payment_portmone_payee_id']) {
            $this->error['payee_id'] = $this->language->get('error_payee_id');
        }
        if (!$this->request->post['payment_portmone_key']) {
            $this->error['key'] = $this->language->get('error_key');
        }
        if (!$this->request->post['payment_portmone_login']) {
            $this->error['login'] = $this->language->get('error_login');
        }
        if (!$this->request->post['payment_portmone_pass']) {
            $this->error['pass'] = $this->language->get('error_pass');
        }

        return !$this->error;
    }
}
