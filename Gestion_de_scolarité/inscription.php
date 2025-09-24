<?php
session_start();

// Connexion à la base de données
$conn = new mysqli("localhost", "root", "", "test");
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

// Traitement du formulaire d'inscription
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupération et sécurisation des données
    $nom = htmlspecialchars($_POST['nom']);
    $prenom = htmlspecialchars($_POST['prenom']);
    $date_naissance = htmlspecialchars($_POST['date_naissance']);
    $classe = htmlspecialchars($_POST['classe']);

    // Insertion dans la base de données
    $stmt = $conn->prepare("INSERT INTO eleve (nomel, prenomel, date_naissance, codecl) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $nom, $prenom, $date_naissance, $classe);

    if ($stmt->execute()) {
        // Inscription réussie
        $_SESSION['user'] = $nom; // On utilise le nom comme identifiant de session
        header("Location: index.php");
        exit();
    } else {
        $error = "Erreur lors de l'inscription : " . $conn->error;
    }
}

// Récupération des classes pour le select
$classes = $conn->query("SELECT codecl, nom FROM classe");
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription Élève | SHARDA UNIVERSITY</title>
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
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: rgba(0, 0, 0, 0.5);
            position: relative;
            overflow: hidden;
        }
        
        #video-background {
            position: fixed;
            right: 0;
            bottom: 0;
            min-width: 100%;
            min-height: 100%;
            width: auto;
            height: auto;
            z-index: -1;
            object-fit: cover;
        }
        
        .register-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 2.5rem;
            border-radius: 15px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
            width: 450px;
            max-width: 90%;
            animation: fadeInUp 0.8s ease-out;
        }
        
        .register-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .register-title {
            color: var(--dark-color);
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .register-subtitle {
            color: var(--primary-color);
            font-size: 1rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--dark-color);
            font-weight: 600;
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
        
        .btn-register {
            width: 100%;
            padding: 1rem;
            background-color: var(--secondary-color);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 1rem;
        }
        
        .btn-register:hover {
            background-color: #27ae60;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(46, 204, 113, 0.4);
        }
        
        .login-link {
            text-align: center;
            margin-top: 1.5rem;
            color: var(--dark-color);
        }
        
        .login-link a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
        }
        
        .login-link a:hover {
            text-decoration: underline;
        }
        
        .error-message {
            color: var(--accent-color);
            text-align: center;
            margin-bottom: 1.5rem;
            font-weight: 500;
            padding: 1rem;
            background-color: rgba(231, 76, 60, 0.1);
            border-radius: 8px;
            border-left: 4px solid var(--accent-color);
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
    <video autoplay muted loop id="video-background">
        <source src="video.mp4" type="video/mp4">
        Votre navigateur ne supporte pas les vidéos HTML5.
    </video>
    
    <div class="register-container">
        <div class="register-header">
            <h1 class="register-title"><i class="fas fa-user-graduate"></i> Inscription Élève</h1>
            <p class="register-subtitle">Rejoignez SHARDA UNIVERSITY</p>
        </div>
        
        <?php if (isset($error)): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="inscription.php">
            <div class="form-group">
                <label for="nom"><i class="fas fa-user"></i> Nom</label>
                <input type="text" id="nom" name="nom" class="form-control" placeholder="Votre nom" required>
            </div>
            
            <div class="form-group">
                <label for="prenom"><i class="fas fa-user"></i> Prénom</label>
                <input type="text" id="prenom" name="prenom" class="form-control" placeholder="Votre prénom" required>
            </div>
            
            <div class="form-group">
                <label for="date_naissance"><i class="fas fa-birthday-cake"></i> Date de naissance</label>
                <input type="date" id="date_naissance" name="date_naissance" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="classe"><i class="fas fa-school"></i> Classe</label>
                <select id="classe" name="classe" class="form-control" required>
                    <option value="">-- Sélectionnez votre classe --</option>
                    <?php while ($classe = $classes->fetch_assoc()): ?>
                        <option value="<?= htmlspecialchars($classe['codecl']) ?>">
                            <?= htmlspecialchars($classe['nom']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <button type="submit" class="btn-register">
                <i class="fas fa-user-plus"></i> S'inscrire
            </button>
        </form>
        
        <div class="login-link">
            Déjà inscrit ? <a href="login.php"><i class="fas fa-sign-in-alt"></i> Connectez-vous</a>
        </div>
    </div>
</body>
</html>