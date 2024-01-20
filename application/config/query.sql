-- noinspection SqlDialectInspectionForFile

-- noinspection SqlNoDataSourceInspectionForFile


############################## Page d accueil #################################

### get_all_series
# Obtient les informations de toutes les séries.
# On triera les séries par age décroissant, et on retourne toutes les données
# des séries
#
# Optionnel :
#   On fournit en plus le nombre d épisodes sortis depuis
#   la dernière connection
#
#
# Paramètre
#   :limit        Nombre de séries à retourner
#   :lastVisit    Dernière connection
#
# Champs attendus
#   serie         tous
#   new           le nombre d épisodes sortis (optionnel)


SELECT * FROM serie
ORDER BY :lastVisit
LIMIT :limit;



### get_series_by_genre
# Obtient les mêmes informations que la requête précédente, mais uniquement pour
# les séries d un genre.
#
# Optionnel :
#   On fournit en plus le nombre d épisodes sortis depuis
#   la dernière connection
#
#
# Paramètre
#   :genre        Le genre à afficher
#   :lastVisit    Dernière connection
#
# Champs attendus
#   serie         tous
#   new           le nombre d épisodes sortis (optionnel)

SELECT * FROM genre
INNER JOIN serie ON  genre.idSerie = serie.id
WHERE genre.nom = :genre AND  genre.id;



################################ Page Catégories ###############################

### get_all_categories
# Obtient la liste de toutes les catégories, avec le nombre de séries.
# On triera les catégories de la plus représentée à la moins présente
#
# Champs attendus
#   nom           de la catégorie
#   count         nombre de séries de la catégorie

SELECT DISTINCT COUNT(serie.id) AS count, genre.nom AS nom FROM genre
INNER JOIN serie ON serie.id = genre.idSerie
GROUP BY genre.nom
ORDER BY COUNT(DISTINCT serie.id) DESC;


################################ Page série ####################################


### get_serie
# Obtient toutes les informations sur une série
#
# Paramètre
#    :id       id de la série à retourner
#
# Champs attendus
#    serie     tous

SELECT * FROM serie WHERE :id = id;

### get_genre
# Obtient l ensemble de tous les genres associés à une série
#
# Paramètre
#    :id       id de la série
#
# Champs attendus
#    nom      nom du tag

SELECT nom FROM genre WHERE :id = idSerie;

### get_cast
# Obtient les paires rôles/acteurs pour une série donnée
# remarque : certains champs ayant le même nom, on utilisera des alias
# pour retourner les résultats
#
# Paramètre
#    :id       id de la série
#S
# Champs attendus
#    p_image   urlImage du personnage
#    p_nom     nom du personnage
#    a_id      id de l acteur
#    a_nom     nom de l acteur

SELECT DISTINCT acteur.id AS a_id, acteur.nom AS a_nom, personnage.urlImage AS p_image, personnage.nom AS p_nom FROM serie
JOIN poste ON poste.idSerie = serie.id
JOIN jouer ON jouer.idSerie = serie.id
JOIN personnage ON jouer.idPersonnage = personnage.id
JOIN personne AS acteur ON jouer.idPersonne = acteur.id
WHERE :id = serie.id;

### get_season_list
# Obtient la liste des saisons pour une série donnée
# Pour chaque saison, on souhaite connaître aussi le nombre d épisodes
# et la durée totale (optionnelle) sous forme heures:minutes:secondes (utilisez
# la fonction SEC_TO_TIME)
#
# Paramètre
#    :id       id de la série
#
# Champs attendus
#    saison
#    nb        nombre d épisodes
#    total     durée totale
#    debut     date du premier épisode
#    fin       date du dernier épisode

SELECT episode.saison AS saison, COUNT(DISTINCT episode.id) AS nb, SEC_TO_TIME(SUM(episode.duree * 60)) AS total, MAX(episode.premiere) AS fin, MIN(episode.premiere) AS debut
FROM episode
JOIN serie ON episode.idSerie = serie.id
WHERE :id = serie.id
GROUP BY episode.saison;

