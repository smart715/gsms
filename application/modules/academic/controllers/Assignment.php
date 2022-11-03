<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/* * *****************Assignment.php**********************************
 * @product name    : Global Multi School Management System Express
 * @type            : Class
 * @class name      : Assignment
 * @description     : Manage student assignment by class teacher.  
 * @author          : Codetroopers Team 	
 * @url             : https://themeforest.net/user/codetroopers      
 * @support         : yousuf361@gmail.com	
 * @copyright       : Codetroopers Team	 	
 * ********************************************************** */

class Assignment extends MY_Controller {

    public $data = array();

    function __construct() {
        parent::__construct();
        $this->load->model('Assignment_Model', 'assignment', true); 
        
        $this->load->library('twilio');
        $this->load->library('clickatell');
        $this->load->library('bulk');
        $this->load->library('msg91');
        $this->load->library('plivo');
        $this->load->library('smscountry');
        $this->load->library('textlocalsms');
        $this->load->library('betasms');
        $this->load->library('bulkpk');
        $this->load->library('smscluster');
        $this->load->library('alphanet');
        $this->load->library('bdbulk');
        $this->load->library('mimsms');
        $this->load->library('bulk360');
        $this->load->library('smsto');
        
    }

    
    /*****************Function index**********************************
    * @type            : Function
    * @function name   : index
    * @description     : Load "Assignment List" user interface                 
    *                    with class wise listing    
    * @param           : $class_id integer value
    * @return          : null 
    * ********************************************************** */
    public function index($class_id = null) {

        check_permission(VIEW);
        
        if(isset($class_id) && !is_numeric($class_id)){
            error($this->lang->line('unexpected_error'));
             redirect('dashboard/index');
        }
        
        // for super admin 
        $school_id = '';
        if($_POST){
            
            $school_id = $this->input->post('school_id');
            $class_id  = $this->input->post('class_id');           
        }
        
        if ($this->session->userdata('role_id') == STUDENT) {
            $class_id = $this->session->userdata('class_id');    
        }
                
        $school = $this->assignment->get_school_by_id($school_id);         
        $this->data['assignments'] = $this->assignment->get_assignment_list($class_id, $school_id, @$school->academic_year_id);
        
        $condition = array();
        $condition['status'] = 1;        
        if($this->session->userdata('role_id') != SUPER_ADMIN){            
            $condition['school_id'] = $this->session->userdata('school_id');
            $this->data['classes'] = $this->assignment->get_list('classes', $condition, '','', '', 'id', 'ASC');
        }
        
        $this->data['class_list'] = $this->assignment->get_list('classes', $condition, '','', '', 'id', 'ASC');
        
        $this->data['class_id'] = $class_id;
        $this->data['filter_class_id'] = $class_id;
        $this->data['filter_school_id'] = $school_id;
        $this->data['schools'] = $this->schools;
        
        $this->data['list'] = TRUE;
        $this->layout->title($this->lang->line('manage_assignment') . ' | ' . SMS);
        $this->layout->view('assignment/index', $this->data);
    }

    
    /*****************Function add**********************************
    * @type            : Function
    * @function name   : add
    * @description     : Load "Add new Asignment" user interface                 
    *                    and process to store "Assignment" into database 
    * @param           : null
    * @return          : null 
    * ********************************************************** */
    public function add() {

        check_permission(ADD);

        if ($_POST) {
            $this->_prepare_assignment_validation();
            if ($this->form_validation->run() === TRUE) {
                $data = $this->_get_posted_assignment_data();

                $insert_id = $this->assignment->insert('assignments', $data);
                if ($insert_id) {
                    
                    $data['assignment_id'] = $insert_id;
                    if($data['sms_notification']){
                        $this->_send_sms_notification($data);
                    }
                    if($data['email_notification']){
                        $this->_send_email_notification($data);
                    }
                    
                    create_log('Has been created an assignment : '.$data['title']); 
                    
                    success($this->lang->line('insert_success'));
                    redirect('academic/assignment/index/'.$data['class_id']);
                } else {
                    error($this->lang->line('insert_failed'));
                    redirect('academic/assignment/add/'.$data['class_id']);
                }
            } else {
                error($this->lang->line('insert_failed'));
                $this->data['post'] = $_POST;
            }
        }
        
        $class_id = $this->uri->segment(4);
        if(!$class_id){
          $class_id = $this->input->post('class_id');
        }
        
        if ($this->session->userdata('role_id') == STUDENT) {
            
            $school = $this->assignment->get_school_by_id($this->session->userdata('school_id'));
            $student_id = $this->session->userdata('profile_id');        
            $enroll_student = $this->assignment->get_single('enrollments', array('student_id' => $student_id, 'academic_year_id' => $school->academic_year_id));
            $class_id = $enroll_student->class_id;            
        }

        $this->data['assignments'] = $this->assignment->get_assignment_list($class_id);
        
        $condition = array();
        $condition['status'] = 1;        
        if($this->session->userdata('role_id') != SUPER_ADMIN){            
            $condition['school_id'] = $this->session->userdata('school_id');
            $this->data['classes'] = $this->assignment->get_list('classes', $condition, '','', '', 'id', 'ASC');
        }
        $this->data['class_list'] = $this->assignment->get_list('classes', $condition, '','', '', 'id', 'ASC');
                
        $this->data['class_id'] = $class_id;       
        $this->data['schools'] = $this->schools;
        
        $this->data['add'] = TRUE;
        $this->layout->title($this->lang->line('add') . ' | ' . SMS);
        $this->layout->view('assignment/index', $this->data);
    }

    
    /*****************Function edit**********************************
    * @type            : Function
    * @function name   : edit
    * @description     : Load Update "Assignment" user interface                 
    *                    with populated "Assignment" value 
    *                    and process to update "Assignment" into database    
    * @param           : $id integer value
    * @return          : null 
    * ********************************************************** */
    public function edit($id = null) {

        check_permission(EDIT);

        if(!is_numeric($id)){
             error($this->lang->line('unexpected_error'));
             redirect('academic/assignment/index');
        }
        
        if ($_POST) {
            $this->_prepare_assignment_validation();
            if ($this->form_validation->run() === TRUE) {
                $data = $this->_get_posted_assignment_data();
                $updated = $this->assignment->update('assignments', $data, array('id' => $this->input->post('id')));

                if ($updated) {
                    
                    $data['assignment_id'] = $this->input->post('id');
                    if($data['sms_notification']){
                        $this->_send_sms_notification($data);
                    }
                    if($data['email_notification']){
                        $this->_send_email_notification($data);
                    }
                    
                    create_log('Has been updated an assignment : '.$data['title']);                    
                    success($this->lang->line('update_success'));
                    redirect('academic/assignment/index/'.$data['class_id']);
                } else {
                    error($this->lang->line('update_failed'));
                    redirect('academic/assignment/edit/' . $this->input->post('id'));
                }
            } else {
                error($this->lang->line('update_failed'));
                $this->data['assignment'] = $this->assignment->get_single('assignments', array('id' => $this->input->post('id')));
            }
        }

        if ($id) {
            $this->data['assignment'] = $this->assignment->get_single('assignments', array('id' => $id));

            if (!$this->data['assignment']) {
                redirect('academic/assignment/index');
            }
        }

        $class_id = $this->data['assignment']->class_id;
        if(!$class_id){
          $class_id = $this->input->post('class_id');
        } 

        if ($this->session->userdata('role_id') == STUDENT) {
            $student_id = $this->session->userdata('profile_id');        
            $enroll_student = $this->assignment->get_single('enrollments', array('student_id' => $student_id, 'academic_year_id' => $this->data['assignment']->academic_year_id));
            $class_id = $enroll_student->class_id;
        }
        
        $this->data['assignments'] = $this->assignment->get_assignment_list($class_id, $this->data['assignment']->school_id, $this->data['assignment']->academic_year_id);
        
        $condition = array();
        $condition['status'] = 1;        
        $condition['school_id'] = $this->data['assignment']->school_id;        
        if($this->session->userdata('role_id') != SUPER_ADMIN){            
            $condition['school_id'] = $this->session->userdata('school_id');
            $this->data['classes'] = $this->assignment->get_list('classes', $condition, '','', '', 'id', 'ASC');
        }
        $this->data['class_list'] = $this->assignment->get_list('classes', $condition, '','', '', 'id', 'ASC');
        
        $this->data['school_id'] = $this->data['assignment']->school_id;        
        $this->data['filter_school_id'] = $this->data['assignment']->school_id;        
        $this->data['class_id'] = $class_id;
        $this->data['filter_class_id'] = $class_id;
        $this->data['schools'] = $this->schools; 
        
        $this->data['edit'] = TRUE;
        $this->layout->title($this->lang->line('edit') . ' | ' . SMS);
        $this->layout->view('assignment/index', $this->data);
    }

    
    /*****************Function view omit **********************************
    * @type            : Function
    * @function name   : view
    * @description     : Load user interface with specific assignment data                 
    *                       
    * @param           : $assignment_id integer value
    * @return          : null 
    * ********************************************************** */
    public function view($assignment_id = null) {

        check_permission(VIEW);

        if(!is_numeric($assignment_id)){
             error($this->lang->line('unexpected_error'));
             redirect('academic/assignment/index');
        }
        
        $this->data['assignment'] = $this->assignment->get_single_assignment($assignment_id);
        $class_id = $this->data['assignment']->class_id;
        
        if ($this->session->userdata('role_id') == STUDENT) {
            
            $school = $this->assignment->get_school_by_id($this->session->userdata('school_id'));
            $student_id = $this->session->userdata('profile_id');        
            $enroll_student = $this->assignment->get_single('enrollments', array('student_id' => $student_id, 'academic_year_id' => $school->academic_year_id));
            $class_id = $enroll_student->class_id;
        }
        
        $this->data['assignments'] = $this->assignment->get_assignment_list($class_id);
        
        $condition = array();
        $condition['status'] = 1;        
        if($this->session->userdata('role_id') != SUPER_ADMIN){            
            $condition['school_id'] = $this->session->userdata('school_id');
            $this->data['classes'] = $this->assignment->get_list('classes', $condition, '','', '', 'id', 'ASC');
        }
        $this->data['class_list'] = $this->assignment->get_list('classes', $condition, '','', '', 'id', 'ASC');
        
        
        $this->data['class_id'] = $class_id;
        $this->data['schools'] = $this->schools;
        $this->data['detail'] = TRUE;
        $this->layout->title($this->lang->line('view') . ' ' . $this->lang->line('assignment') . ' | ' . SMS);
        $this->layout->view('assignment/index', $this->data);
    }

    
           
