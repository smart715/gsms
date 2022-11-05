<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/* * *****************Superadmin.php**********************************
 * @product name    : Global Multi School Management System Express
 * @type            : Class
 * @class name      : Superadmin
 * @description     : Manage superadmin information of the school.  
 * @author          : Codetroopers Team 	
 * @url             : https://themeforest.net/user/codetroopers      
 * @support         : yousuf361@gmail.com	
 * @copyright       : Codetroopers Team	 	
 * ********************************************************** */

class Admin extends MY_Controller {

    public $data = array();

    function __construct() {
        parent::__construct();
        $this->load->model('User_Model', 'user', true);   
        if(!check_school_owner()){ 
            error($this->lang->line('permission_denied'));
             redirect('dashboard/index');
        }
    }

    
    
    /*****************Function index**********************************
    * @type            : Function
    * @function name   : index
    * @description     : Load "Superadmin List" user interface                 
    *                      
    * @param           : $id integer value
    * @return          : null 
    * ********************************************************** */
    public function index() {
        check_permission(VIEW);
        $school_id = getSchoolId();       
        $this->data['admins'] = $this->user->get_list('users', array('role_id'=>2, 'school_id'=>$school_id), '', '', '', 'id', 'ASC');             
        $this->data['list'] = TRUE;
        $this->layout->title('Manage Admin | ' . SMS);
        $this->layout->view('admin/index', $this->data);
    }

    
    /*****************Function add**********************************
    * @type            : Function
    * @function name   : add
    * @description     : Load "Add new Super admin" user interface                 
    *                    and process to store "Super admin" into database 
    * @param           : null
    * @return          : null 
    * ********************************************************** */
    public function add() {


        if ($_POST) {
            $this->_prepare_superadmin_validation();
            if ($this->form_validation->run() === TRUE) {
                $newUser = array();                
                $newUser['school_id'] = getSchoolId();
                $newUser['role_id'] = '2';   
                $newUser['username'] = $this->input->post('username');           
                $newUser['password'] = md5($this->input->post('password'));
                $newUser['status'] = '1';   
                $newUser['created_at'] = date('Y-m-d H:i:s');
                $newUser['created_by'] = logged_in_user_id();     
                $newUser['modified_by'] = logged_in_user_id();   
                $insert_id = $this->user->insert('users', $newUser);
                if ($insert_id) {
                    
                    create_log('Has been created a admin : ');  
                    
                    success($this->lang->line('insert_success'));
                    redirect('administrator/admin/index');
                } else {
                    error($this->lang->line('insert_failed'));
                    redirect('administrator/admin/add');
                }
            } else {
                error($this->lang->line('insert_failed'));
                $this->data['post'] = $_POST;
            }
        }
        
        $school_id = getSchoolId();
        $this->data['admins'] = $this->user->get_list('users', array('role_id'=>2, 'school_id'=>$school_id), '', '', '', 'id', 'ASC');             
        $this->data['add'] = TRUE;
        $this->layout->title($this->lang->line('add') . ' | ' . SMS);
        $this->layout->view('admin/index', $this->data);
    }

    
    
