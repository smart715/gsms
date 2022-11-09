<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
            <div class="x_title">
                <h3 class="head-title"><i class="fa fa-calculator"></i><small> <?php echo $this->lang->line('payment'); ?></small></h3>
                <ul class="nav navbar-right panel_toolbox">
                    <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
                </ul>
                <div class="clearfix"></div>
            </div>

            <?php if (logged_in_role_id() != GUARDIAN) { ?>
                <div class="x_content quick-link">
                    <?php $this->load->view('quick-link'); ?>
                </div>
            <?php } ?>

            <div class="x_content">
                <div class="" data-example-id="togglable-tabs">
                    <ul class="nav nav-tabs bordered">
                        <li class="active"><a href="#tab_fee_list" role="tab" data-toggle="tab" aria-expanded="true"><i class="fa fa-dollar"></i> <?php echo $this->lang->line('payment'); ?></a> </li>
                    </ul>
                    <br />
                    <div class="tab-content">
                        <div class="tab-pane fade in active" id="tab_fee_list">
                            <div class="x_content">
                                <?php echo form_open(site_url('accounting/payment/paid/' . $invoice_id), array('name' => 'add', 'id' => 'add', 'class' => 'form-horizontal form-label-left'), ''); ?>

                                <?php $this->load->view('layout/school_list_form'); ?>

                                <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="amount"><?php echo $this->lang->line('amount'); ?> <span class="required">*</span></label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input class="form-control col-md-7 col-xs-12" name="amount" id="amount" value="<?php echo $due_amount; ?>" placeholder="<?php echo $this->lang->line('amount'); ?>" required="required" type="number" step="any">
                                        <div class="help-block"><?php echo form_error('amount'); ?></div>
                                    </div>
                                </div>
                                <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="payment_method"><?php echo $this->lang->line('payment_method'); ?> <span class="required">*</span></label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <select class="form-control col-md-7 col-xs-12" name="payment_method" id="payment_method" required="required" onchange="check_payment_method(this.value);">
                                            <option value="">--<?php echo $this->lang->line('select'); ?>--</option>
                                            <?php $payments = get_payment_methods(); ?>
                                            <?php foreach ($payments as $key => $value) { ?>
                                                <?php if ($this->session->userdata('role_id') == GUARDIAN || $this->session->userdata('role_id') == STUDENT) { ?>
                                                    <?php if (!in_array($key, array('cash', 'cheque', 'receipt'))) { ?>
                                                        <option value="<?php echo $key; ?>" <?php if (isset($post) && $post['payment_method'] == $key) {
                                                                                                echo 'selected="selected"';
                                                                                            } ?>><?php echo $value; ?></option>
                                                    <?php } ?>
                                                <?php } else { ?>
                                                    <?php if (in_array($key, array('cash', 'cheque', 'receipt'))) { ?>
                                                        <option value="<?php echo $key; ?>" <?php if (isset($post) && $post['payment_method'] == $key) {
                                                                                                echo 'selected="selected"';
                                                                                            } ?>><?php echo $value; ?></option>
                                                    <?php } ?>
                                                <?php } ?>
                                            <?php } ?>

                                        </select>
                                        <div class="help-block"><?php echo form_error('payment_method'); ?></div>
                                    </div>
                                </div>

                                <!-- For cheque Start-->
                                <div class="display fn_cheque" style="<?php if (isset($post) && $post['payment_method'] == 'cheque') {
                                                                            echo 'display:block;';
                                                                        } ?>">
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="bank_name"><?php echo $this->lang->line('bank_name'); ?> <span class="required">*</span> </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input class="form-control col-md-7 col-xs-12" name="bank_name" id="bank_name" value="" placeholder="<?php echo $this->lang->line('bank_name'); ?>" type="text" autocomplete="off">
                                            <div class="help-block"><?php echo form_error('bank_name'); ?></div>
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="cheque_no"><?php echo $this->lang->line('cheque_number'); ?> <span class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input class="form-control col-md-7 col-xs-12" name="cheque_no" id="cheque_no" value="" placeholder="<?php echo $this->lang->line('cheque_number'); ?>" type="text" autocomplete="off">
                                            <div class="help-block"><?php echo form_error('cheque_no'); ?></div>
                                        </div>
                                    </div>
                                </div>
                                <!-- For cheque End-->

                                <!-- For Receipt Start-->
                                <div class="display fn_receipt" style="<?php if (isset($post) && $post['payment_method'] == 'receipt') {
                                                                            echo 'display:block;';
                                                                        } ?>">
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="bank_receipt"><?php echo $this->lang->line('bank_receipt'); ?>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input class="form-control col-md-7 col-xs-12" name="bank_receipt" id="bank_receipt" value="" placeholder="<?php echo $this->lang->line('bank_receipt'); ?>" type="text" autocomplete="off">
                                            <div class="help-block"><?php echo form_error('bank_receipt'); ?></div>
                                        </div>
                                    </div>

                                    <div class="item form-group">
                                        <div class="row">
                                            <label class="control-label col-md-3 col-sm-3 col-xs-12"> <?php echo $this->lang->line('bank_receipt'); ?> File
                                            </label>
                                            <div class="col-md-9 col-sm-9 col-xs-12">
                                                <div class="btn btn-default btn-file">
                                                    <i class="fa fa-paperclip"></i> <?php echo $this->lang->line('upload'); ?>
                                                    <input class="form-control" name="bank_receipt_attach" id="bank_receipt_attach" type="file">
                                                </div>
                                                <div class="text-info">pdf, docx, doc</div>
                                                <div class="help-block"><?php echo form_error('bank_receipt_attach'); ?></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- For Receipt End-->


                                <!-- For PayuMoney Start-->
                                <div class="display fn_payumoney" style="<?php if (isset($post) && $post['payment_method'] == 'payumoney') {
                                                                                echo 'display:block;';
                                                                            } ?>">
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="pay_name"><?php echo $this->lang->line('first_name'); ?> <span class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input class="form-control col-md-7 col-xs-12" name="pay_name" id="pay_name" value="" placeholder="<?php echo $this->lang->line('first_name'); ?>" type="text" autocomplete="off">
                                            <div class="help-block"><?php echo form_error('pay_name'); ?></div>
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="pay_email"><?php echo $this->lang->line('email'); ?> <span class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input class="form-control col-md-7 col-xs-12" name="pay_email" id="pay_email" value="" placeholder="<?php echo $this->lang->line('email'); ?>" type="text" autocomplete="off">
                                            <div class="help-block"><?php echo form_error('pay_email'); ?></div>
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="pay_phone"><?php echo $this->lang->line('phone'); ?> <span class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input class="form-control col-md-7 col-xs-12" name="pay_phone" id="pay_phone" value="" placeholder="<?php echo $this->lang->line('phone'); ?>" type="text" autocomplete="off">
                                            <div class="help-block"><?php echo form_error('pay_phone'); ?></div>
                                        </div>
                                    </div>
                                </div>
                                <!-- For PayUMoney End-->


                                <!-- For sslcommerz Start-->
                                <div class="display fn_sslcommerz" style="<?php if (isset($post) && $post['payment_method'] == 'sslcommerz') {
                                                                                echo 'display:block;';
                                                                            } ?>">
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="ssl_name"><?php echo $this->lang->line('name'); ?> <span class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input class="form-control col-md-7 col-xs-12" name="ssl_name" id="ssl_name" value="" placeholder="<?php echo $this->lang->line('name'); ?>" type="text" autocomplete="off">
                                            <div class="help-block"><?php echo form_error('ssl_name'); ?></div>
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="ssl_email"><?php echo $this->lang->line('email'); ?> <span class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input class="form-control col-md-7 col-xs-12" name="ssl_email" id="ssl_email" value="" placeholder="<?php echo $this->lang->line('email'); ?>" type="text" autocomplete="off">
                                            <div class="help-block"><?php echo form_error('ssl_email'); ?></div>
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="ssl_phone"><?php echo $this->lang->line('phone'); ?> <span class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input class="form-control col-md-7 col-xs-12" name="ssl_phone" id="ssl_phone" value="" placeholder="<?php echo $this->lang->line('phone'); ?>" type="text" autocomplete="off">
                                            <div class="help-block"><?php echo form_error('ssl_phone'); ?></div>
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="ssl_address"><?php echo $this->lang->line('address'); ?> <span class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input class="form-control col-md-7 col-xs-12" name="ssl_address" id="ssl_address" value="" placeholder="<?php echo $this->lang->line('address'); ?>" type="text" autocomplete="off">
                                            <div class="help-block"><?php echo form_error('ssl_phone'); ?></div>
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="ssl_city"><?php echo $this->lang->line('city'); ?> <span class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input class="form-control col-md-7 col-xs-12" name="ssl_city" id="ssl_city" value="" placeholder="<?php echo $this->lang->line('city'); ?>" type="text" autocomplete="off">
                                            <div class="help-block"><?php echo form_error('ssl_city'); ?></div>
                                        </div>
                                    </div>
                                </div>
                                <!-- For sslcommerz End-->

                                <!-- For PayStack Start-->
                                <div class="display fn_paystack" style="<?php if (isset($post) && $post['payment_method'] == 'paystack') {
                                                                            echo 'display:block;';
                                                                        } ?>">
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="stack_email"><?php echo $this->lang->line('email'); ?> <span class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input class="form-control col-md-7 col-xs-12" name="stack_email" id="stack_email" value="" placeholder="<?php echo $this->lang->line('email'); ?>" type="text" autocomplete="off">
                                            <div class="help-block"><?php echo form_error('stack_email'); ?></div>
                                        </div>
                                    </div>
                                </div>
                                <!-- For PayStack End-->

                                <!-- For DBBL Start-->
                                <div class="display fn_dbbl" style="<?php if (isset($post) && $post['payment_method'] == 'dbbl') {
                                                                        echo 'display:block;';
                                                                    } ?>">
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="card_type"><?php echo $this->lang->line('card_type'); ?> <span class="required">*</span></label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div class="row">
                                                <div class="col-md-2 col-sm-2 col-xs-6">
                                                    <input type="radio" name="card_type" required="required" class="dbbl_card_type" value="1">
                                                    <img src="<?php echo IMG_URL; ?>dbbl/dbbl-nexus.png" alt="dbbl-nexus" width="75" />
                                                </div>
                                                <div class="col-md-2 col-sm-2 col-xs-6">
                                                    <input type="radio" name="card_type" required="required" class="dbbl_card_type" value="2">
                                                    <img src="<?php echo IMG_URL; ?>dbbl/dbbl-master.png" alt="dbbl-master" width="75" />
                                                </div>
                                                <div class="col-md-2 col-sm-2 col-xs-6">
                                                    <input type="radio" name="card_type" required="required" class="dbbl_card_type" value="3">
                                                    <img src="<?php echo IMG_URL; ?>dbbl/dbbl-visa.png" alt="dbbl-visa" width="75" />
                                                </div>
                                                <div class="col-md-2 col-sm-2 col-xs-6">
                                                    <input type="radio" name="card_type" required="required" class="dbbl_card_type" value="4">
                                                    <img src="<?php echo IMG_URL; ?>dbbl/visa.png" alt="Visa" width="75" />
                                                </div>
                                                <div class="col-md-2 col-sm-2 col-xs-6">
                                                    <input type="radio" name="card_type" required="required" class="dbbl_card_type" value="5">
                                                    <img src="<?php echo IMG_URL; ?>dbbl/master.png" alt="master" width="75" />
                                                </div>
                                                <div class="col-md-2 col-sm-2 col-xs-6">
                                                    <input type="radio" name="card_type" required="required" class="dbbl_card_type" value="6">
                                                    <img src="<?php echo IMG_URL; ?>dbbl/rocket.png" alt="rocket" width="75" />
                                                </div>
                                            </div>
                                            <div class="help-block"><?php echo form_error('dbbl'); ?></div>
                                        </div>
                                    </div>
                                </div>
                                <!-- For DBBL End-->

                                <!-- For instamojo Start-->
                                <div class="display fn_instamojo" style="<?php if (isset($post) && $post['payment_method'] == 'instamojo') {
                                                                                echo 'display:block;';
                                                                            } ?>">
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="mojo_name"><?php echo $this->lang->line('name'); ?> <span class="required">*</span> </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input class="form-control col-md-7 col-xs-12" name="mojo_name" id="mojo_name" value="" placeholder="<?php echo $this->lang->line('name'); ?>" type="text" autocomplete="off">
                                            <div class="help-block"><?php echo form_error('mojo_name'); ?></div>
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="mojo_phone"><?php echo $this->lang->line('phone'); ?> <span class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input class="form-control col-md-7 col-xs-12" name="mojo_phone" id="mojo_phone" value="" placeholder="<?php echo $this->lang->line('phone'); ?>" type="text" autocomplete="off">
                                            <div class="help-block"><?php echo form_error('mojo_phone'); ?></div>
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="mojo_email"><?php echo $this->lang->line('email'); ?> <span class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input class="form-control col-md-7 col-xs-12" name="mojo_email" id="mojo_email" value="" placeholder="<?php echo $this->lang->line('email'); ?>" type="text" autocomplete="off">
                                            <div class="help-block"><?php echo form_error('mojo_email'); ?></div>
                                        </div>
                                    </div>
                                </div>
                                <!-- For instamojo End-->

                                <!-- For flutterwave Start-->
                                <div class="display fn_flutterwave" style="<?php if (isset($post) && $post['payment_method'] == 'flutterwave') {
                                                                                echo 'display:block;';
                                                                            } ?>">
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="flat_name"><?php echo $this->lang->line('name'); ?> <span class="required">*</span> </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input class="form-control col-md-7 col-xs-12" name="flat_name" id="flat_name" value="" placeholder="<?php echo $this->lang->line('name'); ?>" type="text" autocomplete="off">
                                            <div class="help-block"><?php echo form_error('flat_name'); ?></div>
                                        </div>
                                    </div>

                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="flat_email"><?php echo $this->lang->line('email'); ?> <span class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input class="form-control col-md-7 col-xs-12" name="flat_email" id="flat_email" value="" placeholder="<?php echo $this->lang->line('email'); ?>" type="text" autocomplete="off">
                                            <div class="help-block"><?php echo form_error('flat_email'); ?></div>
                                        </div>
                                    </div>
                                </div>
                                <!-- For flutterwave End-->


                                <!-- For Ipay Start-->
                                <div class="display fn_ipay" style="<?php if (isset($post) && $post['payment_method'] == 'ipay') {
                                                                        echo 'display:block;';
                                                                    } ?>">

                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="ipay_phone"><?php echo $this->lang->line('phone'); ?> <span class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input class="form-control col-md-7 col-xs-12" name="ipay_phone" id="ipay_phone" value="" placeholder="<?php echo $this->lang->line('phone'); ?>" type="text" autocomplete="off">
                                            <div class="help-block"><?php echo form_error('ipay_phone'); ?></div>
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="ipay_email"><?php echo $this->lang->line('email'); ?> <span class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input class="form-control col-md-7 col-xs-12" name="ipay_email" id="ipay_email" value="" placeholder="<?php echo $this->lang->line('email'); ?>" type="text" autocomplete="off">
                                            <div class="help-block"><?php echo form_error('ipay_email'); ?></div>
                                        </div>
                                    </div>
                                </div>
                                <!-- For iPay End-->


                                <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="note"><?php echo $this->lang->line('note'); ?></label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <textarea class="form-control col-md-7 col-xs-12" name="note" id="note" placeholder="<?php echo $this->lang->line('note'); ?>"></textarea>
                                        <div class="help-block"><?php echo form_error('note'); ?></div>
                                    </div>
                                </div>

                                <div class="ln_solid"></div>
                                <div class="form-group">
                                    <div class="col-md-6 col-md-offset-3">
                                        <input type="hidden" name="invoice_type" value="<?php echo $invoice->invoice_type; ?>" />
                                        <input type="hidden" name="invoice_id" value="<?php echo $invoice_id; ?>" />
                                        <input type="hidden" name="due_amount" value="<?php echo $due_amount; ?>" />
                                        <a href="<?php echo site_url('accounting/invoice/due'); ?>" class="btn btn-primary"><?php echo $this->lang->line('cancel'); ?></a>
                                        <button id="send" type="submit" class="btn btn-success"><?php echo $this->lang->line('submit'); ?></button>
                                    </div>
                                </div>
                                <?php echo form_close(); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- datatable with buttons -->
