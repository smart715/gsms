<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Subject_Model extends MY_Model {
    
    function __construct() {
        parent::__construct();
    }
    
     public function get_subject_list($school_id = null){
        
        $this->db->select('S.*, SC.school_name');
        $this->db->from('subjects AS S');
        $this->db->join('schools AS SC', 'SC.id = S.school_id', 'left');
        
        
        if($this->session->userdata('role_id') != SUPER_ADMIN){
            $this->db->where('S.school_id', $this->session->userdata('school_id'));
        }
        if($school_id && $this->session->userdata('role_id') == SUPER_ADMIN){
            $this->db->where('S.school_id', $school_id); 
        }        
        $this->db->where('SC.status', 1);
        $this->db->order_by('S.id', 'DESC');
        
        return $this->db->get()->result();
        
    }
    
    public function get_single_subject($id){
        
        $this->db->select('S.*, SC.school_name, C.name AS class_name, T.name AS teacher');
        $this->db->from('subjects AS S');
        $this->db->join('teachers AS T', 'T.id = S.teacher_id', 'left');
        $this->db->join('schools AS SC', 'SC.id = S.school_id', 'left');
        $this->db->where('S.id', $id);
        return $this->db->get()->row();
        
    }
    
    function duplicate_check($school_id, $name, $id = null ){   
        
        if($id){
            $this->db->where_not_in('id', $id);
        }
        $this->db->where('name', $name);
        $this->db->where('school_id', $school_id);
        return $this->db->get('subjects')->num_rows();        
    }   

}
