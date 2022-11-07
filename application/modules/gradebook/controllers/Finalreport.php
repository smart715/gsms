<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/* * *****************Gallery.php**********************************
 * @product name    : Global Multi School Management System Express
 * @type            : Class
 * @class name      : Finalreport
 * @author          : smartmanage715@gmail.com
 * @copyright       : Codetroopers Team	 	
 * ********************************************************** */

class Finalreport extends MY_Controller {

    public $data = array();

    function __construct() {
        parent::__construct();
        $this->load->model('Finalreport_Model', 'finalreport', true);       
    }


    /*****************Function index**********************************
    * @type            : Function
    * @function name   : index
    * @description     : Load "Gallery List" user interface                 
    *                      
    * @param           : null
    * @return          : null 
    * ********************************************************** */
    public function index($school_id = null) {


        check_permission(VIEW);
        $school_id = getSchoolId();

        $school = $this->finalreport->get_school_by_id($school_id);
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

                $std = $this->finalreport->get_single('students', array('id' => $student_id));
                $student = get_user_by_role(STUDENT, $std->user_id);
            }

            $academic_year_id = $this->input->post('academic_year_id');
            $action_type = $this->input->post('action_type');
            $report_card_id = $this->input->post('report_card_id');
            $report_card = $this->finalreport->get_single('report_cards', array('id' => $report_card_id));
            if(!isset($report_card_id) || $report_card_id == 0){
                $report_card = $this->finalreport->get_single('report_cards', array('school_id' => $school_id,'class_id' => $class_id,'student_id' => $student_id,'academic_year_id' => $school->academic_year_id));
                if($report_card) $report_card_id = $report_card->id;
            }
            
            if(!isset($report_card) ){
                $data = array();
                $data['school_id'] = $school_id;
                $data['class_id'] = $class_id;
                $data['section_id'] = $section_id;
                $data['student_id'] = $student_id;
                $data['academic_year_id'] = $academic_year_id;
                $data['status'] = '0';
                $data['created_at'] = date('Y-m-d H:i:s');
                $data['modified_at'] = date('Y-m-d H:i:s');
                $data['created_by'] = logged_in_user_id();
                $data['modified_by'] = logged_in_user_id();
                $report_card_id = $this->finalreport->insert('report_cards', $data);
            }
            if ($action_type == '2' && $report_card_id != 0) {                
                $this->finalreport->update('report_cards', array('status' => '1'), array('id' => $report_card_id));
            }else if($action_type == '3' && $report_card_id != 0){
                $this->finalreport->update('report_cards', array('status' => '0'), array('id' => $report_card_id));
            }
            
            $report_card = $this->finalreport->get_single('report_cards', array('id' => $report_card_id));
            $this->data['report_card'] = $report_card;

            if ($report_card_id != 0) {
                $time_list = $this->finalreport->get_list('report_card_times', array('report_card_id' => $report_card_id), '', '', '', 'id', 'ASC');
                if (is_null($time_list) || count($time_list) == 0) {
                    $this->finalreport->insert('report_card_times',  array('report_card_id' => $report_card_id, 'type' => 1));
                    $this->finalreport->insert('report_card_times',  array('report_card_id' => $report_card_id, 'type' => 2));
                    $this->finalreport->insert('report_card_times',  array('report_card_id' => $report_card_id, 'type' => 3));
                    $this->finalreport->insert('report_card_times',  array('report_card_id' => $report_card_id, 'type' => 4));
                }
            }
            if ($action_type == 1) {
              
                for ($i = 1; $i <= 4; $i++) {
                    $data = $this->input->post('time_' . $i);
                    $this->finalreport->update('report_card_times', array('period_1' => $data[1], 'period_2' => $data[2], 'period_3' => $data[3], 'period_4' => $data[4], 'period_5' => $data[5], 'period_6' => $data[6]), array('report_card_id' => $report_card_id, 'type' => $i));
                }
                $condition = array();
                $condition['class_id'] = $class_id;
                $condition['school_id'] = $school_id;
                $subjects = $this->finalreport->get_list('subjectbyclass', $condition, '', '', '', 'id', 'ASC');

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
                        $result = $this->finalreport->get_single('grade_reports', $condition);
                        $marks = $this->input->post('report_' . $subject->subject_id . '_' . $key);
                        $is_locked = 0;
                        if ($this->input->post('locked_' . $key) && $this->input->post('locked_' . $key) == 'on')
                            $is_locked = 1;
                        if (!is_null($marks)) {
                            if (empty($result)) {
                                $data['student_id'] = $student_id;
                                $data['class_id'] = $class_id;
                                $data['subject_id'] = $subject->subject_id;
                                $data['type'] = $key;
                                $data['academic_year_id'] = $academic_year_id;
                                $data['is_locked'] = $is_locked;
                                $data['created_at'] = date('Y-m-d H:i:s');
                                $data['created_by'] = logged_in_user_id();
                                $data['marks'] = $marks;
                                $this->finalreport->insert('grade_reports', $data);
                            } else {
                                // $this->finalreport->update('grade_reports', array('marks' => $marks, 'is_locked' => $is_locked), $condition);
                                $this->finalreport->update('grade_reports', array('marks' => $marks, 'is_locked' => $is_locked), array('id' => $result->id));
                            }
                        }
                    }
                }
            }

            $this->data['school'] = $school;
            $this->data['school_id'] = $school_id;
            $this->data['academic_year_id'] = $academic_year_id;
            $this->data['student'] = $student;
            $this->data['class_id'] = $class_id;
            $this->data['section_id'] = $section_id;
            $this->data['student_id'] = $student_id;
            $this->data['result'] = $this->finalreport->get_report_card($school_id, $period_num, $academic_year_id, $class_id, $section_id, $student_id);
            
            $this->data['time_list'] = $this->finalreport->get_list('report_card_times', array('report_card_id' => $report_card_id), '', '', '', 'id', 'ASC');
            if ($this->session->userdata('role_id') == ADMIN || $this->session->userdata('role_id') == SUPER_ADMIN) {
                if(!isset($report_card) || $report_card->status == 0)
                $this->data['editable'] = true;
            }

        }


        $condition = array();
        $condition['status'] = 1;

        $condition['school_id'] = getSchoolId();
        $this->data['classes'] = $this->finalreport->get_list('classes', $condition, '', '', '', 'id', 'ASC');
        $this->data['academic_years'] = $this->finalreport->get_list('academic_years', $condition, '', '', '', 'id', 'ASC');

        $this->data['marking_standard'] = $this->finalreport->get_list('marking_standard', array('school_id' => $school_id), '', '', '', 'id', 'ASC');

        $this->layout->title('Final Report | ' . SMS);
        $this->layout->view('gradebook/finalreport/index', $this->data);
    }


}