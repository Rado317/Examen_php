<?php
session_start();
include('cadre.php');

// Connexion à la base de données avec mysqli
$conn = new mysqli("localhost", "root", "", "test");
if ($conn->connect_error) {
    die("Connexion échouée : " . $conn->connect_error);
}

// ----------------------
// Fonctions utilitaires
// ----------------------
function Edition() {
    if (isset($_SESSION['admin'])) {
        return '<th>Modifier</th><th>Supprimer</th>';
    }
    return '';
}

function rond() {
    // Renvoie la classe CSS utilisée dans ton ancien code
    return 'rounded-q1';
}

function colspan($a, $b) {
    // Si admin, utiliser $b, sinon $a
    return isset($_SESSION['admin']) ? $b : $a;
}

// ----------------------
// Récupération des promotions pour la liste déroulante
// ----------------------
$data = $conn->query("SELECT DISTINCT promotion FROM classe ORDER BY promotion DESC");
?>
<div class="corp">
    <img src="" class="position_titre">
    <center>
        <?php
        // Si formulaire soumis
        if (isset($_POST['nomcl'], $_POST['radiosem'], $_POST['promotion'])) {
            $nomcl = $conn->real_escape_string($_POST['nomcl']);
            $semestre = intval($_POST['radiosem']);
            $promo = $conn->real_escape_string($_POST['promotion']);

            $sql = "
                SELECT enseignement.id, classe.nom AS nomcl, nommat, prof.nom, numsem, promotion 
                FROM enseignement
                JOIN classe ON enseignement.codecl = classe.codecl
                JOIN matiere ON matiere.codemat = enseignement.codemat
                JOIN prof ON prof.numprof = enseignement.numprof
                WHERE classe.nom = '$nomcl' AND promotion = '$promo' AND numsem = $semestre
            ";
            $result = $conn->query($sql);

            if ($result && $result->num_rows > 0) {
                ?>
                <table id="rounded-corner">
                    <thead>
                        <tr>
                            <?php echo Edition(); ?>
                            <th class="<?php echo rond(); ?>">Classe</th>
                            <th class="rounded-q1">Promotion</th>
                            <th class="rounded-q1">Matière</th>
                            <th class="rounded-q1">Professeur</th>
                            <th class="rounded-q4">Semestre</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <td colspan="<?php echo colspan(4, 6); ?>" class="rounded-foot-left"><em>&nbsp;</em></td>
                            <td class="rounded-foot-right">&nbsp;</td>
                        </tr>
                    </tfoot>
                    <tbody>
                        <?php
                        while ($a = $result->fetch_assoc()) {
                            echo "<tr>";
                            if (isset($_SESSION['admin'])) {
                                echo '<td><a href="modif_enseign.php?modif_ensein=' . $a['id'] . '">Modifier</a></td>';
                                echo '<td><a href="modif_enseign.php?supp_ensein=' . $a['id'] . '" onclick="return confirm(\'Êtes-vous sûr de vouloir supprimer cette entrée ?\\nTous les enregistrements liés seront perdus.\');">Supprimer</a></td>';
                            }
                            echo '<td>' . htmlspecialchars($a['nomcl']) . '</td>';
                            echo '<td>' . htmlspecialchars($a['promotion']) . '</td>';
                            echo '<td>' . htmlspecialchars($a['nommat']) . '</td>';
                            echo '<td>' . htmlspecialchars($a['nom']) . '</td>';
                            echo '<td>S' . intval($a['numsem']) . '</td>';
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
                <?php
            } else {
                echo "<p>Aucun résultat trouvé pour cette recherche.</p>";
            }
            ?>
            <br><br><a href="afficher_enseign.php">Revenir à la page précédente</a>
        <?php
        } else {
            // Formulaire
            $retour = $conn->query("SELECT DISTINCT nom FROM classe");
            ?>
            <form method="post" action="afficher_enseign.php" class="formulaire">
                <fieldset>
                    <legend>Critères d'affichage</legend>
                    Classe :
                    <select name="nomcl" required>
                        <?php while ($a = $retour->fetch_assoc()) {
                            echo '<option value="' . htmlspecialchars($a['nom']) . '">' . htmlspecialchars($a['nom']) . '</option>';
                        } ?>
                    </select><br><br>

                    Promotion :
                    <select name="promotion" required>
                        <?php while ($a = $data->fetch_assoc()) {
                            echo '<option value="' . htmlspecialchars($a['promotion']) . '">' . htmlspecialchars($a['promotion']) . '</option>';
                        } ?>
                    </select><br><br>

                    Semestre :
                    <select name="radiosem" required>
                        <?php for ($i = 1; $i <= 4; $i++) {
                            echo '<option value="' . $i . '">Semestre ' . $i . '</option>';
                        } ?>
                    </select><br><br>

                    <input type="submit" value="Afficher">
                </fieldset>
            </form>
        <?php } ?>
    </center>
</div>
</body>
</html>
