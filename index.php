<?php
$host = "localhost";
$db   = "mutuelle";
$user = "root";
$pass = "";
$dsn = "mysql:host=$host;dbname=$db;charset=utf8";

try {
    $pdo = new PDO($dsn, $user, $pass);
} catch (Exception $e) {
    die("Erreur connexion BD : " . $e->getMessage());
}

$mat = $_GET['mat'] ?? '';
if (!$mat) die("Matricule non fourni.");

$sql = "SELECT Assuree, Mat, NDG, MT, Malade 
        FROM mutuelle 
        WHERE Mat = :mat 
        ORDER BY N DESC 
        LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->execute(['mat' => $mat]);
$assure = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$assure) die("Aucun dossier trouvé.");

function formatMessage($Assuree, $Mat, $NDG, $MT, $Malade) {
    $montant = $MT > 0 ? " d’un montant de <strong>" . number_format($MT, 2, ',', ' ') . " DH</strong>" : "";
    $relation = ($Malade && strtolower(trim($Malade)) !== strtolower(trim($Assuree))) 
        ? " de Mr/Mme " . htmlspecialchars($Malade)
        : "";
    return "
        <p>🌟 Bonjour <strong>$Assuree</strong> (<strong>$Mat</strong>) 🌟</p>
        <p>🔹 <strong>SOMAGEC - Service Assurance Maladie</strong> 🔹</p>
        <h2 style='color:#007bff;'>🌟🌟 ACCUSÉ DE RÉCEPTION 🌟🌟</h2>
        <p>Votre dossier <strong>$NDG</strong>$montant$relation a été envoyé à <strong>OMEGA Assurance</strong>.</p>
        <p>Merci de votre confiance ! 😊</p>
    ";
}
$message = formatMessage($assure['Assuree'], $assure['Mat'], $assure['NDG'], $assure['MT'], $assure['Malade']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Accusé de réception</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            max-width: 700px;
            margin: 30px auto;
            padding: 30px;
            background-color: white;
            color: #333;
            border: 2px solid #ccc;
            border-radius: 15px;
        }
        .message {
            padding: 25px;
            border-radius: 10px;
        }
        button {
            background-color: #28a745;
            color: white;
            padding: 12px 25px;
            font-size: 1em;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            margin: 10px;
        }
        button:hover {
            background-color: #218838;
        }
        @media print {
            button { display: none; }
            body { box-shadow: none; background: none; }
        }
    </style>
</head>
<body>
    <div class="message"><?= $message ?></div>
    <center><button onclick="window.print()">🖨️ Imprimer</button></center>
</body>
</html>