<?php
include("header.php");
include("connect.php");
$classe = isset($_GET['classe']) ? $_GET['classe'] : null;

if ($classe) {
    $stmt = $conn->prepare("SELECT * FROM eleve WHERE codecl = ?");
    $stmt->bind_param("s", $classe);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query("SELECT * FROM eleve");
}
?>

<style>
    :root {
        --primary-color: #3498db;
        --secondary-color: #2ecc71;
        --accent-color: #e74c3c;
        --dark-color: #2c3e50;
        --light-color: #ecf0f1;
    }
    
    .titre-page {
        color: var(--dark-color);
        text-align: center;
        margin: 2rem 0;
        font-weight: 700;
        text-shadow: 1px 1px 3px rgba(0,0,0,0.1);
        animation: fadeInDown 1s ease-out;
    }
    
    .filters {
        display: flex;
        justify-content: center;
        gap: 1rem;
        margin-bottom: 2rem;
        flex-wrap: wrap;
        animation: fadeIn 1s ease-out;
    }
    
    .filter-btn {
        padding: 0.8rem 1.5rem;
        background-color: white;
        color: var(--primary-color);
        border: 2px solid var(--primary-color);
        border-radius: 50px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    
    .filter-btn:hover, .filter-btn.active {
        background-color: var(--primary-color);
        color: white;
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(52, 152, 219, 0.3);
    }
    
    .table-container {
        background: white;
        border-radius: 15px;
        padding: 2rem;
        margin: 2rem auto;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        overflow-x: auto;
        animation: fadeInUp 1s ease-out;
        max-width: 95%;
    }
    
    .styled-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        font-size: 1rem;
    }
    
    .styled-table thead tr {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--dark-color) 100%);
        color: white;
        border-radius: 15px;
    }
    
    .styled-table th {
        padding: 1.2rem;
        text-align: left;
        font-weight: 600;
    }
    
    .styled-table td {
        padding: 1rem 1.2rem;
        border-bottom: 1px solid #dddddd;
    }
    
    .styled-table tbody tr {
        transition: all 0.3s ease;
    }
    
    .styled-table tbody tr:nth-child(even) {
        background-color: rgba(236, 240, 241, 0.5);
    }
    
    .styled-table tbody tr:last-child td {
        border-bottom: none;
    }
    
    .styled-table tbody tr:hover {
        background-color: rgba(46, 204, 113, 0.1);
        transform: translateX(5px);
    }
    
    @keyframes fadeInDown {
        from {
            opacity: 0;
            transform: translateY(-30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .add-btn {
        display: block;
        width: fit-content;
        margin: 2rem auto;
        padding: 0.8rem 2rem;
        background: var(--secondary-color);
        color: white;
        text-decoration: none;
        border-radius: 50px;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 2px 10px rgba(46, 204, 113, 0.3);
        animation: fadeIn 1.5s ease-out;
    }
    
    .add-btn:hover {
        background: #27ae60;
        transform: translateY(-3px);
        box-shadow: 0 5px 20px rgba(46, 204, 113, 0.4);
    }
    
    .add-btn i {
        margin-right: 0.5rem;
    }
</style>

<div class="container">
    <h1 class="titre-page">Liste des étudiants<?= $classe ? " - $classe" : "" ?></h1>
    
    <div class="filters">
        <a href="listeEtudiant.php" class="filter-btn <?= !$classe ? 'active' : '' ?>">
            <i class="fas fa-users"></i> Tous
        </a>
        <a href="listeEtudiant.php?classe=GI" class="filter-btn <?= $classe === 'GI' ? 'active' : '' ?>">
            <i class="fas fa-laptop-code"></i> GI
        </a>
        <a href="listeEtudiant.php?classe=TM" class="filter-btn <?= $classe === 'TM' ? 'active' : '' ?>">
            <i class="fas fa-cogs"></i> TM
        </a>
        <a href="listeEtudiant.php?classe=GRH" class="filter-btn <?= $classe === 'GRH' ? 'active' : '' ?>">
            <i class="fas fa-user-tie"></i> GRH
        </a>
    </div>
    
    <a href="Ajout_etudiant.php" class="add-btn">
        <i class="fas fa-user-plus"></i> Ajouter un étudiant
    </a>
    
    <div class="table-container">
        <table class="styled-table">
            <thead>
                <tr>
                    <th><i class="fas fa-user"></i> Nom</th>
                    <th><i class="fas fa-user"></i> Prénom</th>
                    <th><i class="fas fa-birthday-cake"></i> Date de naissance</th>
                    <th><i class="fas fa-graduation-cap"></i> Classe</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) : ?>
                    <tr>
                        <td><?= htmlspecialchars($row['nomel']) ?></td>
                        <td><?= htmlspecialchars($row['prenomel']) ?></td>
                        <td><?= htmlspecialchars($row['date_naissance']) ?></td>
                        <td>
                            <span class="badge" style="background-color: <?= 
                                $row['codecl'] === 'GI' ? '#3498db' : 
                                ($row['codecl'] === 'TM' ? '#e74c3c' : '#2ecc71') 
                            ?>; color: white; padding: 0.3rem 0.8rem; border-radius: 50px;">
                                <?= htmlspecialchars($row['codecl']) ?>
                            </span>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include("footer.php"); ?>