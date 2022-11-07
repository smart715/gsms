<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Gradereport_Model extends MY_Model
{

    function __construct()
    {
        parent::__construct();
    }

    public function get_student_list($school_id = null, $class_id = null, $subject_id = null, $academic_year_id = null, $type = null)
    {

        $this->db->select('S.*, E.roll_no, E.class_id, E.section_id, C.name AS class_name, G.marks, G.is_locked');
        $this->db->from('enrollments AS E');
        $this->db->join('classes AS C', 'C.id = E.class_id', 'left');
        $this->db->join('students AS S', 'S.id = E.student_id', 'left');
        $this->db->join('grade_reports AS G', 'G.class_id = "' . $class_id . '" AND G.student_id = S.id AND  G.subject_id="' . $subject_id . '" AND G.type = "' . $type . '" AND G.academic_year_id = "' . $academic_year_id . '" ', 'left');
        $this->db->where('E.academic_year_id', $academic_year_id);
        $this->db->where('E.school_id', $school_id);
        $this->db->where('E.class_id', $class_id);

        // if($section_id){
        //     $this->db->where('E.section_id', $section_id);
        // }      
        $this->db->where('S.status_type', 'regular');
        $this->db->order_by('E.roll_no', 'ASC');

        return $this->db->get()->result();
    }
}
