<?php
session_start();
include('cadre.php');
include('calendrier.html');

$conn = new mysqli("localhost", "root", "", "test");
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}
$conn->set_charset("utf8");

echo '<div class="corp">';

if (isset($_GET['modif_dev'])) {
    // Affichage du formulaire avec données actuelles
    $id = $conn->real_escape_string($_GET['modif_dev']);
    $sql = "SELECT devoir.*, classe.nom, classe.promotion, matiere.nommat
            FROM devoir
            JOIN classe ON classe.codecl = devoir.codecl
            JOIN matiere ON matiere.codemat = devoir.codemat
            WHERE devoir.numdev = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo "<p>Devoir introuvable.</p>";
        exit;
    }
    $ligne = $result->fetch_assoc();
    $date = $ligne['date_dev'];
    ?>

    <center><pre><h1>Modifier un devoir</h1>
    <form action="modif_devoir.php" method="POST" class="formulaire">
        Matière : <?= htmlspecialchars($ligne['nommat']) ?><br/>
        Classe : <?= htmlspecialchars($ligne['nom']) ?><br/>
        Promotion : <?= htmlspecialchars($ligne['promotion']) ?><br/>
        Coefficient : <input type="text" name="coeficient" value="<?= htmlspecialchars($ligne['coeficient']) ?>" required><br/>
        Semestre : <?= htmlspecialchars($ligne['numsem']) ?><br/>
        Devoir N° : <input type="text" name="n_devoir" value="<?= htmlspecialchars($ligne['n_devoir']) ?>" required><br/>
        Date du devoir : <input type="text" name="date" class="calendrier" value="<?= htmlspecialchars($date) ?>" required />
        <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">
        <br/><br/>
        <input type="submit" value="Modifier">
    </form>
    <br/><br/><a href="afficher_devoir.php">Revenir à la page précédente !</a>
    </pre></center>

    <?php
    $stmt->close();

} elseif (isset($_POST['n_devoir'], $_POST['date'], $_POST['coeficient'], $_POST['id'])) {
    // Traitement modification
    $id = $_POST['id'];
    $n_devoir = $_POST['n_devoir'];
    $date = $_POST['date'];
    $coeficient = $_POST['coeficient'];

    if (($n_devoir == "1" || $n_devoir == "2") && !empty($date) && !empty($coeficient)) {
        // Vérifier qu'un autre devoir similaire n'existe pas (avec même numéro, même date, même id)
        $sql_check = "SELECT COUNT(*) AS nb FROM devoir WHERE n_devoir = ? AND date_dev = ? AND numdev != ?";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param("ssi", $n_devoir, $date, $id);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();
        $count = $result_check->fetch_assoc()['nb'];
        $stmt_check->close();

        if ($count != 0) {
            echo '<script>alert("Erreur de modification : ce devoir existe déjà (vérifiez le numéro et la date).");</script>';
        } else {
            // Mise à jour
            $sql_update = "UPDATE devoir SET n_devoir = ?, coeficient = ?, date_dev = ? WHERE numdev = ?";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bind_param("idsi", $n_devoir, $coeficient, $date, $id);

            if ($stmt_update->execute()) {
                echo '<script>alert("Modifié avec succès !");</script>';
            } else {
                echo '<script>alert("Erreur lors de la modification : ' . $stmt_update->error . '");</script>';
            }
            $stmt_update->close();
        }
    } else {
        echo '<script>alert("Erreur ! Vous devez remplir tous les champs correctement (n° de devoir 1 ou 2).");</script>';
    }

    echo '<br/><br/><a href="modif_devoir.php?modif_dev=' . htmlspecialchars($id) . '">Revenir à la page précédente !</a>';

} elseif (isset($_GET['supp_dev'])) {
    // Suppression devoir + évaluations associées
    $id = $conn->real_escape_string($_GET['supp_dev']);

    $stmt1 = $conn->prepare("DELETE FROM evaluation WHERE numdev = ?");
    $stmt1->bind_param("s", $id);
    $stmt1->execute();
    $stmt1->close();

    $stmt2 = $conn->prepare("DELETE FROM devoir WHERE numdev = ?");
    $stmt2->bind_param("s", $id);
    if ($stmt2->execute()) {
        echo '<script>alert("Supprimé avec succès ! Toutes les évaluations associées ont été supprimées.");</script>';
    } else {
        echo '<script>alert("Erreur lors de la suppression : ' . $stmt2->error . '");</script>';
    }
    $stmt2->close();

    echo '<br/><br/><a href="afficher_devoir.php">Revenir à la page d\'affichage</a>';

} else {
    echo '<p>Aucune action spécifiée.</p>';
    echo '<br/><br/><a href="afficher_devoir.php">Revenir à la page précédente</a>';
}

echo '</div>';

$conn->close();
?>