### get_episode_list
# Obtient la liste des episodes pour une série donnée,
# triés par numéro d épisode
#
# Paramètre
#    :id      id de la série
#    :saison
#
# Champs attendus
#    episode   tous

SELECT episode.* FROM episode
JOIN serie ON episode.idSerie = serie.id
WHERE :saison = episode.saison AND :id = serie.id
ORDER BY episode.numero ASC;

### get_crew_list
# Obtient la liste des membres de l équipe de tournage
#
# Paramètre
#    :id       id de la série
#
# Champs attendus
#    poste et personne   tous (il n y a pas de problème de champ ambigus
#                              donc pas d alias)

SELECT * FROM personne
JOIN poste ON personne.id = poste.idPersonne
WHERE poste.idSerie = :id;


### get_next_episode
# Retourne le premier épisode diffusé aujourd hui ou le plus tôt après
#
# Paramètres
#    :id               id de la série
#
# Champs attendus
#    episode           tous

SELECT episode.* FROM episode
JOIN serie ON episode.idSerie = serie.id
WHERE :id = serie.id
ORDER BY episode.premiere DESC
LIMIT 1;

####################### Page sur une personnes #################################

### get_person
# Obtient toutes les infos d une personne
#
# Paramètre
#    :id

SELECT * FROM personne
WHERE :id = id;


### get_actor_role
# Obtient les personnages joués ainsi que les séries correspondantes
#
# Paramètre
#    :id       id de l acteur
#
# Champs attendus
#    s_id      id de la série
#    s_nom     nom de la série
#    s_image   urlImage de la série
#    p_image   urlImage du personnage
#    p_nom     nom du personnage


SELECT serie.id AS s_id, serie.nom AS s_nom, serie.urlImage AS s_image, personnage.nom AS p_nom, personnage.urlImage AS p_image
FROM personnage
JOIN jouer ON personnage.id = jouer.idPersonnage
JOIN serie ON jouer.idSerie = serie.id
JOIN personne ON jouer.idPersonne = personne.id
WHERE personne.id = :id;

### get_crew_role
# Obtient la liste des postes occuppés dans les équipes des séries
#
# Paramètre
#    :id       id de la personne
#
# Champs attendus
#    titre     de la table poste
#    s_id      id de la série
#    s_nom     nom de la série
#    s_image   urlImage de la série

SELECT serie.id AS s_id, serie.nom AS s_nom, serie.urlImage AS s_image, poste.titre AS titre
FROM poste
JOIN serie ON poste.idSerie = serie.id
JOIN personne ON poste.idPersonne = personne.id
WHERE personne.id = :id;



############################## Gestion des utilisateurs #####################

### check_user
# Obtient les informations de connection d un utilisateur,
# à partir de son email.
#
# Utilisation
#   permet de tester si un utilisateur existe, et le cas échéant de
#   l authentifier.
#   permet de récupérer la dernière date de connection
#
# Remarque
#   La fonction PASSWORD() permet de chiffrer un mot de passe
#
# Paramètre
#   :email
#
# Retourne les champs suivants :
#   id
#   lastVisit
#   ok          (booléen) indique si le mot de passe fournit correspond au
#               mot de passe chiffré dans la base


SELECT idUser AS id, lastCo AS lastVisit, mdp AS ok FROM `user`
WHERE mdp = MD5(:password) AND email = :email;

### register_user
# Ajoute un utilisateur dans la base de données. Il faut configurer
# le mail, le password et la dernière date de connection (lastVisit)
# check_user a déjà été utilisé pour vérifier qu il n y pas d utilisateur avec
# le même email dans la base
#
# Remarque
#   La fonction PASSWORD() permet de chiffrer un mot de passe
#   La fonction CURDATE() permet d obtenir la date actuelle
#
# Paramètres
#   :email
#   :password
#

INSERT INTO `user` (email, mdp, lastCo)
VALUES (:email, MD5(:password), CURDATE());

