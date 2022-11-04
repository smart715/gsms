<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/* * *****************Subject.php**********************************
 * @product name    : Global Multi School Management System Express
 * @type            : Class
 * @class name      : Subject
 * @description     : Manage academic subject for each academic class.  
 * @author          : Codetroopers Team 	
 * @url             : https://themeforest.net/user/codetroopers      
 * @support         : yousuf361@gmail.com	
 * @copyright       : Codetroopers Team	 	
 * ********************************************************** */

class Subjectbyclass extends MY_Controller {

    public $data = array();
    
    
    function __construct() {
        parent::__construct();
  
         $this->load->model('Subjectbyclass_Model', 'subject', true);         
    }

    
    /*****************Function index**********************************
    * @type            : Function
    * @function name   : index
    * @description     : Load "Class Subject List" user interface                 
    *                    with class wise listing    
    * @param           : $id integer value
    * @return          : null 
    * ********************************************************** */
    public function index($class_id = null) {
        
        check_permission(VIEW);
         if(isset($class_id) && !is_numeric($class_id)){
            error($this->lang->line('unexpected_error'));
            redirect('academic/subjectbyclass/index');    
        }
        
        $school_id = getSchoolId();
        
        // for super admin 
        if($_POST){
            $class_id  = $this->input->post('class_id');
        }
        
        $this->data['class_id'] = $class_id;
        $this->data['filter_class_id'] = $class_id;
        $this->data['result'] = $this->subject->get_result_list($class_id, $school_id);        
       
        $condition = array();
        $condition['status'] = 1;                
        $condition['school_id'] = $school_id;
        $this->data['classes'] = $this->subject->get_list('classes', $condition, '','', '', 'id', 'ASC');
        $this->data['subjects'] = $this->subject->get_list('subjects', $condition, '','', '', 'id', 'ASC');
        $this->data['teachers'] = $this->subject->get_list('teachers', $condition, '','', '', 'id', 'ASC');
    
        $this->data['list'] = TRUE;
        $this->data['schools'] = $this->schools;
        $this->layout->title($this->lang->line('manage_subject'). ' | ' . SMS);
        $this->layout->view('subject/subjectbyclass', $this->data); 
    }

    /*****************Function add**********************************
    * @type            : Function
    * @function name   : add
    * @description     : Load "Add new Subject" user interface                 
    *                    and process to store "Subject" into database 
    * @param           : null
    * @return          : null 
    * ********************************************************** */
    public function add() {

        check_permission(ADD);
        
        $school_id = getSchoolId();
        if ($_POST) {
            $this->_prepare_subject_validation();
            if ($this->form_validation->run() === TRUE) {
                $data = $this->_get_posted_subject_data();
                $data['school_id'] = $school_id;

                $insert_id = $this->subject->insert('subjectbyclass', $data);
                if ($insert_id) {
                    
                    $class = $this->subject->get_single('classes', array('id' => $data['class_id'], 'school_id'=>$school_id));
                    create_log('Has been added a sucject : '. $data['name'].' for class : '. $class->name);
                    
                    success($this->lang->line('insert_success'));
                    redirect('academic/subjectbyclass/index/'.$data['class_id']);
                } else {
                    error($this->lang->line('insert_failed'));
                    redirect('academic/subjectbyclass/index/'.$data['class_id']);
                }
            } else {
                error($this->lang->line('insert_failed'));
                $this->data['post'] = $_POST;
            }
        }
        
        // for super admin 
        if($_POST){
            $class_id  = $this->input->post('class_id');
        }
        $this->data['class_id'] = $class_id;
        $this->data['filter_class_id'] = $class_id;
        $this->data['result'] = $this->subject->get_result_list($class_id, $school_id);        
       
        $condition = array();
        $condition['status'] = 1;                
        $condition['school_id'] = $school_id;
        $this->data['classes'] = $this->subject->get_list('classes', $condition, '','', '', 'id', 'ASC');
        $this->data['subjects'] = $this->subject->get_list('subjects', $condition, '','', '', 'id', 'ASC');
        $this->data['teachers'] = $this->subject->get_list('teachers', $condition, '','', '', 'id', 'ASC');
    
        $this->data['list'] = TRUE;
        $this->data['schools'] = $this->schools;
        $this->layout->title($this->lang->line('manage_subject'). ' | ' . SMS);
        $this->layout->view('subject/subjectbyclass', $this->data); 
    }
    
