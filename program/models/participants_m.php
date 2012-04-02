<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 */
class Participants_m extends MY_Model {
    

    function __construct()
    {
        parent::__construct();
    }
    
	public function get_by_rid($id){
	
		return $this->where('recordID',$id)->get_all();

	}
	public function insert($input){
	
		return parent::insert(array(
			'recordID'    => $input['id'],
			'LastName'    => $input['last_name'],
			'FirstName' => $input['first_name'],
			'sex' => $input['sex'],
			'phone' => $input['phone'],
			'email' => $input['email']
		));
	}
	public function get_user_data($id){
		$this->db->select('first_name, last_name, gender, phone, email');
		$this->db->from('users');
		$this->db->join('profiles', 'profiles.user_id = users.id', 'left');
		$this->db->where('users.id', $id);
		$this->db->group_by('users.id');
		return $query = $this->db->get()->row();
	}
	public function already_register($program_id, $email) {
		$this->db->from($this->_table);
		$this->db->where('recordID', $program_id);
		$this->db->where('email', $email);
		$count = $this->db->count_all_results();
		return $count>0;
	}
	public function delete_by_rid($id) {
		$this->db->delete('participants', array('recordID' => $id));
	}
}