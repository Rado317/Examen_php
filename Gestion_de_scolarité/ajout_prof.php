<?php
session_start();
include('cadre.php');

// Connexion sécurisée avec mysqli
$mysqli = new mysqli("localhost", "root", "", "TEST");
if ($mysqli->connect_error) {
    die("Erreur de connexion : " . $mysqli->connect_error);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter Professeur | SHARDA UNIVERSITY</title>
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
            max-width: 600px;
            margin: 2rem auto;
            padding: 2rem;
        }
        
        .form-container {
            background: white;
            border-radius: 15px;
            padding: 2.5rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
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
        
        textarea.form-control {
            min-height: 100px;
            resize: vertical;
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
        }
    </style>
</head>
<body>

<div class="corp">
    <h1 class="page-title"><i class="fas fa-chalkboard-teacher"></i> Ajouter un Professeur</h1>
    
    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Vérification que tous les champs sont remplis
        if (!empty($_POST['nom']) && !empty($_POST['prenom']) && !empty($_POST['adresse']) &&
            !empty($_POST['telephone']) && !empty($_POST['pseudo']) && !empty($_POST['passe'])) {

            // Échappement + nettoyage des données
            $nom = htmlspecialchars(trim($_POST['nom']));
            $prenom = htmlspecialchars(trim($_POST['prenom']));
            $adresse = htmlspecialchars(trim($_POST['adresse']));
            $telephone = htmlspecialchars(trim($_POST['telephone']));
            $pseudo = htmlspecialchars(trim($_POST['pseudo']));
            $passe = htmlspecialchars(trim($_POST['passe']));

            // Vérifier si le prof existe déjà
            $stmt = $mysqli->prepare("SELECT COUNT(*) FROM prof WHERE nom = ? AND prenom = ?");
            $stmt->bind_param("ss", $nom, $prenom);
            $stmt->execute();
            $stmt->bind_result($nb);
            $stmt->fetch();
            $stmt->close();

            if ($nb > 0) {
                echo '<div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> Erreur : Ce professeur existe déjà.
                </div>';
            } else {
                // Insérer dans prof
                $stmt = $mysqli->prepare("INSERT INTO prof (nom, prenom, adresse, telephone) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssss", $nom, $prenom, $adresse, $telephone);
                if ($stmt->execute()) {
                    // Récupérer le dernier id du prof
                    $numprof = $mysqli->insert_id;
                    $stmt->close();

                    // Insertion dans login
                    $stmt = $mysqli->prepare("INSERT INTO login (Num, pseudo, passe, type) VALUES (?, ?, ?, 'prof')");
                    $stmt->bind_param("iss", $numprof, $pseudo, $passe);
                    if ($stmt->execute()) {
                        echo '<div class="alert alert-success">
                            <i class="fas fa-check-circle"></i> Professeur ajouté avec succès !
                        </div>';
                    } else {
                        echo '<div class="alert alert-error">
                            <i class="fas fa-exclamation-circle"></i> Erreur lors de l\'ajout dans le système de connexion.
                        </div>';
                    }
                    $stmt->close();
                } else {
                    echo '<div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i> Erreur lors de l\'ajout du professeur.
                    </div>';
                }
            }
        } else {
            echo '<div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> Veuillez remplir tous les champs obligatoires.
            </div>';
        }

        echo '<div class="text-center">
            <a href="ajout_prof.php" class="back-link">
                <i class="fas fa-arrow-left"></i> Ajouter un autre professeur
            </a>
        </div>';
    } else {
    ?>
    
    <div class="form-container">
        <form action="ajout_prof.php" method="POST">
            <div class="form-group">
                <label for="nom"><i class="fas fa-user"></i> Nom :</label>
                <input type="text" id="nom" name="nom" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="prenom"><i class="fas fa-user"></i> Prénom :</label>
                <input type="text" id="prenom" name="prenom" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="adresse"><i class="fas fa-map-marker-alt"></i> Adresse :</label>
                <textarea id="adresse" name="adresse" class="form-control" required></textarea>
            </div>
            
            <div class="form-group">
                <label for="telephone"><i class="fas fa-phone"></i> Téléphone :</label>
                <input type="text" id="telephone" name="telephone" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="pseudo"><i class="fas fa-user-circle"></i> Pseudo :</label>
                <input type="text" id="pseudo" name="pseudo" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="passe"><i class="fas fa-lock"></i> Mot de passe :</label>
                <input type="password" id="passe" name="passe" class="form-control" required>
            </div>
            
            <button type="submit" class="btn-submit">
                <i class="fas fa-user-plus"></i> Ajouter le professeur
            </button>
        </form>
    </div>
    
    <?php } ?>
</div>

</body>
</html>