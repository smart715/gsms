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
            if ($this->input->post('action_type') == 1) {
                $condition = array();
                $condition['class_id'] = $class_id;
                $condition['school_id'] = $school_id;
                $subjects = $this->resultcard->get_list('subjectbyclass', $condition, '', '', '', 'id', 'ASC');

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
                                $this->resultcard->insert('grade_reports', $data);
                            } else {
                                // $this->resultcard->update('grade_reports', array('marks' => $marks, 'is_locked' => $is_locked), $condition);
                                $this->resultcard->update('grade_reports', array('marks' => $marks, 'is_locked' => $is_locked), array('id' => $result->id));
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
            $this->data['result'] = $this->resultcard->get_report_card($school_id, $period_num, $academic_year_id, $class_id, $section_id, $student_id);
        }

        if ($this->session->userdata('role_id') == ADMIN || $this->session->userdata('role_id') == SUPER_ADMIN) {
            $this->data['editable'] = true;
        }

        $condition = array();
        $condition['status'] = 1;

        $condition['school_id'] = getSchoolId();
        $this->data['classes'] = $this->resultcard->get_list('classes', $condition, '', '', '', 'id', 'ASC');
        $this->data['academic_years'] = $this->resultcard->get_list('academic_years', $condition, '', '', '', 'id', 'ASC');

        $this->data['extracurricular_activities'] = $this->resultcard->get_list('extracurricular_activities', array('school_id' => $school_id), '', '', '', 'id', 'ASC');
        $this->data['marking_standard'] = $this->resultcard->get_list('marking_standard', array('school_id' => $school_id), '', '', '', 'id', 'ASC');

        $this->layout->title($this->lang->line('manage_result_card') . ' | ' . SMS);
        $this->layout->view('gradebook/reportcard/period'.$period_num, $this->data);
    }
}
