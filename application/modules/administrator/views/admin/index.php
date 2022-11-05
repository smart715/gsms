<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
            <div class="x_title">
                <h3 class="head-title"><i class="fa fa-user-md"></i><small> Manage Admin</small></h3>
                <ul class="nav navbar-right panel_toolbox">
                    <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
                </ul>
                <div class="clearfix"></div>
            </div>
            <div class="x_content quick-link">
                <?php $this->load->view('quick-link'); ?>
            </div>

            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <ul class="nav nav-tabs nav-tabs-info nav-justified">

                            <li class="nav-item"><a class="nav-link active" href="#tab_admin_list" role="tab" data-toggle="tab" aria-expanded="true"><i class="fa fa-list-ol"></i> <?php echo $this->lang->line('list'); ?></a> </li>

                            <?php if (has_permission(ADD, 'hrm', 'employee')) { ?>
                                <?php if (isset($edit)) { ?>
                                    <li class="nav-item"><a class="nav-link" href="<?php echo site_url('administrator/admin/add'); ?>" aria-expanded="false"><i class="fa fa-plus-square-o"></i> <?php echo $this->lang->line('add'); ?></a> </li>
                                <?php } else { ?>
                                    <li class="nav-item"><a class="nav-link" href="#tab_add_admin" role="tab" data-toggle="tab" aria-expanded="false"><i class="fa fa-plus-square-o"></i> <?php echo $this->lang->line('add'); ?></a> </li>
                                <?php } ?>
                            <?php } ?>

                            <?php if (isset($edit)) { ?>
                                <li class="active"><a class="nav-link" href="#tab_edit_admin" role="tab" data-toggle="tab" aria-expanded="false"><i class="fa fa-pencil-square-o"></i> <?php echo $this->lang->line('edit'); ?></a> </li>
                            <?php } ?>

                        </ul>
                        <br />

                        <div class="tab-content">
                            <div class="container tab-pane <?php if (isset($list)) {
                                                                echo 'active';
                                                            } ?>" id="tab_admin_list">
                                <div class="x_content">
                                    <table id="datatable-responsive" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                <th><?php echo $this->lang->line('sl_no'); ?></th>
                                                <th>Login Name(Email)</th>
                                                <th>Created At</th>
                                                <th><?php echo $this->lang->line('action'); ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $count = 1;
                                            if (isset($admins) && !empty($admins)) { ?>
                                                <?php foreach ($admins as $obj) { ?>
                                                    <tr>
                                                        <td><?php echo $count++; ?></td>
                                                        <td><?php echo $obj->username; ?></td>
                                                        <td><?php echo $obj->created_at; ?></td>
                                                        <td>
                                                            <a href="<?php echo site_url('administrator/admin/edit/' . $obj->id); ?>" class="btn btn-info btn-xs"><i class="fa fa-pencil-square-o"></i> <?php echo $this->lang->line('edit'); ?> </a><br />
                                                            <?php if ($obj->id != $this->session->userdata('uid')) { ?>
                                                                <a href="<?php echo site_url('administrator/admin/delete/' . $obj->id); ?>" onclick="javascript: return confirm('<?php echo $this->lang->line('confirm_alert'); ?>');" class="btn btn-danger btn-xs"><i class="fa fa-trash-o"></i> <?php echo $this->lang->line('delete'); ?> </a>
                                                            <?php } ?>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="container tab-pane <?php if (isset($add)) {
                                                                echo 'active';
                                                            } ?>" id="tab_add_admin">
                                <div class="x_content">
                                    <?php echo form_open_multipart(site_url('administrator/admin/add'), array('name' => 'add', 'id' => 'add', 'class' => 'form-horizontal form-label-left'), ''); ?>
                                    <div class="row">
                                        <div class="col-md-3 col-sm-6 col-xs-12">
                                            <div class="item form-group" style="text-align: right;">
                                                <label for="username"><?php echo $this->lang->line('email'); ?> <span class="required">*</span></label>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-sm-6 col-xs-12">
                                            <div class="item form-group">
                                                <input class="form-control" name="username" id="username" value="<?php echo isset($post['username']) ?  $post['username'] : ''; ?>" placeholder="<?php echo $this->lang->line('username'); ?>" required="required" type="email" autocomplete="off">
                                                <div class="help-block"><?php echo form_error('username'); ?></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3 col-sm-6 col-xs-12">
                                            <div class="item form-group" style="text-align: right;">
                                                <label for="password"><?php echo $this->lang->line('password'); ?> <span class="required">*</span></label>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-sm-6 col-xs-12">
                                            <div class="item form-group">
                                                <input class="form-control" name="password" id="password" value="" placeholder="<?php echo $this->lang->line('password'); ?>" required="required" type="password" autocomplete="off">
                                                <div class="help-block"><?php echo form_error('password'); ?></div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="ln_solid"></div>
                                <div class="row">
                                    <div class="col-md-3 col-sm-6 col-xs-12">
                                    </div>
                                    <div class="col-md-9 col-sm-6 col-xs-12">
                                        <a href="<?php echo site_url('administrator/admin/index'); ?>" class="btn btn-primary"><?php echo $this->lang->line('cancel'); ?></a>
                                        <button id="send" type="submit" class="btn btn-success"><?php echo $this->lang->line('submit'); ?></button>
                                    </div>
                                    <?php echo form_close(); ?>
                                </div>
                            </div>

                            <?php if (isset($edit)) { ?>

                                <div class="container tab-pane active" id="tab_edit_admin">
                                    <div class="x_content">
                                        <?php echo form_open_multipart(site_url('administrator/admin/edit/' . $admin->id), array('name' => 'edit', 'id' => 'edit', 'class' => 'form-horizontal form-label-left'), ''); ?>

                                        <div class="row">
                                            <div class="col-md-3 col-sm-6 col-xs-12">
                                                <div class="item form-group">
                                                    <label for="email"><?php echo $this->lang->line('email'); ?> </label>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-sm-6 col-xs-12">
                                                <div class="item form-group">
                                                    <input class="form-control" name="username" id="username" readonly="readonly" value="<?php echo isset($admin->username) ?  $admin->username : ''; ?>" placeholder="<?php echo $this->lang->line('username'); ?>" required="required" type="email" autocomplete="off">
                                                    <div class="help-block"><?php echo form_error('username'); ?></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3 col-sm-6 col-xs-12">
                                                <div class="item form-group">
                                                    <label for="password"><?php echo $this->lang->line('password'); ?> </label>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-sm-6 col-xs-12">
                                                <div class="item form-group">
                                                    <input type="password" class="form-control" name="password" id="password" placeholder="<?php echo $this->lang->line('password'); ?>" required="required" type="text" autocomplete="off">
                                                    <div class="help-block"><?php echo form_error('password'); ?></div>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="ln_solid"></div>
                                        <div class="form-group">
                                            <div class="col-md-6 col-md-offset-3">
                                                <input type="hidden" name="id" id="edit_id" value="<?php echo $admin->id; ?>" />
                                                <a href="<?php echo site_url('administrator/admin/index'); ?>" class="btn btn-primary"><?php echo $this->lang->line('cancel'); ?></a>
                                                <button id="send" type="submit" class="btn btn-success"><?php echo $this->lang->line('update'); ?></button>
                                            </div>
                                        </div>
                                        <?php echo form_close(); ?>
                                    </div>
                                </div>
                            <?php } ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade bs-admin-modal-lg" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
                    <h4 class="modal-title"><?php echo $this->lang->line('detail_information'); ?></h4>
                </div>
                <div class="modal-body fn_admin_data">
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        function get_admin_modal(admin_id) {

            $('.fn_employee_data').html('<p style="padding: 20px;"><p style="padding: 20px;text-align:center;"><img src="<?php echo IMG_URL; ?>loading.gif" /></p>');
            $.ajax({
                type: "POST",
                url: "<?php echo site_url('administrator/admin/get_single_admin'); ?>",
                data: {
                    admin_id: admin_id
                },
                success: function(response) {
                    if (response) {
                        $('.fn_admin_data').html(response);
                    }
                }
            });
        }
    </script>




    <link href="<?php echo VENDOR_URL; ?>datepicker/datepicker.css" rel="stylesheet">
    <script src="<?php echo VENDOR_URL; ?>datepicker/datepicker.js"></script>


    <!-- datatable with buttons -->
    <script type="text/javascript">
        $('#add_dob').datepicker();
        $('#edit_dob').datepicker();

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

        $("#add").validate();
        $("#edit").validate();
    </script>