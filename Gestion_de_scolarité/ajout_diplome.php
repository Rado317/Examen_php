<?php
session_start();
include('cadre.php');

$conn = mysqli_connect("localhost", "root", "", "test");
if (!$conn) {
    die("Erreur de connexion à la base : " . mysqli_connect_error());
}

// Fonction pour échapper les valeurs (pour plus de clarté)
function escape($conn, $val) {
    return mysqli_real_escape_string($conn, trim($val));
}

// Affichage du formulaire pour ajouter un type de diplôme (titre)
function afficherFormAjoutTitre() {
    ?>
    <img src="" class="position_titre">
    <form action="ajout_diplome.php" method="POST" class="formulaire">
        Veuillez saisir le titre du diplôme à ajouter : <br/><br/>
        <input type="text" name="ajout_titre" required><br/><br/>
        <center><input type="submit" value="Ajouter"></center>
    </form>
    <?php
}

// Affichage du formulaire pour choisir promotion et classe
function afficherFormChoixClasse($conn) {
    $data = mysqli_query($conn, "SELECT DISTINCT promotion FROM classe ORDER BY promotion DESC");
    $nomclasse = mysqli_query($conn, "SELECT DISTINCT nom FROM classe ORDER BY nom");
    ?>
    <img src=" class="position_titre">
    <form action="ajout_diplome.php" method="POST" class="formulaire">
        Veuillez choisir la classe et la promotion : <br/><br/>
        Promotion : <select name="promotion" required>
            <?php while ($a = mysqli_fetch_assoc($data)): ?>
                <option value="<?= htmlspecialchars($a['promotion']) ?>"><?= htmlspecialchars($a['promotion']) ?></option>
            <?php endwhile; ?>
        </select><br/><br/>
        Classe : <select name="nomcl" required>
            <?php while ($a = mysqli_fetch_assoc($nomclasse)): ?>
                <option value="<?= htmlspecialchars($a['nom']) ?>"><?= htmlspecialchars($a['nom']) ?></option>
            <?php endwhile; ?>
        </select><br/><br/>
        <input type="submit" value="Suivant">
    </form>
    <?php
}

// Affichage du formulaire pour saisir les informations d’un diplôme à un étudiant
function afficherFormDiplomeEtudiant($conn, $nomcl, $promo) {
    $nomcl = escape($conn, $nomcl);
    $promo = escape($conn, $promo);

    $data = mysqli_query($conn, "SELECT numel, nomel, prenomel FROM eleve WHERE codecl=(SELECT codecl FROM classe WHERE nom='$nomcl' AND promotion='$promo')");
    $titre = mysqli_query($conn, "SELECT numdip, titre_dip FROM diplome ORDER BY titre_dip");

    ?>
    <img src="" class="position_titre">
    <form action="ajout_diplome.php" method="POST" class="formulaire">
        Veuillez remplir les informations : <br/>
        Etudiant : <select name="numel" required>
            <?php while ($a = mysqli_fetch_assoc($data)): ?>
                <option value="<?= htmlspecialchars($a['numel']) ?>"><?= htmlspecialchars($a['nomel']) ?> <?= htmlspecialchars($a['prenomel']) ?></option>
            <?php endwhile; ?>
        </select><br/><br/>
        Titre du diplôme : <select name="titre" required>
            <?php while ($var = mysqli_fetch_assoc($titre)): ?>
                <option value="<?= htmlspecialchars($var['numdip']) ?>"><?= htmlspecialchars($var['titre_dip']) ?></option>
            <?php endwhile; ?>
        </select><br/><br/>
        Note : <input type="text" name="note" required><br/><br/>
        Commentaire : <input type="text" name="comment" required><br/><br/>
        Établissement : <input type="text" name="etabli" required><br/><br/>
        Lieu : <input type="text" name="lieu" required><br/><br/>
        Année d'obtention : <input type="text" name="ann_obt" required pattern="\d{4}" title="Année sur 4 chiffres"><br/><br/>
        <center><input type="submit" value="Ajouter"></center>
    </form>
    <?php
}

