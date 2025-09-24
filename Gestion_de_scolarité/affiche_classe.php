<?php
session_start();
include('cadre.php');

// Connexion sécurisée avec PDO
try {
    $pdo = new PDO("mysql:host=localhost;dbname=test;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Requête sécurisée avec PDO
    $sql = "SELECT codecl, classe.nom AS nomcl, promotion, prof.nom AS nomprof 
            FROM classe 
            JOIN prof ON classe.numprofcoord = prof.numprof";
    $stmt = $pdo->query($sql);
    $classes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Affichage des classes | SHARDA UNIVERSITY</title>
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
        
        .data-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin: 2rem 0;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            border-radius: 12px;
            overflow: hidden;
            animation: fadeInUp 0.8s ease-out;
        }
        
        .data-table thead tr {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--dark-color) 100%);
            color: white;
        }
        
        .data-table th {
            padding: 1.2rem;
            text-align: left;
            font-weight: 600;
            position: relative;
        }
        
        .data-table th:not(:last-child)::after {
            content: "";
            position: absolute;
            right: 0;
            top: 50%;
            transform: translateY(-50%);
            height: 60%;
            width: 1px;
            background: rgba(255,255,255,0.2);
        }
        
        .data-table td {
            padding: 1rem 1.2rem;
            border-bottom: 1px solid #eee;
            transition: all 0.3s ease;
        }
        
        .data-table tbody tr:nth-child(even) {
            background-color: rgba(236, 240, 241, 0.5);
        }
        
        .data-table tbody tr:hover {
            background-color: rgba(46, 204, 113, 0.1);
        }
        
        .data-table tbody tr:last-child td {
            border-bottom: none;
        }
        
        .action-btn {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s ease;
            margin: 0.2rem;
        }
        
        .edit-btn {
            background-color: rgba(52, 152, 219, 0.1);
            color: var(--primary-color);
        }
        
        .edit-btn:hover {
            background-color: var(--primary-color);
            color: white;
        }
        
        .delete-btn {
            background-color: rgba(231, 76, 60, 0.1);
            color: var(--accent-color);
        }
        
        .delete-btn:hover {
            background-color: var(--accent-color);
            color: white;
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
        
        .badge {
            display: inline-block;
            padding: 0.3rem 0.8rem;
            border-radius: 50px;
            font-weight: 500;
            font-size: 0.85rem;
        }
        
        .promo-badge {
            background-color: rgba(46, 204, 113, 0.1);
            color: var(--secondary-color);
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
<div class="corp">
    <h1 class="page-title">
        <i class="fas fa-school"></i> Liste des Classes
    </h1>
    
    <table class="data-table">
        <thead>
            <tr>
                <?php if (isset($_SESSION['admin'])): ?>
                    <th>Actions</th>
                <?php endif; ?>
                <th>Nom de la classe</th>
                <th>Promotion</th>
                <th>Prof coordonnateur</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($classes as $row): ?>
            <tr>
                <?php if (isset($_SESSION['admin'])): ?>
                    <td>
                        <a href="modif_classe.php?modif_classe=<?= urlencode($row['codecl']) ?>" 
                           class="action-btn edit-btn"
                           title="Modifier cette classe">
                            <i class="fas fa-edit"></i> Modifier
                        </a>
                        <a href="modif_classe.php?supp_classe=<?= urlencode($row['codecl']) ?>" 
                           class="action-btn delete-btn"
                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette classe ? Tous les enregistrements associés seront également supprimés.')"
                           title="Supprimer cette classe">
                            <i class="fas fa-trash-alt"></i> Supprimer
                        </a>
                    </td>
                <?php endif; ?>
                <td><?= htmlspecialchars($row['nomcl']) ?></td>
                <td><span class="badge promo-badge"><?= htmlspecialchars($row['promotion']) ?></span></td>
                <td><?= htmlspecialchars($row['nomprof']) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    
    <div class="text-center">
        <a href="index.php" class="back-link">
            <i class="fas fa-arrow-left"></i> Retour à l'accueil
        </a>
    </div>
</div>

<script>
    // Confirmation de suppression améliorée
    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            if (!confirm('⚠️ Attention ! Cette action est irréversible.\n\nÊtes-vous certain de vouloir supprimer cette classe et toutes ses données associées ?')) {
                e.preventDefault();
            }
        });
    });
</script>
</body>
</html>