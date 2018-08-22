<?php
/**
 * Script de contrôle et d'affichage du cas d'utilisation "Saisir fiche de frais"
 * @package default
 * @todo  RAS
 */
  $repInclude = './include/';
  require($repInclude . "_init.inc.php");

  // page inaccessible si visiteur non connecté
  if (!estVisiteurConnecte()) {
      header("Location: cSeConnecter.php");
  }
  require($repInclude . "_entete.inc.html");
  require($repInclude . "_sommaire.inc.php");
  // affectation du mois courant pour la saisie des fiches de frais
  $mois = sprintf("%04d%02d", date("Y"), date("m"));
  // vérification de l'existence de la fiche de frais pour ce mois courant
  $existeFicheFrais = existeFicheFrais($idConnexion, $mois, obtenirIdUserConnecte());
  //var_dump($existeFicheFrais);
  // si elle n'existe pas, on la crée avec les élets frais forfaitisés à 0
  //(!K) à revoir avec la fonction remplir_frais_vide
  if ( $existeFicheFrais == False ) {
      ajouterFicheFrais($idConnexion, $mois, obtenirIdUserConnecte());
      $req = obtenirReqEltsForfaitFicheFrais($mois, obtenirIdUserConnecte());
      /*//print($req);
      remplir_frais_vide($idConnexion, $mois, obtenirIdUserConnecte());
      $idJeuEltsFraisForfait = mysqli_query($idConnexion, $req);
      $result = mysqli_query($idConnexion,$req);
      */
  }
  // acquisition des données entrées
  // acquisition de l'étape du traitement
  $etape=lireDonnee("etape","demanderSaisie");
  // acquisition des quantités des éléments forfaitisés
  $tabQteEltsForfait=lireDonneePost("txtEltsForfait", "");
  // acquisition des données d'une nouvelle ligne hors forfait
  $idLigneHF = lireDonnee("idLigneHF", "");
  $dateHF = lireDonnee("txtDateHF", "");
  $libelleHF = lireDonnee("txtLibelleHF", "");
  $montantHF = lireDonnee("txtMontantHF", "");

  // structure de décision sur les différentes étapes du cas d'utilisation
  if ($etape == "validerSaisie") {
      // l'utilisateur valide les éléments forfaitisés
      // vérification des quantités des éléments forfaitisés
      $ok = verifierEntiersPositifs($tabQteEltsForfait);
      if (!$ok) {
          ajouterErreur($tabErreurs, "Chaque quantité doit être renseignée et numérique positive.");
      }
      else { // mise à jour des quantités des éléments forfaitisés
          modifierEltsForfait($idConnexion, $mois, obtenirIdUserConnecte(),$tabQteEltsForfait);
      }
  }
  elseif ($etape == "validerSuppressionLigneHF") {
      supprimerLigneHF($idConnexion, $idLigneHF);
  }
  elseif ($etape == "validerAjoutLigneHF") {
      verifierLigneFraisHF($dateHF, $libelleHF, $montantHF, $tabErreurs);
      if ( nbErreurs($tabErreurs) == 0 ) {
          // la nouvelle ligne ligne doit être ajoutée dans la base de données
          ajouterLigneHF($idConnexion, $mois, obtenirIdUserConnecte(), $dateHF, $libelleHF, $montantHF);
      }
  }
  else { // on ne fait rien, étape non prévue

  }
?>
  <!-- Division principale -->
  <div id="contenu">
      <h2>Renseigner ma fiche de frais du mois de <?php echo obtenirLibelleMois(intval(substr($mois,4,2))) . " " . substr($mois,0,4); ?></h2>
