<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}
?>

<?php include("header.php"); ?>
<style>
    :root {
        --primary-color: #3498db;
        --secondary-color: #2ecc71;
        --accent-color: #e74c3c;
        --dark-color: #2c3e50;
        --light-color: #ecf0f1;
    }
    
    .welcome-section {
        background: linear-gradient(135deg, rgba(52, 152, 219, 0.1) 0%, rgba(46, 204, 113, 0.1) 100%);
        padding: 4rem 2rem;
        border-radius: 15px;
        margin: 2rem auto;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        animation: fadeIn 1s ease-out;
    }
    
    .welcome-title {
        color: var(--dark-color);
        font-weight: 700;
        margin-bottom: 1.5rem;
        text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.1);
        animation: slideInDown 1s ease-out;
    }
    
    .welcome-subtitle {
        color: var(--primary-color);
        font-size: 1.5rem;
        margin-bottom: 3rem;
        animation: fadeIn 1.5s ease-out;
    }
    
    .quick-access-card {
        background: white;
        border-radius: 15px;
        padding: 2rem 1rem;
        margin-bottom: 2rem;
        height: 100%;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        border: 3px solid transparent;
        text-decoration: none;
        display: block;
    }
    
    .quick-access-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
        border-color: var(--primary-color);
    }
    
    .card-icon {
        font-size: 3.5rem;
        margin-bottom: 1.5rem;
        color: var(--primary-color);
    }
    
    .card-title {
        color: var(--dark-color);
        font-weight: 600;
        margin-bottom: 1rem;
    }
    
    .card-description {
        color: #666;
        font-size: 0.9rem;
    }
    
    .campus-image {
        border-radius: 15px;
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
        transition: all 0.5s ease;
        border: 5px solid white;
        margin-top: 3rem;
        max-height: 400px;
        object-fit: cover;
        width: 100%;
        animation: fadeIn 2s ease-out;
    }
    
    .campus-image:hover {
        transform: scale(1.02);
    }
    
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    @keyframes slideInDown {
        from {
            transform: translateY(-50px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }
</style>

<div class="container">
    <div class="welcome-section animate__animated animate__fadeIn">
        <h1 class="welcome-title">Welcome to SHARDA UNIVERSITY</h1>
        <p class="welcome-subtitle">Your success is our first priority</p>
        
        <div class="row">
            <!-- Étudiants -->
            <div class="col-md-3 animate__animated animate__fadeInUp animate__delay-1s">
                <a href="listeEtudiant.php" class="quick-access-card text-center">
                    <i class="fas fa-users card-icon"></i>
                    <h3 class="card-title">Étudiants</h3>
                    <p class="card-description">Gérer la liste des étudiants</p>
                </a>
            </div>
            
            <!-- Professeurs -->
            <div class="col-md-3 animate__animated animate__fadeInUp animate__delay-1-2s">
                <a href="listeprof.php" class="quick-access-card text-center">
                    <i class="fas fa-chalkboard-teacher card-icon"></i>
                    <h3 class="card-title">Professeurs</h3>
                    <p class="card-description">Gérer les enseignants</p>
                </a>
            </div>
            
            <!-- Matières -->
            <div class="col-md-3 animate__animated animate__fadeInUp animate__delay-1-4s">
                <a href="listematière.php" class="quick-access-card text-center">
                    <i class="fas fa-book card-icon"></i>
                    <h3 class="card-title">Matières</h3>
                    <p class="card-description">Liste des cours</p>
                </a>
            </div>
            
            <!-- Classes -->
            <div class="col-md-3 animate__animated animate__fadeInUp animate__delay-1-6s">
                <a href="affiche_classe.php" class="quick-access-card text-center">
                    <i class="fas fa-school card-icon"></i>
                    <h3 class="card-title">Classes</h3>
                    <p class="card-description">Gérer les classes</p>
                </a>
            </div>
        </div>
        
        <img src="Sharda-university.jpg" class="img-fluid campus-image" alt="Campus de Sharda University">
    </div>
</div>

<?php include("footer.php"); ?>