<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Common_model extends CI_Model {

  public $error;

  function __construct()
  {
      parent::__construct();
      $this->error = [];
      $this->load->database();
      $this->load->library('session');


    // chargement des requêtes définies dans application/config/query.sql;
      $this->queries = [];
      $query_file = APPPATH.'config/query.sql';
      $data = file($query_file);

      $current_key = NULL;
      $query = "";
      foreach($data as $line){
    // On cherche les lignes de la forme ### key
        if (preg_match('/### (\w+)/',$line,$key)){
          $current_key = $key[1];
    // On initialise la requête à vide
          $this->queries[$current_key] = '';
        } else if ($current_key!=NULL && strlen($line)>0 && $line[0]!=='#'){
    // Tout ce que l'on trouve après une clef, non vide et noncommenté
    // est ajouté à la requête correspondante
          $this->queries[$current_key] .= $line;
        }
      }
  }

  function log_error($query,$message){
  }

  public function query($name, $data){ // un peu de magie...
    // Cette fonction exécute la requête dont le nom est donné
    // Les paramètres nécessaires sont pris dans $data
    // Les requêtes dont définies dans un fichier de configuration
    // (voir le constructeur pour les détails)

    // On récupère la requête brute, avec des :champs dedans
      $sql_query = $this->queries[$name];
    // Il faut maintenant la réécrire avec de ?, et fournir
    // le tableau de valeurs (dans l'ordre) correspondant à chaque paramètre

    // Motif de recherche (multiligne) :quelquechose
      $pattern= '/:(\w+)/m';

    // On demande toutes les correspondances
      preg_match_all($pattern,$sql_query,$params,PREG_PATTERN_ORDER);
    // Pour chaque correspondance, on extrait de $data la valeur,
      foreach($params[1] as $p){
        $param[] = $data[$p];
      }

    // On remplace maintenant les :param de la requête par des ?
      $sql_query = preg_replace($pattern,'?',$sql_query);

    // On exécute la requête modifiée, en passant le tableau de paramètres
	    $query = $this->db->query($sql_query, $param);

    // retour du résultat
      return $query;
  }

  public function insert_id(){
      return $this->db->insert_id();
  }

  public function login(){
    $this->session->unset_userdata('userId');

    $email = $this->input->post('email'); //'vincent@email.com';
    $password = $this->input->post('password'); //'toto';

    $query = $this->common_model->query('check_user', $_POST);

    $userId =-1;
    $last = NULL;
    if ($query->num_rows()== 0){
      $this->common_model->query('register_user', $_POST);
      $userId = $this->common_model->insert_id();
    } else if ($query->first_row()->password == $password){
      $userId = $query->first_row()->id;
      $last = $query->first_row()->lastVisit;
    }

    if ($userId != -1){
      $this->session->set_userdata('userId', $userId);
      $this->session->set_userdata('email',$email);
      if ($last != NULL) $this->session->set_userdata('last', $last);
    }
  }

  public function logout(){
    $this->session->unset_userdata('userId');
  }

  public function get_logged_user(){
  if ($this->session->has_userdata('userId')){
    $data = [];
    $data['id'] = $this->session->userId;
    $data['email'] = $this->session->email;
    return $data;
  } else {
    return [];
  }
}


}
?>