     /*****************Function get_single_assignment**********************************
     * @type            : Function
     * @function name   : get_single_assignment
     * @description     : "Load single assignment information" from database                  
     *                    to the user interface   
     * @param           : null
     * @return          : null 
     * ********************************************************** */
    public function get_single_assignment(){
        
       $assignment_id = $this->input->post('assignment_id');
       
       $this->data['assignment'] = $this->assignment->get_single_assignment($assignment_id);
       echo $this->load->view('assignment/get-single-assignment', $this->data);
    }

    
    /*****************Function _prepare_assignment_validation**********************************
    * @type            : Function
    * @function name   : _prepare_assignment_validation
    * @description     : Process "assignment" user input data validation                 
    *                       
    * @param           : null
    * @return          : null 
    * ********************************************************** */
    private function _prepare_assignment_validation() {
        $this->load->library('form_validation');
        $this->form_validation->set_error_delimiters('<div class="error-message" style="color: red;">', '</div>');

        $this->form_validation->set_rules('title', $this->lang->line('title'), 'trim|required');
        $this->form_validation->set_rules('school_id', $this->lang->line('school_name'), 'trim|required');
        $this->form_validation->set_rules('class_id', $this->lang->line('class'), 'trim|required');
        $this->form_validation->set_rules('subject_id', $this->lang->line('subject'), 'trim|required');
        $this->form_validation->set_rules('assigment_date', $this->lang->line('assigment_date'), 'trim|required');
        $this->form_validation->set_rules('submission_date', $this->lang->line('submission_date'), 'trim|required');
        $this->form_validation->set_rules('note', $this->lang->line('note'), 'trim');
        $this->form_validation->set_rules('assignment', $this->lang->line('assignment'), 'trim|callback_assignment');
    }

    
    
