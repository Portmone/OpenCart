<form action="<?php echo $action; ?>" method="post">
    <input type="hidden" name="payee_id" value="<?php echo $payee_id; ?>">
    <input type="hidden" name="shop_order_number" value="<?php echo $shop_order_number; ?>">
    <input type="hidden" name="bill_amount" value="<?php echo $bill_amount; ?>">
    <input type="hidden" name="description" value="<?php echo $description; ?>">
    <input type="hidden" name="success_url" value="<?php echo $success_url; ?>">
    <input type="hidden" name="failure_url" value="<?php echo $failure_url; ?>">
    <input type="hidden" name="lang" value="<?php echo $lang; ?>">
    <input type="hidden" name="cms_module_name" value="<?php echo $cms_module_name; ?>">
    <input type="hidden" name="signature" value="<?php echo $signature; ?>">
    <input type="hidden" name="dt" value="<?php echo $dt; ?>">
    <input type="hidden" name="exp_time" value="<?php echo $exp_time; ?>">
    <input type="hidden" name="preauth_flag" value="<?php echo $preauth_flag; ?>">
    <input type="hidden" name="encoding" value="UTF-8">
    <div class="buttons">
        <div class="pull-right">
            <input type="submit" value="<?php echo $button_pay; ?>" class="btn btn-primary" />
        </div>
    </div>
</form>
