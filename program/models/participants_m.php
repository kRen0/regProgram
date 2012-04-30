<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 */
class Participants_m extends MY_Model {
    

    function __construct()
    {
        parent::__construct();
    }
    
	public function get_by_did($id){
	
		return $this->where('pdateID',$id)->get_all();

	}
	public function get_from_wait_list($id){
	
		return $this->where('recordID',$id)->get_all();

	}
	
	public function insert($input){
		if($input['date'] != '0') {
		return parent::insert(array(
			'pdateID'    => $input['date'],
			'LastName'    => $input['last_name'],
			'FirstName' => $input['first_name'],
			'sex' => $input['sex'],
			'phone' => $input['phone'],
			'email' => $input['email']
		));
		} else {
		return parent::insert(array(
			'recordID'    => $input['id'],
			'LastName'    => $input['last_name'],
			'FirstName' => $input['first_name'],
			'sex' => $input['sex'],
			'phone' => $input['phone'],
			'email' => $input['email']
		));
		}
	}
	public function get_user_data($id){
		$this->db->select('first_name, last_name, gender, phone, email');
		$this->db->from('users');
		$this->db->join('profiles', 'profiles.user_id = users.id', 'left');
		$this->db->where('users.id', $id);
		$this->db->group_by('users.id');
		return $query = $this->db->get()->row();
	}
	public function already_register($date_id, $email, $programID=0) {
		$this->db->from($this->_table);
		if($date_id!="0")
			$this->db->where('pdateID', $date_id);
		else
			$this->db->where('recordID', $programID);
		$this->db->where('email', $email);
		$count = $this->db->count_all_results();
		return $count>0;
	}
	public function delete_by_did($id) {
		$this->db->delete('participants', array('pdateID' => $id));
	}
		public function delete_by_rid($id) {
		$this->db->delete('participants', array('recordID' => $id));
	}
	public function count_by_did($id) {
		$this->db->from($this->_table);
		$this->db->where('pdateID', $id);
		return $this->db->count_all_results();
	}
	public function get_unnotifed_by_date($date, $interval ='+1 WEEK') {
		$this->db->select('participants.*, blog.title AS program, program_date.date');
		$this->db->join('program_date', 'program_date.id = participants.pdateID');
		$this->db->join('blog', 'blog.id = program_date.recordID');
		$this->db->from($this->_table);
		$this->db->where('date <', "ADDDATE($date, INTERVAL $interval)");
		$this->db->where('notified', 0);
		return $this->db->get()->result();
	}
	public function check_as_notifed($id) {
		$this->db->where('id', $id);
		$this->db->update($this->_table, array(
			'notified' => 1,
		));
	}
}//Тест