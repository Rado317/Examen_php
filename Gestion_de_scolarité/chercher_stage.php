<?php
session_start();
$_SESSION['admin'] = 'admin'; // à enlever en prod, ici pour test
include('cadre.php');

if (isset($_SESSION['admin']) || isset($_SESSION['etudiant']) || isset($_SESSION['prof'])) {
    ?>
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <title>Recherche de stage</title>
        <link rel="stylesheet" href="style.css"> <!-- Adapte selon ton CSS -->
    </head>
    <body>
    <div class="corp">
        <img src="" class="position_titre">
        <center><pre>

    <?php
    // Connexion
    $conn = new mysqli("localhost", "root", "", "test");
    if ($conn->connect_error) {
        die("Connexion échouée : " . $conn->connect_error);
    }
    $conn->set_charset("utf8");

    if ($_SERVER['REQUEST_METHOD'] === 'GET' || !isset($_POST['submit'])) {
        // Affichage du formulaire

        // Récupérer promotions et classes pour les selects
        $promosRes = $conn->query("SELECT DISTINCT promotion FROM classe ORDER BY promotion DESC");
        $classesRes = $conn->query("SELECT DISTINCT nom FROM classe");

        $promotions = $promosRes->fetch_all(MYSQLI_ASSOC);
        $classes = $classesRes->fetch_all(MYSQLI_ASSOC);

        ?>
        <form action="chercher_stage.php" method="post" class="formulaire">
            Nom : <input type="text" name="nomel" value=""><br/><br/>
            Prénom : <input type="text" name="prenomel" value=""><br/><br/>
            Vous pouvez préciser la promotion si vous voulez :<br/>
            <select name="promotion">
                <option value="">Choisir la promotion</option>
                <?php foreach ($promotions as $row) {
                    echo '<option value="' . htmlspecialchars($row['promotion']) . '">' . htmlspecialchars($row['promotion']) . '</option>';
                } ?>
            </select><br/><br/>

            Vous pouvez préciser la classe si vous voulez :<br/>
            <select name="nomcl">
                <option value="">Choisir la classe</option>
                <?php foreach ($classes as $row) {
                    echo '<option value="' . htmlspecialchars($row['nom']) . '">' . htmlspecialchars($row['nom']) . '</option>';
                } ?>
            </select><br/><br/>

            <input type="submit" name="submit" value="Chercher">
        </form>

        <br/><br/><a href="index.php">Revenir à la page principale</a>

        <?php
    } else {
        // Traitement du formulaire POST

        // Récupérer et nettoyer les données
        $nomel = $_POST['nomel'] ?? '';
        $prenomel = $_POST['prenomel'] ?? '';
        $nomcl = $_POST['nomcl'] ?? '';
        $promo = $_POST['promotion'] ?? '';

        // Construction dynamique des conditions
        $conditions = [];
        $params = [];
        $types = "";

        if ($nomel !== "") {
            $conditions[] = "eleve.nomel LIKE ?";
            $params[] = "%" . $nomel . "%";
            $types .= "s";
        }
        if ($prenomel !== "") {
            $conditions[] = "eleve.prenomel LIKE ?";
            $params[] = "%" . $prenomel . "%";
            $types .= "s";
        }
        if ($nomcl !== "") {
            $conditions[] = "eleve.codecl IN (SELECT codecl FROM classe WHERE nom = ?)";
            $params[] = $nomcl;
            $types .= "s";
        }
        if ($promo !== "") {
            $conditions[] = "eleve.codecl IN (SELECT codecl FROM classe WHERE promotion = ?)";
            $params[] = $promo;
            $types .= "s";
        }

        $where = count($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";

        $sql = "SELECT eleve.nomel, eleve.prenomel, stage.lieu_stage, stage.date_debut, stage.date_fin,
                       classe.nom, classe.promotion
                FROM eleve
                JOIN stage ON stage.numel = eleve.numel
                JOIN classe ON classe.codecl = eleve.codecl
                $where";

        $stmt = $conn->prepare($sql);
        if ($params) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();

        echo '<center><table id="rounded-corner">';
        echo '<thead><tr>
                <th class="rounded-company">Nom</th>
                <th class="rounded-q1">Prénom</th>
                <th class="rounded-q3">Lieu du stage</th>
                <th class="rounded-q3">Date de début</th>
                <th class="rounded-q3">Date de fin</th>
                <th class="rounded-q3">Classe</th>
                <th class="rounded-q4">Promotion</th>
              </tr></thead>';
        echo '<tfoot><tr><td colspan="6" class="rounded-foot-left"><em>&nbsp;</em></td><td class="rounded-foot-right">&nbsp;</td></tr></tfoot>';
        echo '<tbody>';

        if ($result->num_rows > 0) {
            while ($a = $result->fetch_assoc()) {
                echo "<tr>
                        <td>" . htmlspecialchars($a['nomel']) . "</td>
                        <td>" . htmlspecialchars($a['prenomel']) . "</td>
                        <td>" . htmlspecialchars($a['lieu_stage']) . "</td>
                        <td>" . htmlspecialchars($a['date_debut']) . "</td>
                        <td>" . htmlspecialchars($a['date_fin']) . "</td>
                        <td>" . htmlspecialchars($a['nom']) . "</td>
                        <td>" . htmlspecialchars($a['promotion']) . "</td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='7'>Aucun résultat trouvé.</td></tr>";
        }

        echo '</tbody></table></center>';

        echo '<br/><br/><a href="chercher_stage.php">Revenir à la page précédente</a>';
    }

    $conn->close();
    ?>

        </pre></center>
    </div>
    </body>
    </html>
    <?php
} else {
    echo "<p>Accès non autorisé.</p>";
}
?>
