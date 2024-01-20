<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class My_queries {

  protected $CI;

        // We'll use a constructor, as you can't directly call a function
        // from a property definition.
  public function __construct()
  {
    // Assign the CodeIgniter super-object
    $this->CI =& get_instance();

    // Connection base de donnée / chargement bibliothèque sur les sessions
    $this->CI->load->database();
    $this->CI->load->library('session');

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
      $param = [];
      foreach($params[1] as $p){
        $param[] = $data[$p];
      }

    // On remplace maintenant les :param de la requête par des ?
      $sql_query = preg_replace($pattern,'?',$sql_query);

      if (strlen(trim($sql_query))==0) { // Si la requête n'a pas encore été écrite
        return null; //show_error("requete absente : $name");
      }
      // On exécute la requête modifiée, en passant le tableau de paramètres
	    $query = $this->CI->db->query($sql_query, $param);

    // retour du résultat
      return $query;
  }


  public function has($name){
    return isset($this->queries[$name]) && strlen(trim($this->queries[$name]))>0;
  }

  public function require_query($name){
    if (!isset($this->queries[$name])){
        show_error("Requete non trouvée : $name\n".
                  "Vous avez probablement supprimé le commentaire correspondant ".
                  "dans le fichier 'query.sql'");
    }
    $sql_query = $this->queries[$name];
    if (strlen(trim($sql_query))==0) {
      show_error("Désolé, la requête '$name' est nécessaire au bon fonctionnement de cette page");
    }
  }

  public function insert_id(){
      return $this->CI->db->insert_id();
  }

  public function update_serie($id){
    $path=APPPATH.'helpers';
    exec("cd $path;node db_update.js '# $id'",$result);

    $commands = explode(";\n",join("\n",$result));
    foreach($commands as $command){
      $this->CI->db->simple_query($command.';');
    }
  }

  public function search_serie($query){
    $path=APPPATH.'helpers';
    exec("cd $path;node db_update.js '$query'",$result);
    return json_decode(join("\n",$result));
  }


}
