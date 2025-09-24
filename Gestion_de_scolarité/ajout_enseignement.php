<?php
session_start();
include('cadre.php');

$conn = new mysqli("localhost", "root", "", "test");
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}
?>
<html>
<div class="corp">
<img src="" class="position_titre">
<?php
if (isset($_POST['nomcl'])) {
    $_SESSION['nomcl'] = $_POST['nomcl'];
    $nomcl = $_POST['nomcl'];
    $promo = $_POST['promotion'];
    $_SESSION['promo'] = $promo; // pour l'envoyer la 2eme fois 

    // Préparer et exécuter la requête matières
    $stmt = $conn->prepare("SELECT codemat, nommat FROM matiere INNER JOIN classe ON matiere.codecl = classe.codecl WHERE classe.nom = ? AND promotion = ?");
    $stmt->bind_param("ss", $nomcl, $promo);
    $stmt->execute();
    $donnee = $stmt->get_result();

    // Requête professeurs
    $prof = $conn->query("SELECT numprof, nom, prenom FROM prof");
    ?>
    <form action="ajout_enseignement.php" method="POST" class="formulaire">
        <fieldset>
            <legend>Ajout d'un enseignement</legend>
            Matière : 
            <select name="choix_mat" id="choix">
                <?php while ($a = $donnee->fetch_assoc()) {
                    echo '<option value="' . htmlspecialchars($a['codemat']) . '">' . htmlspecialchars($a['nommat']) . '</option>';
                } ?>
            </select><br/><br/>
            Enseignant : 
            <select name="n_prof">
                <?php while ($prof2 = $prof->fetch_assoc()) {
                    echo '<option value="' . htmlspecialchars($prof2['numprof']) . '">' . htmlspecialchars($prof2['nom']) . ' ' . htmlspecialchars($prof2['prenom']) . '</option>';
                } ?>
            </select><br/><br/>
            Semestre : 
            <select name="semestre">
                <?php for ($i = 1; $i <= 4; $i++) {
                    echo '<option value="' . $i . '">Semestre ' . $i . '</option>';
                } ?>
            </select><br/><br/>
            <center><input type="submit" value="Ajouter"></center>
        </fieldset>
    </form>
    <?php
    $stmt->close();
} elseif (isset($_POST['semestre'])) {
    $semestre = (int)$_POST['semestre'];
    $codemat = $_POST['choix_mat'];
    $nomcl = $_SESSION['nomcl'];
    $n_prof = $_POST['n_prof'];
    $promo = $_SESSION['promo'];

    // Récupérer codecl
    $stmt = $conn->prepare("SELECT codecl FROM classe WHERE nom = ? AND promotion = ?");
    $stmt->bind_param("ss", $nomcl, $promo);
    $stmt->execute();
    $res = $stmt->get_result();
    $codeclasse = $res->fetch_assoc();
    if (!$codeclasse) {
        echo '<h2>Classe introuvable</h2>';
        exit;
    }
    $codecl = $codeclasse['codecl'];
    $stmt->close();

    // Vérifier doublon enseignement
    $stmt = $conn->prepare("SELECT COUNT(*) as nb FROM enseignement WHERE codecl = ? AND codemat = ? AND numsem = ?");
    $stmt->bind_param("isi", $codecl, $codemat, $semestre);
    $stmt->execute();
    $res = $stmt->get_result();
    $nb = $res->fetch_assoc();
    $stmt->close();

    if ($nb['nb'] > 0) {
        echo '<h2>Erreur d\'insertion!! (impossible d\'ajouter deux enseignements similaires)</h2>';
        ?>
        <script>alert("Erreur d'insertion\nimpossible d'ajouter deux enseignements similaires");</script>
        <?php
    } else {
        $stmt = $conn->prepare("INSERT INTO enseignement(codecl, codemat, numprof, numsem) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isis", $codecl, $codemat, $n_prof, $semestre);
        if ($stmt->execute()) {
            ?>
            <script>alert("Ajouté avec succès!");</script>
            <?php
        } else {
            echo "<h2>Erreur lors de l'insertion : " . $conn->error . "</h2>";
        }
        $stmt->close();
    }
    echo '<br/><br/><a href="ajout_enseignement.php">Revenir à la page précédente !</a>';
} else {
    // Formulaire de choix classe/promotion
    $data = $conn->query("SELECT DISTINCT promotion FROM classe ORDER BY promotion DESC");
    $donnee = $conn->query("SELECT DISTINCT nom FROM classe");
    ?>
    <form action="ajout_enseignement.php" method="POST" class="formulaire">
        <fieldset>
            <legend>Critères d'ajout</legend>
            Classe : 
            <select name="nomcl">
                <?php while ($a = $donnee->fetch_assoc()) {
                    echo '<option value="' . htmlspecialchars($a['nom']) . '">' . htmlspecialchars($a['nom']) . '</option>';
                } ?>
            </select><br/><br/>
            Promotion : 
            <select name="promotion">
                <?php while ($a = $data->fetch_assoc()) {
                    echo '<option value="' . htmlspecialchars($a['promotion']) . '">' . htmlspecialchars($a['promotion']) . '</option>';
                } ?>
            </select><br/><br/>
            <center><input type="submit" value="Afficher"></center>
        </fieldset>
    </form>
    <?php
}
$conn->close();
?>
</div>
</html>
