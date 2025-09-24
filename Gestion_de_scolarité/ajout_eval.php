<?php
session_start();
include('cadre.php');

// Connexion sécurisée avec PDO
try {
    $pdo = new PDO("mysql:host=localhost;dbname=test;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajout d'Évaluations | SHARDA UNIVERSITY</title>
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
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }
        
        .corp {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 2rem;
            animation: fadeIn 0.8s ease-out;
        }
        
        .page-title {
            color: var(--dark-color);
            text-align: center;
            margin-bottom: 2rem;
            font-weight: 700;
            position: relative;
        }
        
        .page-title::after {
            content: "";
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
            border-radius: 2px;
        }
        
        .form-container {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--dark-color);
        }
        
        select, input[type="text"], input[type="submit"] {
            width: 100%;
            padding: 0.8rem 1rem;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        select:focus, input[type="text"]:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
            outline: none;
        }
        
        .btn-submit {
            background-color: var(--secondary-color);
            color: white;
            border: none;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 1rem;
        }
        
        .btn-submit:hover {
            background-color: #27ae60;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(46, 204, 113, 0.4);
        }
        
        .radio-group {
            margin: 1.5rem 0;
        }
        
        .radio-option {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
            padding: 0.8rem;
            background-color: rgba(236, 240, 241, 0.5);
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .radio-option:hover {
            background-color: rgba(52, 152, 219, 0.1);
        }
        
        .radio-option input {
            margin-right: 1rem;
        }
        
        .data-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin: 2rem 0;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            border-radius: 12px;
            overflow: hidden;
        }
        
        .data-table thead tr {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--dark-color) 100%);
            color: white;
        }
        
        .data-table th {
            padding: 1.2rem;
            text-align: left;
            font-weight: 600;
        }
        
        .data-table td {
            padding: 1rem 1.2rem;
            border-bottom: 1px solid #eee;
        }
        
        .data-table tbody tr:nth-child(even) {
            background-color: rgba(236, 240, 241, 0.5);
        }
        
        .data-table tbody tr:hover {
            background-color: rgba(46, 204, 113, 0.1);
        }
        
        .action-link {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .action-link:hover {
            color: var(--dark-color);
            text-decoration: underline;
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
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .info-badge {
            display: inline-block;
            padding: 0.5rem 1rem;
            background-color: rgba(52, 152, 219, 0.1);
            color: var(--primary-color);
            border-radius: 6px;
            margin: 0.5rem 0;
            font-weight: 500;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-weight: 500;
        }
        
        .alert-success {
            background-color: rgba(46, 204, 113, 0.2);
            color: #27ae60;
            border-left: 4px solid var(--secondary-color);
        }
        
        .alert-error {
            background-color: rgba(231, 76, 60, 0.2);
            color: #c0392b;
            border-left: 4px solid var(--accent-color);
        }
    </style>
</head>
<body>
<div class="corp">
    <h1 class="page-title">
        <i class="fas fa-plus-circle"></i> Ajout d'Évaluations
    </h1>
    
    <?php
    if (isset($_POST['nomcl']) && isset($_POST['radiosem'])) {
        // Étape 1: Sélection de la matière
        $_SESSION['semestre'] = $_POST['radiosem'];
        $nomcl = $_POST['nomcl'];
        $semestre = $_SESSION['semestre'];
        $promo = $_POST['promotion'];
        $_SESSION['promo'] = $promo;
        
        try {
            $stmt = $pdo->prepare("SELECT nommat 
                FROM matiere, enseignement, classe 
                WHERE matiere.codemat = enseignement.codemat 
                AND enseignement.codecl = classe.codecl 
                AND classe.nom = :nomcl 
                AND promotion = :promo 
                AND enseignement.numsem = :semestre");
            $stmt->execute([
                ':nomcl' => $nomcl,
                ':promo' => $promo,
                ':semestre' => $semestre
            ]);
            $matieres = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $_SESSION['classe'] = $nomcl;
            ?>
            <div class="form-container">
                <form method="post" action="ajout_eval.php">
                    <h2><i class="fas fa-book-open"></i> Sélectionnez une matière</h2>
                    <div class="radio-group">
                        <?php foreach ($matieres as $i => $matiere): ?>
                            <div class="radio-option">
                                <input type="radio" name="radio" value="<?= htmlspecialchars($matiere['nommat']) ?>" 
                                       id="matiere<?= $i ?>" required>
                                <label for="matiere<?= $i ?>"><?= htmlspecialchars($matiere['nommat']) ?></label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <input type="submit" value="Afficher les devoirs" class="btn-submit">
                </form>
            </div>
            <?php
        } catch(PDOException $e) {
            echo '<div class="alert alert-error">Erreur : ' . $e->getMessage() . '</div>';
        }
        
    } elseif (isset($_POST['radio'])) {
        // Étape 2: Sélection du devoir
        $semestre = $_SESSION['semestre'];
        $nommat = $_POST['radio'];
        $_SESSION['radio_matiere'] = $nommat;
        $nomcl = $_SESSION['classe'];
        $promo = $_SESSION['promo'];
        
        try {
            $stmt = $pdo->prepare("SELECT numdev, date_dev, nommat, nom, coeficient, numsem, n_devoir 
                FROM devoir, matiere, classe 
                WHERE matiere.codemat = devoir.codemat 
                AND classe.codecl = devoir.codecl 
                AND classe.nom = :nomcl 
                AND devoir.numsem = :semestre 
                AND matiere.nommat = :nommat 
                AND promotion = :promo");
            $stmt->execute([
                ':nomcl' => $nomcl,
                ':semestre' => $semestre,
                ':nommat' => $nommat,
                ':promo' => $promo
            ]);
            $devoirs = $stmt->fetchAll(PDO::FETCH_ASSOC);
            ?>
            <div class="form-container">
                <h2><i class="fas fa-tasks"></i> Sélectionnez un devoir</h2>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Action</th>
                            <th>Matière</th>
                            <th>Date</th>
                            <th>Classe</th>
                            <th>Coefficient</th>
                            <th>Semestre</th>
                            <th>Type</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($devoirs as $devoir): ?>
                            <tr>
                                <td>
                                    <a href="ajout_eval.php?ajout_eval=<?= $devoir['numdev'] ?>" 
                                       class="action-link">
                                        <i class="fas fa-plus"></i> Ajouter
                                    </a>
                                </td>
                                <td><?= htmlspecialchars($devoir['nommat']) ?></td>
                                <td><?= htmlspecialchars($devoir['date_dev']) ?></td>
                                <td><?= htmlspecialchars($devoir['nom']) ?></td>
                                <td><?= htmlspecialchars($devoir['coeficient']) ?></td>
                                <td>S<?= htmlspecialchars($devoir['numsem']) ?></td>
                                <td><?= htmlspecialchars($devoir['n_devoir']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="text-center">
                <a href="ajout_eval.php" class="back-link">
                    <i class="fas fa-arrow-left"></i> Retour
                </a>
            </div>
            <?php
        } catch(PDOException $e) {
            echo '<div class="alert alert-error">Erreur : ' . $e->getMessage() . '</div>';
        }
        
    } elseif (isset($_POST['numel'])) {
        // Étape 4: Insertion de l'évaluation
        $numel = $_POST['numel'];
        $numdev = $_POST['numdev'];
        $nomcl = $_SESSION['classe'];
        $promo = $_SESSION['promo'];
        $note = str_replace(",", ".", $_POST['note']);
        
        try {
            // Vérification si l'évaluation existe déjà
            $stmt = $pdo->prepare("SELECT COUNT(*) as nb 
                FROM evaluation 
                WHERE numdev = :numdev AND numel = :numel");
            $stmt->execute([':numdev' => $numdev, ':numel' => $numel]);
            $compte = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($compte['nb'] > 0) {
                echo '<div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> Erreur : Cette évaluation existe déjà !
                </div>';
            } else {
                // Insertion de la nouvelle évaluation
                $stmt = $pdo->prepare("INSERT INTO evaluation(numdev, numel, note) 
                    VALUES(:numdev, :numel, :note)");
                $stmt->execute([
                    ':numdev' => $numdev,
                    ':numel' => $numel,
                    ':note' => $note
                ]);
                
                echo '<div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> Évaluation ajoutée avec succès !
                </div>';
            }
            ?>
            <div class="text-center">
                <a href="ajout_eval.php" class="back-link">
                    <i class="fas fa-arrow-left"></i> Retour
                </a>
            </div>
            <?php
        } catch(PDOException $e) {
            echo '<div class="alert alert-error">Erreur : ' . $e->getMessage() . '</div>';
        }
        
    } elseif (isset($_GET['ajout_eval'])) {
        // Étape 3: Formulaire d'ajout d'évaluation
        $semestre = $_SESSION['semestre'];
        $nommat = $_SESSION['radio_matiere'];
        $nomcl = $_SESSION['classe'];
        $promo = $_SESSION['promo'];
        $numdev = $_GET['ajout_eval'];
        
        try {
            // Récupération des infos du devoir
            $stmt = $pdo->prepare("SELECT date_dev, coeficient, n_devoir 
                FROM devoir 
                WHERE numdev = :numdev");
            $stmt->execute([':numdev' => $numdev]);
            $devoir = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Récupération des étudiants
            $stmt = $pdo->prepare("SELECT numel, nomel, prenomel 
                FROM eleve 
                WHERE codecl = (SELECT codecl FROM classe WHERE nom = :nomcl AND promotion = :promo)");
            $stmt->execute([':nomcl' => $nomcl, ':promo' => $promo]);
            $etudiants = $stmt->fetchAll(PDO::FETCH_ASSOC);
            ?>
            <div class="form-container">
                <h2><i class="fas fa-plus-circle"></i> Ajouter une évaluation</h2>
                
                <div class="info-badge">
                    <i class="fas fa-info-circle"></i> Filière : <?= htmlspecialchars($nomcl) ?> - <?= htmlspecialchars($promo) ?>
                </div>
                
                <div class="info-badge">
                    <i class="fas fa-book"></i> Matière : <?= htmlspecialchars($nommat) ?>
                </div>
                
                <div class="info-badge">
                    <i class="fas fa-calendar-alt"></i> Semestre : S<?= htmlspecialchars($semestre) ?>
                </div>
                
                <form method="POST" action="ajout_eval.php">
                    <div class="form-group">
                        <label for="date_devoir"><i class="far fa-calendar"></i> Date du devoir :</label>
                        <input type="text" id="date_devoir" value="<?= htmlspecialchars($devoir['date_dev']) ?>" readonly>
                    </div>
                    
                    <div class="form-group">
                        <label for="coefficient"><i class="fas fa-weight"></i> Coefficient :</label>
                        <input type="text" id="coefficient" value="<?= htmlspecialchars($devoir['coeficient']) ?>" readonly>
                    </div>
                    
                    <div class="form-group">
                        <label for="type_devoir"><i class="fas fa-list-ol"></i> Type de devoir :</label>
                        <input type="text" id="type_devoir" value="<?= htmlspecialchars($devoir['n_devoir']) ?>" readonly>
                    </div>
                    
                    <div class="form-group">
                        <label for="numel"><i class="fas fa-user-graduate"></i> Étudiant :</label>
                        <select name="numel" id="numel" required>
                            <?php foreach ($etudiants as $etudiant): ?>
                                <option value="<?= htmlspecialchars($etudiant['numel']) ?>">
                                    <?= htmlspecialchars($etudiant['nomel']) ?> <?= htmlspecialchars($etudiant['prenomel']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="note"><i class="fas fa-star"></i> Note :</label>
                        <input type="text" name="note" id="note" required 
                               pattern="[0-9]+([,\.][0-9]+)?" 
                               title="Veuillez entrer une note valide (ex: 15.5 ou 12,5)">
                    </div>
                    
                    <input type="hidden" name="numdev" value="<?= htmlspecialchars($numdev) ?>">
                    <input type="submit" value="Enregistrer l'évaluation" class="btn-submit">
                </form>
            </div>
            
            <div class="text-center">
                <a href="ajout_eval.php" class="back-link">
                    <i class="fas fa-arrow-left"></i> Retour
                </a>
            </div>
            <?php
        } catch(PDOException $e) {
            echo '<div class="alert alert-error">Erreur : ' . $e->getMessage() . '</div>';
        }
        
    } else {
        // Étape 0: Sélection initiale
        try {
            $promotions = $pdo->query("SELECT DISTINCT promotion FROM classe ORDER BY promotion DESC")->fetchAll(PDO::FETCH_ASSOC);
            $classes = $pdo->query("SELECT DISTINCT nom FROM classe")->fetchAll(PDO::FETCH_ASSOC);
            ?>
            <div class="form-container">
                <h2><i class="fas fa-filter"></i> Sélectionnez les critères</h2>
                <form method="post" action="ajout_eval.php">
                    <div class="form-group">
                        <label for="promotion"><i class="fas fa-graduation-cap"></i> Promotion :</label>
                        <select name="promotion" id="promotion" required>
                            <?php foreach ($promotions as $promo): ?>
                                <option value="<?= htmlspecialchars($promo['promotion']) ?>">
                                    <?= htmlspecialchars($promo['promotion']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="nomcl"><i class="fas fa-school"></i> Classe :</label>
                        <select name="nomcl" id="nomcl" required>
                            <?php foreach ($classes as $classe): ?>
                                <option value="<?= htmlspecialchars($classe['nom']) ?>">
                                    <?= htmlspecialchars($classe['nom']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="radiosem"><i class="fas fa-calendar-alt"></i> Semestre :</label>
                        <select name="radiosem" id="radiosem" required>
                            <?php for ($i = 1; $i <= 4; $i++): ?>
                                <option value="<?= $i ?>">Semestre <?= $i ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    
                    <input type="submit" value="Afficher les matières" class="btn-submit">
                </form>
            </div>
            <?php
        } catch(PDOException $e) {
            echo '<div class="alert alert-error">Erreur : ' . $e->getMessage() . '</div>';
        }
    }
    ?>
</div>
</body>
</html>