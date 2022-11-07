<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Reportcard_Model extends MY_Model
{

    function __construct()
    {
        parent::__construct();
    }

    public function get_report_card($school_id,$period_num, $academic_year_id, $class_id, $section_id, $student_id)
    {
        $periods = getPeriodTypes($period_num);
        $select = '';
        foreach($periods AS $key=>$period){
            $select .= ', GR_'.$key.'.marks AS '.$key.', GR_'.$key.'.is_locked AS '.$key.'_locked';
        }
        $this->db->select('S.id, S.name AS subject_name '.$select);
        $this->db->from('subjectbyclass AS SC');
        $this->db->join('subjects AS S', 'S.id = SC.subject_id', 'left');

        foreach($periods AS $key=>$period){
            $this->db->join('grade_reports AS GR_'.$key, 
            'GR_'.$key.'.subject_id = S.id 
            AND GR_'.$key.'.student_id = '.$student_id.' 
            AND  GR_'.$key.'.academic_year_id = '.$academic_year_id.' 
            AND  GR_'.$key.'.type = "'.$key.'" ', 
            'left');
        }
        // $this->db->where('SC.school_id', $school_id);
        $this->db->where('SC.class_id', $class_id);
        // $this->db->where('E.academic_year_id', $academic_year_id);

        $this->db->order_by('S.id', 'ASC');

        return $this->db->get()->result();
    }

    public function get_student_list($school_id = null, $class_id = null, $section_id = null, $academic_year_id = null)
    {

        $this->db->select('S.*, D.amount, D.title AS discount, G.name AS guardian, E.roll_no, E.section_id, E.class_id, U.username, U.role_id,  C.name AS class_name, SE.name AS section');
        $this->db->from('enrollments AS E');
        $this->db->join('students AS S', 'S.id = E.student_id', 'left');
        $this->db->join('guardians AS G', 'G.id = S.guardian_id', 'left');
        $this->db->join('users AS U', 'U.id = S.user_id', 'left');
        $this->db->join('classes AS C', 'C.id = E.class_id', 'left');
        $this->db->join('sections AS SE', 'SE.id = E.section_id', 'left');
        $this->db->join('discounts AS D', 'D.id = S.discount_id', 'left');
        $this->db->where('S.school_id', $school_id);
        $this->db->where('E.class_id', $class_id);
        $this->db->where('E.academic_year_id', $academic_year_id);

        if ($section_id) {
            $this->db->where('E.section_id', $section_id);
        }

        if ($this->session->userdata('role_id') == GUARDIAN) {
            $this->db->where('S.guardian_id', $this->session->userdata('profile_id'));
        }

        if ($this->session->userdata('role_id') == STUDENT) {
            $this->db->where('S.id', $this->session->userdata('profile_id'));
        }

        $this->db->where('S.status_type', 'regular');
        $this->db->order_by('E.roll_no', 'ASC');

        return $this->db->get()->result();
    }
}
