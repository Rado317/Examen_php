<?php
include("header.php");
include("connect.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nomel = $_POST['nomel'] ?? '';
    $prenomel = $_POST['prenomel'] ?? '';
    $date_naissance = $_POST['date_naissance'] ?? '';
    $codecl = $_POST['codecl'] ?? '';

    if (!empty($nomel) && !empty($prenomel) && !empty($date_naissance) && !empty($codecl)) {
        $stmt = $conn->prepare("INSERT INTO eleve (nomel, prenomel, date_naissance, codecl) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $nomel, $prenomel, $date_naissance, $codecl);

        if ($stmt->execute()) {
            $success_message = "✅ Étudiant ajouté avec succès !";
        } else {
            $error_message = "❌ Erreur lors de l'ajout : " . $stmt->error;
        }

        $stmt->close();
    } else {
        $error_message = "❌ Tous les champs sont obligatoires.";
    }
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
    
    .form-container {
        max-width: 800px;
        margin: 2rem auto;
        padding: 2.5rem;
        background: white;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        animation: fadeInUp 0.8s ease-out;
    }
    
    h2 {
        color: var(--dark-color);
        text-align: center;
        margin-bottom: 2rem;
        font-weight: 700;
        animation: fadeInDown 0.8s ease-out;
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
    
    input, select {
        width: 100%;
        padding: 0.8rem 1rem;
        border: 2px solid #ddd;
        border-radius: 8px;
        font-size: 1rem;
        transition: all 0.3s ease;
    }
    
    input:focus, select:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
        outline: none;
    }
    
    .btn-submit {
        display: block;
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
        margin-top: 1.5rem;
    }
    
    .btn-submit:hover {
        background-color: #27ae60;
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(46, 204, 113, 0.4);
    }
    
    .alert {
        padding: 1rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
        animation: fadeIn 0.5s ease-out;
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
    
    .form-footer {
        text-align: center;
        margin-top: 2rem;
    }
    
    .form-footer a {
        color: var(--primary-color);
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .form-footer a:hover {
        color: var(--dark-color);
        text-decoration: underline;
    }
</style>

<div class="container">
    <div class="form-container">
        <h2><i class="fas fa-user-plus"></i> Ajouter un étudiant</h2>
        
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success">
                <?= $success_message ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div class="alert alert-error">
                <?= $error_message ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="nomel"><i class="fas fa-id-card"></i> Nom :</label>
                <input type="text" id="nomel" name="nomel" required>
            </div>
            
            <div class="form-group">
                <label for="prenomel"><i class="fas fa-id-card"></i> Prénom :</label>
                <input type="text" id="prenomel" name="prenomel" required>
            </div>
            
            <div class="form-group">
                <label for="date_naissance"><i class="fas fa-birthday-cake"></i> Date de naissance :</label>
                <input type="date" id="date_naissance" name="date_naissance" required>
            </div>
            
            <div class="form-group">
                <label for="codecl"><i class="fas fa-graduation-cap"></i> Classe :</label>
                <select id="codecl" name="codecl" required>
                    <option value="">-- Choisir une classe --</option>
                    <option value="GI">GI</option>
                    <option value="TM">TM</option>
                    <option value="GRH">GRH</option>
                </select>
            </div>
            
            <button type="submit" class="btn-submit">
                <i class="fas fa-save"></i> Ajouter l'étudiant
            </button>
        </form>
        
        <div class="form-footer">
            <a href="listeEtudiant.php"><i class="fas fa-arrow-left"></i> Retour à la liste des étudiants</a>
        </div>
    </div>
</div>

<?php include("footer.php"); ?>