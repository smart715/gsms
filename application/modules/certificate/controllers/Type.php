<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/* * *****************Type.php**********************************
 * @product name    : Global Multi School Management System Express
 * @type            : Class
 * @class name      : Type
 * @description     : Manage Certificate Type.  
 * @author          : Codetroopers Team 	
 * @url             : https://themeforest.net/user/codetroopers      
 * @support         : yousuf361@gmail.com	
 * @copyright       : Codetroopers Team	 	
 * ********************************************************** */

class Type extends MY_Controller {

    public $data = array();
    
    
    function __construct() {
        parent::__construct();
        $this->load->model('Type_Model', 'type', true);
        
    }

    
    /*****************Function index**********************************
    * @type            : Function
    * @function name   : index
    * @description     : Load "Certificate Type List" user interface                 
    *                       
    * @param           : null
    * @return          : null 
    * ********************************************************** */
    public function index() {
        
        check_permission(VIEW); 
                  
        $this->data['certificates'] = $this->type->get_certificate_list();
        
        $this->data['list'] = TRUE;
        $this->layout->title($this->lang->line('manage_certificate_type'). ' | ' . SMS);
        $this->layout->view('type/index', $this->data);  
    }

    
    /*****************Function add**********************************
    * @type            : Function
    * @function name   : add
    * @description     : Load "Add new Certificate Type" user interface                 
    *                    and process to store "Certificate Type" into database 
    * @param           : null
    * @return          : null 
    * ********************************************************** */
    public function add() {

         check_permission(ADD);
        if ($_POST) {
            $this->_prepare_type_validation();
            if ($this->form_validation->run() === TRUE) {
                $data = $this->_get_posted_type_data();

                $insert_id = $this->type->insert('certificates', $data);
                if ($insert_id) {
                    
                    create_log('Has been created a certificate type : '.$data['name']);                     
                    success($this->lang->line('insert_success'));
                    redirect('certificate/type/index');
                    
                } else {
                    error($this->lang->line('insert_failed'));
                    redirect('certificate/type/add');
                }
            } else {
                error($this->lang->line('insert_failed'));
                $this->data = $_POST;
            }
        }

               
        if($this->session->userdata('role_id') != SUPER_ADMIN){ 
            $this->data['name'] = $this->session->userdata('school_name');;
        } 
        
        $this->data['certificates'] = $this->type->get_certificate_list();
        
        
        $this->data['add'] = TRUE;
        $this->layout->title($this->lang->line('add'). ' | ' . SMS);
        $this->layout->view('type/index', $this->data);
    }

        
    /*****************Function edit**********************************
    * @type            : Function
    * @function name   : edit
    * @description     : Load Update "Certificate Type" user interface                 
    *                    with populate "Certificate Type" value 
    *                    and process to update "Certificate" into database    
    * @param           : $id integer value
    * @return          : null 
    * ********************************************************** */
    public function edit($id = null) {       

         check_permission(EDIT);
         
        if ($_POST) {
            $this->_prepare_type_validation();
            if ($this->form_validation->run() === TRUE) {
                $data = $this->_get_posted_type_data();
                $updated = $this->type->update('certificates', $data, array('id' => $this->input->post('id')));

                if ($updated) {
                    
                    create_log('Has been updated a certificate type : '.$data['name']);  
                    
                    success($this->lang->line('update_success'));
                    redirect('certificate/type/index');                   
                } else {
                    error($this->lang->line('update_failed'));
                    redirect('certificate/type/edit/' . $this->input->post('id'));
                }
            } else {
                error($this->lang->line('update_failed'));
                $this->data['certificate'] = $this->type->get_single('certificates', array('id' => $this->input->post('id')));
            }
        } else {
            if ($id) {
                $this->data['certificate'] = $this->type->get_single('certificates', array('id' => $id));

                if (!$this->data['certificate']) {
                     redirect('certificate/type/index');
                }
            }
        }
 
        
        $this->data['certificates'] = $this->type->get_certificate_list();
        
        $this->data['school_id'] = $this->data['certificate']->school_id;
        
        $this->data['edit'] = TRUE;
        $this->layout->title($this->lang->line('edit') . ' | ' . SMS);
        $this->layout->view('type/index', $this->data);
    }

       
    /*****************Function view**********************************
    * @type            : Function
    * @function name   : view
    * @description     : Load user interface with specific Certificate data                 
    *                       
    * @param           : $certificate_id integer value
    * @return          : null 
    * ********************************************************** */
    public function view($certificate_id = null) {

        check_permission(VIEW);

        if(!is_numeric($certificate_id)){
             error($this->lang->line('unexpected_error'));
             redirect('dashboard/index');
        }
        
        
        $this->data['certificate'] = $this->type->get_single('certificates', array('id' => $certificate_id));
       
        
        
        $this->data['school'] = $this->type->get_single('schools', array('id'=>$this->data['certificate']->school_id, 'status'=>1));
        
        $this->data['certificates'] = $this->type->get_certificate_list();
        
        $this->data['detail'] = TRUE;
        $this->layout->title($this->lang->line('view')  . ' | ' . SMS);
        $this->layout->view('type/index', $this->data);
    }     
    
    
    /*****************Function _prepare_type_validation**********************************
    * @type            : Function
    * @function name   : _prepare_type_validation
    * @description     : Process "Certificate Type" user input data validation                 
    *                       
    * @param           : null
    * @return          : null 
    * ********************************************************** */
    private function _prepare_type_validation() {
        $this->load->library('form_validation');
        $this->form_validation->set_error_delimiters('<div class="error-message" style="color: red;">', '</div>');
        
        $this->form_validation->set_rules('school_id', $this->lang->line('school'), 'trim|required');
        $this->form_validation->set_rules('name', $this->lang->line('certificate_type') , 'trim|required|callback_name');
        $this->form_validation->set_rules('top_title', $this->lang->line('title') . '/ ' .$this->lang->line('school_name'), 'trim|required');
        $this->form_validation->set_rules('main_text', $this->lang->line('main_certificate_text'), 'trim|required');
        $this->form_validation->set_rules('footer_left', $this->lang->line('footer_left'), 'trim');
        $this->form_validation->set_rules('footer_middle', $this->lang->line('footer_middle'), 'trim');
        $this->form_validation->set_rules('footer_right', $this->lang->line('footer_right'), 'trim');
        $this->form_validation->set_rules('sign1', $this->lang->line('sign1'), 'trim|callback_sign1');
        $this->form_validation->set_rules('sign2', $this->lang->line('sign2'), 'trim|callback_sign2');
        $this->form_validation->set_rules('type_id', $this->lang->line('type_id'), 'trim|required');
    }

                    
    /*****************Function name**********************************
    * @type            : Function
    * @function name   : name
    * @description     : Unique check for "Certificate Name" data/value                  
    *                       
    * @param           : null
    * @return          : boolean true/false 
    * ********************************************************** */ 
    public function name() {
        if ($this->input->post('id') == '') {
            $type = $this->type->duplicate_check($this->input->post('school_id'), $this->input->post('name'));
            if ($type) {
                $this->form_validation->set_message('name', $this->lang->line('already_exist'));
                return FALSE;
            } else {
                return TRUE;
            }
        } else if ($this->input->post('id') != '') {
            $type = $this->type->duplicate_check($this->input->post('school_id'),$this->input->post('name'), $this->input->post('id'));
            if ($type) {
                $this->form_validation->set_message('name', $this->lang->line('already_exist'));
                return FALSE;
            } else {
                return TRUE;
            }
        } else {
            return TRUE;
        }
    }
    
