<!DOCTYPE html> 
<html>
<head>
<meta name="generator" content="Bug" />
<title>TwitWall Factory</title>
<meta name="viewport" content="initial-scale=1,user-scalable=yes" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="message.css" media="screen" rel="stylesheet" type="text/css" />
<!-- <link rel="stylesheet" href="http://code.jquery.com/mobile/1.0a2/jquery.mobile-1.0a2.min.css" /> -->
<script src="http://code.jquery.com/jquery-1.4.4.min.js"></script>
<!-- <script src="http://code.jquery.com/mobile/1.0a2/jquery.mobile-1.0a2.min.js"></script> -->
<script>
$(document).ready(function() {
	$('textarea[maxlength]').keyup(function(){
		//get the limit from maxlength attribute
		var limit = parseInt($(this).attr('maxlength'));
		//get the current text inside the textarea
		var text = $(this).val();
		//count the number of characters in the text
		var chars = text.length;

		//check if there are more characters then allowed
		if(chars > limit){
			//and if there are use substr to get the text before the limit
			var new_text = text.substr(0, limit);

			//and change the current text with the new text
			$(this).val(new_text);
		}
	});
});
</script>
</head>
<body style="overflow: visible;">
<div data-role="page">
	<div id="menu" data-role="header">
		<span class="intitule">Ajouter un message</span>
	</div>
	<div id="content" data-role="content">
		<?php 
		if($_POST['pseudo'] && $_POST['title']){
			require_once('smswall.inc.php');
			
			// Lecture de la config
			$qconfig = $db->query("SELECT * FROM config_wall");
			$config = $qconfig->fetch(PDO::FETCH_ASSOC);
	
			$title = trim($_POST['title']);
			$pseudo = trim($_POST['pseudo']);
			$modo_type = $config['modo_type'];
			$link = "WEB";
			
			if(!empty($title) && !empty($pseudo)){
				$db->exec('INSERT INTO "items" VALUES(NULL,'.$db->quote($pseudo).','.$db->quote($link).','.$db->quote($title).','.time().','.$modo_type.',0,NULL);');
				?>
				<div class="msgRetour">Votre message a été ajouté</div>
				<?php 
			}
		}
		?>
		<form action="message.php" method="post">
			<dl>
				<dt>
					<label for="pseudo">Votre pseudo</label>
				</dt>
				<dd>
					<input type="text" id="pseudo" name="pseudo" value="" />
				</dd>
				
				<dt>
					<label for="title">Votre message (60 caractères maxi)</label>
				</dt>
				<dd>
					<textarea id="title" name="title" maxlength="60"></textarea>
				</dd>
			</dl>
			<input type="submit" value="Envoyer" />
		</form>
	</div>
</div>






















</body>
</html>