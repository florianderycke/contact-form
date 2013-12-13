<?php 

/*
 * CONTACT (version 2013-12-13)
 * 
 * Ce que fais le script :
 *  - vérifie la validité du format de l'email
 *  - vérifie que le message n'est pas vide
 *  - envoie le message par mail si les données sont ok
 *  - stocke les données et les réaffiche dans le formulaire si les données sont incomplètes
 * 
 * Ce que fais le script :
 *  - ne détectes pas les spams
 *  - ne détectes pas les gros cons non plus
 *  - n'empêche pas le bruteforcing
 *  - ne fais pas le café
 * 
 * INSTALLATION
 * Pour qu'un fichier PHP soit lu, il doit être interprété par un serveur PHP donc ça ne fonctionnera pas sur ton PC, 
 * sauf si tu installes un serveur (par exemple WAMP) et que tu y glisses tes fichiers.
 * Tu peux personnaliser certains messages mais attention à ne pas modifier le code.
 * Mais pour commencer, renseigne ton email (ligne 35).
 * 
 * ATTENTION
 * Le script utilise les sessions, ça veut dire qu'il faut que session_start() 
 * soit executé avant tout affichage html, et que tes fichiers soient encodés en UTF8 (sans BOM).
 * Si ce n'est pas le cas, tu auras une jolie erreur et ça ne fonctionnera pas.
 * donc place tout le code PHP en haut de page sans aucun espace ou saut de ligne avant, 
 * et enregistré dans des fichiers renommés en ".php".
 * 
 */

// données configurable
$email_subject = 'Demande de contact';
$email_to = 'supercrocodile@gmail.com'; // ton email
$form_filename = 'contact.php'; // si tu renommes la page contenant ton formulaire

// regex (expression régulière) pour valider le format d'une adresse email
$pattern_email = '`^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$`i';

session_start();

//enregistre une notification de retour avant de recharger la page
function set_return($message)
{
	$_SESSION['notification'] = $message;
	header('Location: '.$form_filename);
	exit;
}

// récupère un éventuel message de notification
function get_return()
{
	if((isset($_SESSION['notification']))&&(!empty($_SESSION['notification'])))
	{
		$notification = $_SESSION['notification'];
		$_SESSION['notification'] = null;
		return $notification;
	}
}

// récupère la valeur d'une variable
function get_contact_data($varname)
{
	if(isset($_POST['contact'][$varname])) // en priorité si elle provient directement du formulaire
	{
		$value =  trim(htmlspecialchars($_POST['contact'][$varname])); // on traite les données pour éviter d'exécuter du code malicieux ou d'afficher des images pourries ou toute sort de contenu html (mais n'évite pas les spams)
		$_SESSION['contact'][$varname] = $value; // on sauvegarde les données en session dans le cas où elles seraient incomplètes
		return $value;
	}
	else
	if(isset($_SESSION['contact'][$varname])) // ou si elle a été stockée en session pour réaffichage (utile si formulaire mal rempli)
	{
		return $_SESSION['contact'][$varname];
	}
	else // ou renvoie une valeur nulle
	{
		return null;
	}
}

// récupération des données
$email_from = get_contact_data('email_from');
$content = get_contact_data('content');

// Si des données sont envoyées via le formulaire
if(isset($_POST['contact']))
{
	// on prépare le corps de l'email, puis de l'entête
	$email_object = 
		"# Message : \n\n".$content."\n\n".
		"\n# Email : ".$email_from.
		"\n# Posté le : ".date('Y-m-d à H:i:s').
		"\n# IP : ".$_SERVER['REMOTE_ADDR']
	;
	$email_headers = "From: ".$email_from;
	
	// on vérifie la validité de l'email
	if(!preg_match($pattern_email, $email_from))
	{
		set_return('Veuillez vérifier votre email');
	}
	
	// on vérifie qu'on envoie pas un message vide
	if(empty($content))
	{
		set_return('Pas de message');
	}
	
	// On envoie le mail
	if(mail($email_to, $email_subject, $email_object, $email_headers)) // voir documentation function mail()
	{
		$_SESSION['contact'] = null; // on efface les données en session
		set_return('Message envoyé');
	}
	else
	{
		set_return('Problème, le message n\'a pas été envoyé');
	}
}
