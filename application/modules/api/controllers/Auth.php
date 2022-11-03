<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Auth extends CI_Controller {

    public function __construct() {

        parent::__construct();

        $this->load->model('Auth_Model', 'auth', true);
    }

    public function login() {

        $api_data = array();

        if ($_POST) {

            $data['username'] = $_POST['username'];
            $data['password'] = md5($_POST['password']);
            
            $login = $this->auth->get_single('users', $data);

            if (!empty($login)) {

                if ($login->role_id != SUPER_ADMIN) {

                    $school = $this->auth->get_single('schools', array('status' => 1, 'id' => $login->school_id));
                    $privileges = $this->auth->get_list('privileges', array('role_id' => $login->role_id));
                    
                    if (empty($school)) {
                        $api_data['error'] = 'Invalid Username OR Password';
                    } else  if (!$login->status) {
                        $api_data['error'] = 'User is inactive';                       
                    }else if(empty ($privileges)){
                        $api_data['error'] = 'privilege not setting';
                    }else{
                        
                        if ($login->role_id == STUDENT) {                    
                            $profile = $this->auth->get_single_student($login->id); 
                        } elseif ($login->role_id == GUARDIAN) {
                            $profile = $this->auth->get_single('guardians', array('user_id' => $login->id));
                        }
                        
                        $api_data['login'] = $login;
                        $api_data['profile'] = $profile;
                    }
                }else{
                    $api_data['error'] = 'Invalid Username OR Password'; 
                }
            } else {
                $api_data['error'] = 'Invalid Username OR Password';
            }
        }else{
             $api_data['error'] = 'Invalid parameter';
        }
        
        echo json_encode($api_data);
        die();        
    }
}