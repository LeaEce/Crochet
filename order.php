<?php
session_start();
$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

$total_price = 0;

// Calculer le prix total des articles
foreach ($cart as $item) {
    $total_price += $item['price'] * $item['quantity'];
}

// Générer un numéro de commande
$orderNumber = generateOrderNumber();

function generateOrderNumber($length = 6) {
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'; // Lettres majuscules et chiffres
    $orderNumber = '';

    for ($i = 0; $i < $length; $i++) {
        $orderNumber .= $characters[rand(0, strlen($characters) - 1)]; // Sélectionne un caractère aléatoire
    }

    return $orderNumber;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Commande</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="cart.css">
    <link rel="stylesheet" href="order.css">
</head>
<body>
    <div class="order-container">
        <h1>Commande</h1>
        <form action="submit_order.php" method="post">
            <input type="hidden" name="order_number" value="<?php echo $orderNumber; ?>">
            <label for="customer_first_name">Prénom :</label>
            <input type="text" id="customer_first_name" name="customer_first_name" required>

            <label for="customer_name">Nom :</label>
            <input type="text" id="customer_name" name="customer_name" required>

            <label for="customer_email">Email :</label>
            <input type="email" id="customer_email" name="customer_email" required>

            <label for="contact_person">Personne de contact :</label>
            <select id="contact_person" name="contact_person" required>
                <option value="lea">Léa</option>
                <option value="laure">Laure</option>
                <option value="anne_cecile">Anne-Cécile</option>
                <option value="autres">Autres (à préciser)</option>
            </select>

            <div id="other_contact_person" style="display: none;">
                <label for="other_contact">Précisez :</label>
                <input type="text" id="other_contact" name="other_contact">
            </div>

            <h2>Récapitulatif des articles</h2>
            <ul>
                <?php foreach ($cart as $item): ?>
                    <li>
                        Produit : <?php echo htmlspecialchars($item['product_name']); ?>,
                        Taille : <?php echo htmlspecialchars($item['size']); ?>,
                        Couleur : <?php echo htmlspecialchars($item['color']); ?>,
                        Prix : <?php echo number_format($item['price'], 2); ?>€,
                        Quantité : <?php echo htmlspecialchars($item['quantity']); ?>
                    </li>
                <?php endforeach; ?>
            </ul>

            <div class="order-summary">
                <p>Numéro de commande : <strong><?php echo $orderNumber; ?></strong></p>
                <p>Prix total : <strong><?php echo number_format($total_price, 2); ?>€</strong></p>
            </div>

            <input type="submit" value="Envoyer la commande">
        </form>
    </div>

    <script>
        document.getElementById('contact_person').addEventListener('change', function() {
            const otherContactInput = document.getElementById('other_contact_person');
            otherContactInput.style.display = this.value === 'autres' ? 'block' : 'none';
        });
    </script>
</body>
</html>