    /*****************Function edit**********************************
    * @type            : Function
    * @function name   : edit
    * @description     : Load Update "Super Admin" user interface                 
    *                    with populate "Super Admin" value 
    *                    and process to update "Super Admin" into database    
    * @param           : $id integer value
    * @return          : null 
    * ********************************************************** */
    public function edit($id = null) {


        if ($_POST) {
            $this->_prepare_superadmin_validation();
            if ($this->form_validation->run() === TRUE) {
                $data = $this->_get_posted_superadmin_data();
                $newUser['username'] = $this->input->post('username');           
                $newUser['password'] = md5($this->input->post('password'));
                $newUser['modified_by'] = logged_in_user_id();   
                $updated = $this->user->update('users', $data, array('id' => $this->input->post('id')));

                if ($updated) {
                    
                    create_log('Has been updated a super admin : '.$data['name']); 
                    
                    success($this->lang->line('update_success'));
                    redirect('administrator/admin/index');
                } else {
                    error($this->lang->line('update_failed'));
                    redirect('administrator/admin/edit/' . $this->input->post('id'));
                }
            } else {  
                error($this->lang->line('update_failed'));
                $this->data['admin'] = $this->user->get_single('users', array('id' => $this->input->post('id')));
            }
        } else {
            if ($id) {
                $this->data['admin'] = $this->user->get_single('users', array('id' => $id));

                if (!$this->data['admin']) {
                    redirect('administrator/admin/index');
                }
            }
        }

        $this->data['admins'] = $this->user->get_superadmin_list();
                
       
        $school_id = getSchoolId();
        $this->data['admins'] = $this->user->get_list('users', array('role_id'=>2, 'school_id'=>$school_id), '', '', '', 'id', 'ASC');       
        $this->data['edit'] = TRUE;
        $this->layout->title($this->lang->line('edit') . ' | ' . SMS);
        $this->layout->view('admin/index', $this->data);
    }

    
     /*****************Function get_single_superadmin**********************************
     * @type            : Function
     * @function name   : get_single_superadmin
     * @description     : "Load single superadmin information" from database                  
     *                    to the user interface   
     * @param           : null
     * @return          : null 
     * ********************************************************** */
    public function get_single_admin(){
        
       $admin_id = $this->input->post('admin_id');
       
       $this->data['superadmin'] = $this->user->get_single_superadmin($admin_id);
       echo $this->load->view('admin/get-single-admin', $this->data);
    }
    
    /*****************Function _prepare_superadmin_validation**********************************
    * @type            : Function
    * @function name   : _prepare_superadmin_validation
    * @description     : Process "Super Admin" user input data validation                 
    *                       
    * @param           : null
    * @return          : null 
    * ********************************************************** */
    private function _prepare_superadmin_validation() {
        $this->load->library('form_validation');
        $this->form_validation->set_error_delimiters('<div class="error-message" style="color: red;">', '</div>');

        $this->form_validation->set_rules('username', $this->lang->line('username'), 'trim|required|callback_username');
        $this->form_validation->set_rules('password', $this->lang->line('password'), 'trim|required');
    }
   
    
                    
    /*****************Function email**********************************
    * @type            : Function
    * @function name   : email
    * @description     : Unique check for "Super Admin Email" data/value                  
    *                       
    * @param           : null
    * @return          : boolean true/false 
    * ********************************************************** */ 
    public function username() {
        if ($this->input->post('id') == '') {
            $username = $this->user->duplicate_check($this->input->post('username'));
            if ($username) {
                $this->form_validation->set_message('username', $this->lang->line('already_exist'));
                return FALSE;
            } else {
                return TRUE;
            }
        } else if ($this->input->post('id') != '') {
            $username = $this->user->duplicate_check($this->input->post('username'), $this->input->post('id'));
            if ($username) {
                $this->form_validation->set_message('username', $this->lang->line('already_exist'));
                return FALSE;
            } else {
                return TRUE;
            }
        } else {
            return TRUE;
        }
    }
    
    /*****************Function _get_posted_superadmin_data**********************************
    * @type            : Function
    * @function name   : _get_posted_superadmin_data
    * @description     : Prepare "Super Admin" user input data to save into database                  
    *                       
    * @param           : null
    * @return          : $data array(); value 
    * ********************************************************** */ 
    private function _get_posted_superadmin_data() {

        $items = array();
        $items[] = 'national_id';
        $items[] = 'name';
        $items[] = 'email';
        $items[] = 'phone';
        $items[] = 'present_address';
        $items[] = 'permanent_address';
        $items[] = 'gender';
        $items[] = 'blood_group';
        $items[] = 'religion';
        $items[] = 'other_info';            
        
        $data = elements($items, $_POST);

        $data['dob'] = date('Y-m-d', strtotime($this->input->post('dob')));

        if ($this->input->post('id')) {
            $data['modified_at'] = date('Y-m-d H:i:s');
            $data['modified_by'] = logged_in_user_id();
        } else {
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['created_by'] = logged_in_user_id();
            $data['status'] = 1;
            // create user 
            $data['user_id'] = $this->user->create_user();
        }

        if ($_FILES['photo']['name']) {
            $data['photo'] = $this->_upload_photo();
        }
        if ($_FILES['resume']['name']) {
            $data['resume'] = $this->_upload_resume();
        }
        return $data;
    }

    
       
