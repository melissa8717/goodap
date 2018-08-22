<?php
/*
 * Page d'accueil de l'application web AppliFrais
 * @package default
 * @todo  RAS
 */
  $repInclude = "./include/";
  require($repInclude . "_init.inc.php");

  // page inaccessible si visiteur non connecté


  if ( ! estVisiteurConnecte() )
  {
        header("Location: cSeConnecter.php");
  }

  require($repInclude . "_entete.inc.html");
  require($repInclude . "_sommaire.inc.php");

?>
  <!-- Division principale -->
  <div id="contenu">
    <h2>Bienvenue sur l'intranet GSB</h2>
  </div>
  <div class = "erreur">
    <h3>
      <?php
        $jour = sprintf("%02d", date("d"));
        $moissuivant = sprintf("%02d", date("m"));
        $moissuivant += 1;
        //printf($jour);
        if($jour <= 5 || $jour >= 20){
          print ("Attention, il ne vous reste que quelques jours pour finaliser vos fiches de frais la date limite est le 5 ". obtenirLibelleMois($moissuivant) . ".");
        }
        ?>
    </h3>
</div>
  <!--
  <div id="unlocked">
        <img width="150px" height="160px" src="images/unlocked.png">
  </div>
  -->
<div class="newsletter">
  <p style="color:red;">CECI EST UN ESPACE DEDIE A LA NEWLETTER POUR INFORMER LES UTILISATEURS DES DERNIERES MODIFICATIONS ET PASSER DES ANNONCES.</p>
  <br>
  Le Lorem Ipsum est simplement du faux texte employé dans la composition et la mise en page avant impression. Le Lorem Ipsum est le faux texte standard de l'imprimerie depuis les années 1500, quand un peintre anonyme assembla ensemble des morceaux de texte pour réaliser un livre spécimen de polices de texte. Il n'a pas fait que survivre cinq siècles, mais s'est aussi adapté à la bureautique informatique, sans que son contenu n'en soit modifié. Il a été popularisé dans les années 1960 grâce à la vente de feuilles Letraset contenant des passages du Lorem Ipsum, et, plus récemment, par son inclusion dans des applications de mise en page de texte, comme Aldus PageMaker.
</div>
<?php

  require($repInclude . "_pied.inc.html");
  require($repInclude . "_fin.inc.php");

?>
