<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/* * *****************Gallery.php**********************************
 * @product name    : Global Multi School Management System Express
 * @type            : Class
 * @class name      : Gradereport
 * @author          : smartmanage715@gmail.com
 * @copyright       : Codetroopers Team	 	
 * ********************************************************** */

class Gradereport extends MY_Controller {

    public $data = array();

    function __construct() {
        parent::__construct();
        $this->load->model('Gradereport_Model', 'gradereport', true);       
    }


    /*****************Function index**********************************
    * @type            : Function
    * @function name   : index
    * @description     : Load "Gallery List" user interface                 
    *                      
    * @param           : null
    * @return          : null 
    * ********************************************************** */
    public function index($school_id = null) {

        check_permission(VIEW);
        
        $this->data['list'] = TRUE;
        $this->layout->title('Grade Report | ' . SMS);
        $this->layout->view('gradebook/gradereport/index', $this->data);
    }


}