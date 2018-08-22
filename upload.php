<?php
 if (isset($_POST['submit'])){
  $file = $_FILES['file'];
  //print_r($file);// affiche le contenu du tableau des infos du fichier
  $fileName = $_FILES['file']['name'];
  $fileTmpName = $_FILES['file']['tmp_name'];
  $fileSize = $_FILES['file']['size'];
  $fileError = $_FILES['file']['error'];
  $fileType = $_FILES['file']['type'];

  $fileOutput = explode('.', $fileName);
  $fileExtension = strtolower(end($fileOutput));

  $allow = array('jpg','jpeg','png','pdf');

  $repInclude = './include/';
  require($repInclude . "_init.inc.php");
  $idUser = obtenirIdUserConnecte() ;
  $lgUser = obtenirDetailVisiteur($idConnexion, $idUser);
  $nom = $lgUser['nom'];
  $prenom = $lgUser['prenom'];
  $mois = sprintf("%04d%02d", date("Y"), date("m"));

  if (in_array($fileExtension, $allow)){
    if ($fileError == 0){
      if($fileSize < 7000000){
        $fileNewName = $prenom.$nom.$mois."(".uniqid('', true).")".".".$fileExtension;
        while(file_exists('uploads/'.$mois.'/'.$fileName)){
          $fileNewName = $prenom.$nom.$mois."(".uniqid('', true).")".".".$fileExtension;
        }
        if(!is_dir('uploads/'.$mois)){
          mkdir('uploads/'.$mois);
        }
        $fileDestination = 'uploads/'.$mois.'/'.$fileNewName;
        move_uploaded_file($fileTmpName, $fileDestination);



        //compression de l'image :
        function compress($source, $destination, $quality) {
		        $info = getimagesize($source);
		          if ($info['mime'] == 'image/jpeg')
			           $image = imagecreatefromjpeg($source);
		          elseif ($info['mime'] == 'image/gif')
			           $image = imagecreatefromgif($source);
		          elseif ($info['mime'] == 'image/png')
			           $image = imagecreatefrompng($source);
                 imagejpeg($image, $destination, $quality);
        return $destination;
	}

	$source_img = $fileDestination;
	$destination_img = $fileDestination;

	$d = compress($source_img, $destination_img, 90);



        print("envoi réussi ! Le fichier est nommé ".$fileNewName);
        ?>
        <br>
        <a href="cAccueil.php">Retourner à l'accueil</a>
        <?php
      }
      else{
        print("Le fichier est trop gros il doit être inferieur à 7mo !");
      }
    }
    else{
      print("Probleme lors de l'envoi, Veuillez réésayer !");
    }
  }
  else{
    print("Format non supporté !");
  }
}
?>
