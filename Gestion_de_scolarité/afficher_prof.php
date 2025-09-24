<?php
session_start();
include('cadre.php');

// Connexion sécurisée avec MySQLi
$conn = mysqli_connect("localhost", "root", "", "test");
if (!$conn) {
    die("Connexion échouée : " . mysqli_connect_error());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails Enseignant | SHARDA UNIVERSITY</title>
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
        
        .page-title {
            color: var(--dark-color);
            text-align: center;
            margin-bottom: 2rem;
            font-weight: 700;
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
        
        .info-badge {
            display: inline-block;
            padding: 0.5rem 1rem;
            background-color: rgba(52, 152, 219, 0.1);
            color: var(--primary-color);
            border-radius: 6px;
            margin: 0.5rem 0;
            font-weight: 500;
        }
        
        @media (max-width: 768px) {
            .corp {
                padding: 1rem;
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
    <?php
    if (isset($_GET['matiere'])) {
        $id = mysqli_real_escape_string($conn, $_GET['matiere']);

        $query = "SELECT prof.nom, prenom, nommat, classe.nom AS nomcl, promotion, numsem 
                  FROM prof 
                  JOIN enseignement ON enseignement.numprof = prof.numprof 
                  JOIN matiere ON matiere.codemat = enseignement.codemat 
                  JOIN classe ON classe.codecl = enseignement.codecl 
                  WHERE prof.numprof = '$id' 
                  ORDER BY promotion DESC";

        $result = mysqli_query($conn, $query);
        ?>
        
        <h1 class="page-title"><i class="fas fa-book-open"></i> Matières enseignées</h1>
        
        <div class="info-badge">
            <i class="fas fa-info-circle"></i> Liste des matières enseignées par cet enseignant
        </div>
        
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th><i class="fas fa-user"></i> Nom</th>
                        <th><i class="fas fa-user"></i> Prénom</th>
                        <th><i class="fas fa-book"></i> Matière</th>
                        <th><i class="fas fa-school"></i> Classe</th>
                        <th><i class="fas fa-graduation-cap"></i> Promotion</th>
                        <th><i class="fas fa-calendar-alt"></i> Semestre</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                while ($a = mysqli_fetch_assoc($result)) {
                    echo '<tr>
                            <td>' . htmlspecialchars($a['nom']) . '</td>
                            <td>' . htmlspecialchars($a['prenom']) . '</td>
                            <td>' . htmlspecialchars($a['nommat']) . '</td>
                            <td>' . htmlspecialchars($a['nomcl']) . '</td>
                            <td>' . htmlspecialchars($a['promotion']) . '</td>
                            <td>Semestre ' . htmlspecialchars($a['numsem']) . '</td>
                          </tr>';
                }
                ?>
                </tbody>
            </table>
        </div>
        <?php 
    }

    else if (isset($_GET['classe'])) {
        $id = mysqli_real_escape_string($conn, $_GET['classe']);

        $query = "SELECT prof.nom, prenom, classe.nom AS nomcl, promotion 
                  FROM prof 
                  JOIN classe ON prof.numprof = classe.numprofcoord 
                  WHERE prof.numprof = '$id' 
                  ORDER BY promotion DESC";

        $result = mysqli_query($conn, $query);
        ?>
        
        <h1 class="page-title"><i class="fas fa-chalkboard-teacher"></i> Classes coordonnées</h1>
        
        <div class="info-badge">
            <i class="fas fa-info-circle"></i> Liste des classes coordonnées par cet enseignant
        </div>
        
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th><i class="fas fa-user"></i> Nom</th>
                        <th><i class="fas fa-user"></i> Prénom</th>
                        <th><i class="fas fa-school"></i> Classe coordonnée</th>
                        <th><i class="fas fa-graduation-cap"></i> Promotion</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                while ($a = mysqli_fetch_assoc($result)) {
                    echo '<tr>
                            <td>' . htmlspecialchars($a['nom']) . '</td>
                            <td>' . htmlspecialchars($a['prenom']) . '</td>
                            <td>' . htmlspecialchars($a['nomcl']) . '</td>
                            <td>' . htmlspecialchars($a['promotion']) . '</td>
                          </tr>';
                }
                ?>
                </tbody>
            </table>
        </div>
        <?php
    }
    ?>
    
    <div class="text-center">
        <a href="javascript:history.back()" class="back-link">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
    </div>
</div>

</body>
</html>