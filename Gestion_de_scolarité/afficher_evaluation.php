<?php
session_start();
include('cadre.php');

// Connexion sécurisée avec PDO
try {
    $pdo = new PDO("mysql:host=localhost;dbname=test;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Récupération des promotions
    $promotions = $pdo->query("SELECT DISTINCT promotion FROM classe ORDER BY promotion DESC")->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Évaluations | SHARDA UNIVERSITY</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2ecc71;
            --accent-color: #e74c3c;
            --dark-color: #2c3e50;
            --light-color: #ecf0f1;
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
            padding-bottom: 1rem;
        }
        
        .page-title::after {
            content: "";
            position: absolute;
            bottom: 0;
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
        
        select, input[type="submit"] {
            width: 100%;
            padding: 0.8rem 1rem;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        select:focus {
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
        }
        
        .btn-submit:hover {
            background-color: #27ae60;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(46, 204, 113, 0.4);
        }
        
        .radio-group {
            margin: 1rem 0;
        }
        
        .radio-option {
            display: flex;
            align-items: center;
            margin-bottom: 0.8rem;
        }
        
        .radio-option input {
            margin-right: 0.8rem;
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
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
    </style>
</head>
<body>
<div class="corp">
    <h1 class="page-title">
        <i class="fas fa-clipboard-check"></i> Gestion des Évaluations
    </h1>
    
    <?php
    if (isset($_POST['nomcl']) && isset($_POST['radiosem'])) {
        $_SESSION['semestre'] = $_POST['radiosem'];
        $nomcl = $_POST['nomcl'];
        $semestre = $_SESSION['semestre'];
        $promo = $_POST['promotion'];
        $_SESSION['promo'] = $promo;
        
        try {
            $stmt = $pdo->prepare("
                SELECT nommat 
                FROM matiere, enseignement, classe 
                WHERE matiere.codemat = enseignement.codemat 
                AND enseignement.codecl = classe.codecl 
                AND classe.nom = :nomcl 
                AND promotion = :promo 
                AND enseignement.numsem = :semestre
            ");
            $stmt->execute([
                ':nomcl' => $nomcl,
                ':promo' => $promo,
                ':semestre' => $semestre
            ]);
            $matieres = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $_SESSION['classe'] = $nomcl;
            ?>
            <div class="form-container">
                <form method="post" action="afficher_evaluation.php">
                    <fieldset>
                        <legend><i class="fas fa-book-open"></i> Sélectionnez une matière</legend>
                        <div class="radio-group">
                            <?php foreach ($matieres as $i => $matiere): ?>
                                <div class="radio-option">
                                    <input type="radio" name="radio" value="<?= htmlspecialchars($matiere['nommat']) ?>" 
                                           id="matiere<?= $i + 1 ?>">
                                    <label for="matiere<?= $i + 1 ?>"><?= htmlspecialchars($matiere['nommat']) ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <input type="submit" value="Afficher les devoirs" class="btn-submit">
                    </fieldset>
                </form>
            </div>
            <?php
        } catch(PDOException $e) {
            echo "<div class='alert'>Erreur : " . $e->getMessage() . "</div>";
        }
        
    } elseif (isset($_POST['radio'])) {
        $semestre = $_SESSION['semestre'];
        $nommat = $_POST['radio'];
        $_SESSION['radio_matiere'] = $nommat;
        $nomcl = $_SESSION['classe'];
        $promo = $_SESSION['promo'];
        
        try {
            $stmt = $pdo->prepare("
                SELECT numdev, date_dev, nommat, nom, coeficient, numsem, n_devoir 
                FROM devoir, matiere, classe 
                WHERE matiere.codemat = devoir.codemat 
                AND classe.codecl = devoir.codecl 
                AND classe.nom = :nomcl 
                AND devoir.numsem = :semestre 
                AND matiere.nommat = :nommat 
                AND promotion = :promo
            ");
            $stmt->execute([
                ':nomcl' => $nomcl,
                ':semestre' => $semestre,
                ':nommat' => $nommat,
                ':promo' => $promo
            ]);
            $devoirs = $stmt->fetchAll(PDO::FETCH_ASSOC);
            ?>
            <div class="text-center">
                <h2><i class="fas fa-tasks"></i> Sélectionnez un devoir</h2>
            </div>
            
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Evaluation</th>
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
                                <a href="afficher_evaluation.php?affich_eval=<?= $devoir['numdev'] ?>" 
                                   class="action-link">
                                    <i class="fas fa-eye"></i> Voir
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
            
            <div class="text-center">
                <a href="afficher_evaluation.php" class="back-link">
                    <i class="fas fa-arrow-left"></i> Retour
                </a>
            </div>
            <?php
        } catch(PDOException $e) {
            echo "<div class='alert'>Erreur : " . $e->getMessage() . "</div>";
        }
        
    } elseif (isset($_GET['affich_eval'])) {
        $numdev = $_GET['affich_eval'];
        
        try {
            $stmt = $pdo->prepare("
                SELECT numeval, date_dev, nommat, nom, nomel, prenomel, note, coeficient, numsem, promotion, n_devoir 
                FROM devoir, matiere, classe, eleve, evaluation 
                WHERE evaluation.numdev = devoir.numdev 
                AND eleve.numel = evaluation.numel 
                AND matiere.codemat = devoir.codemat 
                AND classe.codecl = devoir.codecl 
                AND devoir.numdev = :numdev
            ");
            $stmt->execute([':numdev' => $numdev]);
            $evaluations = $stmt->fetchAll(PDO::FETCH_ASSOC);
            ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Classe</th>
                        <th>Promotion</th>
                        <th>Matière</th>
                        <th>Date</th>
                        <th>Coefficient</th>
                        <th>Semestre</th>
                        <th>Type</th>
                        <th>Note</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($evaluations as $eval): ?>
                        <tr>
                            <td><?= htmlspecialchars($eval['nomel']) ?></td>
                            <td><?= htmlspecialchars($eval['prenomel']) ?></td>
                            <td><?= htmlspecialchars($eval['nom']) ?></td>
                            <td><?= htmlspecialchars($eval['promotion']) ?></td>
                            <td><?= htmlspecialchars($eval['nommat']) ?></td>
                            <td><?= htmlspecialchars($eval['date_dev']) ?></td>
                            <td><?= htmlspecialchars($eval['coeficient']) ?></td>
                            <td>S<?= htmlspecialchars($eval['numsem']) ?></td>
                            <td><?= htmlspecialchars($eval['n_devoir']) ?></td>
                            <td><strong><?= htmlspecialchars($eval['note']) ?></strong></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <div class="text-center">
                <a href="afficher_evaluation.php" class="back-link">
                    <i class="fas fa-arrow-left"></i> Retour
                </a>
            </div>
            <?php
        } catch(PDOException $e) {
            echo "<div class='alert'>Erreur : " . $e->getMessage() . "</div>";
        }
        
    } else {
        try {
            $classes = $pdo->query("SELECT DISTINCT nom FROM classe")->fetchAll(PDO::FETCH_ASSOC);
            ?>
            <div class="form-container">
                <form method="post" action="afficher_evaluation.php">
                    <fieldset>
                        <legend><i class="fas fa-filter"></i> Critères de sélection</legend>
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
                    </fieldset>
                </form>
            </div>
            <?php
        } catch(PDOException $e) {
            echo "<div class='alert'>Erreur : " . $e->getMessage() . "</div>";
        }
    }
    ?>
</div>
</body>
</html>