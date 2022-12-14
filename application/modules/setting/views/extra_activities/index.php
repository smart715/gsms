<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
            <div class="x_title">
                <h3 class="head-title"><i class="fa fa-bars"></i><small> Extra Curricular Activities</small></h3>
                <ul class="nav navbar-right panel_toolbox">
                    <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
                </ul>
                <div class="clearfix"></div>
            </div>
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <ul class="nav nav-tabs nav-tabs-info nav-justified">
                            <li class="nav-item"><a class="nav-link active" href="#tab_section_list" role="tab" data-toggle="tab" aria-expanded="true"><i class="fa fa-list-ol"></i> <?php echo $this->lang->line('list'); ?></a> </li>
                            <?php if (isset($edit)) { ?>
                                <li class="nav-item"><a class="nav-link" href="<?php echo site_url('setting/extracurricularactivities/add'); ?>" aria-expanded="false"><i class="fa fa-plus-square-o"></i> <?php echo $this->lang->line('add'); ?></a> </li>
                            <?php } else { ?>
                                <li class="nav-item"><a class="nav-link" href="#tab_add_section" role="tab" data-toggle="tab" aria-expanded="false"><i class="fa fa-plus-square-o"></i> <?php echo $this->lang->line('add'); ?></a> </li>
                            <?php } ?>
                            <?php if (isset($edit)) { ?>
                                <li class="nav-item"><a class="nav-link" href="#tab_edit_section" role="tab" data-toggle="tab" aria-expanded="false"><i class="fa fa-pencil-square-o"></i> <?php echo $this->lang->line('edit'); ?></a> </li>
                            <?php } ?>


                        </ul>
                        <br />

                        <div class="tab-content">
                            <div class="container tab-pane <?php if (isset($list)) {
                                                                echo 'active';
                                                            } ?>" id="tab_section_list">
                                <div class="x_content">
                                    <table id="datatable-responsive" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                <th><?php echo $this->lang->line('sl_no'); ?></th>
                                                <th width="30%">Name</th>
                                                <th width="30%">Grade</th>
                                                <th><?php echo $this->lang->line('action'); ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $count = 1;
                                            if (isset($activities) && !empty($activities))
                                                foreach ($activities as $obj) { ?>
                                                <tr>
                                                    <td><?php echo $count++; ?></td>
                                                    <td><?php echo $obj->name; ?></td>
                                                    <td><?php echo $obj->grade; ?></td>
                                                    <td>
                                                        <a href="<?php echo site_url('setting/extracurricularactivities/edit/' . $obj->id); ?>" class="btn btn-info btn-xs"><i class="fa fa-pencil-square-o"></i> <?php echo $this->lang->line('edit'); ?> </a>
                                                        <a href="<?php echo site_url('setting/extracurricularactivities/delete/' . $obj->id); ?>" onclick="javascript: return confirm('<?php echo $this->lang->line('confirm_alert'); ?>');" class="btn btn-danger btn-xs"><i class="fa fa-trash-o"></i> <?php echo $this->lang->line('delete'); ?> </a>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="container tab-pane <?php if (isset($add)) {
                                                                echo 'active';
                                                            } ?>" id="tab_add_section">
                                <div class="x_content">
                                    <?php echo form_open(site_url('setting/extracurricularactivities/add'), array('name' => 'add', 'id' => 'add', 'class' => 'form-horizontal form-label-left'), ''); ?>


                                    <div class="item form-group">
                                        <div class="row">
                                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name"><?php echo $this->lang->line('name'); ?> <span class="required">*</span>
                                            </label>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <input class="form-control" name="name" id="name" value="<?php echo isset($post['name']) ?  $post['name'] : ''; ?>" placeholder="<?php echo $this->lang->line('name'); ?>" required="required" type="text" autocomplete="off">
                                                <div class="help-block"><?php echo form_error('name'); ?></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <div class="row">
                                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Grade<span class="required">*</span>
                                            </label>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <input class="form-control" name="grade" id="grade" value="<?php echo isset($post['grade']) ?  $post['grade'] : ''; ?>" placeholder="grade" required="required" type="text" autocomplete="off">
                                                <div class="help-block"><?php echo form_error('grade'); ?></div>
                                            </div>
                                        </div>
                                    </div>



                                    <div class="ln_solid"></div>
                                    <div class="form-group">
                                        <div class="col-md-6 col-md-offset-3">
                                            <a href="<?php echo site_url('setting/extracurricularactivities'); ?>" class="btn btn-primary"><?php echo $this->lang->line('cancel'); ?></a>
                                            <button id="send" type="submit" class="btn btn-success"><?php echo $this->lang->line('submit'); ?></button>
                                        </div>
                                    </div>
                                    <?php echo form_close(); ?>

                                </div>
                            </div>

                            <?php if (isset($edit)) { ?>
                                <div class="container tab-pane active" id="tab_edit_section">
                                    <div class="x_content">
                                        <?php echo form_open(site_url('setting/extracurricularactivities/edit/' . $section->id), array('name' => 'edit', 'id' => 'edit', 'class' => 'form-horizontal form-label-left'), ''); ?>

                                        <?php $this->load->view('layout/school_list_edit_form'); ?>

                                        <div class="item form-group">
                                            <div class="row">
                                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name"><?php echo $this->lang->line('name'); ?> <span class="required">*</span>
                                                </label>
                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <input class="form-control" name="name" id="name" value="<?php echo isset($section->name) ?  $section->name : ''; ?>" placeholder="<?php echo $this->lang->line('name'); ?>" required="required" type="text" autocomplete="off">
                                                    <div class="help-block"><?php echo form_error('name'); ?></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="item form-group">
                                            <div class="row">
                                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Grade <span class="required">*</span>
                                                </label>
                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <input class="form-control" name="grade" id="grade" value="<?php echo isset($section->nagrademe) ?  $section->grade : ''; ?>" placeholder="grade" required="required" type="text" autocomplete="off">
                                                    <div class="help-block"><?php echo form_error('grade'); ?></div>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="ln_solid"></div>
                                        <div class="form-group">
                                            <div class="col-md-6 col-md-offset-3">
                                                <input type="hidden" value="<?php echo isset($section) ? $section->id : $id; ?>" name="id" />
                                                <a href="<?php echo site_url('setting/extracurricularactivities'); ?>" class="btn btn-primary"><?php echo $this->lang->line('cancel'); ?></a>
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



        $("#add").validate();
        $("#edit").validate();
    </script>