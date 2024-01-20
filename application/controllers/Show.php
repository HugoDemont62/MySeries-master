<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Show extends CI_Controller {


	public function detail($id_serie,$saison)
	{
		$this->load->library('my_queries');
    $this->load->model('user');
		$this->load->model('serie');

		$this->my_queries->require_query('get_serie');

	  $data=$this->user->get_logged_user();

		$data['serie'] = $this->serie->get($id_serie);
		$data['genre'] =  $this->serie->get_genre($id_serie);
		$data['cast'] = $this->serie->get_cast($id_serie);
		$data['crew'] = $this->serie->get_crew_list($id_serie);
		$data['season'] = $this->serie->get_season_list($id_serie);
		$data['episode'] = $this->serie->get_episode_list($id_serie,$saison);
		$data['next'] = $this->serie->get_next_episode($id_serie);
		$data['saison'] = $saison;


    $this->load->view('header',$data);
		$this->load->view('serie',$data);
		$this->load->view('footer');
	}

	public function follow($id_serie,$saison){
		$this->load->helper('url');
		$this->load->model('user');
		if(isset($_POST['follow'])&& $this->user->is_logged()){
			$this->user->follow($_POST['follow'],$id_serie);
		}
		redirect("serie/$id_serie/$saison");
	}

	public function vu($id_serie,$saison,$id_episode){
		$this->load->helper('url');
		$this->load->model('user');
		$this->load->model('serie');
		if($this->user->is_logged()){
				$this->serie->watched(isset($_POST['vu']),$id_episode);
		}
		redirect("serie/$id_serie/$saison");
	}

	public function update($id_serie,$saison=1){
		$this->load->helper('url');
		$this->load->library('my_queries');
		$this->my_queries->update_serie($id_serie);

		redirect("serie/$id_serie/$saison");
	}


}
