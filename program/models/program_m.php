<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 */
class Program_m extends MY_Model {
    

    function __construct()
    {
        parent::__construct();
    }
    
    
    public function get_program_list_view($categoryID)
    {
		$this->db->select('title, slug, intro, COUNT('.$this->db->dbprefix('participants').'.id) AS p_count');
		$this->db->join('program_date', 'program_date.recordID = blog.id', 'left');
		$this->db->join('participants', 'participants.pdateID = program_date.id', 'left');
		$this->db->where('category_id', $categoryID);
		$this->db->group_by('blog.id');
		$this->db->order_by('COUNT('.$this->db->dbprefix('program_date').'.id) > 0 AND (SUM(`LIMIT`) = 0 OR SUM(`LIMIT`) > COUNT('.$this->db->dbprefix('participants').'.id))', 'DESC');
		$this->db->order_by('p_count', 'DESC');
		$this->db->limit(5);
		return $query = $this->db->get('blog')->result();
    }
	
	public function get_program($categoryID, $fake_date = false)
    {
		$this->db->select('blog.id, title, slug, COUNT(`date`)>0 AS open, SUM(`LIMIT`) AS `LIMIT`, COUNT('.$this->db->dbprefix('participants').'.id) AS p_count, (MIN(`LIMIT`) < 1) AS unlimited'.
		($fake_date?', COUNT(participants2.id) AS w_p_count':''));
		$this->db->from('blog');
		$this->db->join('program_date', 'program_date.recordID = blog.id', 'left');
		$this->db->join('participants', 'participants.pdateID = program_date.id', 'left');
		$this->db->where('category_id', $categoryID);
		if ($fake_date)
			{
				$this->db->join('participants AS participants2', 'participants2.recordID = blog.id', 'left');
				$this->db->or_where('participants2.pdateID', 'NULL');
			}
		$this->db->group_by('blog.id');
		$this->db->order_by('title');
		return $query = $this->db->get()->result();
    }
	
	public function get_program_by_id($id)
    {
		$this->db->select('blog.id, title, slug');
		$this->db->from('blog');
		$this->db->where('blog.id', $id);
		$this->db->group_by('blog.id');
		$this->db->order_by('title');
		return $query = $this->db->get()->row();
    }
	
	public function get_date_by_id($id, $fake_date = false)
    {
		$this->db->select('program_date.id, date, YEAR(`date`) AS year, MONTH(`date`) AS month,  DAY(`date`) AS day, LIMIT');
		$this->db->from('program_date');
		$this->db->where('program_date.RecordID', $id);
		if($fake_date) {
			$this->db->join('participants', 'participants.pdateID = program_date.id', 'right');
			$this->db->or_where('participants.RecordID', $id);
		}
		$this->db->order_by('date');
		return $query = $this->db->get()->result();
    }
	
	public function get_all_date($categoryID)
    {
		$this->db->select('blog.id AS id, program_date.id AS dateID, date, LIMIT, title, intro');
		$this->db->from('blog');
		$this->db->join('program_date AS program_date', 'program_date.recordID = blog.id');
		$this->db->where('category_id', $categoryID);
		$this->db->where('status', 'live');
		$this->db->where('(`LIMIT` = 0 OR `LIMIT` > (SELECT COUNT(participants.id) FROM '.$this->db->dbprefix('participants').' AS participants WHERE participants.pdateID = program_date.id) )');
		$this->db->group_by('program_date.id');
		$this->db->order_by('date');
		return $query = $this->db->get()->result();
    }
    
	public function get_categories()
    {
		return $this->db->order_by('title', 'DESC')
                    ->get('blog_categories')->result();
    }
	
	public function set($input, $id, $dates = NULL){
	
	$count = 0;
	$success = true;
	$this->db->where('recordID', $id);
	$this->db->where_not_in('date',$input['date']);
	$this->db->delete('program_date');
	$D = ($dates!=NULL?$dates:$input['date']);
	foreach ($D AS $date) {
	$this->where('recordID', $id);
	$this->where('date', $date);
	$this->from('program_date');
	$is_set = $this->count_all_results();
	if(!$is_set) {
	$success =  ($this->db->insert('program_date', array(
			'recordID'    => $id,
			'date' => $date,
			'LIMIT' => $input['limit'][$count]
		)))?$success:false;
	}
	else {
		$this->where('recordID', $id);
		$this->where('date', $date);
	$success = ($success AND $this->db->update('program_date', array(
			'date' => $date,
			'LIMIT' => $input['limit'][$count]
		)))?$success:false;
	}
	$count++;
	}
	return $success;
	}
	
	function get_many_by($params = array())
	{
		$this->load->helper('date');
		
		$this->db->select("blog.*,
		blog_categories.id AS category_id,
		blog_categories.slug AS category_slug,
		blog_categories.title AS category_title,
		count(participants.pdateID) AS p_count,
		count(comments.id) AS comments_count,
		COUNT(program.id) > 0 AND (SUM(`LIMIT`) = 0 OR SUM(`LIMIT`) > COUNT(participants.id)) AS open,
		SUM(program.`LIMIT`) AS `LIMIT`
		");
		$this->db->from('blog');
		$this->db->join('blog_categories', 'blog_categories.id=blog.category_id', 'left');
		$this->db->join('program_date AS program', 'program.recordID=blog.id', 'left');
		$this->db->join('participants AS participants', 'participants.pdateID=program.id', 'left');
		$this->db->join('comments AS comments', 'comments.module_id=blog.id', 'left');
		
		if (!empty($params['category']))
		{
			if (is_numeric($params['category']))
				$this->db->where('blog_categories.id', $params['category']);
			else
				$this->db->where('blog_categories.slug', $params['category']);
		}

		if (!empty($params['month']))
		{
			$this->db->where('MONTH(FROM_UNIXTIME(blog.created_on))', $params['month']);
		}

		if (!empty($params['year']))
		{
			$this->db->where('YEAR(FROM_UNIXTIME(blog.created_on))', $params['year']);
		}

		// Is a status set?
		if (!empty($params['status']))
		{
			// If it's all, then show whatever the status
			if ($params['status'] != 'all')
			{
				// Otherwise, show only the specific status
				$this->db->where('status', $params['status']);
			}
		}

		// Nothing mentioned, show live only (general frontend stuff)
		else
		{
			$this->db->where('status', 'live');
		}

		// By default, dont show future posts
		if (!isset($params['show_future']) || (isset($params['show_future']) && $params['show_future'] == FALSE))
		{
			$this->db->where('blog.created_on <=', now());
		}
		
		$this->db->group_by('blog.id');
		
		$this->db->order_by('open', 'DESC');
		$this->db->order_by('blog.created_on', 'DESC');
		
		// Limit the results based on 1 number or 2 (2nd is offset)
		if (isset($params['limit']) && is_array($params['limit']))
			$this->db->limit($params['limit'][0], $params['limit'][1]);
		elseif (isset($params['limit']))
			$this->db->limit($params['limit']);

		return $this->db->get()->result();
	}
	public function dateId_is_true($id, $date_id)
    {
		$this->db->from('program_date');
		$this->db->where('RecordID', $id);
		$this->db->where('id', $date_id);
		$count = $this->db->count_all_results();
		return $count>0;
    }
}//Тест