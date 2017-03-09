<?php
// On enregistre notre autoload.
function loadClass($classname)
{
    require $classname.'.php';
}

spl_autoload_register('loadClass');

session_start(); // On appelle session_start() APRÈS avoir enregistré l'autoload.
require('db.php');

if (isset($_GET['deconnexion'])) {
    session_destroy();
    header('Location: .');
    exit();
}

$manager = new PersoManager($db);

if (isset($_SESSION['perso'])) { // Si la session perso existe, on restaure l'objet.
  $perso = $_SESSION['perso'];
}

if (isset($_POST['create']) && isset($_POST['name'])) { // Si on a voulu créer un personnage.
  $perso = new Perso(['name' => $_POST['name']]); // On crée un nouveau personnage.

    if (! $perso->validName()) {
        $message = 'Le nom choisi est invalide.';
        unset($perso);
    } elseif ($manager->exist($perso->getName())) {
        $message = 'Le nom du personnage est déjà pris.';
        unset($perso);
    } else {
        $manager->add($perso);
    }
}

elseif (isset($_POST['use']) && isset($_POST['name'])) { // Si on a voulu utiliser un personnage.
    if ($manager->exist($_POST['name'])) { // Si celui-ci existe.
      $perso = $manager->find($_POST['name']);
    } else {
        $message = 'Ce personnage n\'existe pas !'; // S'il n'existe pas, on affichera ce message.
    }
}

elseif (isset($_GET['hit'])) { // Si on a cliqué sur un personnage pour le frapper.
    if (! isset($perso)) {
        $message = 'Merci de créer un personnage ou de vous identifier.';
    } else {
        if (! $manager->exist((int) $_GET['hit'])) {
            $message = 'Le personnage que vous voulez frapper n\'existe pas !';
        } else {
            $persoAFrapper = $manager->find((int) $_GET['hit']);

            $retour = $perso->hit($persoAFrapper); // On stocke dans $retour les éventuelles erreurs ou messages que renvoie la méthode frapper.

            switch ($retour) {
                case Perso::ITS_ME:
                    $message = 'Mais... pourquoi voulez-vous vous frapper ???';
                    break;

                case Perso::IM_HIT:
                    $message = 'Le personnage a bien été frappé !';

                    $manager->update($perso);
                    $manager->update($persoAFrapper);

                    break;

                case Perso::IM_DOWN:
                    $message = 'Vous avez tué ce personnage !';

                    $manager->update($perso);
                    $manager->delete($persoAFrapper->getId());

                  break;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
  <head>
    <title>TP : Mini jeu de combat</title>

    <meta charset="utf-8" />
  </head>
  <body>
    <p>Nombre de personnages créés : <?= $manager->count() ?></p>
<?php
if (isset($message)) { // On a un message à afficher ?
  echo '<p>', $message, '</p>'; // Si oui, on l'affiche.
}

if (isset($perso)) { // Si on utilise un personnage (nouveau ou pas).
?>
    <p><a href="?deconnexion=1">Déconnexion</a></p>

    <fieldset>
      <legend>Mes informations</legend>
      <p>
        Nom : <?= htmlspecialchars($perso->getName()) ?><br />
        Dégâts : <?= $perso->getDamage() ?>
      </p>
    </fieldset>

    <fieldset>
      <legend>Qui frapper ?</legend>
      <p>
<?php
$persos = $manager->getList($perso->getName());

    if (empty($persos)) {
        echo 'Personne à frapper !';
    } else {
        foreach ($persos as $unPerso) {
            echo '<a href="?hit=', $unPerso->getId(), '">', htmlspecialchars($unPerso->getName()), '</a> (dégâts : ', $unPerso->getDamage(), ')<br />';
        }
    } ?>
      </p>
    </fieldset>
<?php

} else {
    ?>
    <form action="" method="post">
      <p>
        Nom : <input type="text" name="name" maxlength="50" />
        <input type="submit" value="Créer ce personnage" name="create" />
        <input type="submit" value="Utiliser ce personnage" name="use" />
      </p>
    </form>
<?php

}
?>
  </body>
</html>
<?php
if (isset($perso)) { // Si on a créé un personnage, on le stocke dans une variable session afin d'économiser une requête SQL.
  $_SESSION['perso'] = $perso;
}
