<?php
session_start();
include('cadre.php');

$conn = mysqli_connect("localhost", "root", "", "test");
if (!$conn) {
    die("Erreur de connexion à la base : " . mysqli_connect_error());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter Devoir | SHARDA UNIVERSITY</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2ecc71;
            --accent-color: #e74c3c;
            --dark-color: #2c3e50;
            --light-color: #ecf0f1;
        }
        
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .corp {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
        }
        
        .form-container {
            background: white;
            border-radius: 15px;
            padding: 2.5rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }
        
        .page-title {
            color: var(--dark-color);
            text-align: center;
            margin-bottom: 2rem;
            font-weight: 700;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--dark-color);
        }
        
        .form-control {
            width: 100%;
            padding: 0.8rem 1rem;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
            outline: none;
        }
        
        .btn-submit {
            background-color: var(--secondary-color);
            color: white;
            border: none;
            padding: 1rem 2rem;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: block;
            margin: 2rem auto 0;
        }
        
        .btn-submit:hover {
            background-color: #27ae60;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(46, 204, 113, 0.4);
        }
        
        .radio-group {
            display: flex;
            gap: 2rem;
            margin: 1rem 0;
        }
        
        .radio-option {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .success-message {
            background-color: rgba(46, 204, 113, 0.2);
            color: #27ae60;
            padding: 1.5rem;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 2rem;
            border-left: 4px solid var(--secondary-color);
        }
        
        .error-message {
            background-color: rgba(231, 76, 60, 0.2);
            color: #c0392b;
            padding: 1.5rem;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 2rem;
            border-left: 4px solid var(--accent-color);
        }
        
        .back-link {
            display: inline-block;
            margin-top: 2rem;
            padding: 0.8rem 1.5rem;
            background-color: var(--dark-color);
            color: white;
            text-decoration: none;
            border-radius: 6px;
            transition: all 0.3s ease;
        }
        
        .back-link:hover {
            background-color: #1a252f;
            transform: translateY(-2px);
        }
        
        @media (max-width: 768px) {
            .corp {
                padding: 1rem;
            }
            
            .form-container {
                padding: 1.5rem;
            }
            
            .radio-group {
                flex-direction: column;
                gap: 1rem;
            }
        }
    </style>
</head>
<body>

<div class="corp">
    <h1 class="page-title"><i class="fas fa-tasks"></i> Ajouter un Devoir</h1>
    
    <?php
    // ==== 1. Étape : Sélection classe/promotion -> Formulaire matières ====
    if (isset($_POST['nomcl']) && isset($_POST['promotion'])) {

        $_SESSION['nomcl'] = $_POST['nomcl'];
        $_SESSION['promo'] = $_POST['promotion'];
        $nomcl = $_POST['nomcl'];
        $promo = $_POST['promotion'];

        $sql = "SELECT codemat, nommat FROM matiere 
                INNER JOIN classe ON matiere.codecl = classe.codecl 
                WHERE nom = ? AND promotion = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ss", $nomcl, $promo);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        ?>

        <div class="form-container">
            <form action="ajout_devoir.php" method="POST">
                <h2><i class="fas fa-book"></i> Détails du Devoir</h2>
                
                <div class="form-group">
                    <label for="choix_mat"><i class="fas fa-book-open"></i> Matière :</label>
                    <select name="choix_mat" id="choix_mat" class="form-control" required>
                        <option value="">-- Sélectionnez une matière --</option>
                        <?php while ($row = mysqli_fetch_assoc($result)) {
                            echo '<option value="'.htmlspecialchars($row['codemat']).'">'.htmlspecialchars($row['nommat']).'</option>';
                        } ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="date"><i class="fas fa-calendar-alt"></i> Date du devoir :</label>
                    <input type="date" name="date" id="date" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="coefficient"><i class="fas fa-weight"></i> Coefficient :</label>
                    <select name="coefficient" id="coefficient" class="form-control">
                        <?php for ($i=1; $i<=15; $i++) {
                            echo "<option value=\"$i\">$i</option>";
                        } ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="semestre"><i class="fas fa-graduation-cap"></i> Semestre :</label>
                    <select name="semestre" id="semestre" class="form-control">
                        <?php for ($i=1; $i<=4; $i++) {
                            echo "<option value=\"$i\">Semestre $i</option>";
                        } ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-list-ol"></i> Type de devoir :</label>
                    <div class="radio-group">
                        <div class="radio-option">
                            <input type="radio" name="devoir" value="1" id="choix1" required>
                            <label for="choix1">1er devoir</label>
                        </div>
                        <div class="radio-option">
                            <input type="radio" name="devoir" value="2" id="choix2">
                            <label for="choix2">2ème devoir</label>
                        </div>
                    </div>
                </div>
                
                <button type="submit" class="btn-submit">
                    <i class="fas fa-plus-circle"></i> Ajouter le devoir
                </button>
            </form>
        </div>
        <?php
        mysqli_stmt_close($stmt);

    // ==== 2. Étape : Soumission du devoir ====
    } elseif (isset($_POST['date'])) {

        $codemat = $_POST['choix_mat'] ?? null;
        $coefficient = isset($_POST['coefficient']) ? (int)$_POST['coefficient'] : null;
        $semestre = isset($_POST['semestre']) ? (int)$_POST['semestre'] : null;
        $n_devoir = isset($_POST['devoir']) ? (int)$_POST['devoir'] : null;
        $date = $_POST['date'] ?? null;
        $nomcl = $_SESSION['nomcl'] ?? null;
        $promo = $_SESSION['promo'] ?? null;

        if (!$codemat || !$date || !$coefficient || !$semestre || !$n_devoir || !$nomcl || !$promo) {
            echo '<div class="error-message">
                <i class="fas fa-exclamation-circle"></i> Erreur : informations manquantes, veuillez reprendre la procédure.
            </div>';
            echo '<div class="text-center"><a href="ajout_devoir.php" class="back-link">Revenir au début</a></div>';
            exit;
        }

        // Récupérer codecl
        $stmt = mysqli_prepare($conn, "SELECT codecl FROM classe WHERE nom = ? AND promotion = ?");
        mysqli_stmt_bind_param($stmt, "ss", $nomcl, $promo);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($res);
        if (!$row) {
            echo '<div class="error-message">
                <i class="fas fa-exclamation-circle"></i> Classe introuvable.
            </div>';
            exit;
        }
        $codecl = $row['codecl'];
        mysqli_stmt_close($stmt);

        // Vérifier si enseignement existe
        $stmt = mysqli_prepare($conn, "SELECT COUNT(*) as nb FROM enseignement WHERE codecl = ? AND codemat = ? AND numsem = ?");
        mysqli_stmt_bind_param($stmt, "isi", $codecl, $codemat, $semestre);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($res);
        mysqli_stmt_close($stmt);
        if ($row['nb'] == 0) {
            echo '<div class="error-message">
                <i class="fas fa-exclamation-circle"></i> Erreur : Cet enseignement n\'existe pas.
            </div>';
            exit;
        }

        // Vérifier doublon devoir
        $stmt = mysqli_prepare($conn, "SELECT COUNT(*) as nb FROM devoir WHERE codecl = ? AND codemat = ? AND numsem = ? AND n_devoir = ?");
        mysqli_stmt_bind_param($stmt, "isii", $codecl, $codemat, $semestre, $n_devoir);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($res);
        mysqli_stmt_close($stmt);
        if ($row['nb'] > 0) {
            echo '<div class="error-message">
                <i class="fas fa-exclamation-circle"></i> Erreur : Devoir déjà enregistré pour ce numéro.
            </div>';
            exit;
        }

        // Insertion
        $stmt = mysqli_prepare($conn, "INSERT INTO devoir(date_dev, coeficient, codemat, codecl, numsem, n_devoir) VALUES (?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "sisiii", $date, $coefficient, $codemat, $codecl, $semestre, $n_devoir);
        if (mysqli_stmt_execute($stmt)) {
            echo '<div class="success-message">
                <i class="fas fa-check-circle"></i> Devoir ajouté avec succès !
            </div>';
        } else {
            echo '<div class="error-message">
                <i class="fas fa-exclamation-circle"></i> Erreur lors de l\'insertion : ' . mysqli_error($conn) . '
            </div>';
        }
        mysqli_stmt_close($stmt);

        echo '<div class="text-center">
            <a href="ajout_devoir.php" class="back-link">
                <i class="fas fa-arrow-left"></i> Ajouter un autre devoir
            </a>
        </div>';

    // ==== 3. Étape : Formulaire initial ====
    } else {
        $res_promo = mysqli_query($conn, "SELECT DISTINCT promotion FROM classe ORDER BY promotion DESC");
        $res_classe = mysqli_query($conn, "SELECT DISTINCT nom FROM classe");
        ?>
        <div class="form-container">
            <form action="ajout_devoir.php" method="POST">
                <h2><i class="fas fa-school"></i> Sélection de la Classe</h2>
                
                <div class="form-group">
                    <label for="promotion"><i class="fas fa-graduation-cap"></i> Promotion :</label>
                    <select name="promotion" id="promotion" class="form-control" required>
                        <option value="">-- Choisir une promotion --</option>
                        <?php while ($row = mysqli_fetch_assoc($res_promo)) {
                            echo '<option value="'.htmlspecialchars($row['promotion']).'">'.htmlspecialchars($row['promotion']).'</option>';
                        } ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="nomcl"><i class="fas fa-users"></i> Classe :</label>
                    <select name="nomcl" id="nomcl" class="form-control" required>
                        <option value="">-- Choisir une classe --</option>
                        <?php 
                        mysqli_data_seek($res_classe, 0);
                        while ($row = mysqli_fetch_assoc($res_classe)) {
                            echo '<option value="'.htmlspecialchars($row['nom']).'">'.htmlspecialchars($row['nom']).'</option>';
                        } ?>
                    </select>
                </div>
                
                <button type="submit" class="btn-submit">
                    <i class="fas fa-arrow-right"></i> Suivant
                </button>
            </form>
        </div>
    <?php
    }
    ?>
</div>

</body>
</html>