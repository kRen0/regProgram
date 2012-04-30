<?php defined('BASEPATH') or exit('No direct script access allowed');

class Module_Program extends Module {

	public $version = '1.0';

	private $program_settings = array();
	public function info()
	{
		return array(
			'name' => array(
				'en' => 'Registration for the program',
				'ru' => 'Регистрация на программы'
			),
			'description' => array(
				'en' => '- no description -',
				'ru' => 'Модуль, для реализации возможности регистрации на программы сайта',
			),
			'frontend' => TRUE,
			'backend' => TRUE,
			'menu' => 'program',
			'author' => 'kReno',
			
			'sections' => array(
				'settings' => array(
					'name'=>'settings_title',
					'uri'=>'admin/program/settings',
				),
				'program'   => array(
					'name' => 'program_title',
					'uri'  => 'admin/program',
				)
			)
		);
	}

	public function install()
	{
		
		/*
		*/
		
		$this->load->library('settings/settings');
		
		$this->program_settings = array(
			
			array('slug' => 'program_category','title' => 'Program Category','description' => '','type' => 'text', 'default' => '0', 'value' => '', 'options' => '', 'is_required' => '1', 'is_gui' => '1', 'module' => 'program' )
		);
		
		$this->dropOldSettings($this->program_settings);
		foreach( $this->program_settings as $program_setting){
		
			$this->settings->add($program_setting);
		}
		
		$this->dbforge->drop_table('program_date');
		$this->dbforge->drop_table('participants');
		
		$program_date = array(
            'id' => array(
            'type' => 'INT',
                'constraint' => '11',
                'auto_increment' => TRUE
            ),
            'recordID' => array(
                'type' => 'INT',
                'constraint' => '11'
            ),
            'date' => array(
                'type' => 'DATE'
            ),
			'LIMIT' => array(
                'type' => 'INT',
				'constraint' => '6'
            )
        );
		
		$this->dbforge->add_field($program_date);
        $this->dbforge->add_key('id', TRUE);

        // Let's try running our DB Forge Table and inserting some settings
        if ( ! $this->dbforge->create_table('program_date') )
        {
            return FALSE;
        }
		
		$participants = array(
            'id' => array(
            'type' => 'INT',
                'constraint' => '11',
                'auto_increment' => TRUE
            ),
			'pdateID' => array(
				'type' => 'INT',
                'constraint' => '11',
				'null' => TRUE,
            ),
			'recordID' => array(
				'type' => 'INT',
                'constraint' => '11',
				'null' => TRUE,
            ),
            'LastName' => array(
                'type' => 'VARCHAR',
                'constraint' => '100'
            ),
			'FirstName' => array(
                'type' => 'VARCHAR',
                'constraint' => '100'
            ),
            'sex' => array(
                'type' => 'INT',
				'constraint' => '1'
            ),
			'phone' => array(
                'type' => 'VARCHAR',
				'constraint' => '30'
            ),
			'email' => array(
                'type' => 'VARCHAR',
				'constraint' => '100'
            ),
			'notified' => array(
                'type' => 'INT',
				'constraint' => '1',
				'default' => 0
            )
        );
		
		$this->dbforge->add_field($participants);
        $this->dbforge->add_key('id', TRUE);

        // Let's try running our DB Forge Table and inserting some settings
        if ( ! $this->dbforge->create_table('participants') )
        {
            return FALSE;
        }
		
		/*$sql =
			"delimiter |
			CREATE TRIGGER delete_program before delete ON {$this->db->dbprefix('blog')}
			FOR EACH ROW BEGIN
				DELETE FROM {$this->db->dbprefix('participants')} WHERE {$this->db->dbprefix('participants')}.recordID = OLD.id;
				DELETE FROM {$this->db->dbprefix('program_date')} WHERE {$this->db->dbprefix('program_date')}.recordID = OLD.id;
			END |
			CREATE TRIGGER delete_program_date before delete ON {$this->db->dbprefix('program_date')}
			FOR EACH ROW BEGIN
				DELETE FROM {$this->db->dbprefix('participants')} WHERE {$this->db->dbprefix('participants')}.pdateID = OLD.id;
			END |";
		
		$this->db->query($sql);	*/	
		
		return true;
		
	}

	private function dropOldSettings($settings){
	
		foreach($settings as $setting){
		
			$this->settings->delete($setting['slug']);
		}
	}
	public function uninstall()
	{
		$this->dbforge->drop_table('program_date');
		$this->dbforge->drop_table('participants');
		$this->dropOldSettings($this->program_settings);
		//$sql ="DROP TRIGGER delete_program;
		//DROP TRIGGER delete_program_date;"; 
		//$this->db->query($sql);
		return TRUE;
	}


	public function upgrade($old_version)
	{
		// Your Upgrade Logic
		return TRUE;
	}

	public function help()
	{
		// Return a string containing help info
		// You could include a file and return it here.
		return "I helped you?";
	}
}
/* End of file details.php */