<?php

defined('BASEPATH') or exit('No direct script access allowed');

/* * *****************Gallery.php**********************************
 * @product name    : Global Multi School Management System Express
 * @type            : Class
 * @class name      : Gradereport
 * @author          : smartmanage715@gmail.com
 * @copyright       : Codetroopers Team	 	
 * ********************************************************** */

class Gradereport extends MY_Controller
{

    public $data = array();

    function __construct()
    {
        parent::__construct();
        $this->load->model('Gradereport_Model', 'result', true);
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

        check_permission(VIEW);
        $school_id = getSchoolId();
        $type = 'period1';

        if ($_POST) {
            $class_id = $this->input->post('class_id');
            $subject_id = $this->input->post('subject_id');
            $type = $this->input->post('type');

            $school = $this->result->get_school_by_id($school_id);
            if (!$school->academic_year_id) {
                error($this->lang->line('set_academic_year_for_school'));
                redirect('gradebook/gradereport/index');
            }

            $this->data['students'] = $this->result->get_student_list($school_id, $class_id, $subject_id, $school->academic_year_id, $type);

            $condition = array(
                'class_id' => $class_id,
                'subject_id' => $subject_id,
                'type' => $type,
                'academic_year_id' => $school->academic_year_id
            );

            if (!empty($this->data['students'])) {

                foreach ($this->data['students'] as $obj) {
                    $condition['student_id'] = $obj->id;
                    $result = $this->result->get_single('grade_reports', $condition);
                    $marks = $this->input->post('marks_' . $obj->id);
                    if (!is_null($marks)) {
                        if (empty($result)) {
                            $data['student_id'] = $obj->id;
                            $data['class_id'] = $class_id;
                            $data['subject_id'] = $subject_id;
                            $data['type'] = $type;
                            $data['academic_year_id'] = $school->academic_year_id;
                            $data['is_locked'] = 0;
                            $data['created_at'] = date('Y-m-d H:i:s');
                            $data['created_by'] = logged_in_user_id();
                            $data['marks'] = $marks;
                            $this->result->insert('grade_reports', $data);
                        } else {
                            $condition['is_locked'] = 0;
                            $this->result->update('grade_reports', array('marks' => $marks), array('id' => $result->id, 'is_locked' => 0));
                        }
                    }
                }
                $this->data['students'] = $this->result->get_student_list($school_id, $class_id, $subject_id, $school->academic_year_id, $type);
            }
            // exit;

            // $this->data['grades'] = $this->result->get_list('grades', array('status' => 1, 'school_id' => $school_id), '', '', '', 'id', 'ASC');
            // $this->data['exam'] =  $this->result->get_single('exams', array('id' => $exam_id, 'school_id' => $school_id));

            $this->data['class_id'] = $class_id;
            $this->data['subject_id'] = $subject_id;
            $this->data['academic_year_id'] = $school->academic_year_id;

            $class = $this->result->get_single('classes', array('id' => $class_id, 'school_id' => $school_id));
            create_log('Has been process exam result for class: ' . $class->name);
        }

        $condition = array();
        $condition['status'] = 1;

        $school = $this->result->get_school_by_id($school_id);

        $condition['school_id'] = $school_id;
        $this->data['classes'] = $this->result->get_list('classes', $condition, '', '', '', 'id', 'ASC');

        $this->data['type'] = $type;
        $this->data['types'] = getPeriodTypes($type);

        $this->layout->title('Grade Report | ' . SMS);
        $this->layout->view('gradebook/gradereport/index', $this->data);
    }
}