    /*****************Function assignment**********************************
    * @type            : Function
    * @function name   : assignment
    * @description     : Process/check assignment document validation                  
    *                       
    * @param           : null
    * @return          : boolean true/false 
    * ********************************************************** */ 
    public function assignment() {

        if ($_FILES['assignment']['name']) {                

            $name = $_FILES['assignment']['name'];
            $ext = pathinfo($name, PATHINFO_EXTENSION);
            if ($ext == 'pdf' || $ext == 'doc' || $ext == 'docx' || $ext == 'txt' || $ext == 'ppt' || $ext == 'pptx') {
                return TRUE;
            } else {
                $this->form_validation->set_message('assignment', $this->lang->line('select_valid_file_format'));
                return FALSE;
            }
        }        
    }

    
    /*****************Function _get_posted_assignment_data**********************************
    * @type            : Function
    * @function name   : _get_posted_assignment_data
    * @description     : Prepare "Assignment" user input data to save into database                  
    *                       
    * @param           : null
    * @return          : $data array(); value 
    * ********************************************************** */
    private function _get_posted_assignment_data() {

        $items = array();
        $items[] = 'school_id';
        $items[] = 'class_id';
        $items[] = 'section_id';
        $items[] = 'subject_id';
        $items[] = 'title';
        $items[] = 'sms_notification';
        $items[] = 'email_notification';
        $items[] = 'note';

        $data = elements($items, $_POST);

        $data['assigment_date']  = date('Y-m-d', strtotime($this->input->post('assigment_date')));
        $data['submission_date'] = date('Y-m-d', strtotime($this->input->post('submission_date')));

        $data['sms_notification'] = $data['sms_notification'] ? $data['sms_notification'] : 0;
        $data['email_notification'] = $data['email_notification'] ? $data['email_notification'] : 0;
        
        if ($this->input->post('id')) {
            $data['status'] = $this->input->post('status');
            $data['modified_at'] = date('Y-m-d H:i:s');
            $data['modified_by'] = logged_in_user_id();
            
        } else {
            
            $data['status'] = 1;
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['created_by'] = logged_in_user_id();
            $data['modified_at'] = date('Y-m-d H:i:s');
            $data['modified_by'] = logged_in_user_id();
            
            $school = $this->assignment->get_school_by_id($data['school_id']);
            
            if(!$school->academic_year_id){
                error($this->lang->line('set_academic_year_for_school'));
                redirect('academic/assignment/index');
            }
            
            $data['academic_year_id'] = $school->academic_year_id;
            
        }


        if ($_FILES['assignment']['name']) {
            $data['assignment'] = $this->_upload_assignment();
        }

        return $data;
    }

    
    
