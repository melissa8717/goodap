<!DOCTYPE html>
<?php
$repInclude = "./include/";
require($repInclude . "_init.inc.php");
require($repInclude . "_entete.inc.html");
require($repInclude . "_sommaire.inc.php");
?>
<html>
<head>
	<title></title>
</head>
<body>
	<form action="upload.php" " method="POST" enctype="multipart/form-data">
		<input type="file" name="file">
		<button type="submit" name="submit">Ajouter</button>
	</form>
	<p>Contenu de votre dossier Factures :</p>
	<?php
  $nom = $lgUser['nom'];
  $prenom = $lgUser['prenom'];
	$nb_fichier = 0;
	$mois = sprintf("%04d%02d", date("Y"), date("m"));
	$chainepourtri = $prenom . $nom . $mois;
	//var_dump($chainepourtri);
  print("<ul>");
	if($dossier = opendir('./uploads')){
		while(false !== ($fichier = readdir($dossier))){ // !== => pas d'erreur
			if($fichier != '.' && $fichier != '..' && $fichier != 'index.php' && strpos($fichier,$chainepourtri) !== false){
				$nb_fichier ++;
				print('<li><a href="./uploads/'. $fichier . '">' .$fichier.'</a></li>');

			}
		}
		print('</ul><br />');
		print('Il y a ' . $nb_fichier. ' facture(s) dans votre dossier');
		closedir($dossier);
	}
	else{
		print('Le dossier n\'a pas pu être ouvert');
	}
	
	?>
</body>
<br>
<br>
<br>
<br>
</html>
<?php
require($repInclude . "_pied.inc.html");
require($repInclude . "_fin.inc.php");
?>
