<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Auth_Model extends MY_Model {
    
    function __construct() {
        parent::__construct();
    }
    
    public function get_single_student($user_id){
        
        $this->db->select('S.*, E.roll_no, E.class_id, E.section_id, U.role_id,  C.name AS class_name, SE.name AS section');
        $this->db->from('enrollments AS E');
        $this->db->join('students AS S', 'S.id = E.student_id', 'left');
        $this->db->join('users AS U', 'U.id = S.user_id', 'left');
        $this->db->join('classes AS C', 'C.id = E.class_id', 'left');
        $this->db->join('sections AS SE', 'SE.id = E.section_id', 'left');
        $this->db->where('S.user_id', $user_id);
        return $this->db->get()->row();
   }
}
