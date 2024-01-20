<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Model {

  public $error;

  function __construct()
  {
      parent::__construct();
      $this->load->library('session');
      $this->load->library('my_queries');
  }

  public function login(){
    $this->session->unset_userdata('userId');

    $email = $this->input->post('email'); //'vincent@email.com';
    $password = $this->input->post('password'); //'toto';

    $query = $this->my_queries->query('check_user', $_POST);

    $userId =-1;
    $last = NULL;
    if ($query->num_rows()== 0){ // nouvel utilisateur, on enregistre
      $this->my_queries->query('register_user', $_POST);
      $userId = $this->my_queries->insert_id();
    } else if ($query->first_row()->ok){
      $userId = $query->first_row()->id;
      $last = $query->first_row()->lastVisit;
      $this->my_queries->query('update_visit', ['id'=>$userId]);
    } else {
      var_dump($query->first_row());
    }

    if ($userId != -1){
      $this->session->set_userdata('userId', $userId);
      $this->session->set_userdata('email',$email);
      $this->session->set_userdata('lastVisit',$last);
    }
  }

  public function logout(){
    $this->session->unset_userdata('userId');
  }

  public function is_logged(){
    return ($this->session->has_userdata('userId'));
  }

  public function get_logged_user(){
    if ($this->session->has_userdata('userId')){
      $data = [];
      $data['id'] = $this->session->userId;
      $data['email'] = $this->session->email;
      $data['lastVisit'] = $this->session->lastVisit;
      return $data;
    } else {
      return [];
    }
  }

  public function follow($value,$idSerie){
    if (!$this->is_logged()) return;
    $this->my_queries->query(($value == "true" ? 'follow' : 'unfollow'),
        ['idUser' =>$this->session->userId, 'idSerie'=>$idSerie]);
  }

}
