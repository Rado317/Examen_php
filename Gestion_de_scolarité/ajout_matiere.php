<?php
session_start();
include('cadre.php'); // ta mise en page (menu/navigation/etc.)

// Connexion sécurisée à la base de données
$mysqli = new mysqli("localhost", "root", "", "test");
if ($mysqli->connect_error) {
    die("Erreur de connexion : " . $mysqli->connect_error);
}
$mysqli->set_charset("utf8");
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajout matière</title>
    <link href="bootstrap.min.css" rel="stylesheet">
    <style>
        .corp {
            padding: 30px;
        }
        .formulaire {
            margin: 30px auto;
            max-width: 600px;
            background: #f9f9f9;
            border-radius: 10px;
            padding: 20px;
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

    <div class="formulaire">
        <?php
        // Étape 1 : Sélection promotion et classe
        if (!isset($_POST['nommat']) && !isset($_POST['promotion'])) {
            $resultPromo = $mysqli->query("SELECT DISTINCT promotion FROM classe ORDER BY promotion DESC");
            $resultClasse = $mysqli->query("SELECT DISTINCT nom FROM classe");

            echo '<form action="" method="POST">';
            echo '<div class="mb-3">';
            echo '<label>Promotion :</label>';
            echo '<select name="promotion" class="form-select">';
            while ($row = $resultPromo->fetch_assoc()) {
                echo '<option value="' . htmlspecialchars($row['promotion']) . '">' . htmlspecialchars($row['promotion']) . '</option>';
            }
            echo '</select></div>';

            echo '<div class="mb-3">';
            echo '<label>Classe :</label>';
            echo '<select name="nomcl" class="form-select">';
            while ($row = $resultClasse->fetch_assoc()) {
                echo '<option value="' . htmlspecialchars($row['nom']) . '">' . htmlspecialchars($row['nom']) . '</option>';
            }
            echo '</select></div>';

            echo '<button type="submit" class="btn btn-primary w-100">Suivant</button>';
            echo '</form>';
        }

        // Étape 2 : Formulaire de saisie de la matière
        else if (isset($_POST['promotion']) && !isset($_POST['nommat'])) {
            $_SESSION['promo'] = $_POST['promotion'];
            $_SESSION['nomcl'] = $_POST['nomcl'];
            echo '<form action="" method="POST">';
            echo '<div class="mb-3">';
            echo '<label for="nommat">Veuillez saisir la nouvelle matière :</label>';
            echo '<input type="text" class="form-control" name="nommat" required>';
            echo '</div>';
            echo '<button type="submit" class="btn btn-success w-100">Ajouter la matière</button>';
            echo '</form>';
        }

        // Étape 3 : Traitement de l'ajout de la matière
        else if (isset($_POST['nommat'])) {
            $nommat = htmlspecialchars(trim($_POST['nommat']));
            $promo = $_SESSION['promo'];
            $nomcl = $_SESSION['nomcl'];

            if (!empty($nommat)) {
                // Récupérer le code de la classe
                $stmt = $mysqli->prepare("SELECT codecl FROM classe WHERE nom = ? AND promotion = ?");
                $stmt->bind_param("ss", $nomcl, $promo);
                $stmt->execute();
                $result = $stmt->get_result();
                $row = $result->fetch_assoc();

                if ($row) {
                    $codecl = $row['codecl'];

                    // Vérifier si la matière existe déjà
                    $stmt2 = $mysqli->prepare("SELECT COUNT(*) as nb FROM matiere WHERE nommat = ? AND codecl = ?");
                    $stmt2->bind_param("si", $nommat, $codecl);
                    $stmt2->execute();
                    $res2 = $stmt2->get_result()->fetch_assoc();

                    if ($res2['nb'] > 0) {
                        echo '<div class="alert alert-warning">Erreur : cette matière existe déjà pour cette classe.</div>';
                    } else {
                        // Insertion
                        $stmt3 = $mysqli->prepare("INSERT INTO matiere (nommat, codecl) VALUES (?, ?)");
                        $stmt3->bind_param("si", $nommat, $codecl);
                        if ($stmt3->execute()) {
                            echo '<div class="alert alert-success">Ajouté avec succès !</div>';
                        } else {
                            echo '<div class="alert alert-danger">Erreur lors de l\'insertion.</div>';
                        }
                    }
                } else {
                    echo '<div class="alert alert-danger">Classe introuvable.</div>';
                }
            } else {
                echo '<div class="alert alert-danger">Veuillez remplir tous les champs.</div>';
            }

            echo '<a href="ajout_matiere.php" class="btn btn-secondary mt-3">Revenir à la page précédente</a>';
        }
        ?>
    </div>
</div>
</body>
</html>