<?php
  if ($etape == "validerSaisie" || $etape == "validerAjoutLigneHF" || $etape == "validerSuppressionLigneHF") {
      if (nbErreurs($tabErreurs) > 0) {
          echo toStringErreurs($tabErreurs);
      }
      else {
?>
      <p class="info">Les modifications de la fiche de frais ont bien été enregistrées</p>
<?php
      }
  }
      ?>
      <form action="" method="post">
      <div class="corpsForm">
          <input type="hidden" name="etape" value="validerSaisie" />
          <fieldset>
            <legend>Eléments forfaitisés
            </legend>
      <?php
            // demande de la requête pour obtenir la liste des éléments
            // forfaitisés du visiteur connecté pour le mois demandé
            $req = obtenirReqEltsForfaitFicheFrais($mois, obtenirIdUserConnecte());
            //print_r($req);
            $idJeuEltsFraisForfait = mysqli_query($idConnexion, $req);
            if($idJeuEltsFraisForfait == false){
              remplir_fiche_frais_vide($idConnexion, $mois, obtenirIdUserConnecte());
            }
            $idJeuEltsFraisForfait = mysqli_query($idConnexion, $req);
            //print_r($idJeuEltsFraisForfait);
            //var_dump($idJeuEltsFraisForfait);
            //(K)
            /*Patch du probleme de non affichage de nouveau mois :
            * Si un utilisateur commence un nouveau mois cette fonction permet de créer un nouvelle entrée
            * dans la BDD avec tous les champs égaux à 0
            * Fonctionne grace au champ num_rows de l'objet $idJeuEltsFraisForfait
            */
            $idVisiteur = obtenirIdUserConnecte();
            $result = mysqli_query($idConnexion,$req);
            //print_r($result);
            /*if(!$result || $result->num_rows == 0){
              remplir_frais_vide($idConnexion, $mois, obtenirIdUserConnecte());
              $idJeuEltsFraisForfait = mysqli_query($idConnexion, $req);
              $result = mysqli_query($idConnexion,$req);
              //var_dump($idJeuEltsFraisForfait);
            }*/
            echo mysqli_error($idConnexion);
            $lgEltForfait = mysqli_fetch_assoc($idJeuEltsFraisForfait);
            //var_dump($lgEltForfait);
            while ( is_array($lgEltForfait) ) {
                $etape = $lgEltForfait["ETP"];
                $km = $lgEltForfait["KM"];
                $nuit = $lgEltForfait["NUI"];
                $rep = $lgEltForfait["REP"];
            ?>
            <p>
              <label for="<?php echo $etape ?>">Forfait Etape: </label>
              <input type="text" id="<?php echo $etape ?>"
                    name="txtEltsForfait[0]"
                    size="10" maxlength="5"
                    title="Entrez la quantité de l'élément forfaitisé"
                    value="<?php echo $etape; ?>" />
            </p>
            <p>
              <label for="<?php echo $km ?>">Forfait Kilométrique: </label>
              <input type="text" id="<?php echo $km ?>"
                    name="txtEltsForfait[1]"
                    size="10" maxlength="5"
                    title="Entrez la quantité de l'élément forfaitisé"
                    value="<?php echo $km; ?>" />
            </p>
            <p>
              <label for="<?php echo $nuit ?>">Forfait Nuit: </label>
              <input type="text" id="<?php echo $nuit ?>"
                    name="txtEltsForfait[2]"
                    size="10" maxlength="5"
                    title="Entrez la quantité de l'élément forfaitisé"
                    value="<?php echo $nuit; ?>" />
            </p>
            <p>
              <label for="<?php echo $rep ?>">Forfait Repas : </label>
              <input type="text" id="<?php echo $rep ?>"
                    name="txtEltsForfait[3]"
                    size="10" maxlength="5"
                    title="Entrez la quantité de l'élément forfaitisé"
                    value="<?php echo $rep; ?>" />
            </p>
            <?php
                $lgEltForfait = mysqli_fetch_assoc($idJeuEltsFraisForfait);
            }
            mysqli_free_result($idJeuEltsFraisForfait);
            ?>
          </fieldset>
      </div>
      <div class="piedForm">
      <p>
        <input id="ok" type="submit" value="Valider" size="20"
               title="Enregistrer les nouvelles valeurs des éléments forfaitisés" />
        <input id="annuler" type="reset" value="Effacer" size="20" />
      </p>
      </div>

      </form>
  	<table class="listeLegere">
  	   <caption>Descriptif des éléments hors forfait
       </caption>
             <tr>
                <th class="date">Date</th>
                <th class="libelle">Libellé</th>
                <th class="montant">Montant</th>
                <th class="facture">Facture</th>
                <th class="action">&nbsp;</th>
             </tr>

              <?php
          // demande de la requête pour obtenir la liste des éléments hors
          // forfait du visiteur connecté pour le mois demandé
          $req = obtenirReqEltsHorsForfaitFicheFrais($mois, obtenirIdUserConnecte());
          $idJeuEltsHorsForfait = mysqli_query($idConnexion, $req);
          $lgEltHorsForfait = mysqli_fetch_assoc($idJeuEltsHorsForfait);

          // parcours des frais hors forfait du visiteur connecté
          while ( is_array($lgEltHorsForfait) ) {
              ?>
              <tr>
                <td><?php echo $lgEltHorsForfait["date"] ; ?></td>
                <td><?php echo filtrerChainePourNavig($lgEltHorsForfait["libelle"]) ; ?></td>
                <td><?php echo $lgEltHorsForfait["montant"] ; ?></td>
                <td><a href="?etape=validerSuppressionLigneHF&amp;idLigneHF=<?php echo $lgEltHorsForfait["id"]; ?>"
                       onclick="return confirm('Voulez-vous vraiment supprimer cette ligne de frais hors forfait ?');"
                       title="Supprimer la ligne de frais hors forfait">Supprimer</a></td>
              </tr>
          <?php
              $lgEltHorsForfait = mysqli_fetch_assoc($idJeuEltsHorsForfait);
          }
          mysqli_free_result($idJeuEltsHorsForfait);
