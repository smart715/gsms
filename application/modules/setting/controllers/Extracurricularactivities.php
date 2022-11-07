<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/* * *****************Section.php**********************************
 * @product name    : Global Multi School Management System Express
 * @type            : Class
 * @class name      : Section
 * @description     : Manage academic class section/ division.  
 * @author          : Codetroopers Team 	
 * @url             : https://themeforest.net/user/codetroopers      
 * @support         : yousuf361@gmail.com	
 * @copyright       : Codetroopers Team	 	
 * ********************************************************** */

class Extracurricularactivities extends MY_Controller {

    public $data = array();
    
    
    function __construct() {
        parent::__construct();
                 
         $this->load->model('ExtracurricularActivities_Model', 'activities', true);
    }

    
    /*****************Function index**********************************
     * @type            : Function
     * @function name   : index
     * @description     : Load "Class section list" user interface                 
     *                    with class wise section list   
     * @param           : $id integer value
     * @return          : null 
     * ********************************************************** */
    public function index() {       
                       
        $school_id = getSchoolId();
        $condition = array();
        $condition['school_id'] = getSchoolId();   
        $this->data['activities'] = $this->activities->get_list('extracurricular_activities', $condition, '','', '', 'id', 'ASC');

        $this->data['list'] = true;
        $this->layout->title('Extra Curricular Activities | ' . SMS);
        $this->layout->view('extra_activities/index', $this->data);            
       
    }

    
    /*****************Function add**********************************
     * @type            : Function
     * @function name   : add
     * @description     : Load "Add new Class Section" user interface                 
     *                    and store "Class Section" into database 
     * @param           : null
     * @return          : null 
     * ********************************************************** */
    public function add() {
        
        
        $school_id = getSchoolId();
        if ($_POST) {
            
            $this->_prepare_section_validation();
         
            if ($this->form_validation->run() === TRUE) {
                $data = array();
                $data['school_id'] = $school_id;  
                $data['name'] = $this->input->post('name');
                $data['grade'] = $this->input->post('grade');
                $insert_id = $this->activities->insert('extracurricular_activities', $data);
                if ($insert_id) {                    
                    success($this->lang->line('insert_success'));
                    redirect('setting/extracurricularactivities/index');
                } else {
                    error($this->lang->line('insert_failed'));
                    redirect('setting/extracurricularactivities/add');
                }
            } else {
                error($this->lang->line('insert_failed'));
                $this->data['post'] = $_POST;
            }
        }

        
        $condition['school_id'] = $school_id;        
        $this->data['activities'] = $this->activities->get_list('extracurricular_activities', $condition, '','', '', 'id', 'ASC');
        $this->data['list'] = true;
        $this->layout->title($this->lang->line('add'). ' | ' . SMS);
        $this->layout->view('extra_activities/index', $this->data);
    }

    
     /*****************Function edit**********************************
     * @type            : Function
     * @function name   : edit
     * @description     : Load Update "Class Section" user interface                 
     *                    with populated "class section" value 
     *                    and update "Class section" database    
     * @param           : $id integer value
     * @return          : null 
     * ********************************************************** */
    public function edit($id = null) {       
       
        
        $school_id = getSchoolId();
        if(!is_numeric($id)){
            error($this->lang->line('unexpected_error'));
            redirect('setting/extracurricularactivities/index/');
        }
        
        if ($_POST) {
            $this->_prepare_section_validation();
            if ($this->form_validation->run() === TRUE) {
                $data = array();
                $data['name'] = $this->input->post('name');
                $data['grade'] = $this->input->post('grade');
                $updated = $this->activities->update('extracurricular_activities', $data, array('id' => $this->input->post('id')));

                if ($updated) {
                    
                    success($this->lang->line('update_success'));
                    redirect('setting/extracurricularactivities/index');                   
                } else {
                    error($this->lang->line('update_failed'));
                    redirect('setting/extracurricularactivities/edit/' . $this->input->post('id'));
                }
            } else {
                 error($this->lang->line('update_failed'));
                 $this->data['section'] = $this->activities->get_single('extracurricular_activities', array('id' => $this->input->post('id')));
            }
        }
        
        if ($id) {
            $this->data['section'] = $this->activities->get_single('extracurricular_activities', array('id' => $id));

            if (!$this->data['section']) {
                redirect('setting/extracurricularactivities/index/');
            }
        }

        $condition['school_id'] = $school_id;        
        $this->data['activities'] = $this->activities->get_list('extracurricular_activities', $condition, '','', '', 'id', 'ASC');
        $this->data['edit'] = TRUE;   
        $this->layout->title($this->lang->line('edit'). ' | ' . SMS);
        $this->layout->view('extra_activities/index', $this->data);
    }

    
    /*****************Function _prepare_section_validation**********************************
     * @type            : Function
     * @function name   : _prepare_section_validation
     * @description     : Process "Class Section" user input data validation                 
     *                       
     * @param           : null
     * @return          : null 
     * ********************************************************** */
    private function _prepare_section_validation() {
        $this->load->library('form_validation');
        $this->form_validation->set_error_delimiters('<div class="error-message" style="color: red;">', '</div>');
        
        $this->form_validation->set_rules('name', $this->lang->line('name'), 'required|trim');
    }
    
    
    /*****************Function delete**********************************
     * @type            : Function
     * @function name   : delete
     * @description     : delete "Class Section" from database                  
     *                       
     * @param           : $id integer value
     * @return          : null 
     * ********************************************************** */
    public function delete($id = null) {
        
        
        if(!is_numeric($id)){
            error($this->lang->line('unexpected_error'));
            redirect('setting/extracurricularactivities/index/');
        }
        
        $section = $this->activities->get_single('extracurricular_activities', array('id' => $id));
        if ($this->activities->delete('extracurricular_activities', array('id' => $id))) {  
            
            create_log('Has been deleted a section : '. $section->name);
            
            success($this->lang->line('delete_success'));
        } else {
            error($this->lang->line('delete_failed'));
        }
        redirect('setting/extracurricularactivities/index');
    }

}