    /*****************Function edit**********************************
    * @type            : Function
    * @function name   : edit
    * @description     : Load Update "Subject" user interface                 
    *                    with populate "Subject" value 
    *                    and process to update "Subject" into database    
    * @param           : $id integer value
    * @return          : null 
    * ********************************************************** */
    public function edit($id = null) {       
       
        check_permission(EDIT);
        if(!is_numeric($id)){
             error($this->lang->line('unexpected_error'));
             redirect('academic/subjectbyclass/index');     
        }
        
        if ($_POST) {
            $this->_prepare_subject_validation();
            if ($this->form_validation->run() === TRUE) {
                $data = $this->_get_posted_subject_data();
                $updated = $this->subject->update('subjects', $data, array('id' => $this->input->post('id')));

                if ($updated) {
                    
                    $class = $this->subject->get_single('classes', array('id' => $data['class_id'], 'school_id'=>$data['school_id']));
                    create_log('Has been updated a sucject : '. $data['name'].' for class : '. $class->name);
                    
                    success($this->lang->line('update_success'));
                    redirect('academic/subjectbyclass/index/'.$data['class_id']);                   
                } else {
                    error($this->lang->line('updtae_failed'));
                    redirect('academic/subjectbyclass/edit/' . $this->input->post('id'));
                }
            } else {
                error($this->lang->line('updtae_failed'));
                $this->data['subject'] = $this->subject->get_single('subjects', array('id' =>  $this->input->post('id')));
            }
        }
        
        if ($id) {
            $this->data['subject'] = $this->subject->get_single('subjects', array('id' => $id));

            if (!$this->data['subject']) {
                 redirect('academic/subjectbyclass/index');      
            }
        }
        
        
        $class_id = $this->data['subject']->class_id;
        if(!$class_id){
          $class_id = $this->input->post('class_id');
        } 
        
        $this->data['class_id'] = $class_id;
        $this->data['filter_class_id'] = $class_id;
        $this->data['subjects'] = $this->subject->get_subject_list($class_id, $this->data['subject']->school_id);        
        
        $condition = array();
        $condition['status'] = 1;        
        if($this->session->userdata('role_id') != SUPER_ADMIN){            
            $condition['school_id'] = $this->session->userdata('school_id');
            $this->data['classes'] = $this->subject->get_list('classes', $condition, '','', '', 'id', 'ASC');
            $this->data['teachers'] = $this->subject->get_list('teachers', $condition, '','', '', 'id', 'ASC');
        }
        
        $this->data['schools'] = $this->schools;
        $this->data['edit'] = TRUE; 
        $this->data['school_id'] = $this->data['subject']->school_id;
        $this->data['filter_school_id'] = $this->data['subject']->school_id;
        
        $this->layout->title($this->lang->line('edit'). ' | ' . SMS);
        $this->layout->view('subject/index', $this->data);
    }
    
    
    
