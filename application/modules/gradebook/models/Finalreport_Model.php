<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Finalreport_Model extends MY_Model {
    
    function __construct() {
        parent::__construct();
    }  

    public function get_result($school_id = NULL, $academic_year_id = NULL, $class_id = NULL, $section_id = NULL, $student_id = NULL)
    {
        //E.roll_no, ST.name, S.school_name, C.name AS class_name, SE.name AS section, SU.name AS subject, AY.session_year
        $this->db->select('A.*,SU.name AS subject_name');
        $this->db->from('final_report_details AS A');
        $this->db->join('final_reports AS GR', 'A.final_report_id = GR.id', 'left');

        // $this->db->join('enrollments AS E', 'E.student_id = GR.student_id', 'left');
        // $this->db->join('schools AS S', 'A.school_id = S.id', 'left');
        // $this->db->join('classes AS C', 'C.id = GR.class_id', 'left');
        // $this->db->join('sections AS SE', 'SE.id = GR.section_id', 'left');
        $this->db->join('subjects AS SU', 'SU.id = A.subject_id', 'left');
        // $this->db->join('academic_years AS AY', 'AY.id = GR.academic_year_id', 'left');
        // $this->db->join('students AS ST', 'ST.id = GR.student_id', 'left');

        if ($school_id > 0) {
            $this->db->where('GR.school_id', $school_id);
        }

        if ($academic_year_id > 0) {
            $this->db->where('GR.academic_year_id', $academic_year_id);
        }

        if ($class_id > 0) {
            $this->db->where('GR.class_id', $class_id);
        }

        if ($section_id > 0) {
            $this->db->where('GR.section_id', $section_id);
        }
        if ($student_id > 0) {
            $this->db->where('GR.student_id', $student_id);
        }

        $this->db->order_by('A.id', 'ASC');

        return $this->db->get()->result();
    }
}