    /*****************Function _upload_photo**********************************
    * @type            : Function
    * @function name   : _upload_photo
    * @description     : Process to upload superadmin photo into server                  
    *                     and return photo name  
    * @param           : null
    * @return          : $return_photo string value 
    * ********************************************************** */ 
    private function _upload_photo() {

        $prev_photo = $this->input->post('prev_photo');
        $photo = $_FILES['photo']['name'];
        $photo_type = $_FILES['photo']['type'];
        $return_photo = '';
        if ($photo != "") {
            if ($photo_type == 'image/jpeg' || $photo_type == 'image/pjpeg' ||
                    $photo_type == 'image/jpg' || $photo_type == 'image/png' ||
                    $photo_type == 'image/x-png' || $photo_type == 'image/gif') {

                // super admin photo folder is same as employee
                $destination = 'assets/uploads/employee-photo/';

                $file_type = explode(".", $photo);
                $extension = strtolower($file_type[count($file_type) - 1]);
                $photo_path = 'photo-' . time() . '-sms.' . $extension;

                move_uploaded_file($_FILES['photo']['tmp_name'], $destination . $photo_path);

                // need to unlink previous photo
                if ($prev_photo != "") {
                    if (file_exists($destination . $prev_photo)) {
                        @unlink($destination . $prev_photo);
                    }
                }

                $return_photo = $photo_path;
            }
        } else {
            $return_photo = $prev_photo;
        }

        return $return_photo;
    }

           
    /*****************Function _upload_resume**********************************
    * @type            : Function
    * @function name   : _upload_resume
    * @description     : Process to upload superadmin resume into server                  
    *                     and return resume file name  
    * @param           : null
    * @return          : $return_resume string value 
    * ********************************************************** */ 
    private function _upload_resume() {
        
        $prev_resume = $this->input->post('prev_resume');
        $resume = $_FILES['resume']['name'];
        $resume_type = $_FILES['resume']['type'];
        $return_resume = '';

        if ($resume != "") {
            if ($resume_type == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' ||
                    $resume_type == 'application/powerpoint' || $resume_type == 'application/vnd.ms-powerpoint' ||
                    $resume_type == 'application/mspowerpoint' || $resume_type == 'application/x-mspowerpoint' ||
                    $resume_type == 'application/msword' || $resume_type == 'text/plain' ||
                    $resume_type == 'application/vnd.ms-office' || $resume_type == 'application/pdf') {

                // super admin resume folder is same as employee
                $destination = 'assets/uploads/employee-resume/';

                $file_type = explode(".", $resume);
                $extension = strtolower($file_type[count($file_type) - 1]);
                $resume_path = 'resume-' . time() . '-sms.' . $extension;

                move_uploaded_file($_FILES['resume']['tmp_name'], $destination . $resume_path);

                // need to unlink previous photo
                if ($prev_resume != "") {
                    if (file_exists($destination . $prev_resume)) {
                        @unlink($destination . $prev_resume);
                    }
                }

                $return_resume = $resume_path;
            }
        } else {
            $return_resume = $prev_resume;
        }

        return $return_resume;
    }

        
    
    /*****************Function delete**********************************
    * @type            : Function
    * @function name   : delete
    * @description     : delete "Employee" data from database                  
    *                     and unlink superadmin photo and Resume from server  
    * @param           : $id integer value
    * @return          : null 
    * ********************************************************** */
    public function delete($id = null) {


        if(!is_numeric($id)){
             error($this->lang->line('unexpected_error'));
             redirect('administrator/admin');       
        }
        $admin =$this->user->get_single('users', array('id' => $id));
        if (!empty($admin)) {
            
            create_log('Has been deleted a super admin : '.$admin->name); 

            // delete superadmin login data
            $this->user->delete('users', array('id' => $id));

            success($this->lang->line('delete_success'));
        } else {
            error($this->lang->line('delete_failed'));
        }
        redirect('administrator/admin/index');
    }

}
