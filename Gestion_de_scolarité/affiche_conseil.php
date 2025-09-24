<?php
session_start();
include('cadre.php');

// Connexion à la base de données (modifie les identifiants si besoin)
$conn = mysqli_connect("localhost", "root", "", "test");
if (!$conn) {
    die("Erreur de connexion : " . mysqli_connect_error());
}

// Chargement des promotions et des classes pour le formulaire
$data = mysqli_query($conn, "SELECT DISTINCT promotion FROM classe ORDER BY promotion DESC");
$retour = mysqli_query($conn, "SELECT DISTINCT nom FROM classe");

// Suppression d’un conseil
if (isset($_GET['supp_conseil'])) {
    $id = intval($_GET['supp_conseil']); // sécurisation avec intval()
    mysqli_query($conn, "DELETE FROM conseil WHERE id = $id");
    echo '<script>alert("Supprimé avec succès !"); window.location.href = "affiche_conseil.php";</script>';
    exit();
}

// Affichage des conseils selon les critères
elseif (isset($_POST['nomcl']) && isset($_POST['numsem']) && isset($_POST['promotion'])) {
    $nomcl = mysqli_real_escape_string($conn, $_POST['nomcl']);
    $promo = mysqli_real_escape_string($conn, $_POST['promotion']);
    $numsem = intval($_POST['numsem']);

    $query = "
        SELECT conseil.id, conseil.numsem, classe.nom 
        FROM conseil 
        INNER JOIN classe ON classe.codecl = conseil.codecl 
        WHERE classe.nom = '$nomcl' 
          AND classe.promotion = '$promo' 
          AND conseil.numsem = '$numsem'
    ";

    $donnee = mysqli_query($conn, $query);

    ?>
    <center>
        <h3>Liste des conseils</h3>
        <table id="rounded-corner">
            <thead>
                <tr>
                    <?php if (isset($_SESSION['admin'])) echo '<th class="rounded-company">Supprimer</th>'; ?>
                    <th class="rounded-q1">Semestre</th>
                    <th class="rounded-q4">Classe</th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <td colspan="3" class="rounded-foot-left"><em>&nbsp;</em></td>
                </tr>
            </tfoot>
            <tbody>
                <?php
                if (mysqli_num_rows($donnee) === 0) {
                    echo '<tr><td colspan="3">Aucun conseil trouvé pour cette classe.</td></tr>';
                } else {
                    while ($a = mysqli_fetch_assoc($donnee)) {
                        echo '<tr>';
                        if (isset($_SESSION['admin'])) {
                            echo '<td><a href="affiche_conseil.php?supp_conseil=' . $a['id'] . '" onclick="return confirm(\'Êtes-vous sûr de vouloir supprimer cette entrée ?\')">Supprimer</a></td>';
                        }
                        echo '<td>S' . htmlspecialchars($a['numsem']) . '</td><td>' . htmlspecialchars($a['nom']) . '</td></tr>';
                    }
                }
                ?>
            </tbody>
        </table>
        <br><a href="affiche_conseil.php">Revenir à la recherche</a>
    </center>
    <?php
}
// Formulaire par défaut
else {
?>
    <form method="post" action="affiche_conseil.php" class="formulaire">
        <h3>Afficher les conseils</h3>
        <label>Classe :</label>
        <select name="nomcl" required>
            <?php while ($a = mysqli_fetch_assoc($retour)) {
                echo '<option value="' . htmlspecialchars($a['nom']) . '">' . htmlspecialchars($a['nom']) . '</option>';
            } ?>
        </select><br/><br/>

        <label>Promotion :</label>
        <select name="promotion" required>
            <?php while ($a = mysqli_fetch_assoc($data)) {
                echo '<option value="' . htmlspecialchars($a['promotion']) . '">' . htmlspecialchars($a['promotion']) . '</option>';
            } ?>
        </select><br/><br/>

        <label>Semestre :</label>
        <select name="numsem" required>
            <?php for ($i = 1; $i <= 4; $i++) {
                echo '<option value="' . $i . '">Semestre ' . $i . '</option>';
            } ?>
        </select><br/><br/>

        <input type="submit" value="Afficher les conseils">
    </form>
<?php
}
?>
