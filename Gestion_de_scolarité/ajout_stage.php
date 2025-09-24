<?php
session_start();
include('cadre.php');
include('calendrier.html');

// Connexion à MySQL avec mysqli
$conn = mysqli_connect("localhost", "root", "", "test");
if (!$conn) {
    die("Erreur de connexion : " . mysqli_connect_error());
}
?>
<html>
<div class="corp">
<img src="" class="position_titre">
<center><pre>
<?php 
// MODIFICATION d'un stage
if (isset($_SESSION['modif_stage']) && isset($_POST['lieu'])) {
    if (!empty($_POST['lieu']) && !empty($_POST['date_debut']) && !empty($_POST['date_fin'])) {
        $id = $_SESSION['modif_stage'];
        $date_debut = $_POST['date_debut'];
        $date_fin   = $_POST['date_fin'];
        $lieu       = $_POST['lieu'];

        mysqli_query($conn, "UPDATE stage 
                              SET lieu_stage='$lieu', date_debut='$date_debut', date_fin='$date_fin' 
                              WHERE numstage='$id'");

        echo '<script>alert("Modification avec succès !");</script>';
        unset($_SESSION['modif_stage']);
        echo '<br/><br/><a href="index.php">Revenir à la page d\'accueil !</a>';
    } else {
        echo '<script>alert("Veuillez remplir tous les champs");</script>';
    }
}
// AJOUT d'un stage
else if (isset($_POST['lieu'])) {
    if (!empty($_POST['lieu']) && !empty($_POST['date_debut']) && !empty($_POST['date_fin'])) {
        $numel       = $_POST['numel'];
        $date_debut  = addslashes(nl2br(htmlspecialchars($_POST['date_debut'])));
        $date_fin    = addslashes(nl2br(htmlspecialchars($_POST['date_fin'])));
        $lieu        = addslashes(nl2br(htmlspecialchars($_POST['lieu'])));

        $res_compte = mysqli_query($conn, "SELECT COUNT(*) AS nb 
                                           FROM stage 
                                           WHERE lieu_stage='$lieu' 
                                           AND numel='$numel' 
                                           AND date_debut='$date_debut' 
                                           AND date_fin='$date_fin'");
        $compte = mysqli_fetch_assoc($res_compte);

        if ($compte['nb'] > 0) {
            echo '<script>alert("Erreur d\'insertion, le stage existe déjà !");</script>';
        } else {
            mysqli_query($conn, "INSERT INTO stage(lieu_stage, date_debut, date_fin, numel) 
                                 VALUES ('$lieu', '$date_debut', '$date_fin', '$numel')");
            echo '<script>alert("Ajouté avec succès !");</script>';
        }
        echo '<a href="index.php">Revenir à la page d\'accueil !</a>';
    } else {
        echo '<script>alert("Vous devez remplir tous les champs !");</script>';
        echo '<a href="index.php">Revenir à la page d\'accueil !</a>';
    }
}
// CHOIX de la classe/promotion
else if (!isset($_POST['nomcl']) && !isset($_GET['modif_stage'])) {
    $data   = mysqli_query($conn, "SELECT DISTINCT promotion FROM classe ORDER BY promotion DESC");
    $retour = mysqli_query($conn, "SELECT DISTINCT nom FROM classe");
    ?>
    <form action="ajout_stage.php" method="POST" class="formulaire">
    Veuillez choisir la classe et la promotion : <br/><br/>
    Promotion : <select name="promotion"> 
    <?php while($a = mysqli_fetch_assoc($data)) {
        echo '<option value="'.$a['promotion'].'">'.$a['promotion'].'</option>';
    } ?></select><br/><br/>
    Classe : <select name="nomcl"> 
    <?php while($a = mysqli_fetch_assoc($retour)) {
        echo '<option value="'.$a['nom'].'">'.$a['nom'].'</option>';
    } ?></select><br/><br/>
    <center><input type="submit" value="Suivant"></center>
    </form>
    <?php
}
// FORMULAIRE d'ajout ou modification
if ((isset($_POST['nomcl']) && isset($_POST['promotion'])) || isset($_GET['modif_stage'])) {
    $id = "";
    $lieu = "";
    $date_debut = "";
    $date_fin = "";

    if (isset($_GET['modif_stage'])) { // Modification
        $id = $_GET['modif_stage'];
        $_SESSION['modif_stage'] = $id;

        $donnee = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM stage WHERE numstage='$id'"));
        $lieu = $donnee['lieu_stage'];
        $date_debut = $donnee['date_debut'];
        $date_fin = $donnee['date_fin'];

        $data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT numel, nomel, prenomel 
                                                        FROM eleve 
                                                        WHERE numel=(SELECT numel FROM stage WHERE numstage='$id')"));
    } else { // Ajout
        $_SESSION['promo'] = $_POST['promotion'];
        $promo = $_POST['promotion'];
        $nomcl = $_POST['nomcl'];

        $data = mysqli_query($conn, "SELECT numel, nomel, prenomel 
                                     FROM eleve, classe 
                                     WHERE classe.codecl = eleve.codecl 
                                     AND nom='$nomcl' 
                                     AND promotion='$promo'");
    }
    ?>
    <form action="ajout_stage.php" method="POST" class="formulaire">
    Eleve :
    <?php if (isset($_GET['modif_stage'])) {
        echo $data['nomel'].' '.$data['prenomel'];
    } else { ?>
        <select name="numel"> 
        <?php while($a = mysqli_fetch_assoc($data)) {
            echo '<option value="'.$a['numel'].'">'.$a['nomel'].' '.$a['prenomel'].'</option>';
        } ?>
        </select><br/><br/>
    <?php } ?>

    Lieu de stage : <input type="text" name="lieu" value="<?php echo $lieu; ?>"><br/><br/>
    Date de début : <input type="text" name="date_debut" class="calendrier" value="<?php echo $date_debut; ?>"><br/><br/>
    Date de fin : <input type="text" name="date_fin" class="calendrier" value="<?php echo $date_fin; ?>"><br/><br/>
    <center><input type="image" src="button.png"></center>
    </form>
    <?php
}
?>
</pre></center>
</div>
</html>