    /*****************Function _upload_assignment**********************************
    * @type            : Function
    * @function name   : _upload_assignment
    * @description     : Process upload assignment document into server                  
    *                    and return document name   
    * @param           : $return_assignment string value
    * @return          : null 
    * ********************************************************** */
    private function _upload_assignment() {

        $prev_assignment = $this->input->post('prev_assignment');
        $assignment = $_FILES['assignment']['name'];
        $assignment_type = $_FILES['assignment']['type'];
        $return_assignment = '';

        if ($assignment != "") {
            if ($assignment_type == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' ||
                    $assignment_type == 'application/msword' || $assignment_type == 'text/plain' ||
                    $assignment_type == 'application/vnd.ms-office' || $assignment_type == 'application/pdf') {

                $destination = 'assets/uploads/assignment/';

                $assignment_type = explode(".", $assignment);
                $extension = strtolower($assignment_type[count($assignment_type) - 1]);
                $assignment_path = 'assignment-' . time() . '-sms.' . $extension;

                move_uploaded_file($_FILES['assignment']['tmp_name'], $destination . $assignment_path);

                // need to unlink previous assignment
                if ($prev_assignment != "") {
                    if (file_exists($destination . $prev_assignment)) {
                        @unlink($destination . $prev_assignment);
                    }
                }

                $return_assignment = $assignment_path;
            }
        } else {
            $return_assignment = $prev_assignment;
        }

        return $return_assignment;
    }

    
    /*****************Function delete**********************************
    * @type            : Function
    * @function name   : delete
    * @description     : delete "Assignment" from database                  
    *                    and unlink assignment document from server   
    * @param           : $id integer value
    * @return          : null 
    * ********************************************************** */
    public function delete($id = null) {

        check_permission(DELETE);
        
        if(!is_numeric($id)){
             error($this->lang->line('unexpected_error'));
             redirect('academic/assignment/index');
        }
        
        $assignment = $this->assignment->get_single('assignments', array('id' => $id));
        
        if ($this->assignment->delete('assignments', array('id' => $id))) {
                        
            // delete submission of this assignments
            $this->assignment->delete('assignment_submissions', array('assignment_id' => $id));

            // delete assignment assignment
            $destination = 'assets/uploads/';
            if (file_exists($destination . '/assignment/' . $assignment->assignment)) {
                @unlink($destination . '/assignment/' . $assignment->assignment);
            }
            
            create_log('Has been deleted an assignment : '.$assignment->title);

            success($this->lang->line('delete_success'));
        } else {
            error($this->lang->line('delete_failed'));
        }
        
        redirect('academic/assignment/index/' . $assignment->class_id);
    }
    
    
         
