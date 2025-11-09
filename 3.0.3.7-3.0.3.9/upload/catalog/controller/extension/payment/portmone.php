<?php
class ControllerExtensionPaymentPortmone extends Controller {
    public $version         = '4.0.7';
    const ORDER_PAYED       = 'PAYED';
    const ORDER_CREATED     = 'CREATED';
    const ORDER_REJECTED    = 'REJECTED';
    const ORDER_PREAUTH     = 'PREAUTH';
    private $liveurl        = 'https://www.portmone.com.ua/gateway/';
    private $msg            = array();
    private $request_orderId;
    private $order_status;

    public function index() {
        $this->load->language('extension/payment/portmone');
        $this->load->model('checkout/order');
        $data['confirm'] = $this->url->link('extension/payment/portmone/confirm', '', 'SSL');

        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        $getProducts = $this->cart->getProducts();
        if (is_array($getProducts)) {
            $description_order = '';
            foreach($getProducts as $product) {
                $description = '(IDproduct ' . $product['product_id'] . ') (quantity '. $product['quantity'] . ') | ';
                $description_order .= $description;
            }
            $description_order .= 'TOTAL '. $this->currency->format(
                    $order_info['total'],
                    $order_info['currency_code'],
                    $order_info['currency_value'],
                    false
                );
        }

        $data['button_pay']         = $this->language->get('button_pay');
        $data['action']             = $this->liveurl;
        $data['payee_id']           = $this->config->get('payment_portmone_payee_id');
        $data['exp_time']           = $this->config->get('payment_portmone_exp_time');
        $data['shop_order_number']  = $this->session->data['order_id'].'_'.time();
        $data['bill_amount']        = $this->currency->format($order_info['total'],
            $order_info['currency_code'],
            $order_info['currency_value'], false);
        $data['bill_currency']      = $this->getBillCurrencyCode($order_info['currency_code']);
        $data['description']        = $description_order;
        $data['success_url']        = $this->url->link('extension/payment/portmone/callback', '', 'SSL');
        $data['failure_url']        = $this->url->link('extension/payment/portmone/callback', '', 'SSL');
        $data['lang']               = $this->getLanguage();
        $data['preauth_flag']       = ($this->config->get('payment_portmone_entry_preauth_flag') == 1)? 'Y' : 'N' ;
        $data['attribute1']         = ($this->config->get('payment_portmone_entry_client_first_last_name_flag') == 1)? $order_info['payment_firstname'] .' ' . $order_info['payment_lastname'] : '' ;
        $data['attribute2']         = ($this->config->get('payment_portmone_entry_client_phone_number_flag') == 1)? $order_info['telephone'] : '' ;
        $data['attribute3']         = ($this->config->get('payment_portmone_entry_client_email_flag') == 1)? $order_info['email'] : '' ;
        $data['cms_module_name']    = json_encode(['name' => 'OpenCart', 'v' => $this->version]);
        $data['payment_portmone_order_confirm_status_id'] = 1 ;
        $key						=$this->config->get('payment_portmone_key');
        $data['dt']       			= date('Ymdhis');
        $signature					= $data['payee_id'].$data['dt'].bin2hex($data['shop_order_number']).$data['bill_amount'] ;
        $signature 					= strtoupper($signature).strtoupper(bin2hex($this->config->get('payment_portmone_login')));
        $signature 					= strtoupper(hash_hmac('sha256', $signature, $key));
        $data['signature']    		= $signature;

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/extension/payment/portmone')) {
            return $this->load->view($this->config->get('config_template') . '/template/extension/payment/portmone', $data);
        } else {
            return $this->load->view('extension/payment/portmone', $data);
        }
    }

    public function confirm() {
        if (!empty($this->session->data['order_id']) && ($this->session->data['payment_method']['code'] == 'portmone')) {
            $this->load->model('checkout/order');
            $this->model_checkout_order->addOrderHistory(
                $this->session->data['order_id'],
                $this->config->get('payment_portmone_order_confirm_id'),
                '#2P '.'Користувач перейшов на Portmone.com для оплати' ,
                '1');
        }
    }

    private function getBillCurrencyCode($currencyCode) {
        if(in_array($currencyCode, ['UAH', 'USD', 'EUR'])) {
            return $currencyCode;
        } else {
            return 'UAH';
        }
    }

    /**
     * Init language
     */
    private function getLanguage() {
        $lang = substr($this->language->get('code'), 0, 2);
        if ($lang == 'ru' || $lang == 'en' || $lang == 'uk') {
            return  $lang;
        } else {
            return  'en';
        }
    }

    /**
     * Validation requests portmone
     */
    private function isPaymentValid() {
        $this->load->model('account/order');
        $order_OrderProducts = $this->model_account_order->getOrderProducts($this->request_orderId);
        $order_Order = $this->model_checkout_order->getOrder($this->request_orderId);
        $this->order_status = $order_Order['order_status_id'];

        if (!is_array($order_OrderProducts)) {
            return $this->language->get('error_orderid');
        }

        if (!$this->needs_payment()) {
            $this->model_checkout_order->addOrderHistory(
                $this->request_orderId,
                $this->order_status,
                '#18P '.$this->language->get('repeated_payment') ,
                '1'
            );

            return $this->language->get('repeated_payment');
        }

        $result_portmone = $this->get_result_portmone($this->request->post['SHOPORDERNUMBER']);
        $parseXml = $this->parseXml($result_portmone);
        $this->load->model('extension/payment/portmone');
        if ($parseXml === false) {
            $order_note_text = $this->matchesError($result_portmone);

            if ($order_note_text != false) {
                $this->model_checkout_order->addOrderHistory(
                    $this->request_orderId,
                    $this->order_status,
                    '#2P '.trim($order_note_text) ,
                    true
                );

                $this->model_extension_payment_portmone->createSetting('payment_portmone', ['payment_portmone_view_error' => trim($order_note_text)]);
            }

            $this->model_checkout_order->addOrderHistory(
                $this->request_orderId,
                $this->order_status,
                '#3P '.'Xml empty',
                true
            );

            if ($this->request->post['RESULT'] !== '0') {
                $this->model_checkout_order->addOrderHistory(
                    $this->request_orderId,
                    $this->config->get('payment_portmone_order_stat_fal_id'),
                    '#4P '.$this->request->post['RESULT'] ,
                    '1'
                );
                return $this->language->get('error_auth') . ' ' . $this->request->post['RESULT'] ;
            } else {
                $this->model_checkout_order->addOrderHistory(
                    $this->request_orderId,
                    $this->config->get('payment_portmone_order_stat_not_verified_id'),
                    '#5P '.$this->language->get('thankyou_text') . ' ' . $this->language->get('number_pay') . ': ' . $this->request_orderId ,
                    '1'
                );
                return false;
            }
        }

        $this->model_extension_payment_portmone->deleteSetting('payment_portmone_view_error');
        if ($this->request->post['RESULT'] !== '0') {
            $this->model_checkout_order->addOrderHistory(
                $this->request_orderId,
                $this->config->get('payment_portmone_order_stat_fal_id'),
                '#6P '.$this->request->post['RESULT'] ,
                '1'
            );
            return  $this->request->post['RESULT'] ;
        }

        $payee_id_return = (array)$parseXml->request->payee_id;
        $order_data = (array)$parseXml->orders->order;

        if ($payee_id_return[0] != $this->config->get('payment_portmone_payee_id')) {
            $this->model_checkout_order->addOrderHistory(
                $this->request_orderId,
                $this->config->get('payment_portmone_order_stat_fal_id'),
                '#7P '.$this->language->get('error_merchant') ,
                '1'
            );
            return $this->language->get('error_merchant');
        }

        if (count($parseXml->orders->order) == 0) {
            return $this->language->get('error_order_in_portmone');
        }

        return $this->change_order_status($parseXml, $order_data);
    }

    function matchesError($result_portmone) {
        $pattern = '#<div class=\"response error\">(.*?)</div>#is';
        preg_match($pattern, $result_portmone, $matches);
        return (isset($matches[0]))? strip_tags($matches[0]) : false ;
    }

    /**
     * Handler notification portmone
     */
    public function notification() {

        $responseData = [
            'errorCode'  => "0",
            'reason'     => "OK",
            'responseId' => (string)time()
        ];
        $this->response->addHeader('Content-Type: application/json');

        $receiveNotification = $this->config->get('payment_portmone_entry_receiveNotification');
        if ($receiveNotification !== '1') {
            $this->response->setOutput(json_encode($responseData));
            return;
        }

        $postData = file_get_contents('php://input');
        $request = json_decode($postData, true);
        if (empty($request) || empty($request['shopOrderNumber']) || empty($request['shopBillId'])) {
            $this->response->setOutput(json_encode($responseData));
            return;
        }

        $this->load->model('checkout/order');
        $this->load->language('extension/payment/portmone');

        $this->request_orderId = $this->get_order_id($request['shopOrderNumber']);
        $order_Order = $this->model_checkout_order->getOrder($this->request_orderId);
        $this->order_status = $order_Order['order_status_id'];

        if (!$this->needs_payment()) {
            $this->model_checkout_order->addOrderHistory(
                $this->request_orderId,
                $this->order_status,
                '#103P '.$this->language->get('notification_error') . ' ' . $this->language->get('repeated_payment') ,
                '1'
            );

            $this->response->setOutput(json_encode($responseData));
            return;
        }

        $result_portmone = $this->get_result_portmone($request['shopOrderNumber']);
        $parseXml = $this->parseXml($result_portmone);
        if ($parseXml === false) {
            $this->model_checkout_order->addOrderHistory(
                $this->request_orderId,
                $this->order_status,
                '#104P '.$this->language->get('notification_error') ,
                '1'
            );

            $this->response->setOutput(json_encode($responseData));
            return;
        }

        $payee_id_return = (array)$parseXml->request->payee_id;
        $order_data = (array)$parseXml->orders->order;

        if ($payee_id_return[0] != $this->config->get('payment_portmone_payee_id')) {
            $this->model_checkout_order->addOrderHistory(
                $this->request_orderId,
                $this->order_status,
                '#105P '.$this->language->get('notification_error') . ' ' .$this->language->get('error_merchant') ,
                '1'
            );

            $this->response->setOutput(json_encode($responseData));
            return;
        }

        if (count($parseXml->orders->order) == 0) {
            $this->model_checkout_order->addOrderHistory(
                $this->request_orderId,
                $this->order_status,
                '#106P '.$this->language->get('notification_error') . ' ' .$this->language->get('error_order_in_portmone') ,
                '1'
            );

            $this->response->setOutput(json_encode($responseData));
            return;
        }

        $this->change_order_status($parseXml, $order_data);

        $this->response->setOutput(json_encode($responseData));
    }

    /**
     * Handler requests portmone
     */
    public function callback() {
        $isSuccess = (isset($this->request->post['RESULT']) && $this->request->post['RESULT'] == 0) ? true : false;
        if($isSuccess) {
            $this->load->model('checkout/order');
            $this->load->language('extension/payment/portmone');
            $shopOrderNumber =  $this->request->post['SHOPORDERNUMBER'];
            $this->request_orderId = $this->get_order_id($shopOrderNumber);

            $receiveNotification = $this->config->get('payment_portmone_entry_receiveNotification');
            if ($receiveNotification == '1') {
                if ($this->request->post['RESULT'] == '0') {
                    $this->msg['message'] = $this->language->get('thankyou_text') . ' <br />' . $this->language->get('number_pay') . ': ' . $this->request_orderId ;
                    $this->cart->clear();
                    $this->callback_requests('heading_title_failure_success', $this->msg['message'], 'success');
                } else {
                    $this->msg['message'] = $this->request->post['RESULT'] . ' <br />' . $this->language->get('number_pay') . ': ' . $this->request_orderId ;
                    $this->callback_requests('heading_title_failure', $this->msg['message'], 'failure');
                }
                return;
            }

            $paymentInfo = $this->isPaymentValid();
            if ($paymentInfo == false) {
                if ($this->request->post['RESULT'] == '0') {
                    $this->msg['message'] = $this->language->get('thankyou_text') . ' <br />' . $this->language->get('number_pay') . ': ' . $this->request_orderId ;
                    $this->cart->clear();
                    $this->callback_requests('heading_title_failure_success', $this->msg['message'], 'success');
                } else {
                    $this->msg['message'] = $this->request->post['RESULT'] . ' <br />' . $this->language->get('number_pay') . ': ' . $this->request_orderId ;
                    $this->callback_requests('heading_title_failure', $this->msg['message'], 'failure');
                }
            } else {
                $this->msg['message'] = $paymentInfo . ' <br />' . $this->language->get('number_pay') . ': ' . $this->request_orderId ;
                $this->callback_requests('heading_title_failure', $this->msg['message'], 'failure');
            }
        } else {
            $this->response->redirect($this->url->link('common/home'));
        }
        exit;
    }

    /**
     * Handler requests portmone
     */
    public function callback_requests($title, $message, $status) {
        $this->load->language('checkout/'. $status);
        $this->load->language('extension/payment/portmone');
        $this->document->setTitle($this->language->get('heading_title'));

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
            'text' => $this->language->get('text_'.$status),
            'href' => $this->url->link('checkout/'.$status)
        );

        $data['heading_title']      = $this->language->get($title);
        $data['text_message']       = $message;
        $data['button_continue']    = $this->language->get('button_continue');
        $data['continue']           = $this->url->link('common/home');
        $data['column_left']        = $this->load->controller('common/column_left');
        $data['column_right']       = $this->load->controller('common/column_right');
        $data['content_top']        = $this->load->controller('common/content_top');
        $data['content_bottom']     = $this->load->controller('common/content_bottom');
        $data['footer']             = $this->load->controller('common/footer');
        $data['header']             = $this->load->controller('common/header');

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/common/success')) {
            echo $this->load->view($this->config->get('config_template') . '/template/common/success', $data);
        } else {
            echo $this->load->view('common/success', $data);
        }
    }

    /**
     * A request to verify the validity of payment in Portmone
     **/
    private function curlRequest($url, $data) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $response = curl_exec($ch);
        $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if (200 !== intval($httpCode)) {
            return false;
        }
        return $response;
    }

    /**
     * Parsing XML response from Portmone
     **/
    private function parseXml($string) {
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($string, 'SimpleXMLElement', LIBXML_NOCDATA);
        if (false !== $xml) {
            return $xml;
        } else {
            return false;
        }
    }

    private function get_order_id($shopOrderNumber)
    {
        $shopnumbercount = strpos($shopOrderNumber, "_");
        return substr($shopOrderNumber, 0, $shopnumbercount);
    }

    private function needs_payment() {
        $array_statuses = [$this->config->get('payment_portmone_order_stat_id'),
            $this->config->get('payment_portmone_order_stat_not_verified_id'),
            $this->config->get('payment_portmone_order_stat_preauth_id'),
            $this->config->get('payment_portmone_order_stat_completed_id')];

        return !in_array($this->order_status, $array_statuses);
    }

    private function get_result_portmone($shopOrderNumber) {
        $data = array(
            "method"            => "result",
            "payee_id"          => $this->config->get('payment_portmone_payee_id') ,
            "login"             => $this->config->get('payment_portmone_login') ,
            "password"          => $this->config->get('payment_portmone_pass') ,
            "shop_order_number" => $shopOrderNumber ,
        );

        $result_portmone = $this->curlRequest($this->liveurl, $data);
        if ($result_portmone === false) {
            $this->model_checkout_order->addOrderHistory(
                $this->request_orderId,
                $this->config->get('payment_portmone_order_stat_not_verified_id'),
                '#1P '.'Curl request error' ,
                '1'
            );
        }

        return $result_portmone;
    }

    private function change_order_status($parseXml, $order_data) {

        if (count($parseXml->orders->order) > 1){
            $no_pay = false;
            foreach($parseXml->orders->order as $order ){
                $status = (array)$order->status;
                if ($status[0] == self::ORDER_PAYED){
                    if($this->order_status !== $this->config->get('payment_portmone_order_stat_id')){
                        $this->model_checkout_order->addOrderHistory(
                            $this->request_orderId,
                            $this->config->get('payment_portmone_order_stat_id'),
                            '#8P '.$this->language->get('thankyou_text') . ' ' . $this->language->get('number_pay') . ': ' . $this->request_orderId ,
                            '1'
                        );
                    }
                    $no_pay = true;
                    break;
                } elseif($status[0] == self::ORDER_PREAUTH) {
                    if($this->order_status !== $this->config->get('payment_portmone_order_stat_preauth_id')){
                        $this->model_checkout_order->addOrderHistory(
                            $this->request_orderId,
                            $this->config->get('payment_portmone_order_stat_preauth_id'),
                            '#9P '.$this->language->get('thankyou_text') . ' ' . $this->language->get('number_pay') . ': ' . $this->request_orderId ,
                            '1'
                        );
                    }
                    $no_pay = true;
                    break;
                }
            }

            if ($no_pay == false) {
                $this->model_checkout_order->addOrderHistory(
                    $this->request_orderId,
                    $this->config->get('payment_portmone_order_stat_fal_id'),
                    '#10P '.$this->language->get('error_order_in_portmone') ,
                    '1'
                );

                return $this->language->get('error_order_in_portmone');
            } else {
                return false;
            }
        }

        if ($order_data['status'] == self::ORDER_REJECTED) {
            $this->model_checkout_order->addOrderHistory(
                $this->request_orderId,
                $this->config->get('payment_portmone_order_stat_fal_id'),
                '#11P '.$this->language->get('order_rejected') ,
                '1'
            );
            return  $this->language->get('order_rejected') ;
        }

        if ($order_data['status'] == self::ORDER_CREATED) {
            $this->model_checkout_order->addOrderHistory(
                $this->request_orderId,
                $this->config->get('payment_portmone_order_stat_fal_id'),
                '#12P '.$this->language->get('order_rejected') ,
                '1'
            );
            return  $this->language->get('order_rejected') ;
        }

        if ($order_data['status'] == self::ORDER_PREAUTH) {
            $this->model_checkout_order->addOrderHistory(
                $this->request_orderId,
                $this->config->get('payment_portmone_order_stat_preauth_id'),
                '#13P '.$this->language->get('thankyou_text') . ' ' . $this->language->get('number_pay') . ': ' . $this->request_orderId  ,
                '1'
            );
            return false;
        }

        if ($order_data['status'] == self::ORDER_PAYED) {
            if($this->order_status !== $this->config->get('payment_portmone_order_stat_id')){
                if($this->order_status !== $this->config->get('payment_portmone_order_stat_id')){
                    $this->model_checkout_order->addOrderHistory(
                        $this->request_orderId,
                        $this->config->get('payment_portmone_order_stat_id'),
                        '#14P '.$this->language->get('thankyou_text') . ' ' . $this->language->get('number_pay') . ': ' . $this->request_orderId ,
                        '1'
                    );
                }
            }
            return false;
        }
        return true;
    }
}
