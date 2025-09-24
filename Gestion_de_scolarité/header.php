<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion de Scolarité</title>
    <link rel="stylesheet" href="bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2ecc71;
            --accent-color: #e74c3c;
            --dark-color: #2c3e50;
            --light-color: #ecf0f1;
        }
        
        .navbar {
            background: linear-gradient(135deg, var(--dark-color) 0%, var(--primary-color) 100%) !important;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            padding: 0.8rem 1rem;
            transition: all 0.3s ease;
        }
        
        .navbar:hover {
            box-shadow: 0 6px 25px rgba(0, 0, 0, 0.2);
        }
        
        .navbar-brand {
            font-weight: 700;
            font-size: 1.8rem;
            color: white !important;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .navbar-brand:hover {
            transform: scale(1.05);
            text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.4);
        }
        
        .nav-link {
            color: rgba(255, 255, 255, 0.9) ;
            font-weight: 500;
            margin: 0 0.3rem;
            padding: 0.5rem 1rem !important;
            border-radius: 0.5rem;
            transition: all 0.3s ease;
            position: relative;
        }
        
        .nav-link:hover {
            color: white !important;
            background-color: rgba(255, 255, 255, 0.15);
            transform: translateY(-2px);
        }
        
        .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 2px;
            background: var(--secondary-color);
            transition: all 0.3s ease;
        }
        
        .nav-link:hover::after {
            width: 70%;
            left: 15%;
        }
        
        .dropdown-menu {
            background-color: white;
            border: none;
            border-radius: 0.5rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
            padding: 0.5rem 0;
            margin-top: 0.5rem !important;
        }
        
        .dropdown-item {
            padding: 0.5rem 1.5rem;
            color: var(--dark-color) !important;
            transition: all 0.2s ease;
            position: relative;
        }
        
        .dropdown-item:hover {
            background-color: rgba(46, 204, 113, 0.1);
            color: var(--secondary-color) !important;
            padding-left: 1.8rem;
        }
        
        .dropdown-item::before {
            content: '→';
            position: absolute;
            left: 0.8rem;
            opacity: 0;
            transition: all 0.2s ease;
            color: var(--secondary-color);
        }
        
        .dropdown-item:hover::before {
            opacity: 1;
            left: 1rem;
        }
        
        .dropdown-toggle::after {
            transition: all 0.3s ease;
        }
        
        .show .dropdown-toggle::after {
            transform: rotate(-180deg);
        }
        
        .nav-item.dropdown:hover .dropdown-menu {
            display: block;
            animation: fadeInUp 0.3s both;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .logout-btn {
            background: var(--accent-color);
            color: white !important;
            border: none;
            border-radius: 0.5rem;
            padding: 0.5rem 1.5rem;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: 0 2px 10px rgba(231, 76, 60, 0.3);
        }
        
        .logout-btn:hover {
            background: #c0392b;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(231, 76, 60, 0.4);
        }
        
        .fa-icon {
            margin-right: 8px;
            font-size: 0.9em;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand navbar-white mb-4 animate__animated animate__fadeInDown">
  <div class="container-fluid">
    <a class="navbar-brand" href="index.php">
        <i class="fas fa-university fa-icon"></i>SHARDA UNIVERSITY
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
            data-bs-target="#navbarNavDropdown"
            aria-controls="navbarNavDropdown" aria-expanded="false"
            aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
  
    <div class="collapse navbar-collapse" id="navbarNavDropdown">
      <ul class="navbar-nav">

        <!-- Étudiants -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button"
             data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fas fa-users fa-icon"></i>Étudiants
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="listeEtudiant.php"><i class="fas fa-list fa-icon"></i>Liste des etudiant</a></li>
            <li><a class="dropdown-item" href="Ajout_etudiant.php"><i class="fas fa-user-plus fa-icon"></i>Ajouter un etudiant</a></li>
            <li><a class="dropdown-item" href="note_etudiant.php"><i class="fas fa-clipboard-check fa-icon"></i>Consulter note des etudiants</a></li>
            <li><a class="dropdown-item" href="chercher_eleve.php"><i class="fas fa-search fa-icon"></i>Chercher un etudiant</a></li>
          </ul>
        </li>

        <!-- Professeurs -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button"
             data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fas fa-chalkboard-teacher fa-icon"></i>Professeurs
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="listeprof.php"><i class="fas fa-list fa-icon"></i>Liste des profs</a></li>
            <li><a class="dropdown-item" href="ajout_prof.php"><i class="fas fa-user-plus fa-icon"></i>Ajouter un prof</a></li>
            <li><a class="dropdown-item" href="chercher_prof.php"><i class="fas fa-search fa-icon"></i>Chercher un prof</a></li>
          </ul>
        </li>

        <!-- Matières -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button"
             data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fas fa-book fa-icon"></i>Matières
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="listematière.php"><i class="fas fa-list fa-icon"></i>Liste des matière</a></li>
            <li><a class="dropdown-item" href="ajout_matiere.php"><i class="fas fa-plus-circle fa-icon"></i>Ajouter un matière</a></li>
          </ul>
        </li>

        <!-- Stage -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button"
             data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fas fa-briefcase fa-icon"></i>Stage
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="afficher_stage.php"><i class="fas fa-list fa-icon"></i>Liste des stages</a></li>
            <li><a class="dropdown-item" href="ajout_stage.php"><i class="fas fa-plus-circle fa-icon"></i>Ajouter un stage</a></li>
            <li><a class="dropdown-item" href="chercher_stage.php"><i class="fas fa-search fa-icon"></i>Chercher un stages</a></li>
          </ul>
        </li>

        <!-- Classes -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button"
             data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fas fa-school fa-icon"></i>Classe
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="affiche_classe.php"><i class="fas fa-list fa-icon"></i>Liste des classe</a></li>
            <li><a class="dropdown-item" href="ajout_classe.php"><i class="fas fa-plus-circle fa-icon"></i>Ajouter des classe</a></li>
          </ul>
        </li>

        <!-- Conseil -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button"
             data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fas fa-comments fa-icon"></i>Conseil
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="affiche_conseil.php"><i class="fas fa-eye fa-icon"></i>Voir les conseil</a></li>
            <li><a class="dropdown-item" href="ajout_conseil.php"><i class="fas fa-plus-circle fa-icon"></i>Ajouter un conseil</a></li>
          </ul>
        </li>

        <!-- Bulletin -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button"
             data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fas fa-file-alt fa-icon"></i>Bulletin
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="afficher_bulettin.php"><i class="fas fa-graduation-cap fa-icon"></i>Note final d'un etudiant</a></li>
          </ul>
        </li>

        <!-- Diplomes -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button"
             data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fas fa-award fa-icon"></i>Diplomes
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="type_diplome.php"><i class="fas fa-tags fa-icon"></i>Type de diplome</a></li>
            <li><a class="dropdown-item" href="diplome_obt.php"><i class="fas fa-trophy fa-icon"></i>Diplome obtenue</a></li>
            <li><a class="dropdown-item" href="ajout_diplome.php"><i class="fas fa-plus-circle fa-icon"></i>Ajouter un diplome</a></li>
          </ul>
        </li>

       
        <!-- Devoir -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button"
             data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fas fa-tasks fa-icon"></i>Devoir
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="ajout_devoir.php"><i class="fas fa-plus-circle fa-icon"></i>Ajouter un devoir</a></li>
            <li><a class="dropdown-item" href="afficher_devoir.php"><i class="fas fa-eye fa-icon"></i>Voir les devoir</a></li>
          </ul>
        </li>

       
        <?php if (isset($_SESSION['user'])): ?>
            <li class="nav-item ms-auto">
                <a href="lougout.php" class="nav-link logout-btn animate__animated animate__pulse">
                    <i class="fas fa-sign-out-alt fa-icon"></i>Déconnexion
                </a>
            </li>
        <?php endif; ?>

      </ul>
    </div>
  </div>
</nav>