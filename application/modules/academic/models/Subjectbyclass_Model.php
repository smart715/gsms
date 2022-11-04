<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Subjectbyclass_Model extends MY_Model {
    
    function __construct() {
        parent::__construct();
    }
    
     public function get_subject_list($class_id = null , $school_id = null){
       
        if(!$class_id){
           $class_id = $this->session->userdata('class_id');
        }
        
        $this->db->select('A.*, S.name AS subject_name, SC.school_name, "" AS section_name, CONCAT(C.name," ",C.numeric_name) AS class_name, T.name AS teacher');
        $this->db->from('subjectbyclass AS A');
        $this->db->join('subjects AS S', 'S.id = A.subject_id AND S.school_id = A.school_id', 'left');
        $this->db->join('sections AS SE', 'SE.id = A.section_id AND SE.class_id = A.class_id', 'left');
        $this->db->join('teachers AS T', 'T.id = A.teacher_id', 'left');
        $this->db->join('classes AS C', 'C.id = A.class_id', 'left');
        $this->db->join('schools AS SC', 'SC.id = A.school_id', 'left');
        if($this->session->userdata('role_id') == TEACHER){
            $this->db->where('A.teacher_id', $this->session->userdata('profile_id'));
        }
        
        if($class_id > 0){
            $this->db->where('A.class_id', $class_id);
        }
        
        if($this->session->userdata('role_id') != SUPER_ADMIN){
            $this->db->where('A.school_id', $this->session->userdata('school_id'));
        }
        if($school_id && $this->session->userdata('role_id') == SUPER_ADMIN){
            $this->db->where('A.school_id', $school_id); 
        }        
        $this->db->where('SC.status', 1);
        $this->db->order_by('A.id', 'DESC');
        
        return $this->db->get()->result();
        
    }
    
    public function get_single_subject($id){
        
        $this->db->select('A.*, SC.school_name, C.name AS class_name, T.name AS teacher');
        $this->db->from('subjectbyclass AS A');
        $this->db->join('teachers AS T', 'T.id = A.teacher_id', 'left');
        $this->db->join('classes AS C', 'C.id = A.class_id', 'left');
        $this->db->join('schools AS SC', 'SC.id = A.school_id', 'left');
        $this->db->where('A.id', $id);
        return $this->db->get()->row();
        
    }
    
    function duplicate_check($school_id, $class_id, $name, $id = null ){   
        
        if($id){
            $this->db->where_not_in('id', $id);
        }
        $this->db->where('class_id', $class_id);
        $this->db->where('name', $name);
        $this->db->where('school_id', $school_id);
        return $this->db->get('subjects')->num_rows();        
    }   

}