?>
    </table>
      <form action="" method="post">
      <div class="corpsForm">
          <input type="hidden" name="etape" value="validerAjoutLigneHF" />
          <fieldset>
            <legend>Nouvel élément hors forfait
            </legend>
            <p>
              <label for="txtDateHF">* Date (JJ/MM/AAAA): </label>
              <input type="text" id="txtDateHF" name="txtDateHF" size="12" maxlength="10"
                     title="Entrez la date d'engagement des frais au format JJ/MM/AAAA"
                     value="<?php echo $dateHF; ?>" />
            </p>
            <p>
              <label for="txtLibelleHF">* Libellé : </label>
              <input type="text" id="txtLibelleHF" name="txtLibelleHF" size="70" maxlength="100"
                    title="Entrez un bref descriptif des frais"
                    value="<?php echo filtrerChainePourNavig($libelleHF); ?>" />
            </p>
            <p>
              <label for="txtMontantHF">* Montant : </label>
              <input type="text" id="txtMontantHF" name="txtMontantHF" size="12" maxlength="10"
                     title="Entrez le montant des frais (le point est le séparateur décimal)" value="<?php echo $montantHF; ?>" />
            </p>
            <div class="piedForm">
              <p>
                <input id="ajouter" type="submit" value="Ajouter" size="20"
                title="Ajouter la nouvelle ligne hors forfait" />
                <input id="effacer" type="reset" value="Effacer" size="20" />
              </p>
            </div>
          </fieldset>
      </div>
      <div class="corpsForm">
        <form method="post" action="upload.php" enctype='multipart/form-data'>
          <fieldset>
            <legend>Gestion des factures
            </legend>
            <h2>
              <a href="factures.php">Aller vers le portail de gestion des factures</a>
            </h2>
          </fieldset>
      </form>
      </div>
<?php
  require($repInclude . "_pied.inc.html");
  require($repInclude . "_fin.inc.php");
?>
