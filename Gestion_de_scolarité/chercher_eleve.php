<?php
session_start();
include('cadre.php');
include('connect.php');  // utilise $conn

if (isset($_SESSION['admin']) || isset($_SESSION['etudiant']) || isset($_SESSION['prof'])) {
    echo '<div class="corp">';
    echo '<img src="" class="position_titre"><center>';

    // Valeurs initiales (vide ou récupérées du POST)
    $nomel = $_POST['nomel'] ?? '';
    $prenomel = $_POST['prenomel'] ?? '';
    $nomcl = $_POST['nomcl'] ?? '';
    $promo = $_POST['promotion'] ?? '';

    if (!isset($_POST['submit'])) {
        // Formulaire initial ou retour
        $classes = mysqli_query($conn, "SELECT DISTINCT nom FROM classe");
        $promos = mysqli_query($conn, "SELECT DISTINCT promotion FROM classe ORDER BY promotion DESC");
        ?>
        <form action="chercher_eleve.php" method="post" class="formulaire">
            <fieldset>
                <legend>Critère de recherche</legend>
                <label>Nom :</label>
                <input type="text" name="nomel" value="<?= htmlspecialchars($nomel) ?>"><br><br>

                <label>Prénom :</label>
                <input type="text" name="prenomel" value="<?= htmlspecialchars($prenomel) ?>"><br><br>

                <label>Promotion :</label>
                <select name="promotion">
                    <option value="">Choisir la promotion</option>
                    <?php while ($p = mysqli_fetch_assoc($promos)): ?>
                        <option value="<?= htmlspecialchars($p['promotion']) ?>" <?= ($promo === $p['promotion']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($p['promotion']) ?>
                        </option>
                    <?php endwhile; ?>
                </select><br><br>

                <label>Classe :</label>
                <select name="nomcl">
                    <option value="">Choisir la classe</option>
                    <?php while ($c = mysqli_fetch_assoc($classes)): ?>
                        <option value="<?= htmlspecialchars($c['nom']) ?>" <?= ($nomcl === $c['nom']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c['nom']) ?>
                        </option>
                    <?php endwhile; ?>
                </select><br><br>

                <center><input type="submit" name="submit" value="Rechercher"></center>
            </fieldset>
        </form>
        <a href="index.php">Revenir à la page principale !</a>

        <?php
    } else {
        // Traitement du formulaire

        // Nettoyage et trim des entrées
        $nomel = trim($nomel);
        $prenomel = trim($prenomel);
        $nomcl = trim($nomcl);
        $promo = trim($promo);

        // Construction dynamique de la requête
        $query = "
            SELECT eleve.*, classe.nom AS classe_nom, classe.promotion
            FROM eleve 
            INNER JOIN classe ON classe.codecl = eleve.codecl
            WHERE eleve.nomel LIKE ? AND eleve.prenomel LIKE ?
        ";

        $params = ['%' . $nomel . '%', '%' . $prenomel . '%'];
        $types = "ss";

        if ($nomcl !== '') {
            $query .= " AND classe.nom = ?";
            $types .= "s";
            $params[] = $nomcl;
        }

        if ($promo !== '') {
            $query .= " AND classe.promotion = ?";
            $types .= "s";
            $params[] = $promo;
        }

        $stmt = mysqli_prepare($conn, $query);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, $types, ...$params);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($result) > 0) {
                echo '<table border="1" cellpadding="10" cellspacing="0"><thead><tr>
                    <th>Nom</th><th>Prénom</th><th>Adresse</th><th>Date de naissance</th>
                    <th>Téléphone</th><th>Classe</th><th>Promotion</th></tr></thead><tbody>';
                while ($row = mysqli_fetch_assoc($result)) {
                    echo '<tr>
                        <td>' . htmlspecialchars($row['nomel']) . '</td>
                        <td>' . htmlspecialchars($row['prenomel']) . '</td>
                        <td>' . htmlspecialchars($row['adresse']) . '</td>
                        <td>' . htmlspecialchars($row['date_naissance']) . '</td>
                        <td>' . htmlspecialchars($row['telephone']) . '</td>
                        <td>' . htmlspecialchars($row['classe_nom']) . '</td>
                        <td>' . htmlspecialchars($row['promotion']) . '</td>
                    </tr>';
                }
                echo '</tbody></table>';
            } else {
                echo "<p><strong>Aucun élève trouvé avec les critères fournis.</strong></p>";
            }
            mysqli_stmt_close($stmt);
        } else {
            echo "<p><strong>Erreur lors de la préparation de la requête :</strong> " . mysqli_error($conn) . "</p>";
        }

        echo '<br><a href="chercher_eleve.php">Revenir à la page de recherche</a>';
    }

    echo '</center></div>';
} else {
    echo "<p>Accès non autorisé.</p>";
}
?>