   public function _send_email_notification($data = null) {
 
            $school_id = $data['school_id'];
            $email_setting = $this->db->get_where('email_settings', array('status' => 1, 'school_id'=>$school_id))->row(); 
                
        
            if(!empty($email_setting) && $email_setting->mail_protocol == 'smtp'){
                $config['protocol']     = 'smtp';
                $config['smtp_host']    = $email_setting->smtp_host;
                $config['smtp_port']    = $email_setting->smtp_port;
                $config['smtp_timeout'] = 5;
                $config['smtp_user']    = $email_setting->smtp_user;
                $config['smtp_pass']    = $email_setting->smtp_pass;
                $config['smtp_crypto']  = $email_setting->smtp_crypto ? $email_setting->smtp_crypto  : 'tls';
                $config['mailtype'] = 'html';
                $config['charset']  = $email_setting->char_set ? $email_setting->char_set  : 'iso-8859-1';
                $config['priority']  = '3';

            }elseif(!empty($email_setting) && $email_setting->mail_protocol != 'smtp'){
                $config['protocol'] = $email_setting->mail_protocol;
                $config['mailpath'] = '/usr/sbin/'.$email_setting->mail_protocol; 
                $config['mailtype'] = 'html';
                $config['charset']  = $email_setting->char_set ? $email_setting->char_set  : 'iso-8859-1';
                $config['priority']  = '3';

            }else{// default    
                $config['protocol'] = 'sendmail';
                $config['mailpath'] = '/usr/sbin/sendmail'; 
            }                              

           
            $config['wordwrap'] = TRUE;            
            $config['newline']  = "\r\n";            

            $this->load->library('email');             
            $this->email->initialize($config);

            $from_email = FROM_EMAIL;
            $from_name  = FROM_NAME;                      
                        
           if(!empty($email_setting)){
                $from_email = $email_setting->from_address;
                $from_name  = $email_setting->from_name;  
            }elseif(!empty($school)){
                $from_email = $school->email;
                $from_name  = $school->school_name;  
            }
            
            $school = $this->assignment->get_school_by_id($data['school_id']);
            $students = $this->assignment->get_student_list($data['school_id'], $data['class_id'], $data['section_id'], $school->academic_year_id);
            $assignment = $this->assignment->get_single_assignment($data['assignment_id']);  
            
            foreach ($students as $obj) {                
           
                // for student
                if($obj->email != ''){
                    
                    if ($assignment->assignment) {
                        $this->email->attach(UPLOAD_PATH . '/assignment/' . $assignment->assignment);
                    }
                    
                    $this->email->from($from_email, $from_name);
                    $this->email->to($obj->email);
                    $subject = $this->lang->line('assignment'). ' '. $this->lang->line('for') . ' ' . $assignment->subject ;
                    $this->email->subject($subject);       

                    $message = $this->lang->line('hi'). ' '. $obj->name.',';
                    $message .= '<br/>';
                    $message .= '<strong>'.$this->lang->line('your_assignment_detail').'</strong>';
                    $message .= '<br/>';
                    $message .= $this->lang->line('title').': ' . $assignment->title;
                    $message .= '<br/>';
                    $message .= $this->lang->line('class').': ' . $assignment->class_name;
                    $message .= '<br/>';
                    $message .= $this->lang->line('section'). ': ' . $assignment->section;
                    $message .= '<br/>';
                    $message .= $this->lang->line('subject'). ': ' . $assignment->subject;
                    $message .= '<br/>';
                    $message .= $this->lang->line('teacher'). ': ' . $assignment->teacher;
                    $message .= '<br/>';
                    $message .= $this->lang->line('assignment_date'). ': ' . date('d/m/Y', strtotime($assignment->assigment_date));
                    $message .= '<br/>';
                    $message .= $this->lang->line('submission_date'). ': ' . date('d/m/Y', strtotime($assignment->submission_date));
                    $message .= '<br/>';
                    $message .= $this->lang->line('note'). ': ' . $assignment->note;
                    $message .= '<br/>';
                    $message .= $this->lang->line('login').' : <a href="'.site_url('auth/login').'"> '.$this->lang->line('login_to_school').' </a><br/>';      
                    $message .= 'OR: '.site_url('auth/login');      
                    $message .= '<br/><br/>';

                    $message .= $this->lang->line('thank_you').'<br/>';
                    $message .= $from_name;

                    $this->email->message($message);           

                    if(!empty($email_setting) && $email_setting->mail_protocol == 'smtp'){
                        $this->email->send(); 
                    }else if(!empty($email_setting) && $email_setting->mail_protocol != 'smtp'){
                        $this->email->send();
                    }else{
                        $headers = "MIME-Version: 1.0\r\n";
                        $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
                        $headers .= "From:  $from_name < $from_email >\r\n";
                        $headers .= "Reply-To:  $from_name < $from_email >\r\n"; 
                        mail($obj->email, $subject, $message, $headers);
                    } 
                
                }
                
                // for guardian
                if($obj->g_email != ''){
                    
                    if ($assignment->assignment) {
                        $this->email->attach(UPLOAD_PATH . '/assignment/' . $assignment->assignment);
                    }
                    
                    $this->email->from($from_email, $from_name);
                    $this->email->to($obj->g_email);
                    $subject = $this->lang->line('assignment'). ' '. $this->lang->line('for') . ' ' . $assignment->subject ;
                    $this->email->subject($subject);       

                    $message = $this->lang->line('hi'). ' '. $obj->g_name.',';
                    $message .= '<br/>';
                    $message .= '<strong>'.$this->lang->line('your_child_assignment_detail').'</strong>';
                    $message .= '<br/>';
                    $message .= $this->lang->line('title').': ' . $assignment->title;
                    $message .= '<br/>';
                    $message .= $this->lang->line('class').': ' . $assignment->class_name;
                    $message .= '<br/>';
                    $message .= $this->lang->line('section'). ': ' . $assignment->section;
                    $message .= '<br/>';
                    $message .= $this->lang->line('subject'). ': ' . $assignment->subject;
                    $message .= '<br/>';
                    $message .= $this->lang->line('teacher'). ': ' . $assignment->teacher;
                    $message .= '<br/>';
                    $message .= $this->lang->line('assignment_date'). ': ' . date('d/m/Y', strtotime($assignment->assigment_date));
                    $message .= '<br/>';
                    $message .= $this->lang->line('submission_date'). ': ' . date('d/m/Y', strtotime($assignment->submission_date));
                    $message .= '<br/>';
                    $message .= $this->lang->line('note'). ': ' . $assignment->note;
                    $message .= '<br/>';
                    $message .= $this->lang->line('login').' : <a href="'.site_url('auth/login').'"> '.$this->lang->line('login_to_school').' </a><br/>';      
                    $message .= 'OR: '.site_url('auth/login');      
                    $message .= '<br/><br/>';

                    $message .= $this->lang->line('thank_you').'<br/>';
                    $message .= $from_name;

                    $this->email->message($message);           

                    if(!empty($email_setting) && $email_setting->mail_protocol == 'smtp'){
                        $this->email->send(); 
                    }else if(!empty($email_setting) && $email_setting->mail_protocol != 'smtp'){
                        $this->email->send();
                    }else{
                        $headers = "MIME-Version: 1.0\r\n";
                        $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
                        $headers .= "From:  $from_name < $from_email >\r\n";
                        $headers .= "Reply-To:  $from_name < $from_email >\r\n"; 
                        mail($obj->g_email, $subject, $message, $headers);
                    } 
                
                }
            }
    }
    
        
    /*****************Function _send_sms_notification**********************************
    * @type            : Function
    * @function name   : _send_sms_notification
    * @description     : Process to send SMS to the users                  
    *                    
    * @param           : $data array() value
    * @return          : null 
    * ********************************************************** */
    private function _send_sms_notification($data) {
       
        
        $school = $this->assignment->get_school_by_id($data['school_id']);
        $students = $this->assignment->get_student_list($data['school_id'], $data['class_id'], $data['section_id'], $school->academic_year_id);
        $assignment = $this->assignment->get_single_assignment($data['assignment_id']);       
        
        // get active sms gateway for the school
        $sms_gateway = $this->db->get_where('sms_settings', array('status' => 1, 'school_id'=>$data['school_id']))->row(); 
        $gateway = '';

        if ($sms_gateway->clickatell_status) {
            $gateway = 'clicktell';
        }elseif ($sms_gateway->twilio_status) {
            $gateway = 'twilio';
        }elseif ($sms_gateway->bulk_status) {
            $gateway = 'bulk';
        }elseif ($sms_gateway->msg91_status) {
            $gateway = 'msg91';
        }elseif ($sms_gateway->plivo_status) {
            $gateway = 'plivo';
        }elseif ($sms_gateway->textlocal_status) {
            $gateway = 'text_local';
        }elseif ($sms_gateway->smscountry_status) {
            $gateway = 'sms_country';
        }elseif ($sms_gateway->betamsm_status) {
            $gateway = 'beta_sms';
        }elseif ($sms_gateway->bulk_pk_status) {
            $gateway = 'bulk_pk';
        }elseif ($sms_gateway->cluster_status) {
            $gateway = 'sms_custer';
        }elseif ($sms_gateway->alpha_status) {
            $gateway = 'alpha_net';      
        }elseif ($sms_gateway->mim_status) {
            $gateway = 'mim_sms';
        } elseif ($sms_gateway->bulk360_status) {
            $gateway = 'bulk_360';
        } elseif ($sms_gateway->smsto_status) {
            $gateway = 'sms_to';
        }
        
        if($this->sms_gateway($gateway)){

            foreach ($students as $obj) {

                // student sms
                if($obj->phone != ''){                    
                    $message = $this->lang->line('hi').' '. $obj->name. ', ';
                    $message .= $this->lang->line('assignment_detail'). '. ';
                    $message .= $this->lang->line('class').': '.$assignment->class_name. ', ';
                    $message .= $this->lang->line('section').': '.$assignment->section. ', ';
                    $message .= $this->lang->line('subject').': '.$assignment->subject. ', ';
                    $message .= $this->lang->line('assignment_date'). ': ' . date('d/m/Y', strtotime($assignment->assigment_date)).',';
                    $message .= $this->lang->line('submission_date'). ': ' . date('d/m/Y', strtotime($assignment->submission_date));
                    $this->_send($gateway, $obj->phone, $message);           
                }
                
                // guardian phone
                if($obj->g_phone != ''){ 
                    $message = $this->lang->line('hi').' '. $obj->g_name. ', ';
                    $message .= $this->lang->line('assignment_detail'). '. ';
                    $message .= $this->lang->line('class').': '.$assignment->class_name. ', ';
                    $message .= $this->lang->line('section').': '.$assignment->section. ', ';
                    $message .= $this->lang->line('subject').': '.$assignment->subject. ', ';
                    $message .= $this->lang->line('assignment_date'). ': ' . date('d/m/Y', strtotime($assignment->assigment_date)).',';
                    $message .= $this->lang->line('submission_date'). ': ' . date('d/m/Y', strtotime($assignment->submission_date));
                    $this->_send($gateway, $obj->g_phone, $message);             
                }
            }

        }      
    }
    
