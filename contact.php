<?php require 'contact-script.php'; ?>
<!DOCTYPE html>
<html lang="fr-FR">
<head>

	<meta charset="utf-8"> 
	<meta name="viewport" content="width=device-width,initial-scale=1">
  <!--[if lt IE 9]><script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
	<title>Contact</title>
	<meta name="description" content="Me contacter">
	
</head>
<body id="clustercarbone">

	<?= get_return() ?>

	<form method="post" action="contact.php">
		
		<fieldset>
		
			<legend>Me contacter</legend>
		
			<p>
				<label for="contact-email">Email</label>
				<input id="contact-email" type="email" name="contact[email_from]" value="<?= $email_from ?>" placeholder="Votre email" required>
			</p>
			
			<p>
				<label for="contact-content">Message</label>
				<textarea id="contact-content" type="text" name="contact[content]" placeholder="Votre message" required><?= $content ?></textarea>
			</p>
			
			<button type="submit">
				Envoyer
			</button>
		
		</fieldset>
		
	</form>
			
</body>
</html>