<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer-master/src/Exception.php';
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';

$mail = new PHPMailer(true);

try {
    // Configuration du serveur SMTP
    $mail->isSMTP();
    $mail->Host = 'smtp.office365.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'le-crochet-sur-mesure@outlook.com';
    $mail->Password = 'hdecysdfvyyjttwc';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    // Destinataire
    $mail->setFrom('le-crochet-sur-mesure@outlook.com', 'GAUTHIER');
    $mail->addAddress('thibault.leonardon@gmail.com', 'LEONARDON');

    // Contenu de l'e-mail
    $mail->isHTML(true);
    $mail->Subject = 'Récapitulatif de votre commande';
    $mail->Body    = 'Voici le récapitulatif de votre commande...';
    $mail->AltBody = 'Voici le récapitulatif de votre commande...';

    // Envoi
    $mail->send();
    echo 'L\'e-mail a été envoyé avec succès';
} catch (Exception $e) {
    echo "L'e-mail n'a pas pu être envoyé. Erreur : {$mail->ErrorInfo}";
}
?>
