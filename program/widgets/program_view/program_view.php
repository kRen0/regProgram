<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 */

class Widget_Program_view extends Widgets
{
	public $title		= array(
			'en' => 'Program list',
			'ru' => 'Список программ'
		);
    public $description	= array(
		'en' => '',
		'ru' => 'Отображает на сайте список имеющихся программ.'
		);
	public $author = 'kRen0';
	public $website='';
	public $version = '1.0';


        public function run()
        {
			$this->load->library('settings/settings');
			$this->load->model('settings/settings_m');
			//print_r($this->settings_m->get_many_by('program'));
			//die();
			$categoryID = Settings::get('program_category');
			$this->load->model('program/program_m');
			$categories = $this->program_m->get_program_list_view($categoryID);
			return array('categories' => $categories);
			
        }
}