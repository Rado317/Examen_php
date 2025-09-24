<?php
session_start();
$_SESSION['admin'] = 'admin'; // Si tu veux forcer admin connecté
include('cadre.php');
require_once('connect.php'); // Connexion à la base

// Définitions des fonctions manquantes
function Edition() {
    // Colonne pour Modifier et Supprimer
    return '<th>Modifier</th><th>Supprimer</th>';
}

function rond() {
    // Classe CSS pour la première colonne
    return 'rounded-q1';
}

function colspan($start, $end) {
    // Calcule le nombre de colonnes à fusionner dans le tfoot
    return $end - $start;
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Diplômes obtenus</title>
</head>
<body>
<div class="corp">
<img src="" class="position_titre">
<center><pre>
<?php
if (isset($_POST['nomcl']) && isset($_POST['promotion'])) {
    // Sécurisation des entrées
    $nomcl = mysqli_real_escape_string($conn, $_POST['nomcl']);
    $promo = mysqli_real_escape_string($conn, $_POST['promotion']);

    $query = "
    SELECT 
        eleve_diplome.id,
        diplome.titre_dip,
        eleve.nomel,
        eleve.prenomel,
        classe.nom,
        classe.promotion,
        eleve_diplome.note,
        eleve_diplome.commentaire,
        eleve_diplome.etablissement,
        eleve_diplome.lieu,
        eleve_diplome.annee_obtention
    FROM 
        eleve
    INNER JOIN 
        classe ON eleve.codecl = classe.codecl
    INNER JOIN 
        eleve_diplome ON eleve.numel = eleve_diplome.numel
    INNER JOIN 
        diplome ON diplome.numdip = eleve_diplome.numdip
    WHERE 
        classe.nom = '$nomcl' AND classe.promotion = '$promo'
    ";

    $donnee = mysqli_query($conn, $query);

    if (!$donnee) {
        die("Erreur dans la requête : " . mysqli_error($conn));
    }
    ?>
    <center>
    <table id="rounded-corner">
        <thead>
            <tr>
                <?php echo Edition(); ?>
                <th class="<?php echo rond(); ?>">Nom</th>
                <th class="rounded-q2">Prénom</th>
                <th class="rounded-q2">Classe</th>
                <th class="rounded-q2">Promo</th>
                <th class="rounded-q2">Titre du diplôme</th>
                <th class="rounded-q2">Note</th>
                <th class="rounded-q2">Commentaire</th>
                <th class="rounded-q2">Établissement</th>
                <th class="rounded-q2">Lieu</th>
                <th class="rounded-q4">Année d'obtention</th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <td colspan="<?php echo colspan(2,11); ?>" class="rounded-foot-left"><em>&nbsp;</em></td>
                <td class="rounded-foot-right">&nbsp;</td>
            </tr>
        </tfoot>
        <tbody>
        <?php
        while ($a = mysqli_fetch_assoc($donnee)) {
            echo '<tr>';
            if (isset($_SESSION['admin'])) {
                echo '<td><a href="modif_diplome.php?modif_dip=' . $a['id'] . '">Modifier</a></td>';
                echo '<td><a href="modif_diplome.php?supp_dip=' . $a['id'] . '" onclick="return confirm(\'Êtes-vous sûr de vouloir supprimer cette entrée ?\');">Supprimer</a></td>';
            }
            echo '<td>' . htmlspecialchars($a['nomel']) . '</td>';
            echo '<td>' . htmlspecialchars($a['prenomel']) . '</td>';
            echo '<td>' . htmlspecialchars($a['nom']) . '</td>';
            echo '<td>' . htmlspecialchars($a['promotion']) . '</td>';
            echo '<td>' . htmlspecialchars($a['titre_dip']) . '</td>';
            echo '<td>' . htmlspecialchars($a['note']) . '</td>';
            echo '<td>' . htmlspecialchars($a['commentaire']) . '</td>';
            echo '<td>' . htmlspecialchars($a['etablissement']) . '</td>';
            echo '<td>' . htmlspecialchars($a['lieu']) . '</td>';
            echo '<td>' . htmlspecialchars($a['annee_obtention']) . '</td>';
            echo '</tr>';
        }
        ?>
        </tbody>
    </table>
    </center>
    <br><br>
    <a href="diplome_obt.php">Revenir à la page précédente</a>

<?php
} else {
    // Formulaire de sélection
    $data = mysqli_query($conn, "SELECT DISTINCT promotion FROM classe ORDER BY promotion DESC");
    $retour = mysqli_query($conn, "SELECT DISTINCT nom FROM classe");

    if (!$data || !$retour) {
        die("Erreur lors de la récupération des données");
    }
    ?>
    <form method="post" action="diplome_obt.php" class="formulaire">
        Veuillez choisir la classe et la promotion :<br><br>
        Promotion : <select name="promotion"> 
        <?php while($a = mysqli_fetch_assoc($data)) {
            echo '<option value="'.htmlspecialchars($a['promotion']).'">'.htmlspecialchars($a['promotion']).'</option>';
        } ?>
        </select><br><br>
        Classe : <select name="nomcl"> 
        <?php while($a = mysqli_fetch_assoc($retour)) {
            echo '<option value="'.htmlspecialchars($a['nom']).'">'.htmlspecialchars($a['nom']).'</option>';
        } ?>
        </select><br><br>
        <input type="submit" value="Afficher les diplômes obtenus">
    </form>
    <br><br>
    <a href="index.php">Revenir à la page principale</a>
<?php
}
?>
</pre></center>
</div>
</body>
</html>
<?php
mysqli_close($conn);
?>