// Partie traitement POST pour ajout titre
if (isset($_POST['ajout_titre'])) {
    $titre = escape($conn, $_POST['ajout_titre']);

    $res = mysqli_query($conn, "SELECT COUNT(*) as nb FROM diplome WHERE titre_dip='$titre'");
    $nb = mysqli_fetch_assoc($res)['nb'];

    if ($nb > 0) {
        echo '<script>alert("Erreur! Ce diplôme existe déjà!");window.location.href="ajout_diplome.php?ajout_type";</script>';
        exit;
    }

    $sql = "INSERT INTO diplome(titre_dip) VALUES ('$titre')";
    if (mysqli_query($conn, $sql)) {
        echo '<script>alert("Ajout réussi!");window.location.href="ajout_diplome.php?ajout_type";</script>';
        exit;
    } else {
        echo "<h2>Erreur lors de l'insertion : " . mysqli_error($conn) . "</h2>";
        exit;
    }
}

// Partie traitement POST pour ajout diplôme étudiant
if (isset($_POST['numel'], $_POST['titre'], $_POST['note'], $_POST['comment'], $_POST['etabli'], $_POST['lieu'], $_POST['ann_obt'])) {
    $numel = escape($conn, $_POST['numel']);
    $numdip = escape($conn, $_POST['titre']);
    $note = str_replace(',', '.', $_POST['note']);
    $comment = escape($conn, $_POST['comment']);
    $etabli = escape($conn, $_POST['etabli']);
    $lieu = escape($conn, $_POST['lieu']);
    $annee = escape($conn, $_POST['ann_obt']);

    if (!is_numeric($note)) {
        echo '<script>alert("La note doit être un nombre.");window.history.back();</script>';
        exit;
    }
    if (!preg_match('/^\d{4}$/', $annee)) {
        echo '<script>alert("L\'année doit comporter 4 chiffres.");window.history.back();</script>';
        exit;
    }

    // Vérifier si cet élève a déjà ce diplôme
    $res = mysqli_query($conn, "SELECT COUNT(*) as nb FROM eleve_diplome WHERE numel='$numel' AND numdip='$numdip'");
    $nb = mysqli_fetch_assoc($res)['nb'];

    if ($nb > 0) {
        echo '<script>alert("Erreur! Cet enregistrement existe déjà!");window.history.back();</script>';
        exit;
    }

    $sql = "INSERT INTO eleve_diplome (numdip, numel, note, commentaire, etablissement, lieu, annee_obtention) 
            VALUES ('$numdip', '$numel', '$note', '$comment', '$etabli', '$lieu', '$annee')";
    if (mysqli_query($conn, $sql)) {
        echo '<script>alert("Ajout avec succès!");window.location.href="ajout_diplome.php?ajout_diplome";</script>';
        exit;
    } else {
        echo "<h2>Erreur lors de l'insertion : " . mysqli_error($conn) . "</h2>";
        exit;
    }
}

// Affichage des formulaires selon GET ou POST

if (isset($_GET['ajout_type'])) {
    afficherFormAjoutTitre();
} elseif (isset($_GET['ajout_diplome'])) {
    afficherFormChoixClasse($conn);
} elseif (isset($_POST['nomcl'], $_POST['promotion'])) {
    afficherFormDiplomeEtudiant($conn, $_POST['nomcl'], $_POST['promotion']);
} else {
    // Page d'accueil : lien pour ajouter un titre ou un diplôme
    ?>
    <div class="corp">
        <img src="" class="position_titre">
        <center>
            <a href="ajout_diplome.php?ajout_type">Ajouter un nouveau type de diplôme (titre)</a><br/><br/>
            <a href="ajout_diplome.php?ajout_diplome">Ajouter un diplôme à un étudiant</a>
        </center>
    </div>
    <?php
}

mysqli_close($conn);
?>
