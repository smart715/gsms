<?php

defined('BASEPATH') or exit('No direct script access allowed');

/* * *****************Gallery.php**********************************
 * @product name    : Global Multi School Management System Express
 * @type            : Class
 * @class name      : Finalreport
 * @author          : smartmanage715@gmail.com
 * @copyright       : Codetroopers Team	 	
 * ********************************************************** */

class Finalreport extends MY_Controller
{

    public $data = array();

    function __construct()
    {
        parent::__construct();
        $this->load->model('Finalreport_Model', 'final_report', true);
    }


    /*****************Function index**********************************
     * @type            : Function
     * @function name   : index
     * @description     : Load "Gallery List" user interface                 
     *                      
     * @param           : null
     * @return          : null 
     * ********************************************************** */
    public function index($school_id = null)
    {
        $school_id = getSchoolId();
        $this->data['editable'] = 0;
        $school = $this->final_report->get_school_by_id($school_id);
        $period_num = $school->period_num;

        if ($_POST) {

            if ($this->session->userdata('role_id') == STUDENT) {

                $student = get_user_by_role($this->session->userdata('role_id'), $this->session->userdata('uid'));

                $school_id = $student->school_id;
                $class_id = $student->class_id;
                $section_id = $student->section_id;
                $student_id = $student->id;
            } else {

                // $school_id = $this->input->post('school_id');
                $class_id = $this->input->post('class_id');
                $section_id = $this->input->post('section_id');
                $student_id = $this->input->post('student_id');

                $std = $this->final_report->get_single('students', array('id' => $student_id));
                $student = get_user_by_role(STUDENT, $std->user_id);
            }
            $academic_year_id = $this->input->post('academic_year_id');
            if ($std)
                $student = get_user_by_role(STUDENT, $std->user_id);

            $action_type = $this->input->post('action_type');
            $final_report_id = $this->input->post('final_report_id');

            $final_report = $this->final_report->get_single('final_reports', array('id' => $final_report_id));
            if (!isset($report_card_id) || $report_card_id == 0) {
                $final_report = $this->final_report->get_single('final_reports', array('school_id' => $school_id, 'class_id' => $class_id, 'student_id' => $student_id, 'academic_year_id' => $school->academic_year_id));
                if ($final_report) $final_report_id = $final_report->id;
            }

            if (!isset($final_report)) {
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
                $final_report_id = $this->final_report->insert('final_reports', $data);
            }

            if ($action_type == '2' && $final_report_id != 0) {
                $this->final_report->update('final_reports', array('status' => '1'), array('id' => $final_report_id));
            } else if ($action_type == '3' && $final_report_id != 0) {
                $this->final_report->update('final_reports', array('status' => '0'), array('id' => $final_report_id));
            }
            $condition = array();
            $condition['class_id'] = $class_id;
            $condition['school_id'] = $school_id;
            $subjects = $this->final_report->get_list('subjectbyclass', $condition, '', '', '', 'id', 'ASC');

            if ($action_type == 1) {
              
                $final_report = $this->final_report->get_single('final_reports', array('id' => $final_report_id));
                $final_report_id = $final_report->id;
                if ($final_report_id != 0) {
                    foreach ($subjects as $subject) {
                        $final_report_detail = $this->final_report->get_single('final_report_details', array('final_report_id' => $final_report_id, 'subject_id' => $subject->id));
                        if (is_null($final_report_detail)) {
                            $this->final_report->insert('final_report_details',  array('final_report_id' => $final_report_id, 'subject_id' => $subject->id));
                        }
                    }
                }
            }
            if ($final_report->status == 0)
                $this->data['editable'] = 1;

            $std = $this->final_report->get_single('students', array('id' => $student_id));
            $this->data['subjects'] = $subjects;
            $this->data['school'] = $school;
            $this->data['school_id'] = $school_id;
            $this->data['academic_year_id'] = $academic_year_id;
            $this->data['student'] = $student;
            $this->data['class_id'] = $class_id;
            $this->data['section_id'] = $section_id;
            $this->data['student_id'] = $student_id;
            $this->data['final_report'] = $final_report;
            $this->data['report_list'] = $this->final_report->get_result($school_id, $academic_year_id, $class_id, $section_id, $student_id);
            // echo $final_report_id;exit;
            $class = $this->final_report->get_single('classes', array('id' => $class_id));
            create_log('Has been filter result card for class: ' . $class->name . ', ' . $this->data['student']->name);
        }


        $condition = array();
        $condition['status'] = 1;
        $condition['school_id'] = $school_id;
        $this->data['classes'] = $this->final_report->get_list('classes', $condition, '', '', '', 'id', 'ASC');
        $this->data['academic_years'] = $this->final_report->get_list('academic_years', $condition, '', '', '', 'id', 'ASC');

        $this->data['extracurricular_activities'] = $this->final_report->get_list('extracurricular_activities', array('school_id' => $school_id), '', '', '', 'id', 'ASC');
        $this->data['marking_standard'] = $this->final_report->get_list('marking_standard', array('school_id' => $school_id), '', '', '', 'id', 'ASC');

        $this->layout->title('Final Report | ' . SMS);
        $this->layout->view('gradebook/finalreport/index', $this->data);
    }
}
