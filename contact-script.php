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
 *  - ne détecte pas les spams
 *  - n'empêche pas les envois abusifs
 *  - ne fais pas le café !
 * 
 */

require 'contact-config.php';

// regex (expression régulière) pour valider le format d'une adresse email
$pattern_email = '`^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$`i';

session_start();

//enregistre une notification de retour avant de recharger la page
function set_return($message)
{
	$_SESSION['notification'] = $message;
	header('Location: contact.php');
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
		$value =  trim(htmlspecialchars($_POST['contact'][$varname]));
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
$contact = array(
	'email_from' => get_contact_data('email_from'),
	'content' => get_contact_data('content')
);

// si des données sont envoyées via le formulaire
if(isset($_POST['contact']))
{
	// on vérifie la validité de l'email
	if(!preg_match($pattern_email, $contact['email_from']))
	{
		set_return('Veuillez vérifier votre email');
	}
	
	// on vérifie qu'on envoie pas un message vide
	if(empty($contact['content']))
	{
		set_return('Pas de message');
	}
	
	// préparation du contenu de l'email
	$email = array(
		'to' => $config['contact']['email_to'],
		'subject' => $config['contact']['email_subject'],
		'object' => 
			'# Message : '."\n\n".$contact['content']."\n\n".
			"\n".'# Email : '.$contact['email_from'].
			"\n".'# Posté le : '.date('Y-m-d à H:i:s').
			"\n".'# IP : '.$_SERVER['REMOTE_ADDR']
		,
		'headers' => 
			'From: '.$email_from.
			"\r\n".'MIME-Version: 1.0'. 
			"\r\n".'Content-type: text/plain; charset=UTF-8'
	);
	
	// On envoie le mail
	if(mail($email['to'], $email['subject'], $email['object'], $email['headers']))
	{
		$_SESSION['contact'] = null; // on efface les données en session
		set_return('Message envoyé');
	}
	else
	{
		set_return('Problème, le message n\'a pas été envoyé');
	}
}
