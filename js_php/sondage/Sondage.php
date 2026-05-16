<?php
// Configuration de la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sondage_projet";

// Connexion à la base de données
$connection = mysqli_connect($servername, $username, $password, $dbname);

// Vérification de la connexion
if (!$connection) {
    die("Erreur de connexion: " . mysqli_connect_error());
}

// Définir le charset UTF-8
mysqli_set_charset($connection, "utf8");

// Vérification que la requête est POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Récupération et validation des données du formulaire
    $user = isset($_POST['user']) ? trim($_POST['user']) : '';
    $pass = isset($_POST['pass']) ? $_POST['pass'] : '';
    $genre = isset($_POST['sel']) ? $_POST['sel'] : '';
    
    // Validation basique
    if (empty($user) || empty($pass) || empty($genre)) {
        die("Erreur: Tous les champs sont obligatoires");
    }
    
    // Validation de l'email
    if (!filter_var($user, FILTER_VALIDATE_EMAIL)) {
        die("Erreur: Email invalide");
    }
    
    // Validation du genre
    if (!in_array($genre, ['M', 'F', 'A'])) {
        die("Erreur: Genre invalide");
    }
    
    // Récupération des réponses du sondage
    $q1 = isset($_POST['q1']) && in_array($_POST['q1'], ['oui', 'non', 'sans_avis']) ? $_POST['q1'] : null;
    $q2 = isset($_POST['q2']) && in_array($_POST['q2'], ['oui', 'non', 'sans_avis']) ? $_POST['q2'] : null;
    $q3 = isset($_POST['q3']) && in_array($_POST['q3'], ['oui', 'non', 'sans_avis']) ? $_POST['q3'] : null;
    
    // Vérifier qu'au moins une question est répondue
    if (!$q1 && !$q2 && !$q3) {
        die("Erreur: Veuillez répondre à au moins une question");
    }
    
    try {
        // Commencer une transaction
        $connection->begin_transaction();
        
        // Préparation de la requête INSERT pour l'utilisateur (avec requête préparée)
        $stmt = $connection->prepare("INSERT INTO users (user, passwordi) VALUES (?, ?)");
        if (!$stmt) {
            throw new Exception("Erreur de préparation: " . $connection->error);
        }
        
        $stmt->bind_param("ss", $user, $pass);
        
        if (!$stmt->execute()) {
            throw new Exception("Erreur lors de l'insertion de l'utilisateur: " . $stmt->error);
        }
        
        // Récupérer l'ID du participant
        $participantId = $connection->insert_id;
        
        // Insérer les réponses du sondage
        if ($q1 !== null) {
            $stmt_q1 = $connection->prepare("INSERT INTO reponse (NumQ, NumS, IdParticipant, Rep) VALUES (1, 1, ?, ?)");
            if (!$stmt_q1) throw new Exception("Erreur de préparation q1");
            $stmt_q1->bind_param("is", $participantId, $q1);
            if (!$stmt_q1->execute()) throw new Exception("Erreur lors de l'insertion de q1");
            $stmt_q1->close();
        }
        
        if ($q2 !== null) {
            $stmt_q2 = $connection->prepare("INSERT INTO reponse (NumQ, NumS, IdParticipant, Rep) VALUES (2, 1, ?, ?)");
            if (!$stmt_q2) throw new Exception("Erreur de préparation q2");
            $stmt_q2->bind_param("is", $participantId, $q2);
            if (!$stmt_q2->execute()) throw new Exception("Erreur lors de l'insertion de q2");
            $stmt_q2->close();
        }
        
        if ($q3 !== null) {
            $stmt_q3 = $connection->prepare("INSERT INTO reponse (NumQ, NumS, IdParticipant, Rep) VALUES (3, 1, ?, ?)");
            if (!$stmt_q3) throw new Exception("Erreur de préparation q3");
            $stmt_q3->bind_param("is", $participantId, $q3);
            if (!$stmt_q3->execute()) throw new Exception("Erreur lors de l'insertion de q3");
            $stmt_q3->close();
        }
        
        // Valider la transaction
        $connection->commit();
        $stmt->close();
        
        // Redirection vers la page de statistiques
        header("Location: statistiques.html");
        exit();
        
    } catch (Exception $e) {
        // Annuler la transaction en cas d'erreur
        $connection->rollback();
        die("Erreur: " . $e->getMessage());
    }
}

mysqli_close($connection);
?>