<?php
session_start();
include('cadre.php');

// Définition des fonctions manquantes pour éviter les erreurs
function Edition() {
    // Affiche deux colonnes "Modifier" et "Supprimer"
    return '<th>Modifier</th><th>Supprimer</th>';
}

function rond() {
    return 'rounded-company'; // classe CSS à appliquer
}

function colspan($a, $b) {
    return 8; // nombre fixe de colonnes pour le colspan
}

// Connexion avec mysqli
$conn = new mysqli("localhost", "root", "", "test");
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}
$conn->set_charset("utf8");

// Récupération des promotions et classes
$data = $conn->query("SELECT DISTINCT promotion FROM classe ORDER BY promotion DESC");
$retour = $conn->query("SELECT DISTINCT nom FROM classe");
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Afficher Stage</title>
    <link rel="stylesheet" href="style.css"> <!-- si tu as un CSS -->
</head>
<body>
<div class="corp">
    <img src="" class="position_titre">
    <center><pre>

<?php
if (isset($_POST['nomcl']) && isset($_POST['promotion'])) {
    $nomcl = $conn->real_escape_string($_POST['nomcl']);
    $promo = $conn->real_escape_string($_POST['promotion']);

    $sql = "SELECT stage.numstage, nomel, prenomel, classe.nom, promotion, date_debut, date_fin, lieu_stage 
            FROM eleve 
            JOIN stage ON eleve.numel = stage.numel 
            JOIN classe ON classe.codecl = eleve.codecl 
            WHERE classe.nom = '$nomcl' AND promotion = '$promo'";

    $donnee = $conn->query($sql);

    if ($donnee->num_rows > 0) {
        echo '<center><table id="rounded-corner">';
        echo '<thead><tr>';
        if (isset($_SESSION['admin'])) echo Edition();
        echo '<th class="' . rond() . '">Nom de l\'étudiant</th>
              <th class="rounded-q2">Prénom</th>
              <th class="rounded-q2">Classe</th>
              <th class="rounded-q2">Promotion</th>
              <th class="rounded-q2">Date de début</th>
              <th class="rounded-q2">Date de fin</th>
              <th class="rounded-q4">Lieu du stage</th></tr></thead>';
        echo '<tfoot><tr>
              <td colspan="' . colspan(6, 8) . '" class="rounded-foot-left"><em>&nbsp;</em></td>
              <td class="rounded-foot-right">&nbsp;</td>
              </tr></tfoot><tbody>';

        while ($a = $donnee->fetch_assoc()) {
            echo '<tr>';
            if (isset($_SESSION['admin'])) {
                echo '<td><a href="ajout_stage.php?modif_stage=' . $a['numstage'] . '">Modifier</a></td>';
                echo '<td><a href="supp_stage.php?supp_stage=' . $a['numstage'] . '" onclick="return confirm(\'Êtes-vous sûr de vouloir supprimer cette entrée ?\');">Supprimer</a></td>';
            }
            echo '<td>' . htmlspecialchars($a['nomel']) . '</td>';
            echo '<td>' . htmlspecialchars($a['prenomel']) . '</td>';
            echo '<td>' . htmlspecialchars($a['nom']) . '</td>';
            echo '<td>' . htmlspecialchars($a['promotion']) . '</td>';
            echo '<td>' . htmlspecialchars($a['date_debut']) . '</td>';
            echo '<td>' . htmlspecialchars($a['date_fin']) . '</td>';
            echo '<td>' . htmlspecialchars($a['lieu_stage']) . '</td>';
            echo '</tr>';
        }
        echo '</tbody></table></center>';
    } else {
        echo "<p>Aucun stage trouvé pour cette classe et cette promotion.</p>";
    }
} else {
?>
<form method="post" action="afficher_stage.php" class="formulaire">
    Veuillez choisir la classe et la promotion :<br><br>
    Promotion : 
    <select name="promotion">
        <?php while ($a = $data->fetch_assoc()) {
            echo '<option value="' . htmlspecialchars($a['promotion']) . '">' . htmlspecialchars($a['promotion']) . '</option>';
        } ?>
    </select><br><br>
    Classe :
    <select name="nomcl">
        <?php while ($a = $retour->fetch_assoc()) {
            echo '<option value="' . htmlspecialchars($a['nom']) . '">' . htmlspecialchars($a['nom']) . '</option>';
        } ?>
    </select><br><br>
    <input type="submit" value="Afficher les stages">
</form>
<?php } ?>
<br><br><a href="afficher_stage.php">Revenir à la page précédente</a>
</pre></center>
</div>
</body>
</html>
<?php
$conn->close();
?>