    /*****************Function sign1**********************************
    * @type            : Function
    * @function name   : sign1
    * @description     : Check sign1
    *
    * @param           : null
    * @return          : boolean true/false
    * ********************************************************** */
    public function sign1()
    {
        if ($_FILES['sign1']['name']) {
            $name = $_FILES['sign1']['name'];
            $ext = pathinfo($name, PATHINFO_EXTENSION);
            if ($ext == 'jpg' || $ext == 'jpeg' || $ext == 'png') {
                return true;
            } else {
                $this->form_validation->set_message('sign1', $this->lang->line('select_valid_file_format'));

                return false;
            }
        }
    }
    /*****************Function sign2**********************************
    * @type            : Function
    * @function name   : sign2
    * @description     : Check sign2
    *
    * @param           : null
    * @return          : boolean true/false
    * ********************************************************** */
    public function sign2()
    {
        if ($_FILES['sign2']['name']) {
            $name = $_FILES['sign2']['name'];
            $ext = pathinfo($name, PATHINFO_EXTENSION);
            if ($ext == 'jpg' || $ext == 'jpeg' || $ext == 'png') {
                return true;
            } else {
                $this->form_validation->set_message('sign2', $this->lang->line('select_valid_file_format'));

                return false;
            }
        }
    }
       
    /*****************Function _get_posted_type_data**********************************
    * @type            : Function
    * @function name   : _get_posted_type_data
    * @description     : Prepare "Certificate" user input data to save into database                  
    *                       
    * @param           : null
    * @return          : $data array(); value 
    * ********************************************************** */
    private function _get_posted_type_data() {

        $items = array();
        $items[] = 'school_id';
        $items[] = 'name';
        $items[] = 'top_title';
        $items[] = 'main_text';
        $items[] = 'footer_left';
        $items[] = 'footer_middle';
        $items[] = 'footer_right';
        $items[] = 'sign1';
        $items[] = 'sign2';
        $items[] = 'signer_name1';
        $items[] = 'signer_name2';
        $items[] = 'type_id';
        
        $data = elements($items, $_POST);        
        
        if ($this->input->post('id')) {
            $data['modified_at'] = date('Y-m-d H:i:s');
            $data['modified_by'] = logged_in_user_id();
        } else {
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['created_by'] = logged_in_user_id();
            $data['status'] = 1;
        }
        if (isset($_FILES['sign1']['name'])) {
            $data['sign1'] = $this->_upload_sign1();
        }
        if (isset($_FILES['sign2']['name'])) {
            $data['sign2'] = $this->_upload_sign2();
        }
        
        return $data;
    }

