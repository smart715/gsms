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


            <?php echo form_open_multipart(site_url('gradebook/reportcard/index'), array('name' => 'reportcard', 'id' => 'reportcard', 'class' => 'form-horizontal form-label-left'), ''); ?>
            <input type="hidden" value="0" id="action_type" name="action_type">
            <input type="hidden" value="0" id="report_card_id" name="report_card_id">
            <div class="x_content no-print">
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

                    <div class="col-md-4 col-sm-4 col-xs-12">
                        <div class="form-group"><br />
                            <button id="send" type="submit" class="btn btn-success"><?php echo $this->lang->line('find'); ?></button>
                            <?php if (isset($report_card) && $report_card->status == 1) { ?>
                                <button id="unlockBtn" type="button" class="btn btn-danger">Unlock</button>
                            <?php } else if (isset($report_card)) { ?>
                                <button id="lockBtn" type="button" class="btn btn-warning">Lock</button>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>

            <?php if (isset($student) && !empty($student)) { ?>
                <div class="x_content">
                    <div class="row">
                        <div class="col-sm-6 col-xs-6  col-sm-offset-3 col-xs-offset-3  layout-box">
                            <h4><strong>Report Card</strong></h4>
                            <div class="profile-pic">
                                <?php if ($school->logo) { ?>
                                    <img class="certificate-title-img" src="<?php echo UPLOAD_PATH; ?>/logo/<?php echo $school->logo; ?>" alt="" style="width: 70px;margin: 0 20px;" />
                                <?php } elseif ($school->frontend_logo) { ?>
                                    <img class="certificate-title-img" src="<?php echo UPLOAD_PATH; ?>/logo/<?php echo $school->frontend_logo; ?>" alt="" style="width: 70px;margin: 0 20px;" />
                                <?php } else { ?>
                                    <img class="certificate-title-img" src="<?php echo UPLOAD_PATH; ?>/logo/<?php echo $this->global_setting->brand_logo; ?>" alt="" style="width: 70px;margin: 0 20px;" />
                                <?php } ?>
                            </div>

                            <table style="margin: 20px;width:100%;text-align: left;">
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
                                    $period1_locked = $obj->period1_locked;
                                    $period2_locked = $obj->period2_locked;
                                    $period3_locked = $obj->period3_locked;
                                    $exam1_locked = $obj->exam1_locked;
                                    $period4_locked = $obj->period4_locked;
                                    $period5_locked = $obj->period5_locked;
                                    $period6_locked = $obj->period6_locked;
                                    $exam2_locked = $obj->exam2_locked;
                                    $average_period_1 += $obj->period1;
                                    $average_period_2 += $obj->period2;
                                    $average_period_3 += $obj->period3;
                                    $average_period_4 += $obj->period4;
                                    $average_period_5 += $obj->period5;
                                    $average_period_6 += $obj->period6;
                                    $average_exam_1 += $obj->exam1;
                                    $average_exam_2 += $obj->exam2;
                                    $average_1 = number_format((($obj->period1 + $obj->period2 + $obj->period3) / 3 + $obj->exam1) / 2, 2);
                                    $average_2 = number_format((($obj->period4 + $obj->period5 + $obj->period6) / 3 + $obj->exam2) / 2, 2);
                                    $average_3 = number_format(($average_1 + $average_2) / 2, 2);
                                    if ($average_1 <= 0 || $obj->exam1 <= 0) $average_1 = "";
                                    if ($average_2 <= 0 || $obj->exam2 <= 0) $average_2 = "";
                                    if ($average_3 <= 0 || $obj->exam2 <= 0) $average_3 = ""; ?>
                                    <tr style="background: #f9f9f9;">
                                        <td align="center"><?php echo $index++;  ?></td>
                                        <td align="center"><?php echo $obj->subject_name; ?></td>
                                        <td align="right">
                                            <?php if ($editable) { ?>
                                                <input type="number" name="report_<?php echo $obj->id; ?>_period1" value="<?php echo $obj->period1 ?? ''; ?>" min="0" max="100" step="0.01" style="text-align: right;width:100%;">
                                            <?php } else {
                                                echo $obj->period1;
                                            } ?>
                                        </td>
                                        <td align="right">
                                            <?php if ($editable) { ?>
                                                <input type="number" name="report_<?php echo $obj->id; ?>_period2" value="<?php echo $obj->period2 ?? ''; ?>" min="0" max="100" step="0.01" style="text-align: right;width:100%;">
                                            <?php } else {
                                                echo $obj->period2;
                                            } ?>
                                        </td>
                                        <td align="right">
                                            <?php if ($editable) { ?>
                                                <input type="number" name="report_<?php echo $obj->id; ?>_period3" value="<?php echo $obj->period3 ?? ''; ?>" min="0" max="100" step="0.01" style="text-align: right;width:100%;">
                                            <?php } else {
                                                echo $obj->period3;
                                            } ?>
                                        </td>
                                        <td align="right">
                                            <?php if ($editable) { ?>
                                                <input type="number" name="report_<?php echo $obj->id; ?>_exam1" value="<?php echo $obj->exam1 ?? ''; ?>" min="0" max="100" step="0.01" style="text-align: right;width:100%;">
                                            <?php } else {
                                                echo $obj->exam1;
                                            } ?>
                                        </td>
                                        <td align="right"><?php echo $average_1;  ?></td>
                                        <td align="right">
                                            <?php if ($editable) { ?>
                                                <input type="number" name="report_<?php echo $obj->id; ?>_period4" value="<?php echo $obj->period4 ?? ''; ?>" min="0" max="100" step="0.01" style="text-align: right;width:100%;">
                                            <?php } else {
                                                echo $obj->period4;
                                            } ?>
                                        </td>
                                        <td align="right">
                                            <?php if ($editable) { ?>
                                                <input type="number" name="report_<?php echo $obj->id; ?>_period5" value="<?php echo $obj->period5 ?? ''; ?>" min="0" max="100" step="0.01" style="text-align: right;width:100%;">
                                            <?php } else {
                                                echo $obj->period5;
                                            } ?>
                                        </td>
                                        <td align="right">
                                            <?php if ($editable) { ?>
                                                <input type="number" name="report_<?php echo $obj->id; ?>_period6" value="<?php echo $obj->period6 ?? ''; ?>" min="0" max="100" step="0.01" style="text-align: right;width:100%;">
                                            <?php } else {
                                                echo $obj->period6;
                                            } ?>
                                        </td>
                                        <td align="right">
                                            <?php if ($editable) { ?>
                                                <input type="number" name="report_<?php echo $obj->id; ?>_exam2" value="<?php echo $obj->exam2 ?? ''; ?>" min="0" max="100" step="0.01" style="text-align: right;width:100%;" <?php echo $obj->exam2_locked ? 'readonly' : '' ?>>
                                            <?php } else {
                                                echo $obj->exam2;
                                            } ?>
                                        </td>
                                        <td align="right"><?php echo $average_2;  ?></td>
                                        <td align="right"><?php echo $average_3;  ?></td>
                                    </tr>
                                <?php } ?>

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
                            <?php } else { ?>
                                <tr>
                                    <td colspan="17" align="center"><?php echo $this->lang->line('no_data_found'); ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                        <p>
                            <?php if (isset($time_list) && count($time_list) > 0) { ?>
                                <tbody>
                                    <?php foreach ($time_list as $obj) {
                                        if ($obj->type == 1) $type_name = "Days Absent";
                                        else if ($obj->type == 2) $type_name = "Conduct";
                                        else if ($obj->type == 3) $type_name = "Days present";
                                        else $type_name = "Time/tardy";
                                    ?>
                                        <tr>
                                            <td></td>
                                            <td><?php echo $type_name; ?></td>
                                            <td align="right">
                                                <?php if ($editable) { ?>
                                                    <input type="text" name="time_<?php echo $obj->type; ?>[1]" value="<?php echo $obj->period_1 ?? ''; ?>" style="width:100%;">
                                                <?php } else {
                                                    echo $obj->period_1 > 0 ? $obj->period_1 : '';
                                                } ?>
                                            </td>
                                            <td align="right">
                                                <?php if ($editable) { ?>
                                                    <input type="text" name="time_<?php echo $obj->type; ?>[2]" value="<?php echo $obj->period_2 ?? ''; ?>" style="width:100%;">
                                                <?php } else {
                                                    echo $obj->period_2 > 0 ? $obj->period_2 : '';
                                                } ?>
                                            </td>
                                            <td align="right">
                                                <?php if ($editable) { ?>
                                                    <input type="text" name="time_<?php echo $obj->type; ?>[3]" value="<?php echo $obj->period_3 ?? ''; ?>" style="width:100%;">
                                                <?php } else {
                                                    echo $obj->period_3 > 0 ? $obj->period_3 : '';
                                                } ?>
                                            </td>
                                            <td></td>
                                            <td></td>
                                            <td align="right">
                                                <?php if ($editable) { ?>
                                                    <input type="text" name="time_<?php echo $obj->type; ?>[4]" value="<?php echo $obj->period_4 ?? ''; ?>" min="0" style="width:100%;">
                                                <?php } else {
                                                    echo $obj->period_4 > 0 ? $obj->period_4 : '';
                                                } ?>
                                            </td>
                                            <td align="right">
                                                <?php if ($editable) { ?>
                                                    <input type="text" name="time_<?php echo $obj->type; ?>[5]" value="<?php echo $obj->period_5 ?? ''; ?>" min="0" style="width:100%;">
                                                <?php } else {
                                                    echo $obj->period_5 > 0 ? $obj->period_5 : '';
                                                } ?>
                                            </td>
                                            <td align="right">
                                                <?php if ($editable) { ?>
                                                    <input type="text" name="time_<?php echo $obj->type; ?>[6]" value="<?php echo $obj->period_6 ?? ''; ?>" min="0" style="width:100%;">
                                                <?php } else {
                                                    echo $obj->period_6 > 0 ? $obj->period_6 : '';
                                                } ?>
                                            </td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            <?php } ?>

                            <?php if ($editable && isset($result) && !is_null($result)) { ?>
                                <tfooter>
                                    <tr>
                                        <th></th>
                                        <th align="center">Is Locked?</th>
                                        <th style="text-align: center;"><input type="checkbox" name="locked_period1" <?php echo $period1_locked ? 'checked' : ''; ?>></th>
                                        <th style="text-align: center;"><input type="checkbox" name="locked_period2" <?php echo $period2_locked ? 'checked' : ''; ?>></th>
                                        <th style="text-align: center;"><input type="checkbox" name="locked_period3" <?php echo $period3_locked ? 'checked' : ''; ?>></th>
                                        <th style="text-align: center;"><input type="checkbox" name="locked_exam1" <?php echo $exam1_locked ? 'checked' : ''; ?>></th>
                                        <th></th>
                                        <th style="text-align: center;"><input type="checkbox" name="locked_period4" <?php echo $period4_locked ? 'checked' : ''; ?>></th>
                                        <th style="text-align: center;"><input type="checkbox" name="locked_period5" <?php echo $period5_locked ? 'checked' : ''; ?>></th>
                                        <th style="text-align: center;"><input type="checkbox" name="locked_period6" <?php echo $period6_locked ? 'checked' : ''; ?>></th>
                                        <th style="text-align: center;"><input type="checkbox" name="locked_exam2" <?php echo $exam2_locked ? 'checked' : ''; ?>></th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                </tfooter>
                            <?php } ?>
                    </table>


                </div>

                <?php echo form_close(); ?>


                <?php if (isset($extracurricular_activities) && !is_null($extracurricular_activities)) { ?>
                    <div class="x_content">
                        <div class="row">
                            <div class="col-sm-6 col-xs-6  col-sm-offset-3 col-xs-offset-3">
                                <div style="font-size: 20px;text-align:center;"><span style="font-size: 20px;text-align:center;">EXTRA CURRICULAR ACTIVITIES</span></div>
                                <table class="table table-striped_ table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <th align="center">Learning Areas</th>
                                            <th align="center">Grade</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($extracurricular_activities as $activity) { ?>
                                            <tr>
                                                <td><?php echo $activity->name; ?></td>
                                                <td><?php echo $activity->grade; ?></td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                <?php } ?>
                <?php if (isset($marking_standard) && !is_null($marking_standard)) { ?>
                    <div class="x_content">
                        <div class="row">
                            <div class="col-sm-6 col-xs-6  col-sm-offset-3 col-xs-offset-3 ">
                                <div style="font-size: 20px;text-align:center;"><span style="font-size: 20px;text-align:center;">MARKING STANDARD</span></div>
                                <table class="table table-striped_ table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                                    <tbody>
                                        <tr>
                                            <?php foreach ($marking_standard as $obj) { ?>
                                                <th><?php echo $obj->name; ?></th>
                                            <?php } ?>
                                        </tr>
                                        <tr>
                                            <?php foreach ($marking_standard as $obj) { ?>
                                                <td><?php echo $obj->percent; ?></td>
                                            <?php } ?>
                                        </tr>
                                        <tr>
                                            <?php foreach ($marking_standard as $obj) { ?>
                                                <td><?php echo $obj->note; ?></td>
                                            <?php } ?>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                <?php } ?>
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
                        <?php if ($editable) { ?>
                            <button id="savebtn" type="button" class="btn btn-success">Save</button>
                        <?php } ?>
                        <button class="btn btn-default " onclick="window.print();"><i class="fa fa-print"></i> <?php echo $this->lang->line('print'); ?></button>
                    </div>
                </div>
            <?php } ?>
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

    $("#savebtn").click(function() {
        $("#action_type").val("1");
        $("#reportcard").submit();
    });
    $("#lockBtn").click(function() {
        $("#action_type").val("2");
        $("#reportcard").submit();
    });
    $("#unlockBtn").click(function() {
        $("#action_type").val("3");
        $("#reportcard").submit();
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