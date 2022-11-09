<?php

defined('BASEPATH') or exit('No direct script access allowed');

/* * *****************Gallery.php**********************************
 * @product name    : Global Multi School Management System Express
 * @type            : Class
 * @class name      : Reportcard
 * @author          : smartmanage715@gmail.com
 * @copyright       : Codetroopers Team	 	
 * ********************************************************** */

class Reportcard extends MY_Controller
{

    public $data = array();

    function __construct()
    {
        parent::__construct();
        $this->load->model('Reportcard_Model', 'resultcard', true);
    }


    /*****************Function index**********************************
     * @type            : Function
     * @function name   : index
     * @description     : Load "result card" user interface                 
     *                    with data filter option
     * @param           : null
     * @return          : null 
     * ********************************************************** */
    public function index()
    {

        check_permission(VIEW);
        $school_id = getSchoolId();
        if($school_id == 0) redirect('dashboard/index');   

        $school = $this->resultcard->get_school_by_id($school_id);
        $period_num = $school->period_num;

        if ($_POST) {

            if ($this->session->userdata('role_id') == STUDENT) {

                $student = get_user_by_role($this->session->userdata('role_id'), $this->session->userdata('id'));

                $class_id = $student->class_id;
                $section_id = $student->section_id;
                $student_id = $student->id;
            } else {

                $school_id = $this->input->post('school_id');
                $class_id = $this->input->post('class_id');
                $section_id = $this->input->post('section_id');
                $student_id = $this->input->post('student_id');

                $std = $this->resultcard->get_single('students', array('id' => $student_id));
                $student = get_user_by_role(STUDENT, $std->user_id);
            }

            $academic_year_id = $this->input->post('academic_year_id');
            $action_type = $this->input->post('action_type');
            $report_card_id = $this->input->post('report_card_id');
            $report_card = $this->resultcard->get_single('report_cards', array('id' => $report_card_id));
            if (!isset($report_card_id) || $report_card_id == 0) {
                $report_card = $this->resultcard->get_single('report_cards', array('school_id' => $school_id, 'class_id' => $class_id, 'student_id' => $student_id, 'academic_year_id' => $school->academic_year_id));
                if ($report_card) $report_card_id = $report_card->id;
            }

            if (!isset($report_card)) {
                $temp = array();
                $temp['school_id'] = $school_id;
                $temp['class_id'] = $class_id;
                $temp['section_id'] = $section_id;
                $temp['student_id'] = $student_id;
                $temp['academic_year_id'] = $academic_year_id;
                $temp['status'] = '0';
                $temp['created_at'] = date('Y-m-d H:i:s');
                $temp['modified_at'] = date('Y-m-d H:i:s');
                $temp['created_by'] = logged_in_user_id();
                $temp['modified_by'] = logged_in_user_id();
                $report_card_id = $this->resultcard->insert('report_cards', $temp);
            }
            if ($action_type == '2' && $report_card_id != 0) {
                $this->resultcard->update('report_cards', array('status' => '1'), array('id' => $report_card_id));
            } else if ($action_type == '3' && $report_card_id != 0) {
                $this->resultcard->update('report_cards', array('status' => '0'), array('id' => $report_card_id));
            }

            $report_card = $this->resultcard->get_single('report_cards', array('id' => $report_card_id));
            $this->data['report_card'] = $report_card;

            if ($report_card_id != 0) {
                $time_list = $this->resultcard->get_list('report_card_times', array('report_card_id' => $report_card_id), '', '', '', 'id', 'ASC');
                if (is_null($time_list) || count($time_list) == 0) {
                    $this->resultcard->insert('report_card_times',  array('report_card_id' => $report_card_id, 'type' => 1));
                    $this->resultcard->insert('report_card_times',  array('report_card_id' => $report_card_id, 'type' => 2));
                    $this->resultcard->insert('report_card_times',  array('report_card_id' => $report_card_id, 'type' => 3));
                    $this->resultcard->insert('report_card_times',  array('report_card_id' => $report_card_id, 'type' => 4));
                }
            }
            $condition = array();
            $condition['class_id'] = $class_id;
            $condition['school_id'] = $school_id;
            $subjects = $this->resultcard->get_list('subjectbyclass', $condition, '', '', '', 'id', 'ASC');
            if ($action_type == 1) {
                for ($i = 1; $i <= 4; $i++) {
                    $data = $this->input->post('time_' . $i);
                    $this->resultcard->update('report_card_times', array('period_1' => $data[1], 'period_2' => $data[2], 'period_3' => $data[3], 'period_4' => $data[4], 'period_5' => $data[5], 'period_6' => $data[6]), array('report_card_id' => $report_card_id, 'type' => $i));
                }

                $periods = getPeriodTypes($period_num);
                foreach ($subjects as $subject) {
                    foreach ($periods as $key => $period) {
                        $condition = array(
                            'class_id' => $class_id,
                            'student_id' => $student_id,
                            'subject_id' => $subject->subject_id,
                            'type' => $key,
                            'academic_year_id' => $academic_year_id
                        );
                        $result = $this->resultcard->get_single('grade_reports', $condition);
                        $marks = $this->input->post('report_' . $subject->subject_id . '_' . $key);
                        $is_locked = 0;
                        if ($this->input->post('locked_' . $key) && $this->input->post('locked_' . $key) == 'on')
                            $is_locked = 1;
                            
                        if (!is_null($marks) && $marks > 0) {
                            if (empty($result)) {
                                $temp = array();
                                $temp['student_id'] = $student_id;
                                $temp['class_id'] = $class_id;
                                $temp['subject_id'] = $subject->subject_id;
                                $temp['type'] = $key;
                                $temp['academic_year_id'] = $academic_year_id;
                                $temp['is_locked'] = $is_locked;
                                $temp['created_at'] = date('Y-m-d H:i:s');
                                $temp['created_by'] = logged_in_user_id();
                                $temp['marks'] = $marks;
                                $this->resultcard->insert('grade_reports', $temp);
                            } else {
                                // $this->resultcard->update('grade_reports', array('marks' => $marks, 'is_locked' => $is_locked), $condition);
                                $this->resultcard->update('grade_reports', array('marks' => $marks, 'is_locked' => $is_locked), array('id' => $result->id));
                            }
                        }
                    }
                }
            }

            if ($action_type == 2) {

                // Write final result
                $final_result_id =  0;
                $condition = array(
                    'school_id' => $school_id,
                    'class_id' => $class_id,
                    'student_id' => $student_id,
                    'academic_year_id' => $academic_year_id
                );
                $final_result = $this->resultcard->get_single('final_results', $condition);
                if (empty($final_result)) {
                    $temp = array();
                    $temp['school_id'] = $school_id;
                    $temp['class_id'] = $class_id;
                    $temp['section_id'] = $section_id;
                    $temp['student_id'] = $student_id;
                    $temp['academic_year_id'] = $academic_year_id;
                    $temp['total_subject'] = 0;
                    $temp['fail_subject'] = 0;
                    $temp['total_mark'] = 0;
                    $temp['created_at'] = date('Y-m-d H:i:s');
                    $temp['created_by'] = logged_in_user_id();
                    $final_result_id = $this->resultcard->insert('final_results', $temp);
                } else {
                    $final_result_id =  $final_result->id;
                    $this->resultcard->update('final_results', array('total_subject' => 0, 'fail_subject' => 0, 'total_mark' => 0), array('id' => $final_result_id));
                }

                $periods = getPeriodTypes($period_num);
                foreach ($subjects as $subject) {
                    $marks = $this->input->post('report_' . $subject->subject_id . '_average');
                    if (is_null($marks)) {
                        $marks = 0;
                    }
                    $is_fail = 0;
                    if ($marks < 70) $is_fail = 1;
                    $final_result = $this->resultcard->get_single('final_results', array('id' => $final_result_id));
                    $this->resultcard->update('final_results', array('total_subject' => $final_result->total_subject + 1, 'fail_subject' => $final_result->fail_subject + $is_fail, 'total_mark' => $final_result->total_mark + $marks), array('id' => $final_result_id));
                }
            }
            $this->data['school'] = $school;
            $this->data['school_id'] = $school_id;
            $this->data['academic_year_id'] = $academic_year_id;
            $this->data['student'] = $student;
            $this->data['class_id'] = $class_id;
            $this->data['section_id'] = $section_id;
            $this->data['student_id'] = $student_id;
            $this->data['result'] = $this->resultcard->get_report_card($school_id, $period_num, $academic_year_id, $class_id, $section_id, $student_id);

            $this->data['time_list'] = $this->resultcard->get_list('report_card_times', array('report_card_id' => $report_card_id), '', '', '', 'id', 'ASC');
            if ($this->session->userdata('role_id') == ADMIN || $this->session->userdata('role_id') == SUPER_ADMIN) {
                if (!isset($report_card) || $report_card->status == 0)
                    $this->data['editable'] = true;
            }
        }


        $condition = array();
        $condition['status'] = 1;

        $condition['school_id'] = getSchoolId();
        $this->data['classes'] = $this->resultcard->get_list('classes', $condition, '', '', '', 'id', 'ASC');
        $this->data['academic_years'] = $this->resultcard->get_list('academic_years', $condition, '', '', '', 'id', 'ASC');

        $this->data['extracurricular_activities'] = $this->resultcard->get_list('extracurricular_activities', array('school_id' => $school_id), '', '', '', 'id', 'ASC');
        $this->data['marking_standard'] = $this->resultcard->get_list('marking_standard', array('school_id' => $school_id), '', '', '', 'id', 'ASC');

        $this->layout->title($this->lang->line('manage_result_card') . ' | ' . SMS);
        $this->layout->view('gradebook/reportcard/period' . $period_num, $this->data);
    }
}