    public function sms_gateway($getway) {

        if ($getway == "clicktell") {
            if ($this->clickatell->ping() == TRUE) {
                return TRUE;
            } else {
                return FALSE;
            }
        } elseif ($getway == 'twilio') {            
            $get = $this->twilio->get_twilio();
            $ApiVersion = $get['version'];
            $AccountSid = $get['accountSID'];
            $check = $this->twilio->request("/$ApiVersion/Accounts/$AccountSid/Calls");

            if ($check->IsError) {
                return FALSE;
            }
            return TRUE;
        } elseif ($getway == 'bulk') {
            if ($this->bulk->ping() == TRUE) {
                return TRUE;
            } else {
                return FALSE;
            }
        } elseif ($getway == 'msg91') {
            return true;
        } elseif ($getway == 'plivo') {
            return true;
        } elseif ($getway == 'text_local') {
            return true;       
        } elseif ($getway == 'sms_country') {
            return true;
        }elseif ($getway == 'beta_sms') {
            return true;
        }elseif ($getway == 'bulk_pk') {
            return true;
        }elseif ($getway == 'sms_custer') {
            return true;
        }elseif ($getway == 'alpha_net') {
            return true;
        }elseif ($getway == 'bd_bulk') {
            return true;
        }elseif ($getway == 'mim_sms') {
            return true;
        }elseif ($getway == 'bulk_360') {
            return true;
        }elseif ($getway == 'sms_to') {
            return true;
        }        
    }

