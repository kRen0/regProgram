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
		$this->db->join('participants', 'participants.recordID = blog.id', 'left');
		$this->db->join('program', 'program.recordID = blog.id', 'left');
		$this->db->where('category_id', $categoryID);
		$this->db->group_by('blog.id');
		$this->db->order_by('open', 'DESC');
		$this->db->order_by('p_count', 'DESC');
		$this->db->limit(5);
		return $query = $this->db->get('blog')->result();
    }
	
	public function get_program($categoryID)
    {
		$this->db->select('blog.id, title, slug, open, LIMIT, COUNT('.$this->db->dbprefix('participants').'.id) AS p_count');
		$this->db->from('blog');
		$this->db->join('program', 'program.recordID = blog.id', 'left');
		$this->db->join('participants', 'participants.recordID = blog.id', 'left');
		$this->db->where('category_id', $categoryID);
		$this->db->group_by('blog.id');
		$this->db->order_by('title');
		return $query = $this->db->get()->result();
    }
	
	public function get_program_by_id($id)
    {
		$this->db->select('blog.id, title, slug, open, LIMIT, COUNT('.$this->db->dbprefix('participants').'.id) AS p_count');
		$this->db->from('blog');
		$this->db->join('program', 'program.recordID = blog.id', 'left');
		$this->db->join('participants', 'participants.recordID = blog.id', 'left');
		$this->db->where('blog.id', $id);
		$this->db->group_by('blog.id');
		$this->db->order_by('title');
		return $query = $this->db->get()->row();
    }
    
	public function get_categories()
    {
		return $this->db->order_by('title', 'DESC')
                    ->get('blog_categories')->result();
    }
	
	public function set($input, $id){
	
	$this->where('recordID', $id);
	$this->from('program');
	$is_set = $this->count_all_results();
	if(!$is_set) {
	return $this->db->insert('program', array(
			'recordID'    => $id,
			'open' => $input['status'],
			'LIMIT' => $input['limit']
		));
	}
	else {
		$this->where('recordID', $id);
	return $this->db->update('program', array(
			'open' => $input['status'],
			'LIMIT' => $input['limit']
		));
	}
	}
	
	function get_many_by($params = array())
	{
		$this->load->helper('date');
		
		$this->db->select("blog.*,
		blog_categories.id AS category_id,
		blog_categories.slug AS category_slug,
		blog_categories.title AS category_title,
		count({$this->db->dbprefix('participants')}.recordID) AS p_count,
		count({$this->db->dbprefix('comments')}.id) AS comments_count,
		program.open,
		program.LIMIT
		");
		$this->db->from('blog');
		$this->db->join('blog_categories', 'blog_categories.id=blog.category_id', 'left');
		$this->db->join('program AS program', 'program.recordID=blog.id', 'left');
		$this->db->join('participants', 'participants.recordID=blog.id', 'left');
		$this->db->join('comments', 'comments.module_id=blog.id', 'left');
		
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
}