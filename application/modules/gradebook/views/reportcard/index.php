<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
            <div class="x_title no-print">
                <h3 class="head-title"><i class="fa fa-file-text-o"></i><small> Report Card</small></h3>
                <ul class="nav navbar-right panel_toolbox">
                    <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
                </ul>
                <div class="clearfix"></div>
            </div>


            <div class="x_content no-print">
                <?php echo form_open_multipart(site_url('gradebook/reportcard/index'), array('name' => 'resultcard', 'id' => 'resultcard', 'class' => 'form-horizontal form-label-left'), ''); ?>
                <div class="row">

                    <?php $this->load->view('layout/school_list_filter'); ?>

                    <div class="col-md-2 col-sm-2 col-xs-12">
                        <div class="item form-group">
                            <div><?php echo $this->lang->line('academic_year'); ?> <span class="required">*</span></div>
                            <select class="form-control col-md-7 col-xs-12" name="academic_year_id" id="academic_year_id" required="required">
                                <option value="">--<?php echo $this->lang->line('select'); ?>--</option>
                                <?php foreach ($academic_years as $obj) { ?>
                                    <?php $running = $obj->is_running ? ' [' . $this->lang->line('running_year') . ']' : ''; ?>
                                    <option value="<?php echo $obj->id; ?>" <?php if (isset($academic_year_id) && $academic_year_id == $obj->id) {
                                                                                echo 'selected="selected"';
                                                                            } ?>><?php echo $obj->session_year;
                                                                                    echo $running; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <?php if ($this->session->userdata('role_id') != STUDENT) { ?>

                        <div class="col-md-2 col-sm-2 col-xs-12">
                            <div class="item form-group">
                                <?php $teacher_student_data = get_teacher_access_data('student'); ?>
                                <?php $guardian_class_data = get_guardian_access_data('class'); ?>
                                <div><?php echo $this->lang->line('class'); ?> <span class="required">*</span></div>
                                <select class="form-control col-md-7 col-xs-12" name="class_id" id="class_id" required="required" onchange="get_section_by_class(this.value,'');">
                                    <option value="">--<?php echo $this->lang->line('select'); ?>--</option>
                                    <?php foreach ($classes as $obj) { ?>
                                        <?php if ($this->session->userdata('role_id') == TEACHER && !in_array($obj->id, $teacher_student_data)) {
                                            continue;  ?>
                                        <?php } elseif ($this->session->userdata('role_id') == GUARDIAN && !in_array($obj->id, $guardian_class_data)) {
                                            continue;
                                        } ?>
                                        <option value="<?php echo $obj->id; ?>" <?php if (isset($class_id) && $class_id == $obj->id) {
                                                                                    echo 'selected="selected"';
                                                                                } ?>><?php echo $obj->name; ?></option>
                                    <?php } ?>
                                </select>
                                <div class="help-block"><?php echo form_error('class_id'); ?></div>
                            </div>
                        </div>

                        <div class="col-md-2 col-sm-2 col-xs-12">
                            <div class="item form-group">
                                <div><?php echo $this->lang->line('section'); ?> </div>
                                <select class="form-control col-md-7 col-xs-12" name="section_id" id="section_id" onchange="get_student_by_section(this.value,'');">
                                    <option value="">--<?php echo $this->lang->line('select'); ?>--</option>
                                </select>
                                <div class="help-block"><?php echo form_error('section_id'); ?></div>
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-2 col-xs-12">
                            <div class="item form-group">
                                <div><?php echo $this->lang->line('student'); ?> <span class="required">*</span></div>
                                <select class="form-control col-md-7 col-xs-12" name="student_id" id="student_id" required="required">
                                    <option value="">--<?php echo $this->lang->line('select'); ?>--</option>
                                </select>
                                <div class="help-block"><?php echo form_error('student_id'); ?></div>
                            </div>
                        </div>
                    <?php } ?>

                    <div class="col-md-1 col-sm-1 col-xs-12">
                        <div class="form-group"><br />
                            <button id="send" type="submit" class="btn btn-success"><?php echo $this->lang->line('find'); ?></button>
                        </div>
                    </div>
                </div>
                <?php echo form_close(); ?>
            </div>

            <?php if (isset($student) && !empty($student)) { ?>
                <div class="x_content">
                    <div class="row">
                        <div class="col-sm-6 col-xs-6  col-sm-offset-3 col-xs-offset-3  layout-box">
                            <h4>Report Card</h4>
                            <div class="profile-pic">
                                <?php if ($student->photo != '') { ?>
                                    <img src="<?php echo UPLOAD_PATH; ?>/student-photo/<?php echo $student->photo; ?>" alt="" width="80" />
                                <?php } else { ?>
                                    <img src="<?php echo IMG_URL; ?>/default-user.png" alt="" width="45" />
                                <?php } ?>
                            </div>
                            
                            <table  style="margin: 20px;width:100%;text-align: left;">
                                <tbody>
                                    <tr>
                                        <td width="50%">
                                            <span><strong><?php echo $this->lang->line('name'); ?> : </strong><?php echo $student->name; ?></span><br>
                                            <span><strong><?php echo $this->lang->line('class'); ?> : </strong><?php echo $student->class_name; ?></span><br>
                                            <!-- <span><strong><?php echo $this->lang->line('section'); ?>  : </strong><?php echo $student->section; ?></span><br> -->
                                            <!-- <span><strong>School Year : </strong></span><br> -->
                                            <span><strong>Gender : </strong></span><?php echo $student->gender; ?><br>
                                            <!-- <span><strong>Date of Birth : </strong></span><br>
                                            <span><strong>Place of Birth : </strong></span><br> -->
                                            <span><strong>Address : </strong><?php echo $student->present_address; ?></span><br>
                                        </td>
                                        <td width="50%">
                                            <span><strong>School Name : </strong><?php echo $school->school_name; ?></span><br>
                                            <span><strong>School Address : </strong><?php echo $school->address; ?></span><br>
                                            <span><strong>School Phone : </strong><?php echo $school->phone; ?></span><br>
                                            <span><strong>School Email : </strong><?php echo $school->email; ?></span><br>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php } ?>

            <div class="x_content">
                <table id="datatable-responsive" class="table table-striped_ table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th><?php echo $this->lang->line('sl_no'); ?></th>
                            <th width="12%" style="text-align: center;"><?php echo $this->lang->line('subject'); ?></th>
                            <th>1st Period</th>
                            <th>2nd Period</th>
                            <th>3rd Period</th>
                            <th>Semester Exam</th>
                            <th>Semester Average</th>
                            <th>4th Period</th>
                            <th>5th Period</th>
                            <th>6th Period</th>
                            <th>Semester Exam</th>
                            <th>Semester Average</th>
                            <th>Year Average</th>
                        </tr>
                    </thead>
                    <tbody id="fn_mark">
                        <?php $index = 1;

                        $average_period_1 = 0;
                        $average_period_2 = 0;
                        $average_period_3 = 0;
                        $average_period_4 = 0;
                        $average_period_5 = 0;
                        $average_period_6 = 0;
                        $average_exam_1 = 0;
                        $average_exam_2 = 0;
                        ?>
                        <?php if (isset($result) && !empty($result)) {
                            foreach ($result as $obj) {
                                $average_period_1 += $obj->period1;
                                $average_period_2 += $obj->period2;
                                $average_period_3 += $obj->period3;
                                $average_period_4 += $obj->period4;
                                $average_period_5 += $obj->period5;
                                $average_period_6 += $obj->period6;
                                $average_exam_1 += $obj->exam_1;
                                $average_exam_2 += $obj->exam_2;
                                $average_1 = number_format((($obj->period1 + $obj->period2 + $obj->period3) / 3 + $obj->exam_1) / 2, 2);
                                $average_2 = number_format((($obj->period4 + $obj->period5 + $obj->period6) / 3 + $obj->exam_2) / 2, 2);
                                $average_3 = number_format(($average_1 + $average_2) / 2, 2);
                                if ($average_1 <= 0 || $obj->exam_1 <= 0) $average_1 = "";
                                if ($average_2 <= 0 || $obj->exam_2 <= 0) $average_2 = "";
                                if ($average_3 <= 0 || $obj->exam_2 <= 0) $average_3 = ""; ?>
                                <tr style="background: #f9f9f9;">
                                    <td align="center"><?php echo $index++;  ?></td>
                                    <td align="center"><?php echo $obj->subject_name; ?></td>
                                    <td align="right"><?php echo $obj->period1; ?></td>
                                    <td align="right"><?php echo $obj->period2; ?></td>
                                    <td align="right"><?php echo $obj->period3; ?></td>
                                    <td align="right"><?php echo $obj->exam1; ?></td>
                                    <td align="right"><?php echo $average_1;  ?></td>
                                    <td align="right"><?php echo $obj->period4; ?></td>
                                    <td align="right"><?php echo $obj->period5; ?></td>
                                    <td align="right"><?php echo $obj->period6; ?></td>
                                    <td align="right"><?php echo $obj->exam2; ?></td>
                                    <td align="right"><?php echo $average_2;  ?></td>
                                    <td align="right"><?php echo $average_3;  ?></td>
                                </tr>
                            <?php } ?>
                        <?php } else { ?>
                            <tr>
                                <td colspan="17" align="center"><?php echo $this->lang->line('no_data_found'); ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>

                    <?php if (isset($result) && !is_null($result)) { ?>
                        <tfooter>
                            <tr>
                                <td></td>
                                <td align="center">Average</td>
                                <td align="right"><?php if ($average_period_1 > 0) echo number_format($average_period_1 / ($index - 1), 2); ?></td>
                                <td align="right"><?php if ($average_period_2 > 0) echo number_format($average_period_2 / ($index - 1), 2); ?></td>
                                <td align="right"><?php if ($average_period_3 > 0) echo number_format($average_period_3 / ($index - 1), 2); ?></td>
                                <td align="right"><?php if ($average_exam_1 > 0) echo number_format($average_exam_1 / ($index - 1), 2); ?></td>
                                <td align="right"><?php if ($average_exam_1 > 0) echo number_format((($average_period_1 + $average_period_2 + $average_period_3) / 3 + $average_exam_1) / (2 * ($index - 1)), 2);  ?></td>

                                <td align="right"><?php if ($average_period_4 > 0) echo number_format($average_period_4 / ($index - 1), 2); ?></td>
                                <td align="right"><?php if ($average_period_5 > 0) echo number_format($average_period_5 / ($index - 1), 2); ?></td>
                                <td align="right"><?php if ($average_period_6 > 0) echo number_format($average_period_6 / ($index - 1), 2); ?></td>
                                <td align="right"><?php if ($average_exam_2 > 0) echo number_format($average_exam_2 / ($index - 1), 2); ?></td>
                                <td align="right"><?php if ($average_exam_2 > 0) echo number_format((($average_period_4 + $average_period_5 + $average_period_6) / 3 + $average_exam_2) / (2 * ($index - 1)), 2);  ?></td>
                                <td align="right"><?php if ($average_exam_2 > 0) echo number_format((($average_period_1 + $average_period_2 + $average_period_3 + $average_period_4 + $average_period_5 + $average_period_6) / 12 + ($average_exam_1 + $average_exam_2) / 4) / ($index - 1), 2);  ?></td>
                            </tr>
                        </tfooter>
                    <?php } ?>
                </table>


            </div>

            <!-- <div class="rowt">
                <div class="col-lg-12">&nbsp;</div>
            </div>
            <div class="rowt">
                <div class="col-xs-4 text-center signature">
                    <?php echo $this->lang->line('principal'); ?>
                </div>
                <div class="col-xs-2 text-center">
                    &nbsp;
                </div>
                <div class="col-xs-4 text-center signature">
                    <?php echo $this->lang->line('class_teacher'); ?>
                </div>
            </div> -->

            <div class="row no-print">
                <div class="col-xs-12 text-right">
                    <button class="btn btn-default " onclick="window.print();"><i class="fa fa-print"></i> <?php echo $this->lang->line('print'); ?></button>
                </div>
            </div>
            <!-- <div class="col-md-12 col-sm-12 col-xs-12 no-print">
                <div class="instructions"><strong><?php echo $this->lang->line('instruction'); ?>: </strong> <?php echo $this->lang->line('mark_sheet_instruction'); ?></div>
            </div> -->
        </div>
    </div>
</div>



<!-- Super admin js START  -->
<script type="text/javascript">
    $("document").ready(function() {
        <?php if (isset($school_id) && !empty($school_id) &&  $this->session->userdata('role_id') == SUPER_ADMIN) { ?>
            $(".fn_school_id").trigger('change');
        <?php } ?>
    });

    $('.fn_school_id').on('change', function() {

        var school_id = $(this).val();
        var academic_year_id = '';
        var class_id = '';

        <?php if (isset($school_id) && !empty($school_id)) { ?>
            academic_year_id = '<?php echo $academic_year_id; ?>';
            class_id = '<?php echo $class_id; ?>';
        <?php } ?>

        if (!school_id) {
            toastr.error('<?php echo $this->lang->line("select_school"); ?>');
            return false;
        }

        $.ajax({
            type: "POST",
            url: "<?php echo site_url('ajax/get_academic_year_by_school'); ?>",
            data: {
                school_id: school_id,
                academic_year_id: academic_year_id
            },
            async: false,
            success: function(response) {
                if (response) {
                    $('#academic_year_id').html(response);
                    get_class_by_school(school_id, class_id);
                }
            }
        });
    });

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
    <?php if (isset($class_id) && isset($section_id)) { ?>
        get_section_by_class('<?php echo $class_id; ?>', '<?php echo $section_id; ?>');
    <?php } ?>

    function get_section_by_class(class_id, section_id) {

        var school_id = $('.fn_school_id').val();

        if (!school_id) {
            toastr.error('<?php echo $this->lang->line("select_school"); ?>');
            return false;
        }
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('ajax/get_section_by_class'); ?>",
            data: {
                school_id: school_id,
                class_id: class_id,
                section_id: section_id
            },
            async: false,
            success: function(response) {
                if (response) {
                    $('#section_id').html(response);
                } else {
                    get_student_by_section(class_id, '', '');
                    $('#section_id').html('<option value="">--Select--</option>');
                }
            }
        });
    }

    <?php if (isset($class_id) || isset($section_id)) { ?>
        get_student_by_section('<?php echo $class_id; ?>', '<?php echo $section_id; ?>', '<?php echo $student_id; ?>');
    <?php } ?>

    function get_student_by_section(class_id, section_id, student_id) {

        $.ajax({
            type: "POST",
            url: "<?php echo site_url('ajax/get_student_by_section'); ?>",
            data: {
                class_id: class_id,
                section_id: section_id,
                student_id: student_id
            },
            async: false,
            success: function(response) {
                if (response) {
                    $('#student_id').html(response);
                }
            }
        });
    }

    $("#resultcard").validate();
    $("#marksheet").validate();
</script>
<style>
    .table>thead>tr>th,
    .table>tbody>tr>td {
        padding: 2px;
    }
</style>