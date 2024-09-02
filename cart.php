<?php
session_start();

// Supprimer un élément du panier
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['remove_id'])) {
    $remove_id = $_POST['remove_id'];
    foreach ($_SESSION['cart'] as $key => $item) {
        if ($item['product_id'] == $remove_id) {
            unset($_SESSION['cart'][$key]);
            break;
        }
    }
    // Réindexer le tableau pour éviter les trous dans les indices
    $_SESSION['cart'] = array_values($_SESSION['cart']);
}

$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

// Calculer le prix total
$total_price = 0;
foreach ($cart as $item) {
    $total_price += $item['price'] * $item['quantity'];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panier</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="cart.css">
</head>
<body>
    <div class="wrapper">
        <header>
            <div class="header-content">
                <h1>Le crochet sur mesure</h1>
                <a href="informations.php" class="information">Informations</a>
                <a href="cart.php" class="cart-button">Panier</a>
            </div>
        </header>
        <main>
            <div class="cart-container">
                <div class="cart-left">
                    <h2>Panier</h2>
                    <div class="cart-items">
                        <?php foreach ($cart as $item): ?>
                            <div class="cart-item">
                                <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="Image du produit" class="cart-item-image">
                                <div class="cart-item-details">
                                    <span>Article: <?php echo htmlspecialchars($item['product_name']); ?></span>
                                    <?php if ($item['size']): ?>
                                        <span>Taille: <?php echo htmlspecialchars($item['size']); ?></span>
                                    <?php endif; ?>
                                    <?php if ($item['color']): ?>
                                        <span>Couleur: <?php echo htmlspecialchars($item['color']); ?></span>
                                    <?php endif; ?>
                                    <?php if ($item['width'] && $item['height']): ?>
                                        <span>Dimensions: <?php echo htmlspecialchars($item['width']); ?> cm x <?php echo htmlspecialchars($item['height']); ?> cm</span>
                                    <?php endif; ?>
                                    <?php if ($item['diameter']): ?>
                                        <span>Diamètre: <?php echo htmlspecialchars($item['diameter']); ?> cm</span>
                                    <?php endif; ?>
                                    <span>Quantité: <?php echo htmlspecialchars($item['quantity']); ?></span>
                                    <span>Prix: <?php echo htmlspecialchars(number_format($item['price'], 2)); ?>€</span>
                                </div>
                                <form method="post" class="remove-form">
                                    <input type="hidden" name="remove_id" value="<?php echo htmlspecialchars($item['product_id']); ?>">
                                    <button type="submit" class="remove-button">X</button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php if (count($cart) > 0): ?>
                        <p></p>
                    <?php else: ?>
                        <p>Votre panier est vide.</p>
                    <?php endif; ?>
                </div>
                
                <div class="summary-container">
                    
                    <h2>Résumé</h2>
                    <p>Nombre d'articles: <?php echo count($cart); ?></p>
                    <p class="total-price">Total: <?php echo number_format($total_price, 2); ?>€</p>
                    <?php if (count($cart) > 0): ?>
                        <form action="order.php" method="post">
                            <input type="submit" value="Valider le panier" class="validate-button">
                        </form>
                    <?php endif; ?>
                </div>
            </div>
            <a href="index.php" class="back-button">Retour à l'accueil</a>
        </main>
        <footer class="footer">
            <p>&copy; 2024 Le crochet sur mesure. Tous droits réservés.</p>
        </footer>
    </div>
</body>
</html>
