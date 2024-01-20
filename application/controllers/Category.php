<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Category extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */


	public function index(){
		$this->query();
	}

	public function query($tag = null)
	{
		$this->load->model('user');
		$this->load->model('serie');
		$data=$this->user->get_logged_user();

		$data['categories'] = $this->serie->get_all_categories();
		$data['current_cat'] = null;
    if (isset($tag) && $tag != null){
			$data['current_cat'] = $tag;

			$lastVisit = isset($data['lastVisit']) ? $data['lastVisit'] : null;
			if ($lastVisit==null || $lastVisit=='') $lastVisit=date('Y-m-d',strtotime('-7 day'));

			$data['serie_list'] = $this->serie->get_by_genre($tag,$lastVisit);
    }

    $this->load->view('header',$data);
    $this->load->view('category',$data);
		if (isset($data['serie_list'])) $this->load->view('gallery',$data);
    $this->load->view('footer');
	}


}
