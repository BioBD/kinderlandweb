<?php 
require_once APPPATH . 'core/CK_Controller.php';
class System extends CK_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->helper('url');
		$this->load->model('person_model');
		$this->load->model('address_model');
		$this->load->model('generic_model');
		$this->load->model('telephone_model');
		$this->load->model('personuser_model');
		
		$this->person_model->setLogger($this->Logger);
		$this->address_model->setLogger($this->Logger);
		$this->generic_model->setLogger($this->Logger);
		$this->telephone_model->setLogger($this->Logger);
		$this->personuser_model->setLogger($this->Logger);
	}

	public function menu(){
		$this->Logger->info("Starting " . __METHOD__);
		$data['fullname'] = $this->session->userdata("fullname");
		$data['permissions'] = $this->session->userdata("user_types");

		$this->Logger->debug(print_r($data ,true));

		$this->loadView("system/menu", $data);
	}

}

?>