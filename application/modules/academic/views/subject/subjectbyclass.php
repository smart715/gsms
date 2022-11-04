<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
            <div class="x_title">
                <h3 class="head-title"><i class="fa fa-folder-open"></i><small> Subject - Class - Teacher</small></h3>
                <ul class="nav navbar-right panel_toolbox">
                    <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
                </ul>
                <div class="clearfix"></div>
            </div>
            <div class="x_content quick-link">
                <?php $this->load->view('quick-link'); ?>
            </div>
            <div class="x_content">
                <div class="" data-example-id="togglable-tabs">
                    <ul class="nav nav-tabs bordered">

                        <li class="li-class-list">
                            <?php if ($this->session->userdata('role_id') == SUPER_ADMIN) {  ?>

                                <?php echo form_open(site_url('academic/subjectbyclass/index'), array('name' => 'filter', 'id' => 'filter', 'class' => 'form-horizontal form-label-left'), ''); ?>
                                <select class="form-control col-md-7 col-xs-12" style="width:auto;" name="school_id" onchange="get_class_by_school(this.value, '');">
                                    <option value="">--<?php echo $this->lang->line('select_school'); ?>--</option>
                                    <?php foreach ($schools as $obj) { ?>
                                        <option value="<?php echo $obj->id; ?>" <?php if (isset($filter_school_id) && $filter_school_id == $obj->id) {
                                                                                    echo 'selected="selected"';
                                                                                } ?>> <?php echo $obj->school_name; ?></option>
                                    <?php } ?>
                                </select>
                                <select class="form-control col-md-7 col-xs-12" id="filter_class_id" name="class_id" style="width:auto;" onchange="this.form.submit();">
                                    <option value="">--<?php echo $this->lang->line('class'); ?>--</option>
                                    <?php if (isset($class_list) && !empty($class_list)) { ?>
                                        <?php foreach ($class_list as $obj) { ?>
                                            <option value="<?php echo $obj->id; ?>"><?php echo $obj->name; ?></option>
                                        <?php } ?>
                                    <?php } ?>
                                </select>
                                <?php echo form_close(); ?>

                            <?php } elseif ($this->session->userdata('role_id') == TEACHER) {  ?>
                            <?php } else {  ?>
                                <select class="form-control col-md-7 col-xs-12" onchange="get_subject_by_class(this.value);">
                                    <?php if ($this->session->userdata('role_id') != STUDENT) { ?>
                                        <option value="<?php echo site_url('academic/subjectbyclass/index'); ?>">--<?php echo $this->lang->line('select'); ?>--</option>
                                    <?php } ?>

                                    <?php $guardian_class_data = get_guardian_access_data('class'); ?>
                                    <?php foreach ($classes as $obj) { ?>
                                        <?php if ($this->session->userdata('role_id') == STUDENT) { ?>
                                            <?php if ($obj->id != $this->session->userdata('class_id')) {
                                                continue;
                                            } ?>
                                            <option value="<?php echo site_url('academic/subjectbyclass/index/' . $obj->id); ?>" <?php if (isset($class_id) && $class_id == $obj->id) {
                                                                                                                                        echo 'selected="selected"';
                                                                                                                                    } ?>><?php echo $obj->name; ?></option>
                                        <?php } elseif ($this->session->userdata('role_id') == GUARDIAN) { ?>
                                            <?php if (!in_array($obj->id, $guardian_class_data)) {
                                                continue;
                                            } ?>
                                            <option value="<?php echo site_url('academic/subjectbyclass/index/' . $obj->id); ?>" <?php if (isset($class_id) && $class_id == $obj->id) {
                                                                                                                                        echo 'selected="selected"';
                                                                                                                                    } ?>><?php echo $obj->name; ?></option>
                                        <?php } else { ?>
                                            <option value="<?php echo site_url('academic/subjectbyclass/index/' . $obj->id); ?>" <?php if (isset($class_id) && $class_id == $obj->id) {
                                                                                                                                        echo 'selected="selected"';
                                                                                                                                    } ?>><?php echo $obj->name; ?></option>
                                        <?php } ?>
                                    <?php } ?>
                                </select>
                            <?php } ?>
                        </li>
                    </ul>
                    <br />

                    <div class="tab-content">
                        <div class="tab-pane fade in <?php if (isset($list)) {
                                                            echo 'active';
                                                        } ?>" id="tab_subject_list">
                            <div class="x_content">
                                <table id="datatable-responsive" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <th><?php echo $this->lang->line('sl_no'); ?></th>
                                            <?php if ($this->session->userdata('role_id') == SUPER_ADMIN) { ?>
                                                <th><?php echo $this->lang->line('school'); ?></th>
                                            <?php } ?>
                                            <th><?php echo $this->lang->line('class'); ?></th>
                                            <th><?php echo $this->lang->line('section'); ?></th>
                                            <th><?php echo $this->lang->line('subject'); ?></th>
                                            <th><?php echo $this->lang->line('teacher'); ?></th>
                                            <th><?php echo $this->lang->line('action'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $count = 1;
                                        if (isset($result) && !empty($result)) { ?>
                                            <?php foreach ($result as $obj) { ?>
                                                <?php
                                                if ($this->session->userdata('role_id') == GUARDIAN) {
                                                    if (!in_array($obj->class_id, $guardian_class_data)) {
                                                        continue;
                                                    }
                                                } elseif ($this->session->userdata('role_id') == STUDENT) {
                                                    if ($obj->class_id != $this->session->userdata('class_id')) {
                                                        continue;
                                                    }
                                                }
                                                ?>
                                                <tr>
                                                    <td><?php echo $count++; ?></td>
                                                    <?php if ($this->session->userdata('role_id') == SUPER_ADMIN) { ?>
                                                        <td><?php echo $obj->school_name; ?></td>
                                                    <?php } ?>
                                                    <td><?php echo $obj->class_name; ?></td>
                                                    <td><?php echo $obj->section_name; ?></td>
                                                    <td><?php echo $obj->subject_name; ?></td>
                                                    <td><?php echo $obj->teacher; ?></td>
                                                    <td>
                                                        <?php if (has_permission(EDIT, 'academic', 'subject')) { ?>
                                                            <a href="<?php echo site_url('academic/subjectbyclass/edit/' . $obj->id); ?>" class="btn btn-info btn-xs"><i class="fa fa-pencil-square-o"></i> <?php echo $this->lang->line('edit'); ?> </a>
                                                        <?php } ?>
                                                        <?php if (has_permission(DELETE, 'academic', 'subject')) { ?>
                                                            <a href="<?php echo site_url('academic/subjectbyclass/delete/' . $obj->id); ?>" onclick="javascript: return confirm('<?php echo $this->lang->line('confirm_alert'); ?>');" class="btn btn-danger btn-xs"><i class="fa fa-trash-o"></i> <?php echo $this->lang->line('delete'); ?> </a>
                                                        <?php } ?>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        <?php } ?>
                                        <tr>
                                            <td><?php echo $count++; ?>
                                            </td>
                                            <td>
                                                <?php $this->load->view('layout/school_list_form'); ?>
                                            </td>
                                            <td>

                                                <select class="form-control" name="class_id" id="add_class_id" required="required">
                                                    <option value="">--<?php echo $this->lang->line('select'); ?>--</option>
                                                    <?php foreach ($classes as $obj) { ?>
                                                        <option value="<?php echo $obj->id; ?>" <?php echo (isset($post['class_id']) && $post['class_id'] == $obj->id) || isset($class_id) && $class_id == $obj->id ?  'selected="selected"' : ''; ?>><?php echo $obj->name; ?></option>
                                                    <?php } ?>
                                                </select>

                                            </td>
                                            <td>
                                                <select class="form-control" name="subject_id" id="subject_id" required="required">
                                                    <option value="">--<?php echo $this->lang->line('select'); ?>--</option>
                                                    <?php foreach ($subjects as $obj) { ?>
                                                        <option value="<?php echo $obj->id; ?>"><?php echo $obj->name; ?></option>
                                                    <?php } ?>
                                                </select>

                                            </td>
                                            <td>

                                                <select class="form-control" name="subject_id" id="subject_id" required="required">
                                                    <option value="">--<?php echo $this->lang->line('select'); ?>--</option>
                                                    <?php foreach ($subjects as $obj) { ?>
                                                        <option value="<?php echo $obj->id; ?>"><?php echo $obj->name; ?></option>
                                                    <?php } ?>
                                                </select>

                                            </td>
                                            <td>
                                                <select class="form-control" name="teacher_id" id="add_teacher_id" required="required">
                                                    <option value="">--<?php echo $this->lang->line('select'); ?>--</option>
                                                    <?php foreach ($teachers as $obj) { ?>
                                                        <option value="<?php echo $obj->id; ?>" <?php echo isset($post['teacher_id']) && $post['teacher_id'] == $obj->id ?  'selected="selected"' : ''; ?>><?php echo $obj->name; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </td>
                                            <td>
                                                <?php if (has_permission(EDIT, 'academic', 'subject')) { ?>
                                                    <a onclick="get_subject_modal(<?php echo $obj->id; ?>);" data-toggle="modal" data-target=".bs-subject-modal-lg" class="btn btn-success btn-xs"><i class="fa fa-save"></i> Save </a>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade bs-subject-modal-lg" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title"><?php echo $this->lang->line('detail_information'); ?></h4>
            </div>
            <div class="modal-body fn_subject_data">
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    function get_subject_modal(subject_id) {

        $('.fn_subject_data').html('<p style="padding: 20px;"><p style="padding: 20px;text-align:center;"><img src="<?php echo IMG_URL; ?>loading.gif" /></p>');
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('academic/subjectbyclass/get_single_subject'); ?>",
            data: {
                subject_id: subject_id
            },
            success: function(response) {
                if (response) {
                    $('.fn_subject_data').html(response);
                }
            }
        });
    }
</script>


<!-- Super admin js START  -->
<script type="text/javascript">
    var edit = false;
    <?php if (isset($school_id)) { ?>
        edit = true;
    <?php } ?>

    $("document").ready(function() {
        <?php if (isset($school_id) && !empty($school_id)) { ?>
            $("#edit_school_id").trigger('change');
        <?php } ?>
    });

    $('.fn_school_id').on('change', function() {

        var school_id = $(this).val();
        var class_id = '';
        var teacher_id = '';

        <?php if (isset($subject) && !empty($subject)) { ?>
            class_id = '<?php echo $subject->class_id; ?>';
            teacher_id = '<?php echo $subject->teacher_id; ?>';
        <?php } ?>

        if (!school_id) {
            toastr.error('<?php echo $this->lang->line("select_school"); ?>');
            return false;
        }

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
                    if (edit) {
                        $('#edit_class_id').html(response);
                    } else {
                        $('#add_class_id').html(response);
                    }

                    get_teacher_by_school(school_id, teacher_id);
                }
            }
        });
    });


    function get_teacher_by_school(school_id, teacher_id) {

        $.ajax({
            type: "POST",
            url: "<?php echo site_url('ajax/get_teacher_by_school'); ?>",
            data: {
                school_id: school_id,
                teacher_id: teacher_id
            },
            async: false,
            success: function(response) {
                if (response) {
                    if (edit) {
                        $('#edit_teacher_id').html(response);
                    } else {
                        $('#add_teacher_id').html(response);
                    }
                }
            }
        });
    }
</script>
<!-- Super admin js end -->

<!-- datatable with buttons -->
<script type="text/javascript">
    $(document).ready(function() {
        $('#datatable-responsive').DataTable({
            dom: 'Bfrtip',
            iDisplayLength: 15,
            buttons: [
                'copyHtml5',
                'excelHtml5',
                'csvHtml5',
                'pdfHtml5',
                'pageLength'
            ],
            search: true,
            responsive: true
        });
    });



    <?php if (isset($filter_class_id)) { ?>
        get_class_by_school('<?php echo $filter_school_id; ?>', '<?php echo $filter_class_id; ?>');
    <?php } ?>

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
                    $('#filter_class_id').html(response);
                }
            }
        });
    }

    function get_subject_by_class(url) {

        if (url) {
            window.location.href = url;
        }
    }

    $("#add").validate();
    $("#edit").validate();
</script>