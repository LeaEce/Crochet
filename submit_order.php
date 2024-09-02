<?php
session_start();
$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

// Récupérer les données du formulaire
$customerName = $_POST['customer_name'];
$customerEmail = $_POST['customer_email'];
$contactPerson = $_POST['contact_person'];
$orderNumber = $_POST['order_number']; // Ajoutez ce champ caché dans le formulaire

// Générer le récapitulatif de la commande
$orderDetails = "Numéro de commande: $orderNumber\n";
$orderDetails .= "Nom: $customerName\n";
$orderDetails .= "Email: $customerEmail\n";
$orderDetails .= "Personne de contact: $contactPerson\n\n";
$orderDetails .= "Récapitulatif des articles:\n";

$totalPrice = 0;

foreach ($cart as $item) {
    $itemTotal = $item['price'] * $item['quantity'];
    $totalPrice += $itemTotal;
    $orderDetails .= "Produit: " . htmlspecialchars($item['product_name']) .
                    ", Taille: " . htmlspecialchars($item['size']) .
                    ", Couleur: " . htmlspecialchars($item['color']) .
                    ", Prix unitaire: " . htmlspecialchars(number_format($item['price'], 2)) .
                    "€, Quantité: " . htmlspecialchars($item['quantity']) .
                    ", Total: " . htmlspecialchars(number_format($itemTotal, 2)) . "€\n";
}

$orderDetails .= "\nTotal de la commande: " . number_format($totalPrice, 2) . "€\n";
$orderDetails .= "\nUne fois votre commande confirmée, elle sera transmise à votre personne de contact, qui vous la remettra en échange du montant total demandé.";

// PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer-master/src/Exception.php';
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';

$mail = new PHPMailer(true);

try {
    // Configuration du serveur SMTP pour Outlook
    $mail->isSMTP();
    $mail->Host = 'smtp.office365.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'votre-email@outlook.com'; // Votre adresse e-mail Outlook
    $mail->Password = 'votre-mot-de-passe-d-application'; // Mot de passe d'application ou votre mot de passe Outlook
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    // Paramètres de l'e-mail
    $mail->setFrom('no-reply@le-crochet-sur-mesure.com', 'Le Crochet sur Mesure');
    $mail->addAddress($customerEmail); // Envoi à l'utilisateur
    $mail->addAddress('lea.rc@laposte.net'); // Envoi à l'admin

    // Contenu de l'e-mail
    $mail->isHTML(false); // Envoi en texte brut
    $mail->Subject = 'Récapitulatif de votre commande';
    $mail->Body    = $orderDetails;

    // Envoi de l'e-mail
    $mail->send();
    $mailSent = true;

    // Nettoyer le panier après l'envoi
    unset($_SESSION['cart']);

} catch (Exception $e) {
    $mailSent = false;
    $errorMessage = "L'e-mail n'a pas pu être envoyé. Erreur de Mailer : {$mail->ErrorInfo}";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Confirmation</title>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            <?php if ($mailSent): ?>
                alert("Merci pour votre commande !");
                window.location.href = "index.php"; // Redirige vers la page d'accueil après la confirmation
            <?php else: ?>
                alert("Une erreur est survenue : <?php echo $errorMessage; ?>");
                window.location.href = "cart.php"; // Redirige vers le panier en cas d'erreur
            <?php endif; ?>
        });
    </script>
</head>
<body>
</body>
</html>