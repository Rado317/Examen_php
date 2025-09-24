<?php
session_start();
include('cadre.php');

// Connexion sécurisée
$mysqli = new mysqli("localhost", "root", "", "test");
if ($mysqli->connect_error) {
    die("Connexion échouée : " . $mysqli->connect_error);
}
$mysqli->set_charset("utf8");
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajout Conseil de Classe</title>
    <link rel="stylesheet" href="bootstrap.min.css"> <!-- si tu l’utilises -->
    <style>
        .corp {
            padding: 20px;
        }
        .formulaire {
            background-color: #f8f9fa;
            padding: 25px;
            border-radius: 10px;
            max-width: 600px;
            margin: 20px auto;
        }
        .position_titre {
            width: 100%;
            max-height: 200px;
            object-fit: cover;
        }
    </style>
</head>
<body>
<div class="corp">
    <img src="" class="position_titre">

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nomcl'], $_POST['promotion'], $_POST['radiosem'])) {
    $nomcl = $_POST['nomcl'];
    $promo = $_POST['promotion'];
    $semestre = $_POST['radiosem'];

    // Obtenir codecl
    $stmt = $mysqli->prepare("SELECT codecl FROM classe WHERE nom = ? AND promotion = ?");
    $stmt->bind_param("ss", $nomcl, $promo);
    $stmt->execute();
    $result = $stmt->get_result();
    $codecl = $result->fetch_assoc()['codecl'] ?? null;

    if (!$codecl) {
        echo '<div class="alert alert-danger">Classe introuvable !</div>';
    } else {
        // Vérifier si le conseil existe déjà
        $check = $mysqli->prepare("SELECT COUNT(*) as nb FROM conseil WHERE numsem = ? AND codecl = ?");
        $check->bind_param("ii", $semestre, $codecl);
        $check->execute();
        $nb = $check->get_result()->fetch_assoc()['nb'];

        if ($nb > 0) {
            echo '<div class="alert alert-warning">❌ Ce conseil existe déjà pour cette classe et ce semestre.</div>';
        } else {
            // Insertion dans conseil
            $insert = $mysqli->prepare("INSERT INTO conseil (numsem, codecl) VALUES (?, ?)");
            $insert->bind_param("ii", $semestre, $codecl);
            $insert->execute();

            // Calculer les moyennes et remplir le bulletin
            $query = "
                SELECT e.numel, m.codemat, AVG(ev.note) AS moyen
                FROM eleve e
                JOIN evaluation ev ON ev.numel = e.numel
                JOIN devoir d ON d.numdev = ev.numdev
                JOIN matiere m ON m.codemat = d.codemat
                WHERE d.codecl = ? AND d.numsem = ?
                GROUP BY e.numel, m.codemat
            ";
            $stmt = $mysqli->prepare($query);
            $stmt->bind_param("ii", $codecl, $semestre);
            $stmt->execute();
            $bulletins = $stmt->get_result();

            $insertBulletin = $mysqli->prepare("INSERT INTO bulletin (numsem, numel, codemat, notefinal) VALUES (?, ?, ?, ?)");

            while ($row = $bulletins->fetch_assoc()) {
                $insertBulletin->bind_param(
                    "iiid",
                    $semestre,
                    $row['numel'],
                    $row['codemat'],
                    $row['moyen']
                );
                $insertBulletin->execute();
            }

            echo '<div class="alert alert-success">✅ Conseil ajouté et bulletins générés avec succès !</div>';
        }
    }

    echo '<a href="ajout_conseil.php" class="btn btn-secondary mt-3">⬅️ Retour</a>';

} else {
    // Formulaire
    $promotions = $mysqli->query("SELECT DISTINCT promotion FROM classe ORDER BY promotion DESC");
    $classes = $mysqli->query("SELECT DISTINCT nom FROM classe");

    ?>
    <form method="post" action="ajout_conseil.php" class="formulaire">
        <h4>Ajouter un Conseil de Classe</h4><br/>
        <div class="mb-3">
            <label>Promotion :</label>
            <select name="promotion" class="form-select" required>
                <?php while ($row = $promotions->fetch_assoc()) : ?>
                    <option value="<?= htmlspecialchars($row['promotion']) ?>"><?= htmlspecialchars($row['promotion']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="mb-3">
            <label>Classe :</label>
            <select name="nomcl" class="form-select" required>
                <?php while ($row = $classes->fetch_assoc()) : ?>
                    <option value="<?= htmlspecialchars($row['nom']) ?>"><?= htmlspecialchars($row['nom']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="mb-3">
            <label>Semestre :</label>
            <select name="radiosem" class="form-select" required>
                <?php for ($i = 1; $i <= 4; $i++) : ?>
                    <option value="<?= $i ?>">Semestre <?= $i ?></option>
                <?php endfor; ?>
            </select>
        </div>

        <button type="submit" class="btn btn-primary w-100">Valider le Conseil</button>
    </form>
<?php
}
?>
</div>
</body>
</html>