<link href="<?php echo VENDOR_URL; ?>datepicker/datepicker.css" rel="stylesheet">
<script src="<?php echo VENDOR_URL; ?>datepicker/datepicker.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $("#expire_month").datepicker({
            format: "mm",
            viewMode: "months",
            minViewMode: "months"
        });
        $("#expire_year").datepicker({
            format: "yyyy",
            viewMode: "years",
            minViewMode: "years"
        });
    });

    function check_payment_method(payment_method) {

        $('.fn_cheque').hide();
        $('.fn_receipt').hide();
        $('.fn_payumoney').hide();
        $('.fn_sslcommerz').hide();
        $('.fn_paystack').hide();
        $('.fn_dbbl').hide();
        $('.fn_instamojo').hide();
        $('.fn_flutterwave').hide();
        $('.fn_ipay').hide();

        $('#bank_name').prop('required', false);
        $('#cheque_no').prop('required', false);

        // $('#bank_receipt').prop('required', false);

        $('#pay_name').prop('required', false);
        $('#pay_email').prop('required', false);
        $('#pay_phone').prop('required', false);

        $('#ssl_name').prop('required', false);
        $('#ssl_email').prop('required', false);
        $('#ssl_phone').prop('required', false);
        $('#ssl_address').prop('required', false);
        $('#ssl_city').prop('required', false);

        $('#stack_email').prop('required', false);

        $('.dbbl_card_type').prop('required', false);

        $('#mojo_name').prop('required', false);
        $('#mojo_email').prop('required', false);
        $('#mojo_phone').prop('required', false);

        $('#flat_name').prop('required', false);
        $('#flat_email').prop('required', false);

        $('#ipay_email').prop('required', false);
        $('#ipay_phone').prop('required', false);


        if (payment_method == "cheque") {

            $('.fn_cheque').show();
            $('#bank_name').prop('required', true);
            $('#cheque_no').prop('required', true);

        } else if (payment_method == "receipt") {

            $('.fn_receipt').show();
            // $('#bank_receipt').prop('required', true);

        } else if (payment_method == "payumoney") {

            $('.fn_payumoney').show();
            $('#pay_name').prop('required', true);
            $('#pay_email').prop('required', true);
            $('#pay_phone').prop('required', true);

        } else if (payment_method == "sslcommerz") {

            $('.fn_sslcommerz').show();
            $('#ssl_name').prop('required', true);
            $('#ssl_email').prop('required', true);
            $('#ssl_phone').prop('required', true);
            $('#ssl_address').prop('required', true);
            $('#ssl_city').prop('required', true);

        } else if (payment_method == "paystack") {

            $('.fn_paystack').show();
            $('#stack_email').prop('required', true);

        } else if (payment_method == "dbbl") {

            $('.fn_dbbl').show();
            $('.dbbl_card_type').prop('required', true);

        } else if (payment_method == "instamojo") {

            $('.fn_instamojo').show();
            $('#mojo_name').prop('required', true);
            $('#mojo_email').prop('required', true);
            $('#mojo_phone').prop('required', true);


        } else if (payment_method == "flutterwave") {

            $('.fn_flutterwave').show();
            $('#flat_name').prop('required', true);
            $('#flat_email').prop('required', true);

        } else if (payment_method == "ipay") {

            $('.fn_ipay').show();
            $('#ipay_email').prop('required', true);
            $('#ipay_phone').prop('required', true);

        }
    }

    $("#add").validate();
</script>