<?php
/**
* @author : Mohan J.<mohan212jadhav@gamil.com>
* @desc   : Base Model to handle all dabase related actions
* @date   : 28/05/2019
**/
defined('BASEPATH') OR exit('No direct script access allowed');

class BookModel extends CI_Model {

	var $table = 'books';
	var $column_order = array('book_name','author_name','book_status','issued_date','return_date',null); //set column field database for datatable orderable
	var $column_search = array('book_name','author_name'); //set column field database for datatable searchable just book_name , author_name , isbn are searchable
	var $order = array('id' => 'desc'); // default order 

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	/**
	* @author : Mohan J.<mohan212jadhav@gamil.com>
	* @desc   : Get records for in datatables formate along with order and its paging.
	* @date   : 28/05/2019
	* @param  : Form input
	* @return : Query result
	**/
	private function _get_datatables_query()
	{
		
		$this->db->from($this->table);

		$i = 0;
	
		foreach ($this->column_search as $item) // loop column 
		{
			if($_POST['search']['value']) // if datatable send POST for search
			{
				
				if($i===0) // first loop
				{
					$this->db->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
					$this->db->like($item, $_POST['search']['value']);
				}
				else
				{
					$this->db->or_like($item, $_POST['search']['value']);
				}

				if(count($this->column_search) - 1 == $i) //last loop
					$this->db->group_end(); //close bracket
			}
			$i++;
		}
		
		if(isset($_POST['order'])) // here order processing
		{
			$this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} 
		else if(isset($this->order))
		{
			$order = $this->order;
			$this->db->order_by(key($order), $order[key($order)]);
		}
	}

	/**
	* @author : Mohan J.<mohan212jadhav@gamil.com>
	* @desc   : List of all books from database
	* @date   : 28/05/2019
	* @return : {Query result : array}
	**/
	function get_datatables()
	{
		$this->_get_datatables_query();
		if($_POST['length'] != -1)
		$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	/**
	* @author : Mohan J.<mohan212jadhav@gamil.com>
	* @desc   : Filtered books count after seach in datatables search.
	* @date   : 28/05/2019
	* @return : {Total count : int }
	**/
	function count_filtered()
	{
		$this->_get_datatables_query();
		$query = $this->db->get();
		return $query->num_rows();
	}

	/**
	* @author : Mohan J.<mohan212jadhav@gamil.com>
	* @desc   : Total count for databales pagination
	* @date   : 28/05/2019
	* @return : {Total count : int }
	**/
	public function count_all()
	{
		$this->db->from($this->table);
		return $this->db->count_all_results();
	}

	/**
	* @author : Mohan J.<mohan212jadhav@gamil.com>
	* @desc   : Get the row details for edit the individual book details
	* @date   : 28/05/2019
	* @param  : {$id : int}
	* @return : {query data : object }
	**/
	public function get_by_id($id)
	{
		$this->db->from($this->table);
		$this->db->where('id',$id);
		$query = $this->db->get();
		return $query->row();
	}

	/**
	* @author : Mohan J.<mohan212jadhav@gamil.com>
	* @desc   : Save the book details in database
	* @date   : 28/05/2019
	* @param  : {$data : array}
	* @return : {last insert id : int }
	**/
	public function save($data)
	{
		$this->db->insert($this->table, $data);
		return $this->db->insert_id();
	}

	/**
	* @author : Mohan J.<mohan212jadhav@gamil.com>
	* @desc   : Update the individual book details
	* @date   : 28/05/2019
	* @param  : {$where : int, $data : array}
	* @return : {last affected id : int }
	**/
	public function update($where, $data)
	{

		$this->db->update($this->table, $data, $where);
		return $this->db->affected_rows();
	}

	/**
	* @author : Mohan J.<mohan212jadhav@gamil.com>
	* @desc   : Delete the individual book details
	* @date   : 28/05/2019
	* @param  : {$id : int}
	**/
	public function delete_by_id($id)
	{
		$this->db->where('id', $id);
		$this->db->delete($this->table);
	}

}
