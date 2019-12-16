<?php
class ControllerPaymentPortmonepay extends Controller {
    const ORDER_PAYED       = 'PAYED';
    const ORDER_CREATED     = 'CREATED';
    const ORDER_REJECTED    = 'REJECTED';
    private $liveurl        = 'https://www.portmone.com.ua/gateway/';
    private $msg            = array();
    private $request_orderId;
    private $order_status;

    public function index() {
        $this->load->language('payment/portmonepay');
        $this->load->model('checkout/order');
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
        $data['payee_id']           = $this->config->get('portmonepay_payee_id');
        $data['shop_order_number']  = $this->session->data['order_id'].'_'.time();
        $data['bill_amount']        = $this->currency->format($order_info['total'],
                                      $order_info['currency_code'],
                                      $order_info['currency_value'], false);
        $data['description']        = $description_order;
        $data['success_url']        = $this->url->link('payment/portmonepay/callback', '', 'SSL');
        $data['failure_url']        = $this->url->link('payment/portmonepay/callback', '', 'SSL');
        $data['lang']               = $this->getLanguage();

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/portmonepay.tpl')) {
            return $this->load->view($this->config->get('config_template') . '/template/payment/portmonepay.tpl', $data);
        } else {
            return $this->load->view('default/template/payment/portmonepay.tpl', $data);
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
        $order_OrderProducts = false;
        $this->load->model('account/order');
        $order_OrderProducts = $this->model_account_order->getOrderProducts($this->request_orderId);
        $order_Order = $this->model_account_order->getOrder($this->request_orderId);
        $this->order_status = $order_Order['order_status_id'];

        if (!is_array($order_OrderProducts)) {
            return $this->language->get('error_orderid');
        }

        $data = array(
            "method"            => "result",
            "payee_id"          => $this->config->get('portmonepay_payee_id') ,
            "login"             => $this->config->get('portmonepay_login') ,
            "password"          => $this->config->get('portmonepay_pass') ,
            "shop_order_number" => $this->request->post['SHOPORDERNUMBER'] ,
        );
        $result_portmone = $this->curlRequest($this->liveurl, $data);
        $parseXml = $this->parseXml($result_portmone);

        if ($parseXml === false) {
            $this->model_checkout_order->addOrderHistory(
                $this->request_orderId,
                $this->config->get('portmonepay_order_stat_fal_id'),
                $this->language->get('error_auth') ,
                '1'
            );
            preg_match_all('#<div class="response error">(.+?)</div>#is', $result_portmone, $arr);
            return $this->language->get('error_auth');
        }

        $payee_id_return = (array)$parseXml->request->payee_id;
        $order_data = (array)$parseXml->orders->order;

        if ($this->request->post['RESULT'] !== '0') {
            $this->model_checkout_order->addOrderHistory(
                $this->request_orderId,
                $this->config->get('portmonepay_order_stat_fal_id'),
                $this->request->post['RESULT'] ,
                '1'
            );
            return  $this->request->post['RESULT'] ;
        }

        if ($payee_id_return[0] != $this->config->get('portmonepay_payee_id')) {
            $this->model_checkout_order->addOrderHistory(
                $this->request_orderId,
                $this->config->get('portmonepay_order_stat_fal_id'),
                $this->language->get('error_merchant') ,
                '1'
            );
            return $this->language->get('error_merchant');
        }

        if (count($parseXml->orders->order) == 0) {
            return $this->language->get('error_order_in_portmone');
        } elseif (count($parseXml->orders->order) > 1){
            $no_pay = false;
            foreach($parseXml->orders->order as $order ){
                $status = (array)$order->status;
                if ($status[0] == self::ORDER_PAYED){
                    if($this->order_status !== $this->config->get('portmonepay_order_stat_id')){
                        $this->model_checkout_order->addOrderHistory(
                            $this->request_orderId,
                            $this->config->get('portmonepay_order_stat_id'),
                            $this->language->get('thankyou_text') . ' ' . $this->language->get('number_pay') . ': ' . $this->request_orderId ,
                            '1'
                        );
                        $this->model_checkout_order->addOrderHistory(
                            $this->request_orderId,
                            $this->config->get('portmonepay_order_stat_id'),
                            $this->request->post['RECEIPT_URL']
                        );
                    }
                    $no_pay = true;
                    break;
                }
            }

            if ($no_pay == false) {
                $this->model_checkout_order->addOrderHistory(
                    $this->request_orderId,
                    $this->config->get('portmonepay_order_stat_fal_id'),
                    $this->language->get('error_order_in_portmone') ,
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
                $this->config->get('portmonepay_order_stat_fal_id'),
                $this->language->get('order_rejected') ,
                '1'
            );
            return  $this->language->get('order_rejected') ;
        }

        if ($order_data['status'] == self::ORDER_CREATED) {
            $this->model_checkout_order->addOrderHistory(
                $this->request_orderId,
                $this->config->get('portmonepay_order_stat_fal_id'),
                $this->language->get('order_rejected') ,
                '1'
            );
            return  $this->language->get('order_rejected') ;
        }

        if ($order_data['status'] == self::ORDER_PAYED) {
            if($this->order_status !== $this->config->get('portmonepay_order_stat_id')){
                if($this->order_status !== $this->config->get('portmonepay_order_stat_id')){
                    $this->model_checkout_order->addOrderHistory(
                        $this->request_orderId,
                        $this->config->get('portmonepay_order_stat_id'),
                        $this->language->get('thankyou_text') . ' ' . $this->language->get('number_pay') . ': ' . $this->request_orderId ,
                        '1'
                    );
                    $this->model_checkout_order->addOrderHistory(
                        $this->request_orderId,
                        $this->config->get('portmonepay_order_stat_id'),
                        $this->request->post['RECEIPT_URL']
                    );
                }
            }
            return false;
        }
        return true;
    }

    /**
     * Handler requests portmone
     */
    public function callback() {
        $isSuccess = (isset($this->request->post['RESULT']) && $this->request->post['RESULT'] == 0) ? true : false;
        if($isSuccess) {
            $this->load->model('checkout/order');
            $this->load->language('payment/portmonepay');
            $shopnumbercount = strpos($this->request->post['SHOPORDERNUMBER'], "_");
            $this->request_orderId = substr($this->request->post['SHOPORDERNUMBER'], 0, $shopnumbercount);
            $paymentInfo = $this->isPaymentValid();
            if ($paymentInfo == false) {
                if ($this->request->post['RESULT'] == '0') {
                    $this->msg['message'] = $this->language->get('thankyou_text') . ' <br />' . $this->language->get('number_pay') . ': ' . $this->request_orderId ;
                    $this->callback_requests('heading_title_failure_success', $this->msg['message'], 'success');
                    $this->cart->clear();
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
        $this->load->language('checkout/'.$status);
        $this->load->language('payment/portmonepay');
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

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/common/success.tpl')) {
            echo $this->load->view($this->config->get('config_template') . '/template/common/success.tpl', $data);
        } else {
            echo $this->load->view('default/template/common/success.tpl', $data);
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
}