<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Promotion_Model extends MY_Model
{

    function __construct()
    {
        parent::__construct();
    }


    public function get_result_list($school_id = null, $class_id = null, $academic_year_id = null)
    {
        
        $this->db->select('S.*,sections.name AS section_name, F.total_subject, F.fail_subject, F.total_mark');
        $this->db->from('enrollments AS E');
        $this->db->join('sections', 'sections.id = E.section_id', 'left');
        $this->db->join('classes AS C', 'C.id = E.class_id', 'left');
        $this->db->join('students AS S', 'S.id = E.student_id', 'left');
        $this->db->join('academic_years AS AY', 'AY.id = E.academic_year_id', 'left');
        $this->db->join('final_results AS F', 'F.student_id= E.student_id AND F.academic_year_id = E.academic_year_id', 'left');
        $this->db->where('E.academic_year_id', $academic_year_id);
        $this->db->where('E.class_id', $class_id);
        $this->db->where('S.school_id', $school_id);
        $this->db->where('S.status_type', 'regular');

        $this->db->order_by('E.roll_no', 'ASC');

        return $this->db->get()->result();
    }
}
