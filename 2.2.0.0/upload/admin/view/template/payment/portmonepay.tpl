<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-portmonepay" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
      <h1><?php echo $text_portmonepay; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
<div class="container-fluid">
    <?php if ($error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
      </div>
      <div class="panel-body">
          <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-portmone" class="form-horizontal">
              <ul class="nav nav-tabs">
                  <li class="active"><a href="#tab-general" data-toggle="tab"><?php echo $tab_general; ?></a></li>
                  <li><a href="#tab-status" data-toggle="tab"><?php echo $tab_order_status; ?></a></li>
              </ul>
              <div class="tab-content">
                  <div class="tab-pane active" id="tab-general">
                      <div class="form-group">
                          <label class="col-sm-2 control-label" for="input-status">
                              <span data-toggle="tooltip" title="<?php echo $h_entry_status; ?>"><?php echo $entry_status; ?></span>
                          </label>
                          <div class="col-sm-10">
                              <select name="portmonepay_status" id="input-status" class="form-control">
                                  <?php if ($portmonepay_status) { ?>
                                      <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                                      <option value="0"><?php echo $text_disabled; ?></option>
                                  <?php } else { ?>
                                      <option value="1"><?php echo $text_enabled; ?></option>
                                      <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                                  <?php } ?>
                              </select>
                          </div>
                      </div>
                      <div class="form-group">
                          <label class="col-sm-2 control-label" for="input-merchant">
                              <span data-toggle="tooltip" title="<?php echo $h_entry_name; ?>"><?php echo $entry_name; ?></span>
                          </label>
                          <div class="col-sm-10">
                              <input type="text" name="portmonepay_name" value="<?php echo $portmonepay_name; ?>" placeholder="<?php echo $h_entry_name; ?>" id="input-portmonepay-name" class="form-control" />
                          </div>
                      </div>
                      <div class="form-group required">
                          <label class="col-sm-2 control-label" for="input-merchant">
                              <span data-toggle="tooltip" title="<?php echo $h_entry_payee_id; ?>"><?php echo $entry_payee_id; ?></span>
                          </label>
                          <div class="col-sm-10">
                              <input type="text" name="portmonepay_payee_id" value="<?php echo $portmonepay_payee_id; ?>" placeholder="<?php echo $h_entry_payee_id; ?>" id="input-portmonepay-payee-id" class="form-control" />
                              <?php if ($error_payee_id) { ?>
                                  <div class="text-danger"><?php echo $error_payee_id; ?></div>
                              <?php } ?>
                          </div>
                      </div>
                      <div class="form-group required">
                          <label class="col-sm-2 control-label" for="input-merchant">
                              <span data-toggle="tooltip" title="<?php echo $h_entry_login; ?>"><?php echo $entry_login; ?></span>
                          </label>
                          <div class="col-sm-10">
                              <input type="text" name="portmonepay_login" value="<?php echo $portmonepay_login; ?>" placeholder="<?php echo $h_entry_login; ?>" id="input-portmonepay-login" class="form-control" />
                              <?php if ($error_login) { ?>
                                  <div class="text-danger"><?php echo $error_login; ?></div>
                              <?php } ?>
                          </div>
                      </div>
                      <div class="form-group required">
                          <label class="col-sm-2 control-label" for="input-merchant">
                              <span data-toggle="tooltip" title="<?php echo $h_entry_pass; ?>"><?php echo $entry_pass; ?></span>
                          </label>
                          <div class="col-sm-10">
                              <input type="text" name="portmonepay_pass" value="<?php echo $portmonepay_pass; ?>" placeholder="<?php echo $h_entry_pass; ?>" id="input-portmonepay-pass" class="form-control" />
                              <?php if ($error_pass) { ?>
                                  <div class="text-danger"><?php echo $error_pass; ?></div>
                              <?php } ?>
                          </div>
                      </div>
                      <div class="form-group required">
                          <label class="col-sm-2 control-label" for="input-merchant">
                              <span data-toggle="tooltip" title="<?php echo $h_entry_key; ?>"><?php echo $entry_key; ?></span>
                          </label>
                          <div class="col-sm-10">
                              <input type="text" name="portmonepay_key" value="<?php echo $portmonepay_key; ?>" placeholder="<?php echo $h_entry_key; ?>" id="input-portmonepay-key" class="form-control" />
                              <?php if ($error_key) { ?>
                              <div class="text-danger"><?php echo $error_key; ?></div>
                              <?php } ?>
                          </div>
                      </div>
                      <div class="form-group">
                          <label class="col-sm-2 control-label" for="input-merchant">
                              <span data-toggle="tooltip" title="<?php echo $h_entry_exp_time; ?>"><?php echo $entry_exp_time; ?></span>
                          </label>
                          <div class="col-sm-10">
                              <input type="text" name="portmonepay_exp_time" value="<?php echo $portmonepay_exp_time; ?>" placeholder="<?php echo $h_entry_exp_time; ?>" id="input-portmonepay-key" class="form-control" />
                          </div>
                      </div>
                      <div class="form-group">
                          <label class="col-sm-2 control-label" for="input-preauth">
                              <span data-toggle="tooltip" title="<?php echo $h_entry_preauth; ?>"><?php echo $entry_preauth; ?></span>
                          </label>
                          <div class="col-sm-10">
                              <select name="portmonepay_preauth" id="input-preauth" class="form-control">
                                  <?php if ($portmonepay_preauth == 'Y') { ?>
                                  <option value="Y" selected="selected"><?php echo $text_enabled; ?></option>
                                  <option value="N"><?php echo $text_disabled; ?></option>
                                  <?php } else { ?>
                                  <option value="Y"><?php echo $text_enabled; ?></option>
                                  <option value="N" selected="selected"><?php echo $text_disabled; ?></option>
                                  <?php } ?>
                              </select>
                          </div>
                      </div>
                      <div class="form-group">
                          <label class="col-sm-2 control-label" for="input-order-status">
                              <span data-toggle="tooltip" title="<?php echo $h_entry_order_stat; ?>"><?php echo $entry_order_stat; ?></span>
                          </label>
                          <div class="col-sm-10">
                              <select name="portmonepay_order_stat_id" id="input-order-status" class="form-control">
                                  <?php foreach ($order_statuses as $order_status) { ?>
                                      <?php if ($order_status['order_status_id'] == $portmonepay_order_stat_id) { ?>
                                          <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                                      <?php } else { ?>
                                          <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                                      <?php } ?>
                                  <?php } ?>
                              </select>
                          </div>
                      </div>
                      <div class="form-group">
                          <label class="col-sm-2 control-label" for="input-order-status-failure">
                              <span data-toggle="tooltip" title="<?php echo $h_entry_order_stat_fa; ?>"><?php echo $entry_order_stat_fa; ?></span>
                          </label>
                          <div class="col-sm-10">
                              <select name="portmonepay_order_stat_fal_id" id="input-order-status-failure" class="form-control">
                                  <?php foreach ($portmonepay_order_stat_fa as $order_status) { ?>
                                      <?php if ($order_status['order_status_id'] == $portmonepay_order_stat_fal_id) { ?>
                                          <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                                      <?php } else { ?>
                                          <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                                      <?php } ?>
                                  <?php } ?>
                              </select>
                          </div>
                      </div>
                      <div class="form-group">
                          <label class="col-sm-2 control-label" for="input-order-status-preauth">
                              <span data-toggle="tooltip" title="<?php echo $h_entry_order_stat_preauth; ?>"><?php echo $entry_order_stat_preauth; ?></span>
                          </label>
                          <div class="col-sm-10">
                              <select name="portmonepay_order_stat_preauth_id" id="input-order-status-preauth" class="form-control">
                                  <?php foreach ($portmonepay_order_stat_preauth as $order_status) { ?>
                                  <?php if ($order_status['order_status_id'] == $portmonepay_order_stat_preauth_id) { ?>
                                  <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                                  <?php } else { ?>
                                  <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                                  <?php } ?>
                                  <?php } ?>
                              </select>
                          </div>
                      </div>
                      <div class="form-group">
                          <label class="col-sm-2 control-label" for="input-geo-zone">
                              <span data-toggle="tooltip" title="<?php echo $entry_geo_zone; ?>"><?php echo $entry_geo_zone; ?></span>
                          </label>
                          <div class="col-sm-10">
                              <select name="portmonepay_geo_zone_id" id="input-geo-zone" class="form-control">
                                  <option value="0"><?php echo $text_all_zones; ?></option>
                                  <?php foreach ($geo_zones as $geo_zone) { ?>
                                      <?php if ($geo_zone['geo_zone_id'] == $portmonepay_geo_zone_id) { ?>
                                          <option value="<?php echo $geo_zone['geo_zone_id']; ?>" selected="selected"><?php echo $geo_zone['name']; ?></option>
                                      <?php } else { ?>
                                          <option value="<?php echo $geo_zone['geo_zone_id']; ?>"><?php echo $geo_zone['name']; ?></option>
                                      <?php } ?>
                                  <?php } ?>
                              </select>
                          </div>
                      </div>
                      <div class="form-group">
                          <label class="col-sm-2 control-label" for="input-geo-zone">
                              <span data-toggle="tooltip" title="<?php echo $h_entry_showlogo; ?>"><?php echo $entry_showlogo; ?></span>
                          </label>
                          <div class="col-sm-10">
                              <div class="checkbox">
                                  <label>
                                      <?php if (isset($portmonepay_entry_showlogo) && $portmonepay_entry_showlogo=='1') { ?>
                                          <input type="checkbox" name="portmonepay_entry_showlogo" id="portmonepay_entry_showlogo" value="1" checked="checked" />
                                      <?php } else { ?>
                                          <input type="checkbox" name="portmonepay_entry_showlogo" id="portmonepay_entry_showlogo" value="1" />
                                      <?php } ?>
                                  </label>
                              </div>
                          </div>
                      </div>
                      <div class="form-group">
                          <label class="col-sm-2 control-label" for="input-sort-order">
                              <span data-toggle="tooltip" title="<?php echo $h_entry_sort_order; ?>"><?php echo $entry_sort_order; ?></span>
                          </label>
                          <div class="col-sm-10">
                              <input type="text" name="portmonepay_sort_order" value="<?php echo $portmonepay_sort_order; ?>" placeholder="<?php echo $h_entry_sort_order; ?>" id="input-sort-order" class="form-control" />
                          </div>
                      </div>
                  </div>
                  <div class="tab-pane" id="tab-status">
                      <div class="form-group">
                          <label class="col-sm-2 control-label"><?php echo $OP_version; ?></label>
                          <div class="col-sm-10">
                              <div><?php echo $entry_OP_version; ?></div>
                          </div>
                      </div>
                      <div class="form-group">
                          <label class="col-sm-2 control-label"><?php echo $Plugin_version; ?></label>
                          <div class="col-sm-10">
                              <div><?php echo $entry_Plugin_version; ?></div>
                          </div>
                      </div>
                  </div>
              </div>
          </form>
      </div>
    </div>
  </div>
</div>
<?php echo $footer; ?>