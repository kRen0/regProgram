<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 */
class Program extends Public_Controller {
    
    /**
     */
	
	 
	 private $register_validation_rules = array(	
		array(
			'field' => 'date',
			'label' => 'lang:program.date',
			'rules' => 'trim||required|numeric|max_length[11]'
		),
		array(
			'field' => 'first_name',
			'label' => 'lang:program.firstname',
			'rules' => 'trim|min_length[1]|max_length[100]|required'
		),
		array(
			'field' => 'last_name',
			'label' => 'lang:program.lastname',
			'rules' => 'trim|min_length[1]|max_length[100]|required'
		),
		array(
			'field' => 'email',
			'label' => 'lang:program.email',
			'rules' => 'trim|valid_email|required'
		),
		array(
			'field' => 'phone',
			'label' => 'lang:program.phone',
			'rules' => 'trim|min_length[4]|max_length[30]|required'
		),
		array(
			'field' => 'sex',
			'label' => 'lang:program.sex',
			'rules' => 'is_natural_no_zero|required'
		),
		array(
			'field' => 'id',
			'label' => 'id',
			'rules' => 'is_natural_no_zero|required'
		)
	);
	 
	 
    function __construct()
    {
        parent::__construct();
        $this->load->model('program_m');
        $this->load->model('participants_m');
        $this->lang->load('program');
		$this->load->library('session');
		$this->load->library('Ajax');
		
		//blog
        $this->load->model('blog/blog_m');
		$this->load->model('blog/blog_categories_m');
		$this->load->model('comments/comments_m');
		$this->load->library(array('keywords/keywords'));
		$this->lang->load('blog/blog');
		
        $this->load->library('form_validation');
    }
    

    
    /**

     */
	public function index()
	{

		// Get category data
		$categoryID = Settings::get('program_category');
		$category = $this->blog_categories_m->get($categoryID);

		// Count total blog posts and work out how many pages exist
		$pagination = create_pagination('program/', $this->blog_m->count_by(array(
			'category'=> $categoryID,
			'status' => 'live'
		)), NULL, 4);

		// Get the current page of blog posts
		$blog = $this->program_m->limit($pagination['limit'])->get_many_by(array(
			'category'=> $categoryID,
			'status' => 'live'
		));

		// Set meta description based on post titles
		$meta = $this->_posts_metadata($blog);
		
		foreach ($blog AS &$post)
		{
			$post->keywords = Keywords::get_links($post->keywords, 'blog/tagged');
		}

		// Build the page
		$this->template->title($this->module_details['name'], $category->title )
			->set_metadata('description', $category->title.'. '.$meta['description'] )
			->set_metadata('keywords', $category->title )
			->set('blog', $blog)
			->set('category', $category)
			->set('pagination', $pagination)
			->set('is_program_cat', true)
			->build('blog/category', $this->data );
	}	
	 
    public function view($slug = '', $nouser=false)
    {
		if ( ! $slug or ! $post = $this->blog_m->get_by('slug', $slug))
		{
			redirect('blog');
		}

		if ($post->status != 'live' && ! $this->ion_auth->is_admin())
		{
			redirect('blog');
		}
		
		// if it uses markdown then display the parsed version
		if ($post->type == 'markdown')
		{
			$post->body = $post->parsed;
		}

		// IF this post uses a category, grab it
		if ($post->category_id && ($category = $this->blog_categories_m->get($post->category_id)))
		{
			$post->category = $category;
		}

		// Set some defaults
		else
		{
			$post->category->id		= 0;
			$post->category->slug	= '';
			$post->category->title	= '';
		}

		$this->session->set_flashdata(array('referrer' => $this->uri->uri_string));

		$this->template->title($post->title, lang('blog_blog_title'))
			->set_metadata('description', $post->intro)
			->set_metadata('keywords', implode(', ', Keywords::get_array($post->keywords)))
			->set_breadcrumb(lang('blog_blog_title'), 'blog');

		if ($post->category->id > 0)
		{
			$this->template->set_breadcrumb($post->category->title, 'blog/category/'.$post->category->slug);
		}
		
		$post->keywords = Keywords::get_links($post->keywords, 'blog/tagged');
		$profile = NULL;
		if(!$nouser) {
		$user_id = $this->session->userdata('user_id');
		if(!empty($user_id))
		$profile = $this->participants_m->get_user_data($user_id);
		}
		
		$open = false;
		$count = $limit = 0;
	
		$dates = array();
		
		$D = $this->program_m->get_date_by_id($post->id);
		
		foreach ($D AS $date) {
		$count_on_date = $this->participants_m->count_by_did($date->id);
		$count += $count_on_date;
		$limit += $date->LIMIT;
		
		if(!($date->LIMIT + 0 ) || $date->LIMIT > $count_on_date) {
			$dates[$date->id] = $date->date;
			$open = true;
		}
		}
		
		$this->template
			->set_breadcrumb($post->title)
			->set('post', $post)
			->set('is_program', true)
			->set('dates', $dates)
			->set('open', $open)
			->set('count', $count)
			->set('limit', $limit)
			->set('profile', $profile)
			->set('slug', $slug)
			->build('blog/view', $this->data);
	}
	