    /*****************Function view**********************************
    * @type            : Function
    * @function name   : view
    * @description     : Load user interface with specific subject data                 
    *                       
    * @param           : $subject_id integer value
    * @return          : null 
    * ********************************************************** */
    public function view($subject_id = null){
        
        check_permission(VIEW);
        
        if(!is_numeric($subject_id)){
             error($this->lang->line('unexpected_error'));
             redirect('academic/subjectbyclass/index');    
        }
        
        $this->data['subject'] = $this->subject->get_single_subject($subject_id);
        $class_id = $this->data['subject']->class_id;
        
        $this->data['subjects'] = $this->subject->get_subject_list($class_id);        
        
        $condition = array();
        $condition['status'] = 1;        
        if($this->session->userdata('role_id') != SUPER_ADMIN){            
            $condition['school_id'] = $this->session->userdata('school_id');
        }
        $this->data['classes'] = $this->subject->get_list('classes', $condition, '','', '', 'id', 'ASC');
        $this->data['teachers'] = $this->subject->get_list('teachers', $condition, '','', '', 'id', 'ASC');
        
        $this->data['class_id'] = $class_id;
        $this->data['schools'] = $this->schools; 
        $this->data['detail'] = TRUE;       
        $this->layout->title($this->lang->line('view'). ' | ' . SMS);
        $this->layout->view('subject/index', $this->data);
    }

    
    
    
     /*****************Function get_single_subject**********************************
     * @type            : Function
     * @function name   : get_single_subject
     * @description     : "Load single subject information" from database                  
     *                    to the user interface   
     * @param           : null
     * @return          : null 
     * ********************************************************** */
    public function get_single_subject(){
        
       $subject_id = $this->input->post('subject_id');
       
       $this->data['subject'] = $this->subject->get_single_subject($subject_id);
       echo $this->load->view('subject/get-single-subject', $this->data);
    }
    
    
    /*****************Function _prepare_subject_validation**********************************
    * @type            : Function
    * @function name   : _prepare_subject_validation
    * @description     : Process "subject" user input data validation                 
    *                       
    * @param           : null
    * @return          : null 
    * ********************************************************** */
    private function _prepare_subject_validation() {
        $this->load->library('form_validation');
        $this->form_validation->set_error_delimiters('<div class="error-message" style="color: red;">', '</div>');
          
        $this->form_validation->set_rules('class_id', $this->lang->line('class'), 'trim|required');   
        $this->form_validation->set_rules('subject_id', $this->lang->line('subject'), 'trim|required');  
        $this->form_validation->set_rules('teacher_id', $this->lang->line('teacher'), 'trim|required');  
    }
    
    
    /*****************Function _get_posted_subject_data**********************************
    * @type            : Function
    * @function name   : _get_posted_subject_data
    * @description     : Prepare "Subject" user input data to save into database                  
    *                       
    * @param           : null
    * @return          : $data array(); value 
    * ********************************************************** */
    private function _get_posted_subject_data() {

        $items = array();
        $items[] = 'class_id';
        $items[] = 'subject_id';
        $items[] = 'teacher_id';
        $data = elements($items, $_POST);        
        
        if ($this->input->post('id')) {
            $data['modified_at'] = date('Y-m-d H:i:s');
            $data['modified_by'] = logged_in_user_id();
        } else {
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['created_by'] = logged_in_user_id();                       
        }

        return $data;
    }

    
    
    /*****************Function delete**********************************
    * @type            : Function
    * @function name   : delete
    * @description     : delete "Subject" data from database                  
    *                       
    * @param           : $id integer value
    * @return          : null 
    * ********************************************************** */
    public function delete($id = null) {
        
        check_permission(DELETE);
        
        if(!is_numeric($id)){
             error($this->lang->line('unexpected_error'));
             redirect('academic/subjectbyclass/index');    
        }
        
        $subject = $this->subject->get_single('subjects', array('id' => $id));
        
        if ($this->subject->delete('subjects', array('id' => $id))) { 
            
            $class = $this->subject->get_single('classes', array('id' => $subject->class_id, 'school_id'=>$subject->school_id));
            create_log('Has been deleted a sucject : '. $subject->name.' for class : '. $class->name);
            
            success($this->lang->line('delete_success'));
        } else {
            error($this->lang->line('delete_failed'));
        }
        redirect('academic/subjectbyclass/index/'.$subject->class_id);
        
    }
}
