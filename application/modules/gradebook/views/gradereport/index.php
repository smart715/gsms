<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
            <div class="x_title">
                <h3 class="head-title"><i class="fa fa-file-text-o"></i><small>Grade Report</small></h3>
                <ul class="nav navbar-right panel_toolbox">
                    <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
                </ul>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <?php echo form_open_multipart(site_url('gradebook/gradereport/index'), array('name' => 'result', 'id' => 'result', 'class' => 'form-horizontal form-label-left'), ''); ?>
                <div class="row">

                    <div class="col-md-10 col-sm-10 col-xs-12">

                        <div class="col-md-3 col-sm-3 col-xs-12">
                            <div class="item form-group">
                                <div><?php echo $this->lang->line('class'); ?> <span class="required">*</span></div>
                                <select class="form-control col-md-7 col-xs-12" name="class_id" id="class_id" required="required" onchange="get_subject_by_class(this.value,'');">
                                    <option value="">--<?php echo $this->lang->line('select'); ?>--</option>
                                    <?php foreach ($classes as $obj) { ?>
                                        <option value="<?php echo $obj->id; ?>" <?php if (isset($class_id) && $class_id == $obj->id) { echo 'selected="selected"';} ?>><?php echo $obj->name; ?></option>
                                    <?php } ?>
                                </select>
                                <div class="help-block"><?php echo form_error('class_id'); ?></div>
                            </div>
                        </div>

                        <div class="col-md-3 col-sm-3 col-xs-12">
                            <div class="item form-group">
                                <div><?php echo $this->lang->line('subject'); ?></div>
                                <select class="form-control col-md-7 col-xs-12" required="required" name="subject_id" id="subject_id">
                                    <option value="">--<?php echo $this->lang->line('select'); ?>--</option>
                                </select>
                                <div class="help-block"><?php echo form_error('subject_id'); ?></div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-3 col-xs-12">
                            <div class="item form-group">
                                <div>Grade Type</div>
                                <select class="form-control col-md-7 col-xs-12" required="required" name="type" id="type">
                                    <option value="">--<?php echo $this->lang->line('select'); ?>--</option>
                                    <?php foreach ($types as $key=>$type_name) { ?>
                                        <option value="<?php echo $key; ?>" <?php if (isset($type) && $type == $key) { echo 'selected="selected"';} ?>><?php echo $type_name; ?></option>
                                    <?php } ?>
                                </select>
                                <div class="help-block"><?php echo form_error('type'); ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-2 col-xs-12">
                        <div class="form-group"><br />
                            <button id="send" type="submit" class="btn btn-success"><?php echo $this->lang->line('find'); ?></button>
                        </div>
                    </div>
                </div>
                <?php echo form_close(); ?>
            </div>

            <div class="x_content">
                <?php echo form_open(site_url('gradebook/gradereport/index'), array('name' => 'add', 'id' => 'add', 'class' => 'form-horizontal form-label-left'), ''); ?>
                <table id="datatable-responsive" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Student No</th>
                            <th><?php echo $this->lang->line('name'); ?></th>
                            <th><?php echo $this->lang->line('photo'); ?></th>
                        </tr>
                    </thead>
                    <tbody id="fn_result">
                        <?php
                        $count = 1;
                        if (isset($students) && !empty($students)) {
                        ?>
                            <?php foreach ($students as $obj) { ?>
                                <tr>
                                    <td><?php echo $count++; ?></td>
                                    <td><?php echo $obj->roll_no; ?></td>
                                    <td><?php echo ucfirst($obj->name); ?></td>
                                    <td>
                                        <?php if ($obj->photo != '') { ?>
                                            <img src="<?php echo UPLOAD_PATH; ?>/student-photo/<?php echo $obj->photo; ?>" alt="" width="45" />
                                        <?php } else { ?>
                                            <img src="<?php echo IMG_URL; ?>/default-user.png" alt="" width="45" />
                                        <?php } ?>
                                        <!-- <input type="hidden" value="<?php echo $obj->id; ?>" name="students[]" /> -->
                                    </td>
                                    <td>
                                        <input class="form-control"  type="number" min="0" max="100" step="0.01" name="marks_<?php echo $obj->id; ?>" value="<?php echo $obj->marks > 0 ? @number_format($obj->marks , 2) : ''; ?>" style="width:100%;" autocomplete="off" <?php echo $obj->is_locked == 1 ? 'readonly' : ''; ?>/>
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php } else { ?>
                            <tr>
                                <td colspan="12" align="center"><?php echo $this->lang->line('no_data_found'); ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <div class="ln_solid"></div>
                <div class="form-group">
                    <div class="col-md-6 col-md-offset-5">
                        <input type="hidden" value="<?php echo isset($class_id) ? $class_id : ''; ?>" name="class_id" />
                        <input type="hidden" value="<?php echo isset($section_id) ? $section_id : ''; ?>" name="section_id" />
                        <input type="hidden" value="<?php echo isset($subject_id) ? $subject_id : ''; ?>" name="subject_id" />
                        <input type="hidden" value="<?php echo isset($type) ? $type : ''; ?>" name="type" />
                        <?php if (isset($students) && !empty($students)) { ?>
                            <a href="<?php echo site_url('gradebook/gradereport/index'); ?>" class="btn btn-primary"><?php echo $this->lang->line('cancel'); ?></a>
                            <button id="send" type="submit" class="btn btn-success"><?php echo $this->lang->line('submit'); ?></button>
                        <?php } ?>
                    </div>
                </div>
                <?php echo form_close(); ?>

            </div>

        </div>
    </div>
</div>


<!-- Super admin js START  -->
<script type="text/javascript">

    function get_class_by_school(school_id, class_id) {

        $.ajax({
            type: "POST",
            url: "<?php echo site_url('ajax/get_class_by_school'); ?>",
            data: {
                school_id: school_id,
                class_id: class_id
            },
            async: false,
            success: function(response) {
                if (response) {
                    $('#class_id').html(response);
                }
            }
        });
    }
</script>
<!-- Super admin js end -->


<script type="text/javascript">
    <?php if (isset($class_id) && isset($subject_id)) { ?>
        get_subject_by_class('<?php echo $class_id; ?>','<?php echo $subject_id; ?>');
    <?php } ?>

    function get_subject_by_class(class_id,subject_id) {

        $.ajax({
            type: "POST",
            url: "<?php echo site_url('ajax/get_subject_by_class'); ?>",
            data: {
                class_id: class_id,
                subject_id: subject_id,
            },
            async: false,
            success: function(response) {
                if (response) {
                    $('#subject_id').html(response);
                }
            }
        });
    }
    $("#result").validate();
    $("#add").validate();
</script>
<style>
    #datatable-responsive label.error {
        display: none !important;
    }
</style>