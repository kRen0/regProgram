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
			'field' => 'date[]',
			'label' => 'lang:program.opened',
			'rules' => 'trim'
		),
		array(
			'field' => 'limit[]',
			'label' => 'lang:program.limit',
			'rules' => 'integer'
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
		$program = $this->program_m->get_program(Settings::get('program_category'),true);
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
		$dates = $this->program_m->get_date_by_id($program->id);
		/*$status = array (0=>lang('program.opened.n'),1=>lang('program.opened.y'));*/
		
		$post = $this->input->post();
		
		if ($this->form_validation->run()) {
			$D = $this->check_date($this->input->post('date'));
			if($D !=NULL) {
				if( $this->program_m->set($this->input->post(), $id, $D)){
					$this->session->set_flashdata('success', lang('program.edit_success'));
								redirect('admin/program');
					return TRUE;
				}
				else 
					{
						$this->session->set_flashdata('error', lang('program.edit_error'));
					}
			}
			else 
				$this->session->set_flashdata('error', lang('program.editdate_error'));
			
		}

		$this->template
			->title($this->module_details['name'])
			->append_metadata( js('http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.1/jquery-ui.min.js') )
			->append_metadata( js('jquery.ui.datepicker-ru.js', 'program'))
			->set('program',$program)
			->set('dates',$dates)
			->set('post',$post)
			->build('admin/view');
	}
	else redirect('admin/program');
	}
	
	public function participants($slug=0) {
		$program = $this->program_m->get_program_by_id($slug);
		$dates = $this->program_m->get_date_by_id($slug,true);
		$participants = array();
		foreach ($dates AS $date)
		$participants[$date->id] = $this->participants_m->get_by_did($date->id);
		
		$participants[0] = $this->participants_m->get_from_wait_list($slug);
		
		$this->template->set('program',$program)
		->set('dates',$dates)
		->set('participants',$participants)
		->build('admin/participants');
	}
	
	public function participants_del($id=NULL)
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
		$dates = $this->program_m->get_date_by_id($id);
		foreach($dates AS $date)
			$this->participants_m->delete_by_did($date->id);
		$this->participants_m->delete_by_rid($id);
		
		$this->program_m->set(array(), $id);
		}
		redirect('admin/program');
	}
	
	private function check_date($dates)
	{
	if(!empty($dates)) {
		$D=array();
		foreach($dates AS $date) {
		if (preg_match ("/^([0-9]{1,2}).([0-9]{1,2}).([0-9]{4})/", $date, $regs)) {
			$D[] = "$regs[3]-$regs[2]-$regs[1]";
		} else {
			return NULL;
		}
		}
		return $D;
		}
		return 1;
	}

}
//Тест