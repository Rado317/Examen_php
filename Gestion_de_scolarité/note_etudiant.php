<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once('connect.php'); // doit définir $conn

include('cadre.php');

if (!isset($_SESSION['etudiant'])) {
    echo "<div class='corp'><p>Erreur : Aucun étudiant connecté. Veuillez vous connecter.</p></div>";
    exit;
}

$id = intval($_SESSION['etudiant']);

$query = "
    SELECT b.numel, e.nomel, e.prenomel, m.nommat, b.numsem, c.promotion, b.notefinal, c.nom
    FROM bulletin b
    JOIN eleve e ON b.numel = e.numel
    JOIN matiere m ON b.codemat = m.codemat
    JOIN classe c ON e.codecl = c.codecl
    WHERE b.numel = ?
    ORDER BY b.numsem
";

if (!$stmt = $conn->prepare($query)) {
    die("Erreur préparation requête : " . $conn->error);
}

$stmt->bind_param("i", $id);

if (!$stmt->execute()) {
    die("Erreur exécution requête : " . $stmt->error);
}

$result = $stmt->get_result();

?>

<div class="corp">
    <img src="titre_img/affich_stage.png" class="position_titre">
    <center>
        <?php if ($result->num_rows === 0): ?>
            <p>Aucun bulletin trouvé pour cet étudiant.</p>
        <?php else: ?>
            <table id="rounded-corner">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Classe</th>
                        <th>Promotion</th>
                        <th>Matière</th>
                        <th>Note finale</th>
                        <th>Semestre</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['nomel']) ?></td>
                        <td><?= htmlspecialchars($row['prenomel']) ?></td>
                        <td><?= htmlspecialchars($row['nom']) ?></td>
                        <td><?= htmlspecialchars($row['promotion']) ?></td>
                        <td><?= htmlspecialchars($row['nommat']) ?></td>
                        <td><?= htmlspecialchars($row['notefinal']) ?></td>
                        <td><?= 'S' . htmlspecialchars($row['numsem']) ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </center>
    <br><br>
    <a href="index.php">Revenir à la page précédente</a>
</div>

<?php
$stmt->close();
$conn->close();
?>
