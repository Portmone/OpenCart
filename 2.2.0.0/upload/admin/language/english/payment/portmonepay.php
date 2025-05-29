<?php
// Heading
$_['heading_title']         = 'Portmone pay';
$_['text_payment']          = 'Payments';

// Text
$_['text_success']          = 'Settings modified';
$_['text_edit']             = 'Edit Portmone';
$_['text_pay']              = 'Portmone';
$_['text_portmonepay']      = '<a target="_BLANK" href="https://www.portmone.com.ua/r3/uk/"><img width=200" src="view/image/payment/portmonepay.svg" alt="Portmone pay" title="Portmone pay" /></a>';
$_['tab_general']           = 'Settings';
$_['tab_order_status']      = 'Information';

// Entry
$_['h_entry_name']          = 'The name of the merchant, which the customer sees when paying';
$_['entry_name']            = 'Merchant name';
$_['entry_status']          = 'Enable payment via Portmone';
$_['h_entry_status']        = 'Enable Portmone Payment Module';
$_['entry_payee_id']        = 'Store identifier in the Portmone system(Payee ID)';
$_['h_entry_payee_id']      = 'The online store ID provided by the Portmone manager';
$_['entry_login']           = 'The login of the online store in Portmone system';
$_['h_entry_login']         = 'The login of the online store provided by the Portmone manager';
$_['entry_pass']            = 'The password of the online store in the Portmone system';
$_['h_entry_pass']          = 'The password of the online store provided by the manager of the provided by the Portmone manager';
$_['entry_order_stat']      = 'Order status after successful payment';
$_['h_entry_order_stat']    = 'What changes the status of the order after successful payment?';
$_['entry_order_stat_fa']   = 'The status of the order after the failure payment';
$_['h_entry_order_stat_fa'] = 'What will the status of the order change after the failure payment?';
$_['entry_geo_zone']        = 'Geographical area';
$_['entry_showlogo']        = 'Show logo on payment page';
$_['h_entry_showlogo']      = 'Show Portmone logo when ordering';
$_['entry_sort_order']      = 'Sort Order';
$_['h_entry_sort_order']    = 'Order of sorting the payment system when placing an order';
$_['OP_version']            = 'OP version';
$_['Plugin_version']        = 'Plug-in version';
$_['entry_key']             = 'Signature key';
$_['h_entry_key']           = 'Signature key stored at Portmone';
$_['entry_exp_time']        = 'Time for payment';
$_['h_entry_exp_time']      = 'Time allotted for payment';
$_['entry_preauth']         = 'Pre-authorization mode';
$_['h_entry_preauth']       = 'Funds are only blocked on the client’s card, but there is no financial write-off from the client’s account';
$_['entry_order_stat_preauth']      = 'Order status after blocking funds on the buyer\'s account after payment';
$_['h_entry_order_stat_preauth']    = 'What to change the order status after blocking funds on the buyer\'s account after payment';


// Error
$_['error_permission']      = 'Warning: You do not have permission to modify payment Portmone!';
$_['error_payee_id']        = 'Required';
$_['error_login']           = 'Required';
$_['error_pass']            = 'Required';
$_['error_key']             = 'Required';