    public function _send($sms_gateway, $phone, $message) {
       
        
        if ($sms_gateway == "clicktell") {
            
            $this->clickatell->send_message($phone, $message);
        } elseif ($sms_gateway == 'twilio') {
            
            $get = $this->twilio->get_twilio();
            $from = $get['number'];            
            $response = $this->twilio->sms($from, $phone, $message);          
        } elseif ($sms_gateway == 'bulk') {

            //https://github.com/anlutro/php-bulk-sms     
            
            $this->bulk->send($phone, $message);
        } elseif ($sms_gateway == 'msg91') {
            
            $response = $this->msg91->send($phone, $message);
        } elseif ($sms_gateway == 'plivo') {
            
            $response = $this->plivo->send($phone, $message);
        }elseif ($sms_gateway == 'sms_country') { 
            
            $response = $this->smscountry->sendSms($phone, $message);            
        } elseif ($sms_gateway == 'text_local') {  
            
            $response = $this->textlocalsms->sendSms(array($phone), $message);
        } elseif ($sms_gateway == 'beta_sms') {     
            
            $response = $this->betasms->sendSms(array($phone), $message);
        } elseif ($sms_gateway == 'bulk_pk') {     
            
            $response = $this->bulkpk->sendSms($phone, $message);
        } elseif ($sms_gateway == 'sms_custer') {     
            
            $response = $this->smscuster->sendSms($phone, $message);
        } elseif ($sms_gateway == 'alpha_net') {     
            
            $response = $this->alphanet->sendSms($phone, $message);
        } elseif ($sms_gateway == 'bd_bulk') {     
            
            $response = $this->bdbulk->sendSms($phone, $message);
        } elseif ($sms_gateway == 'mim_sms') {     
            
            $response = $this->mimsms->sendSms($phone, $message);
        } elseif ($sms_gateway == 'bulk_360') {     
            
            $response = $this->bulk360->sendSms($phone, $message);
        } elseif ($sms_gateway == 'sms_to') {     
            
            $response = $this->smsto->sendSms($phone, $message);
        }
                
    }    
    
    
    /***************Function get_assignment_by_section**********************************
     * @type            : Function
     * @function name   : get_assignment_by_section
     * @description     : this function used to populate assignment list by section 
       for user interface
     * @param           : null 
     * @return          : $str string  value with student list
     * ********************************************************** */

    public function get_assignment_by_section() {

        $class_id = $this->input->post('class_id');
        $section_id = $this->input->post('section_id');
        $school_id = $this->input->post('school_id');
        $assignment_id = $this->input->post('assignment_id');
        
        $school = $this->assignment->get_school_by_id($school_id); 
        $assignments = $this->assignment->get_list('assignments', array('class_id'=>$class_id, 'section_id'=>$section_id, 'academic_year_id'=>$school->academic_year_id), '','', '', 'id', 'ASC');
        $str = '<option value="">--' . $this->lang->line('select') . '--</option>';            
                
        $select = 'selected="selected"';
        if (!empty($assignments)) {
            foreach ($assignments as $obj) {
                $selected = $assignment_id == $obj->id ? $select : '';
                $str .= '<option value="' . $obj->id . '" ' . $selected . '>' . $obj->title . '</option>';
            }
        }

        echo $str;
    }

}