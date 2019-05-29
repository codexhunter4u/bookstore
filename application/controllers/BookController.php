<?php
/**
* @author : Mohan J.<mohan212jadhav@gamil.com>
* @desc : Base controller to manage the books activity
* @date : 28/05/2019 
**/
defined('BASEPATH') OR exit('No direct script access allowed');

class BookController extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('BookModel','books');
	}

	public function index()
	{
		$this->load->view('booksView');
		$this->load->view('addEditForm');
	}

	/**
	* @author : Mohan J.<mohan212jadhav@gamil.com>
	* @desc   : Add books
	* @date   : 28/05/2019
	* @param  : Form Input data
	* @return : TRUE/FALSE
	**/
	public function add_books()
	{
		$this->_validate();
		$data = array(
			'book_name' => $this->input->post('book_name'),
			'author_name' => $this->input->post('author_name'),
			'book_status' => '0',
		);
		$insert = $this->books->save($data);
		echo json_encode(array("status" => TRUE));
	}

	/**
	* @author : Mohan J.<mohan212jadhav@gamil.com>
	* @desc : Get the default book list.
	* @date : 28/05/2019
	* @return array of result
	**/
	public function getBookList()
	{
		$list = $this->books->get_datatables();
		$data = array();
		$no = $_POST['start'];
		foreach ($list as $books) {
			$no++;
			$row = array();
			$row[] = $books->book_name;
			$row[] = $books->author_name;
			$row[] = empty($books->issued_date) ? ' - ' : $books->issued_date;
			$row[] = empty($books->return_date) ? ' - ' : $books->return_date;
			$row[] = ($books->book_status == 0) ? '<span class="available">Available<span>' : '<span class="not-available">Issued</span>';
			$btn = $books->book_status == 0 ? 'disabled' : '';
			//add html for action buttons
			$row[] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit" onclick="edit_books('."'".$books->id."'".')"><i class="glyphicon glyphicon-pencil"></i></a>
				  <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Delete" onclick="delete_books('."'".$books->id."'".')"><i class="glyphicon glyphicon-trash"></i></a>
				  <a class="btn btn-sm btn-success" href="javascript:void(0)" title="Issue Book" onclick="issue_books('."'".$books->id."'".')"><i class="glyphicon glyphicon-level-up"></i></a>
				  <a '.$btn.' class="btn btn-sm btn-danger" href="javascript:void(0)" title="Return book" onclick="return_books('."'".$books->id."'".')"><i class="glyphicon glyphicon-download-alt"></i></a>';
		
			$data[] = $row;
		}

		$output = array(
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->books->count_all(),
			"recordsFiltered" => $this->books->count_filtered(),
			"data" => $data,
		);
		//output to json format
		echo json_encode($output);
	}

	/**
	* @author : Mohan J.<mohan212jadhav@gamil.com>
	* @desc   : Issue book to student
	* @date   : 28/05/2019
	* @param  : $id  Unique Id of book to issue the book
	* @return : TRUE/FALSE
	**/
	public function issue_books($id)
	{
		
		$check = $this->_isAvailable($id);
		
		if($check == 1){
			echo json_encode(array("status" => FALSE));
			exit;
		}

		$data = array(
			'book_status' => '1',
			'issued_date' => date('Y-m-d H:i:s'),
			'return_date' => '',
		);
		$this->books->update(array('id' => $id), $data);
		echo json_encode(array("status" => TRUE));
	}

	/**
	* @author : Mohan J.<mohan212jadhav@gamil.com>
	* @desc   : Return book from student
	* @date   : 28/05/2019
	* @param  : $id  Unique Id of book to return the book
	* @return : TRUE/FALSE
	**/
	public function return_books($id)
	{
		$data = array(
			'book_status' => '0',
			'return_date' => date('Y-m-d H:i:s'),
		);
		$this->books->update(array('id' => $id), $data);
		echo json_encode(array("status" => TRUE));
	}

	/**
	* @author : Mohan J.<mohan212jadhav@gamil.com>
	* @desc   : Get the row data to edit the book details.
	* @date   : 28/05/2019
	* @param  : Form input the data to edit
	* @return : TRUE/FALSE
	**/
	public function edit_books($id)
	{
		$data = $this->books->get_by_id($id);
		echo json_encode($data);
	}

	/**
	* @author : Mohan J.<mohan212jadhav@gamil.com>
	* @desc   : Update the book details
	* @date   : 28/05/2019
	* @param  : Form input the data to update
	* @return : TRUE/FALSE
	**/
	public function update_books()
	{
		$this->_validate();
		$data = array(
			'book_name' => $this->input->post('book_name'),
			'author_name' => $this->input->post('author_name'),
		);
		$this->books->update(array('id' => $this->input->post('id')), $data);
		echo json_encode(array("status" => TRUE));
	}

	/**
	* @author : Mohan J.<mohan212jadhav@gamil.com>
	* @desc   : Check the book status is available or not
	* @date   : 28/05/2019
	* @param  : $id Book ID
	* @return : {book_status: int}
	**/
	public function _isAvailable($id){
		$data = $this->books->get_by_id($id);
		return $data->book_status;
	}

	/**
	* @author : Mohan J.<mohan212jadhav@gamil.com>
	* @desc   : Delete the books
	* @date   : 28/05/2019
	* @param  : $id Book ID
	* @return : TRUE/FALSE
	**/
	public function delete_books($id)
	{
		$this->books->delete_by_id($id);
		echo json_encode(array("status" => TRUE));
	}

	/**
	* @author : Mohan J.<mohan212jadhav@gamil.com>
	* @desc   : Validate the form empty fileds
	* @date   : 28/05/2019
	* @param  : Form inout
	* @return : TRUE/FALSE
	**/
	private function _validate()
	{
		$data = array();
		$data['error_string'] = array();
		$data['inputerror'] = array();
		$data['status'] = TRUE;

		if($this->input->post('book_name') == '')
		{
			$data['inputerror'][] = 'book_name';
			$data['error_string'][] = 'Book name is required';
			$data['status'] = FALSE;
		}

		if($this->input->post('author_name') == '')
		{
			$data['inputerror'][] = 'author_name';
			$data['error_string'][] = 'Author name is required';
			$data['status'] = FALSE;
		}

		if($data['status'] === FALSE)
		{
			echo json_encode($data);
			exit();
		}
	}

}
