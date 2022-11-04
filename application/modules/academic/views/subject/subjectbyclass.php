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
                            <?php if ($this->session->userdata('role_id') == SUPER_ADMIN || $this->session->userdata('role_id') == ADMIN) {  ?>

                                <?php echo form_open(site_url('academic/subjectbyclass/index'), array('name' => 'filter', 'id' => 'filter', 'class' => 'form-horizontal form-label-left'), ''); ?>
                                <select class="form-control col-md-7 col-xs-12" id="filter_class_id" name="class_id" style="width:auto;" onchange="this.form.submit();">
                                    <option value="">--<?php echo $this->lang->line('class'); ?>--</option>
                                    <?php if (isset($classes) && !empty($classes)) { ?>
                                        <?php foreach ($classes as $obj) { ?>
                                            <option value="<?php echo $obj->id; ?>" <?php if ($filter_class_id == $obj->id) echo "selected" ?>><?php echo $obj->name; ?></option>
                                        <?php } ?>
                                    <?php } ?>
                                </select>
                                <?php echo form_close(); ?>
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
                                            <th><?php echo $this->lang->line('class'); ?></th>
                                            <th><?php echo $this->lang->line('subject'); ?></th>
                                            <th><?php echo $this->lang->line('teacher'); ?></th>
                                            <th><?php echo $this->lang->line('action'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $count = 1;
                                        if (isset($result) && !empty($result)) { ?>
                                            <?php foreach ($result as $row) { ?>
                                                <tr style="text-align: center;">
                                                    <?php echo form_open(site_url('academic/subjectbyclass/edit'), array('name' => 'filter', 'id' => 'filter', 'class' => 'form-horizontal form-label-left'), ''); ?>

                                                    <td><?php echo $count++; ?>
                                                        <input name="id" value="<?php echo $row->id; ?>" type="hidden">
                                                    </td>
                                                    <td>
                                                        <div class="item-show-<?php echo $row->id; ?>">
                                                            <?php echo $row->class_name; ?>
                                                        </div>
                                                        <select class="form-control item-edit-<?php echo $row->id; ?>" name="class_id" id="add_class_id" required="required" style="display:none;width: 100%;">
                                                            <option value="">--<?php echo $this->lang->line('select'); ?>--</option>
                                                            <?php foreach ($classes as $obj) { ?>
                                                                <option value="<?php echo $obj->id; ?>" <?php echo $row->class_id == $obj->id ?  'selected="selected"' : ''; ?>><?php echo $obj->name; ?></option>
                                                            <?php } ?>
                                                        </select>

                                                    </td>
                                                    <td>
                                                        <div class="item-show-<?php echo $row->id; ?>">
                                                            <?php echo $row->subject_name; ?>
                                                        </div>
                                                        <select class="form-control item-edit-<?php echo $row->id; ?>" name="subject_id" id="subject_id" required="required" style="display:none; width: 100%;">
                                                            <option value="">--<?php echo $this->lang->line('select'); ?>--</option>
                                                            <?php foreach ($subjects as $obj) { ?>
                                                                <option value="<?php echo $obj->id; ?>" <?php echo $row->subject_id == $obj->id ?  'selected="selected"' : ''; ?>><?php echo $obj->name; ?></option>
                                                            <?php } ?>
                                                        </select>

                                                    </td>

                                                    <td>
                                                        <div class="item-show-<?php echo $row->id; ?>">
                                                            <?php echo $row->teacher; ?>
                                                        </div>
                                                        <select class="form-control item-edit-<?php echo $row->id; ?>" name="teacher_id" id="add_teacher_id" required="required" style="display:none; width: 100%;">
                                                            <option value="">--<?php echo $this->lang->line('select'); ?>--</option>
                                                            <?php foreach ($teachers as $obj) { ?>
                                                                <option value="<?php echo $obj->id; ?>" <?php echo $row->teacher_id == $obj->id ?  'selected="selected"' : ''; ?>><?php echo $obj->name; ?></option>
                                                            <?php } ?>
                                                        </select>

                                                    </td>
                                                    <td>
                                                        <?php if (has_permission(EDIT, 'academic', 'subject')) { ?>
                                                            <a class="btn btn-info btn-xs item-edit-btn item-show-<?php echo $row->id; ?>" data-id="<?php echo $row->id; ?>"><i class="fa fa-pencil-square-o"></i> <?php echo $this->lang->line('edit'); ?> </a>

                                                            <button class="btn btn-success btn-xs item-edit-<?php echo $row->id; ?>" type="submit" style="display: none;"><i class="fa fa-save"></i> Save </button>

                                                            <a class="btn btn-danger btn-xs item-show-<?php echo $row->id; ?>" href="<?php echo site_url('academic/subjectbyclass/delete/' . $row->id); ?>" onclick="javascript: return confirm('<?php echo $this->lang->line('confirm_alert'); ?>');"><i class="fa fa-trash-o"></i> <?php echo $this->lang->line('delete'); ?> </a>
                                                        <?php } ?>
                                                    </td>
                                                    <?php echo form_close(); ?>
                                                </tr>
                                            <?php } ?>
                                        <?php } ?>
                                        <tr style="text-align: center;">
                                            <?php echo form_open(site_url('academic/subjectbyclass/add'), array('name' => 'filter', 'id' => 'filter', 'class' => 'form-horizontal form-label-left'), ''); ?>
                                            <td><?php echo $count++; ?>
                                            </td>
                                            <td>

                                                <select class="form-control" name="class_id" id="add_class_id" required="required" style="width: 100%;">
                                                    <option value="">--<?php echo $this->lang->line('select'); ?>--</option>
                                                    <?php foreach ($classes as $obj) { ?>
                                                        <option value="<?php echo $obj->id; ?>" <?php echo (isset($post['class_id']) && $post['class_id'] == $obj->id) || isset($class_id) && $class_id == $obj->id ?  'selected="selected"' : ''; ?>><?php echo $obj->name; ?></option>
                                                    <?php } ?>
                                                </select>

                                            </td>
                                            <td>

                                                <select class="form-control" name="subject_id" id="subject_id" required="required" style="width: 100%;">
                                                    <option value="">--<?php echo $this->lang->line('select'); ?>--</option>
                                                    <?php foreach ($subjects as $obj) { ?>
                                                        <option value="<?php echo $obj->id; ?>"><?php echo $obj->name; ?></option>
                                                    <?php } ?>
                                                </select>

                                            </td>
                                            <td>
                                                <select class="form-control" name="teacher_id" id="add_teacher_id" required="required" style="width: 100%;">
                                                    <option value="">--<?php echo $this->lang->line('select'); ?>--</option>
                                                    <?php foreach ($teachers as $obj) { ?>
                                                        <option value="<?php echo $obj->id; ?>" <?php echo isset($post['teacher_id']) && $post['teacher_id'] == $obj->id ?  'selected="selected"' : ''; ?>><?php echo $obj->name; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </td>
                                            <td>
                                                <?php if (has_permission(EDIT, 'academic', 'subject')) { ?>
                                                    <button class="btn btn-success btn-xs" type="submit"><i class="fa fa-save"></i> Save </button>
                                                <?php } ?>
                                            </td>
                                            <?php echo form_close(); ?>
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
    $('.item-edit-btn').click(function() {
        let id = $(this).data('id');
        console.log(id);
        $('.item-show-' + id).hide();
        $('.item-edit-' + id).show();

    })
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
</script>