### update_visit
# Mets à jour la date de dernière visite d un utilisateur
#
# Paramètres
#    :id       id de l utilisateur

UPDATE `user`
SET lastCo = CURDATE()
WHERE :id = idUser;


###################### Gestion des séries suivies #############################


### follow
# Indique qu un utilisateur suit une serie
#
# Utilisation
#   A partir de la page d une serie
#
# Paramètres
#   :idUser
#   :idSerie

INSERT INTO `uservisu` (idUser, idSerie) VALUES (:idUser, :idSerie);

### unfollow
# Indique qu un utilisateur ne suit plus une serie
#
# Utilisation
#   A partir de la page d une serie
#
# Paramètres
#   :idUser
#   :idSerie

DELETE FROM `uservisu`
WHERE :idUser = idUser AND :idSerie = idSerie;

### isFollowing
# Indique si un utilisateur suit une série. On considère que le résultat
# est vrai si et seulement si la requête retourne une ligne
#
# Utilisation
#   A partir de la page d une serie, ou plus tard à partir de la page perso
#   d un utilisateur connecté
#
# Paramètres
#   :idUser
#   :idSerie

SELECT DISTINCT idUser, idSerie FROM `uservisu`
WHERE  (idSerie = :idSerie AND idUser = :idUser);


############################### Gestion des épisodes vus ######################

### watched
# Indique qu un utilisateur a vu un épisode
#
# Utilisation
#   A partir de la page d une serie, ou plus tard à partir de la page perso
#   d un utilisateur connecté
#
# Paramètres
#   :idUser
#   :idEpisode

INSERT INTO `userepisode` (idUser, idEpisode,booleen)
VALUES (:idUser, :idEpisode, 1);

### unwatched
# Retire un épisode des episodes vus
#
# Utilisation
#   A partir de la page d une serie
#
# Paramètres
#   :idUser
#   :idEpisode

DELETE FROM `userepisode`
WHERE :idUser = idUser AND :idEpisode = idEpisode;

### get_episode_list_vu
# Obtient la liste des episodes pour une série donnée
# On retournera aussi pour chaque épisode si il a été vu ou non par l utilisateur
#
#
# Utilisation
#   A partir de la page d une serie
#
# Paramètres
#    :id         id de la serie
#    :userId
#    :saison
#
# Champs attendus
#    episode     tous les champs
#    vu          (booleén) vrai si l épisode a été vu


#SELECT DISTINCT episode.*, userepisode.booleen AS vu FROM episode
#LEFT JOIN userepisode ON userepisode.idUser = episode.idSerie
#LEFT JOIN serie ON serie.id = episode.idSerie
#WHERE serie.id = :id
#GROUP BY userepisode.booleen, serie.id;



### get_followed_series
# Obtient les données de toutes les séries suivies.
# On calcule aussi le nombre d épisodes restant à voir, et le nombre total
# Les séries seront triées épisodes restant à voir, puis par age décroissant
#
# Utilisation
#   Les séries retournées sont affichées sur la page de profil
#
# Paramètre
#    :userId
#
# Champs attendus
#    serie     tous les champs
#    total     nombre total d épisodes dans la série
#    reste     le nombre d épisodes non vus

SELECT serie.*, COUNT(episode.idSerie) AS total, userepisode.booleen AS reste FROM serie
JOIN episode ON serie.id = episode.idSerie
JOIN uservisu ON serie.id = uservisu.idSerie
JOIN userepisode ON uservisu.idUser = userepisode.idUser
WHERE uservisu.id = :userId AND userepisode.booleen = 1;

### get_next_episode_user
# Obtient les infos des episodes non encore vus de chaque série suivie
# On triera par série, saison et numero.
#
# Utilisation
#   Les épisodes retournées sont affichées sur la page de profil
#
# Paramètre
#    :userId
#
# Champs attendus
#    episode     tous les champs

# Remplacez ces lignes par votre requête. Ne modifiez pas le bloc de
# commentaires situé au dessus