    /*****************Function _upload_sign1**********************************
    * @type            : Function
    * @function name   : _upload_sign1
    * @description     : Process to upload certificate sign1 into server
    *                     and return sign1 name
    * @param           : null
    * @return          : $return_sign1 string value
    * ********************************************************** */
    private function _upload_sign1()
    {
        $prev_sign1 = $this->input->post('prev_sign1');
        $sign1 = $_FILES['sign1']['name'];
        $sign1_type = $_FILES['sign1']['type'];
        $return_sign1 = '';
        if ($sign1 != '') {
            if ($sign1_type == 'image/jpeg' || $sign1_type == 'image/pjpeg' ||
                    $sign1_type == 'image/jpg' || $sign1_type == 'image/png' ||
                    $sign1_type == 'image/x-png' || $sign1_type == 'image/gif') {
                $destination = 'assets/uploads/certificate/';

                $file_type = explode('.', $sign1);
                $extension = strtolower($file_type[count($file_type) - 1]);
                $sign1_path = 'certificate-'.time().'-sign1.'.$extension;

                move_uploaded_file($_FILES['sign1']['tmp_name'], $destination.$sign1_path);

                // need to unlink previous sign1
                if ($prev_sign1 != '') {
                    if (file_exists($destination.$prev_sign1)) {
                        @unlink($destination.$prev_sign1);
                    }
                }

                $return_sign1 = $sign1_path;
            }
        } else {
            $return_sign1 = $prev_sign1;
        }

        return $return_sign1;
    }
    /*****************Function _upload_sign2**********************************
    * @type            : Function
    * @function name   : _upload_sign2
    * @description     : Process to upload certificate sign2 into server
    *                     and return sign2 name
    * @param           : null
    * @return          : $return_sign2 string value
    * ********************************************************** */
    private function _upload_sign2()
    {
        $prev_sign2 = $this->input->post('prev_sign2');
        $sign2 = $_FILES['sign2']['name'];
        $sign2_type = $_FILES['sign2']['type'];
        $return_sign2 = '';
        if ($sign2 != '') {
            if ($sign2_type == 'image/jpeg' || $sign2_type == 'image/pjpeg' ||
                    $sign2_type == 'image/jpg' || $sign2_type == 'image/png' ||
                    $sign2_type == 'image/x-png' || $sign2_type == 'image/gif') {
                $destination = 'assets/uploads/certificate/';

                $file_type = explode('.', $sign2);
                $extension = strtolower($file_type[count($file_type) - 1]);
                $sign2_path = 'certificate-'.time().'-sign2.'.$extension;

                move_uploaded_file($_FILES['sign2']['tmp_name'], $destination.$sign2_path);

                // need to unlink previous sign2
                if ($prev_sign2 != '') {
                    if (file_exists($destination.$prev_sign2)) {
                        @unlink($destination.$prev_sign2);
                    }
                }

                $return_sign2 = $sign2_path;
            }
        } else {
            $return_sign2 = $prev_sign2;
        }

        return $return_sign2;
    }

        
    
    /*****************Function delete**********************************
    * @type            : Function
    * @function name   : delete
    * @description     : delete "Certificate Type" data from database                  
    *                       
    * @param           : $id integer value
    * @return          : null 
    * ********************************************************** */
    public function delete($id = null) {
        
        check_permission(DELETE);
         
        if(!is_numeric($id)){
             error($this->lang->line('unexpected_error'));
             redirect('certificate/type/index');        
        }
        
        $certificate = $this->type->get_single('certificates', array('id' => $id));
        
        if (!empty($certificate)) {

            // delete employee data
            $this->type->delete('certificates', array('id' => $id));          
            // delete certificate sign1
            $destination = 'assets/uploads/';
            if (file_exists($destination.'/certificate/'.$certificate->sign1)) {
                @unlink($destination.'/certificate/'.$certificate->sign1);
            }
            // delete certificate sign2
            $destination = 'assets/uploads/';
            if (file_exists($destination.'/certificate/'.$certificate->sign2)) {
                @unlink($destination.'/certificate/'.$certificate->sign2);
            }
            create_log('Has been deleted a certificate type : '.$certificate->name);
            success($this->lang->line('delete_success'));
            
        } else {
            error($this->lang->line('delete_failed'));
        }
        
        redirect('certificate/type/index');
    }

}
