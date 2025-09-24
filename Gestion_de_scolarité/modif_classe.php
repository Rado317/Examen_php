<?php
session_start();
include('cadre.php');

// Connexion mysqli
$conn = new mysqli("localhost", "root", "", "test");
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}
$conn->set_charset("utf8");

// Fonction helper pour sélectionner l'option par défaut dans un select
function choixpardefault2($valeur, $valeurComparaison) {
    return ($valeur === $valeurComparaison) ? "selected" : "";
}

echo '<div class="corp">';
echo '<img src="" class="position_titre">';
echo '<center><pre>';

if (isset($_GET['modif_classe'])) {
    // Affichage formulaire modification
    $id = $conn->real_escape_string($_GET['modif_classe']);
    $sql = "SELECT classe.codecl, classe.nom AS nomcl, promotion, numprofcoord, prof.nom, prof.prenom
            FROM classe
            JOIN prof ON prof.numprof = classe.numprofcoord
            WHERE codecl = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo "Classe introuvable.";
        exit;
    }
    $ligne = $result->fetch_assoc();

    // Récupérer promotions et profs pour les selects
    $promos = $conn->query("SELECT DISTINCT promotion FROM classe ORDER BY promotion");
    $profs = $conn->query("SELECT numprof, nom, prenom FROM prof ORDER BY nom, prenom");

    $nom = htmlspecialchars($ligne['nomcl']);
    $numprof = $ligne['numprofcoord'];
    $promotion = $ligne['promotion'];

    ?>
    <form action="modif_classe.php" method="POST" class="formulaire">
        <h4>Veuillez choisir les nouvelles informations :</h4><br><br>
        Nom de la classe : <input type="text" name="nom" value="<?= $nom ?>" required><br><br>

        Prof coordinateur : 
        <select name="prof" required>
            <?php while ($a = $profs->fetch_assoc()) {
                $selected = choixpardefault2($a['numprof'], $numprof);
                echo '<option value="' . htmlspecialchars($a['numprof']) . '" ' . $selected . '>'
                     . htmlspecialchars($a['nom']) . ' ' . htmlspecialchars($a['prenom']) . '</option>';
            } ?>
        </select><br><br>

        Promotion : 
        <select name="promo" required>
            <?php while ($a = $promos->fetch_assoc()) {
                $selected = choixpardefault2($a['promotion'], $promotion);
                echo '<option value="' . htmlspecialchars($a['promotion']) . '" ' . $selected . '>'
                     . htmlspecialchars($a['promotion']) . '</option>';
            } ?>
        </select><br><br>

        <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">
        <center><input type="submit" value="Modifier"></center>
    </form>
    <br><br>
    <a href="affiche_classe.php">Revenir à la page précédente !</a>
    <?php
    $stmt->close();
} elseif (isset($_POST['nom'], $_POST['prof'], $_POST['promo'], $_POST['id'])) {
    // Traitement modification
    $id = $conn->real_escape_string($_POST['id']);
    $nom = trim($_POST['nom']);
    $prof = $conn->real_escape_string($_POST['prof']);
    $promo = $conn->real_escape_string($_POST['promo']);

    if ($nom === "") {
        echo '<h1>Erreur ! Le nom de la classe ne peut pas être vide.</h1>';
        echo '<br/><br/><a href="modif_classe.php?modif_classe=' . htmlspecialchars($id) . '">Revenir à la page précédente !</a>';
        exit;
    }

    $sql = "UPDATE classe SET nom = ?, numprofcoord = ?, promotion = ? WHERE codecl = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("siss", $nom, $prof, $promo, $id);

    if ($stmt->execute()) {
        echo '<script>alert("Modifié avec succès !");</script>';
        echo '<br/><br/><a href="modif_classe.php?modif_classe=' . htmlspecialchars($id) . '">Revenir à la page précédente !</a>';
    } else {
        echo "<h1>Erreur lors de la modification : " . $stmt->error . "</h1>";
        echo '<br/><br/><a href="modif_classe.php?modif_classe=' . htmlspecialchars($id) . '">Revenir à la page précédente !</a>';
    }

    $stmt->close();

} elseif (isset($_GET['supp_classe'])) {
    // Suppression classe
    $id = $conn->real_escape_string($_GET['supp_classe']);
    $sql = "DELETE FROM classe WHERE codecl = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $id);
    if ($stmt->execute()) {
        echo '<script>alert("Supprimé avec succès !");</script>';
        echo '<br/><br/><a href="affiche_classe.php">Revenir à la page précédente !</a>';
    } else {
        echo "<h1>Erreur lors de la suppression : " . $stmt->error . "</h1>";
        echo '<br/><br/><a href="affiche_classe.php">Revenir à la page précédente !</a>';
    }
    $stmt->close();
} else {
    echo "<h2>Aucune action spécifiée.</h2>";
    echo '<br/><br/><a href="affiche_classe.php">Revenir à la page précédente !</a>';
}

echo '</pre></center></div>';

$conn->close();
?>
