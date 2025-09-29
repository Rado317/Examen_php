<?php
session_start();
include('cadre.php');

$connection = mysqli_connect("localhost", "root", "", "test");

if (!$connection) {
    die("Erreur de connexion : " . mysqli_connect_error());
}

$data = mysqli_query($connection, "SELECT DISTINCT promotion FROM classe ORDER BY promotion DESC");
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Afficher les Devoirs | SHARDA UNIVERSITY</title>
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
            max-width: 1200px;
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
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
            overflow: hidden;
            background: white;
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
        
        .delete-link {
            color: var(--accent-color);
        }
        
        .delete-link:hover {
            color: #c0392b;
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
            
            .data-table {
                font-size: 0.9rem;
            }
            
            .data-table th,
            .data-table td {
                padding: 0.8rem;
            }
        }
    </style>
</head>
<body>

<div class="corp">
    <h1 class="page-title"><i class="fas fa-tasks"></i> Affichage des Devoirs</h1>
    
    <?php
    if (isset($_POST['nomcl']) && isset($_POST['radiosem']) && isset($_POST['promotion'])) {
        $_SESSION['semestre'] = $_POST['radiosem'];
        $nomcl = mysqli_real_escape_string($connection, $_POST['nomcl']);
        $semestre = $_SESSION['semestre'];
        $promo = mysqli_real_escape_string($connection, $_POST['promotion']);
        $_SESSION['promo'] = $promo;

        $donnee = mysqli_query($connection, "SELECT nommat 
            FROM matiere
            JOIN enseignement ON matiere.codemat = enseignement.codemat
            JOIN classe ON enseignement.codecl = classe.codecl
            WHERE classe.nom = '$nomcl' 
            AND promotion = '$promo' 
            AND enseignement.numsem = '$semestre'");

        $_SESSION['classe'] = $nomcl;
        ?>
        
        <div class="form-container">
            <form method="post" action="afficher_devoir.php">
                <h2><i class="fas fa-book-open"></i> Sélectionnez une matière</h2>
                <p>Les matières étudiées par la classe choisie :</p>
                
                <div class="radio-group">
                    <?php
                    while ($a = mysqli_fetch_array($donnee)) {
                        echo '<div class="radio-option">
                                <input type="radio" name="radio" value="' . htmlspecialchars($a['nommat']) . '" 
                                       id="choix_' . htmlspecialchars($a['nommat']) . '" required>
                                <label for="choix_' . htmlspecialchars($a['nommat']) . '">' . 
                                htmlspecialchars($a['nommat']) . '</label>
                              </div>';
                    }
                    ?>
                </div>
                
                <button type="submit" class="btn-submit">
                    <i class="fas fa-eye"></i> Afficher les devoirs
                </button>
            </form>
        </div>
        <?php
        
    } else if (isset($_POST['radio'])) {
        $semestre = $_SESSION['semestre'];
        $nommat = mysqli_real_escape_string($connection, $_POST['radio']);
        $nomcl = mysqli_real_escape_string($connection, $_SESSION['classe']);
        $promo = mysqli_real_escape_string($connection, $_SESSION['promo']);

        $donnee = mysqli_query($connection, "SELECT devoir.numdev, devoir.date_dev, matiere.nommat, classe.nom, 
                                                   devoir.coeficient, devoir.numsem, devoir.n_devoir 
            FROM devoir
            JOIN matiere ON matiere.codemat = devoir.codemat
            JOIN classe ON classe.codecl = devoir.codecl
            WHERE classe.nom = '$nomcl' 
            AND devoir.numsem = '$semestre' 
            AND matiere.nommat = '$nommat' 
            AND promotion = '$promo'");
        ?>
        
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <?php if (isset($_SESSION['admin'])): ?>
                            <th>Actions</th>
                        <?php endif; ?>
                        <th>Matière</th>
                        <th>Date devoir</th>
                        <th>Classe</th>
                        <th>Coefficient</th>
                        <th>Semestre</th>
                        <th>Type</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($a = mysqli_fetch_array($donnee)) {
                        echo '<tr>';
                        if (isset($_SESSION['admin'])) {
                            echo '<td>
                                    <a href="modif_devoir.php?modif_dev=' . $a['numdev'] . '" class="action-link">
                                        <i class="fas fa-edit"></i> Modifier
                                    </a>
                                    <a href="modif_devoir.php?supp_dev=' . $a['numdev'] . '" 
                                       class="action-link delete-link"
                                       onclick="return confirm(\'Êtes-vous sûr de vouloir supprimer ce devoir ?\\n\\nTous les enregistrements associés seront également supprimés.\')">
                                        <i class="fas fa-trash-alt"></i> Supprimer
                                    </a>
                                  </td>';
                        }
                        echo '<td>' . htmlspecialchars($a['nommat']) . '</td>';
                        echo '<td>' . htmlspecialchars($a['date_dev']) . '</td>';
                        echo '<td>' . htmlspecialchars($a['nom']) . '</td>';
                        echo '<td>' . htmlspecialchars($a['coeficient']) . '</td>';
                        echo '<td>S' . htmlspecialchars($a['numsem']) . '</td>';
                        echo '<td>' . htmlspecialchars($a['n_devoir']) . '</td>';
                        echo '</tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
        
        <div class="text-center">
            <a href="afficher_devoir.php" class="back-link">
                <i class="fas fa-arrow-left"></i> Retour à la page principale
            </a>
        </div>
        <?php
        
    } else {
        $retour = mysqli_query($connection, "SELECT DISTINCT nom FROM classe");
        ?>
        
        <div class="form-container">
            <form method="post" action="afficher_devoir.php">
                <h2><i class="fas fa-filter"></i> Critères de sélection</h2>
                <p>Veuillez choisir le semestre, la promotion et la classe :</p>
                
                <div class="form-group">
                    <label for="promotion"><i class="fas fa-graduation-cap"></i> Promotion :</label>
                    <select name="promotion" id="promotion" class="form-control" required>
                        <option value="">-- Choisir une promotion --</option>
                        <?php 
                        mysqli_data_seek($data, 0);
                        while ($a = mysqli_fetch_array($data)) {
                            echo '<option value="' . htmlspecialchars($a['promotion']) . '">' . 
                                 htmlspecialchars($a['promotion']) . '</option>';
                        } 
                        ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="nomcl"><i class="fas fa-school"></i> Classe :</label>
                    <select name="nomcl" id="nomcl" class="form-control" required>
                        <option value="">-- Choisir une classe --</option>
                        <?php 
                        while ($a = mysqli_fetch_array($retour)) {
                            echo '<option value="' . htmlspecialchars($a['nom']) . '">' . 
                                 htmlspecialchars($a['nom']) . '</option>';
                        } 
                        ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="radiosem"><i class="fas fa-calendar-alt"></i> Semestre :</label>
                    <select name="radiosem" id="radiosem" class="form-control" required>
                        <?php for ($i = 1; $i <= 4; $i++) {
                            echo '<option value="' . $i . '">Semestre ' . $i . '</option>';
                        } ?>
                    </select>
                </div>
                
                <button type="submit" class="btn-submit">
                    <i class="fas fa-search"></i> Afficher les matières
                </button>
            </form>
        </div>
        <?php 
    }
    ?>
</div>

</body>

</html>
