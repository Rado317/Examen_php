<?php
session_start();
include('cadre.php');

if (!isset($_SESSION['admin']) && !isset($_SESSION['etudiant']) && !isset($_SESSION['prof'])) {
    echo '<p>Accès non autorisé.</p>';
    exit;
}

// Connexion à la base
$conn = new mysqli("localhost", "root", "", "test");
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}
$conn->set_charset("utf8");

// Fonction pour récupérer promotions
function getPromotions($conn) {
    $promos = [];
    $result = $conn->query("SELECT DISTINCT promotion FROM classe ORDER BY promotion DESC");
    while ($row = $result->fetch_assoc()) {
        $promos[] = $row['promotion'];
    }
    return $promos;
}

// Fonction pour récupérer classes
function getClasses($conn) {
    $classes = [];
    $result = $conn->query("SELECT DISTINCT nom FROM classe ORDER BY nom");
    while ($row = $result->fetch_assoc()) {
        $classes[] = $row['nom'];
    }
    return $classes;
}

// Récupérer listes promos/classes pour le formulaire
$promotions = getPromotions($conn);
$classes = getClasses($conn);

// Initialiser valeurs soumises
$nomel = $_POST['nomel'] ?? '';
$prenomel = $_POST['prenomel'] ?? '';
$promotion = $_POST['promotion'] ?? '';
$nomcl = $_POST['nomcl'] ?? '';

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Recherche Professeur</title>
    <link rel="stylesheet" href="style.css"> <!-- Adapté à ton CSS -->
</head>
<body>
<div class="corp">
    <img src="" class="position_titre">
    <center>
        <form method="post" action="chercher_prof.php" class="formulaire" style="margin:20px 0;">
            <fieldset style="width: 320px; text-align:left;">
                <legend>Critères de recherche</legend>
                <label for="nomel">Nom du prof :</label><br>
                <input type="text" id="nomel" name="nomel" value="<?= htmlspecialchars($nomel) ?>"><br><br>

                <label for="prenomel">Prénom du prof :</label><br>
                <input type="text" id="prenomel" name="prenomel" value="<?= htmlspecialchars($prenomel) ?>"><br><br>

                <label for="promotion">Promotion :</label><br>
                <select id="promotion" name="promotion">
                    <option value="">Choisir la promotion</option>
                    <?php foreach ($promotions as $promo) : ?>
                        <option value="<?= htmlspecialchars($promo) ?>" <?= ($promotion === $promo) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($promo) ?>
                        </option>
                    <?php endforeach; ?>
                </select><br><br>

                <label for="nomcl">Classe :</label><br>
                <select id="nomcl" name="nomcl">
                    <option value="">Choisir la classe</option>
                    <?php foreach ($classes as $classe) : ?>
                        <option value="<?= htmlspecialchars($classe) ?>" <?= ($nomcl === $classe) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($classe) ?>
                        </option>
                    <?php endforeach; ?>
                </select><br><br>

                <input type="submit" value="Rechercher">
            </fieldset>
        </form>

<?php
// Si formulaire soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Construire la requête dynamique
    $query = "
        SELECT prof.nom AS nomp, prof.prenom AS prenomp, prof.adresse, prof.telephone, 
               classe.nom AS nomcl, classe.promotion, matiere.nommat
        FROM prof
        INNER JOIN enseignement ON prof.numprof = enseignement.numprof
        INNER JOIN classe ON classe.codecl = enseignement.codecl
        INNER JOIN matiere ON matiere.codemat = enseignement.codemat
        WHERE prof.nom LIKE ? AND prof.prenom LIKE ?";

    $types = "ss";
    $params = ['%' . $nomel . '%', '%' . $prenomel . '%'];

    if ($nomcl !== '') {
        $query .= " AND classe.nom = ?";
        $types .= "s";
        $params[] = $nomcl;
    }
    if ($promotion !== '') {
        $query .= " AND classe.promotion = ?";
        $types .= "s";
        $params[] = $promotion;
    }

    $stmt = $conn->prepare($query);
    if (!$stmt) {
        echo "<p>Erreur lors de la préparation de la requête : " . htmlspecialchars($conn->error) . "</p>";
        exit;
    }
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo '<table id="rounded-corner" style="margin-top:20px;">';
        echo '<thead><tr>
                <th>Nom du prof</th>
                <th>Prénom du prof</th>
                <th>Adresse</th>
                <th>Téléphone</th>
                <th>Classe enseignée</th>
                <th>Matière enseignée</th>
                <th>Promotion</th>
              </tr></thead><tbody>';
        while ($row = $result->fetch_assoc()) {
            echo '<tr>
                    <td>' . htmlspecialchars($row['nomp']) . '</td>
                    <td>' . htmlspecialchars($row['prenomp']) . '</td>
                    <td>' . htmlspecialchars($row['adresse']) . '</td>
                    <td>' . htmlspecialchars($row['telephone']) . '</td>
                    <td>' . htmlspecialchars($row['nomcl']) . '</td>
                    <td>' . htmlspecialchars($row['nommat']) . '</td>
                    <td>' . htmlspecialchars($row['promotion']) . '</td>
                  </tr>';
        }
        echo '</tbody></table>';
    } else {
        echo '<p><strong>Aucun professeur trouvé avec ces critères.</strong></p>';
    }

    $stmt->close();
}

$conn->close();
?>
    <br>
    <a href="index.php">Revenir à la page principale</a>
    </center>
</div>
</body>
</html>
