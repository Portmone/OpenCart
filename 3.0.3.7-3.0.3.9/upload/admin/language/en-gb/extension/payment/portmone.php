<?php
// Heading
$_['heading_title']         = 'Portmone';
$_['text_payment']          = 'Payments';

// Text
$_['text_success']          = 'Settings modified';
$_['text_edit']             = 'Edit Portmone';
$_['text_pay']              = 'Portmone';
$_['text_portmone']         = '<a target="_BLANK" href="https://www.portmone.com.ua/"><img width=200" src="view/image/payment/portmone.svg" alt="Portmone.com" title="Portmone.com"></a>';
$_['tab_general']           = 'Settings';
$_['tab_order_status']      = 'Information';

// Entry
$_['h_entry_name']          = 'The name of the merchant, which the customer sees when paying';
$_['entry_name']            = 'Merchant name';
$_['entry_status']          = 'Enable payment via Portmone';
$_['h_entry_status']        = 'Enable Portmone Payment Module';
$_['entry_payee_id']        = 'Store identifier in the Portmone system(Payee ID)';
$_['h_entry_payee_id']      = 'The online store ID provided by the Portmone manager';
$_['entry_key']        		= 'Signature key';
$_['h_entry_key']     		= 'Signature key stored at Portmone';
$_['entry_exp_time']        = 'Time for payment';
$_['h_entry_exp_time']     	= 'Time allotted for payment';
$_['entry_login']           = 'The login of the online store in Portmone system';
$_['h_entry_login']         = 'The login of the online store provided by the Portmone manager';
$_['entry_pass']            = 'The password of the online store in the Portmone system';
$_['h_entry_pass']          = 'The password of the online store provided by the manager of the provided by the Portmone manager';
$_['entry_order_stat']      = 'Order status after successful payment';
$_['h_entry_order_stat']    = 'What changes the status of the order after successful payment?';
$_['entry_order_stat_fa']   = 'The status of the order after the failure payment';
$_['h_entry_order_stat_fa'] = 'What will the status of the order change after the failure payment?';
$_['entry_order_stat_preauth']      = 'Order status after blocking funds on the buyer\'s account after payment';
$_['h_entry_order_stat_preauth']    = 'What to change the order status after blocking funds on the buyer\'s account after payment';
$_['entry_order_stat_completed']    = 'Completed order status.';
$_['h_entry_order_stat_completed']  = 'An order is considered completed when the service life cycle is completely completed.';
$_['entry_geo_zone']        = 'Geographical area';
$_['entry_preauth_flag']    = 'Pre-authorization mode';
$_['h_entry_preauth_flag']  = 'Funds are only blocked on the client’s card, but there is no financial write-off from the client’s account';
$_['entry_showlogo']        = 'Show logo on payment page';
$_['h_entry_showlogo']      = 'Show Portmone logo when ordering';
$_['entry_receiveNotification']     = 'Receive notifications about successful payment';
$_['h_entry_receiveNotification']   = 'Receive a message about successful payment in JSON format. To activate this functionality, please write to b2bsupport@portmone.me';
$_['entry_sort_order']      = 'Sort Order';
$_['h_entry_sort_order']    = 'Order of sorting the payment system when placing an order';
$_['OP_version']            = 'OpenCart version';
$_['Plugin_version']        = 'Plug-in version';
$_['entry_order_confirm_stat']      = 'Order status after placing an order';
$_['h_entry_order_confirm_stat']    = 'When you click the "Confirm" button at the last stage of placing an order, the order will be assigned the selected status';
$_['entry_client_first_last_name_flag']    = 'Save the client\'s first name, last name and patronymic';
$_['h_entry_client_first_last_name_flag']  = 'The client\'s first name, last name and patronymic are taken from the user profile and agreed upon with the Portmone manager.';
$_['entry_client_phone_number_flag']       = 'Save customer phone number';
$_['h_entry_client_phone_number_flag']     = 'The client\'s phone number is taken from the user\'s profile and is agreed upon with the Portmone manager';
$_['entry_client_email_flag']              = 'Save customer email';
$_['h_entry_client_email_flag']            = 'The client\'s email is taken from the user\'s profile and is agreed upon with the Portmone manager';


// Error
$_['error_permission']      = 'Warning: You do not have permission to modify payment Portmone!';
$_['error_payee_id']        = 'Required';
$_['error_key']        		= 'Required';
$_['error_login']           = 'Required';
$_['error_pass']            = 'Required';
$_['notice_description']    = 'The error will disappear after a successful payment';
$_['plagin_status_success'] = 'version is relevant for the plugin';
$_['plagin_status_warning'] = 'the plugin is NOT tested on this version and may be unstable';
$_['user_redirected_to_portmone']       = 'User has been redirected to Portmone.com to pay';
