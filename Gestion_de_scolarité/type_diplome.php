<?php
session_start();
$_SESSION['admin'] = 'admin';
include('cadre.php');
require_once('connect.php'); // Assure que la connexion à la base est bien établie avec mysqli
?>
<div class="corp">
<img src="" class="position_titre">
<center>
<pre>
<?php
// Suppression d'un diplôme (si demandé dans l'URL)
if (isset($_GET['supp_type'])) {
    $id = intval($_GET['supp_type']); // Sécurisation
    $stmt = $conn->prepare("DELETE FROM diplome WHERE numdip = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    echo "<p style='color:green'>Le diplôme a bien été supprimé.</p>";
}

// Requête pour récupérer tous les diplômes
$result = $conn->query("SELECT * FROM diplome");

if ($result && $result->num_rows > 0) {
    echo '<center><table id="rounded-corner">
    <thead>
    <tr>';
    if (isset($_SESSION['admin'])) echo '<th class="rounded-company">Supprimer</th>';
    echo '<th class="rounded-q1">Titre du diplôme</th>
    </tr></thead>
    <tfoot>
    <tr>
    <td colspan="2" class="rounded-foot-left"><em>&nbsp;</em></td>
    </tr>
    </tfoot>
    <tbody>';

    while ($a = $result->fetch_assoc()) {
        echo "<tr>";
        if (isset($_SESSION['admin'])) {
            echo '<td><a href="type_diplome.php?supp_type=' . $a['numdip'] . '" onclick="return confirm(\'Êtes-vous sûr de vouloir supprimer cette entrée ?\');">Supprimer</a></td>';
        }
        echo '<td>' . htmlspecialchars($a['titre_dip']) . '</td>';
        echo "</tr>";
    }

    echo '</tbody></table></center>';
} else {
    echo "<p>Aucun diplôme trouvé.</p>";
}
?>
</pre>
<br><br><a href="index.php">Revenir à la page principale</a>
</center>
</div>
