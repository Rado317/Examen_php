<?php
session_start();
require_once('connect.php'); // Connexion mysqli, variable $conn
include('cadre.php'); // Layout

// Fonctions pour récupérer promotions et classes
function getPromotions($conn) {
    $result = $conn->query("SELECT DISTINCT promotion FROM classe ORDER BY promotion DESC");
    $promos = [];
    while ($row = $result->fetch_assoc()) {
        $promos[] = $row['promotion'];
    }
    return $promos;
}

function getClasses($conn) {
    $result = $conn->query("SELECT DISTINCT nom FROM classe ORDER BY nom");
    $classes = [];
    while ($row = $result->fetch_assoc()) {
        $classes[] = $row['nom'];
    }
    return $classes;
}

$promotions = getPromotions($conn);
$classes = getClasses($conn);
?>

<div class="corp">
    <img src="" class="position_titre">
    <pre>

<?php
if (isset($_POST['codemat'], $_POST['nomclasse'], $_POST['promo'], $_POST['semestre'])) {
    // Étape 2 : afficher les notes finales
    $codemat = $_POST['codemat'];
    $nomcl = $_POST['nomclasse'];
    $promo = $_POST['promo'];
    $semestre = $_POST['semestre'];

    $sql = "SELECT eleve.nomel, eleve.prenomel, classe.nom, classe.promotion, matiere.nommat, bulletin.numsem, bulletin.notefinal
            FROM eleve
            JOIN bulletin ON eleve.numel = bulletin.numel
            JOIN matiere ON matiere.codemat = bulletin.codemat
            JOIN classe ON classe.codecl = eleve.codecl
            WHERE matiere.codemat = ?
              AND bulletin.numsem = ?
              AND eleve.codecl = (
                SELECT codecl FROM classe WHERE nom = ? AND promotion = ?
              )
            ORDER BY eleve.nomel, eleve.prenomel";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("siss", $codemat, $semestre, $nomcl, $promo);
    $stmt->execute();
    $result = $stmt->get_result();

    echo '<center><table id="rounded-corner">
        <thead>
            <tr>
                <th class="rounded-company">Nom</th>
                <th class="rounded-q1">Prénom</th>
                <th class="rounded-q3">Classe</th>
                <th class="rounded-q3">Promotion</th>
                <th class="rounded-q3">Matière</th>
                <th class="rounded-q3">Semestre</th>
                <th class="rounded-q4">Note Finale</th>
            </tr>
        </thead>
        <tfoot>
            <tr><td colspan="6" class="rounded-foot-left"><em>&nbsp;</em></td>
            <td class="rounded-foot-right">&nbsp;</td></tr>
        </tfoot>
        <tbody>';

    while ($row = $result->fetch_assoc()) {
        echo '<tr>
            <td>' . htmlspecialchars($row['nomel']) . '</td>
            <td>' . htmlspecialchars($row['prenomel']) . '</td>
            <td>' . htmlspecialchars($row['nom']) . '</td>
            <td>' . htmlspecialchars($row['promotion']) . '</td>
            <td>' . htmlspecialchars($row['nommat']) . '</td>
            <td>' . htmlspecialchars($row['numsem']) . '</td>
            <td>' . htmlspecialchars($row['notefinal']) . '</td>
        </tr>';
    }

    echo '</tbody></table></center><br/><br/>';
    echo '<a href="afficher_bulletin.php">Revenir à la page précédente</a>';

    $stmt->close();

} elseif (isset($_POST['nomcl'], $_POST['promotion'], $_POST['radiosem'])) {
    // Étape 1 : choisir la matière
    $nomcl = $_POST['nomcl'];
    $promo = $_POST['promotion'];
    $semestre = $_POST['radiosem'];

    $sql = "SELECT matiere.codemat, matiere.nommat
            FROM enseignement
            JOIN matiere ON enseignement.codemat = matiere.codemat
            WHERE enseignement.codecl = (
                SELECT codecl FROM classe WHERE nom = ? AND promotion = ?
            )
            AND enseignement.numsem = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $nomcl, $promo, $semestre);
    $stmt->execute();
    $result = $stmt->get_result();

    echo '<form method="post" action="afficher_bulletin.php" class="formulaire">';
    echo 'Veuillez choisir la matière pour : ' . htmlspecialchars($nomcl) . ' ' . htmlspecialchars($promo) . '<br/><br/><br/>';
    ?>
    <fieldset>
        <legend>Matières étudiées</legend>
        Matière :
        <select name="codemat">
            <?php while ($row = $result->fetch_assoc()) : ?>
                <option value="<?= htmlspecialchars($row['codemat']) ?>">
                    <?= htmlspecialchars($row['nommat']) ?>
                </option>
            <?php endwhile; ?>
        </select>
        <input type="hidden" name="nomclasse" value="<?= htmlspecialchars($nomcl) ?>">
        <input type="hidden" name="promo" value="<?= htmlspecialchars($promo) ?>">
        <input type="hidden" name="semestre" value="<?= htmlspecialchars($semestre) ?>">
        <input type="submit" value="Afficher les notes finales">
    </fieldset>
    <br/><br/><a href="afficher_bulletin.php">Revenir à la page précédente</a>
    </form>
    <?php
    $stmt->close();

} else {
    // Formulaire initial : choisir semestre, promotion, classe
    ?>
    <form method="post" action="afficher_bulletin.php" class="formulaire">
        Veuillez choisir le Semestre, la promotion et la classe :<br/><br/><br/>
        <fieldset>
            <legend>Critères d'affichage</legend>
            <pre>
Promotion      : <select name="promotion" required>
<?php foreach ($promotions as $p): ?>
    <option value="<?= htmlspecialchars($p) ?>"><?= htmlspecialchars($p) ?></option>
<?php endforeach; ?>
</select>

Classe         : <select name="nomcl" required>
<?php foreach ($classes as $c): ?>
    <option value="<?= htmlspecialchars($c) ?>"><?= htmlspecialchars($c) ?></option>
<?php endforeach; ?>
</select>

Semestre       : <select name="radiosem" required>
<?php for ($i = 1; $i <= 4; $i++): ?>
    <option value="<?= $i ?>">Semestre <?= $i ?></option>
<?php endfor; ?>
</select>

<input type="submit" value="Afficher les matières">
            </pre>
        </fieldset>
    </form>
    <?php
}
?>
    </pre>
</div>
</body>
</html>
