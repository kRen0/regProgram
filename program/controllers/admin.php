<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**

 */
class Admin extends Admin_Controller
{
	private $settings_validation_rules = array(
		array(
			'field' => 'program_category',
			'label' => 'lang:category',
			'rules' => 'trim|integer'
		)
	);
	
	private $edit_validation_rules = array(
		array(
			'field' => 'status',
			'label' => 'lang:program.opened',
			'rules' => 'trim|integer|required'
		),
		array(
			'field' => 'limit',
			'label' => 'lang:program.limit',
			'rules' => 'trim|integer|exact_length[65000]|required'
		)
	);

	public function __construct()
	{
		parent::__construct();

		$this->load->library('settings/settings');
		$this->load->model('settings/settings_m');
		$this->load->model('program_m');
		$this->load->model('participants_m');
		$this->load->library('form_validation');
		$this->lang->load('program');
	}

	/*

	*/
	public function index(){
		$program = $this->program_m->get_program(Settings::get('program_category'));
		$this->template->build('admin/index',array( 'program' => $program ));
	}
	
	public function settings(){
		$this->lang->load('program');
		$settings = $this->settings_m->get_many_by('program');
		$this->form_validation->set_rules($this->settings_validation_rules);
		
		$categories = array();
		foreach($this->program_m->get_categories() AS $T)
			$categories[$T->id] = $T->title;
		
		if ($this->form_validation->run() )
		{
			foreach($this->settings_validation_rules as $r){
				$this->settings_m->update($r['field'],array('value' => $this->input->post($r['field'])));
			}
			$this->session->set_flashdata('success', lang('success_save_settings'));
			redirect('admin/program/settings');
			return TRUE;
		}
		
		$this->template
			->title($this->module_details['name'])
			->set('settings',$settings)
			->set('categories',$categories)
			->build('admin/settings');
	}
	
	public function view($id){
	if(isset($id)) {
		$this->form_validation->set_rules($this->edit_validation_rules);
		$program = $this->program_m->get_program_by_id($id);
		$status = array (0=>lang('program.opened.n'),1=>lang('program.opened.y'));
		if ($this->form_validation->run()) {
			if( $this->program_m->set($this->input->post(), $id)){
				$this->session->set_flashdata('success', lang('program.edit_success'));
                            redirect('admin/program');
				return TRUE;
			}
			
		}
		$this->template
			->title($this->module_details['name'])
			->set('program',$program)
			->set('status',$status)
			->build('admin/view');
	}
	else redirect('admin/program');
	}
	
	public function participants($slug=0){
		$program = $this->program_m->get_program_by_id($slug);
		$participants = $this->participants_m->get_by_rid($slug);
		
		$this->template->set('program',$program)
		->set('participants',$participants)
		->build('admin/participants');
	}
	
	public function participants_del($id)
	{	
		$return = $this->input->post('record_id');
		$ids = $this->input->post('action_to');
		if(!empty($ids))
		{
			$i = 0;
			$count = count($ids);
			foreach($ids as $id)
			{
				if($this->participants_m->delete($id))
				{
					$i++;
				}
			}
			$this->session->set_flashdata('success', sprintf(lang('participants.delete_success'), $i, $count));
		}
		else
		{
			if(!empty($id))
			{	
				$return = $this->input->get('return');
				$this->participants_m->delete($id);
				$this->session->set_flashdata('success', lang('participants.delete_success'));
			}
			else $this->session->set_flashdata('notice', lang('participants.action_error'));
		}
		redirect('admin/program/participants/'.$return);
	}
	
	public function reset($id='')
	{	
		if($id!='') {
		$this->program_m->set(array('status' => 1, 'limit' => 0), $id);
		$this->participants_m->delete_by_rid($id);
		}
		redirect('admin/program');
	}
}