	public function register($slug = '')
	{
		$this->form_validation->set_rules($this->register_validation_rules);
		if ($this->form_validation->run()) {
		if($this->input->post('date') != '0' AND !$this->open($this->input->post('id'))) {
			$this->message('error', lang('program.reg_closed'), '/program/view/'.$slug);
			return false;
		}
		if($this->input->post('date') != '0' AND !$this->program_m->dateId_is_true($this->input->post('id'),$this->input->post('date'))) {
			$this->message('error', lang('program.date_error'), '/program/view/'.$slug);
			return false;
		}
		if(!$this->participants_m->already_register($this->input->post('date'),$this->input->post('email'),$this->input->post('id'))) {
			if( $this->participants_m->insert($this->input->post()) ){
				$this->message('success', lang('program.registration_success'), '/program/view/'.$slug);
				return TRUE;
			}
		}
		else {
			$this->message('error', lang('program.already_register'), '/program/view/'.$slug);
			return FALSE;
		}
		}
		if($this->ajax->is_ajax_request()) $this->form_validation->set_error_delimiters('');
		if(!$this->message('error', lang('program.registration_error'), '',
			array('first_name' => form_error('first_name'),
			'last_name' => form_error('last_name'),
			'email' => form_error('email'),
			'phone' => form_error('phone'),
			'sex' => form_error('sex'),
			'id' => form_error('id'))
		))
			$this->view($slug, true);
		return FALSE;
	}
	public function calendar()
	{
			$dates = $this->program_m->get_all_date(Settings::get('program_category'));
			
			$profile = NULL;
			$user_id = $this->session->userdata('user_id');
			if(!empty($user_id))
				$profile = $this->participants_m->get_user_data($user_id);
			
			$this->template
			->append_metadata( css('core.css', 'program') )
			->append_metadata( css('cupertino/jquery-ui.cupertino.css', 'program') )
			->append_metadata( js('http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.1/jquery-ui.min.js'))
			->append_metadata( js('jMonthCalendar.js', 'program') )
			->append_metadata( js('jquery.simplemodal.js', 'program') )
			->set('dates', $dates)
			->set('profile', $profile)
			->build('calendar');
			
	}
	
	private function open($id)
	{
		$program = $this->program_m->get_date_by_id($id);
		if(!empty($program)) {
		$count = $this->participants_m->count_by_did($program[0]->id);
		if( !($program[0]->LIMIT + 0) || $program[0]->LIMIT > $count ) {
			return true;
		}
		}
		return false;
	}
	private function message($status, $message, $redirect='', $data = Array())
	{
		if($this->ajax->is_ajax_request()) {
			$this->ajax->build_json(array('status' => $status, 'message' => $message, 'data' => $data));
			return true;
		}
		else {
			$this->session->set_flashdata('error', lang('program.reg_closed'));
                            if($redirect != '') redirect($redirect);
			}
		return false;
	}
	private function _posts_metadata(&$posts = array())
	{
		$keywords = array();
		$description = array();

		// Loop through posts and use titles for meta description
		if(!empty($posts))
		{
			foreach($posts as &$post)
			{
				if($post->category_title)
				{
					$keywords[$post->category_id] = $post->category_title .', '. $post->category_slug;
				}
				$description[] = $post->title;
			}
		}

		return array(
			'keywords' => implode(', ', $keywords),
			'description' => implode(', ', $description)
		);
	}
	
	public function send_naty()
	{
	
	if (isset($_SERVER['REMOTE_ADDR'])) die('Permission denied.');
		$email_from = Settings::get("server_email");
		//$email_from_name = 'Alexander Strigin';
		$program_template = 'to_program';

		
		$participants = $this->participants_m->get_unnotifed_by_date(date('Y-m-d'));
		
		foreach($participants AS $participant) {
		 $data['name']			= 'no-reply';
				$data['slug'] 			= $program_template;
				$data['participant']	= $participant->FirstName .' '. $participant->LastName;
				$data['program']	= $participant->program;
				$data['program_date']	= $participant->date;
				

				$data['to']				= $participant->email;
				$data['from']			= $email_from;

				$results = Events::trigger('email', $data, 'array');
				
						
				foreach ($results as $result)
				{
					if ( ! $result)
					{					
						$this->session->set_flashdata('error', $result);
					}
					else {
						$this->participants_m->check_as_notifed($participant->id);
					}
				}
				
		}
		
	}
}//Тест