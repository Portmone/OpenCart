<form action="<?php echo $action; ?>" method="post">
    <input type="hidden" name="bodyRequest" value='<?php echo $data; ?>'>
    <input type="hidden" name="typeRequest" value='json' />
    <div class="buttons">
        <div class="pull-right">
            <input type="submit" value="<?php echo $button_pay; ?>" class="btn btn-primary" />
        </div>
    </div>
</form>
