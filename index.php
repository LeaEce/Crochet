<?php
// Connexion à la base de données
$conn = new mysqli('localhost', 'root', '', 'crochet');

// Vérifier la connexion
if ($conn->connect_error) {
    die("Connexion échouée : " . $conn->connect_error);
}

// Récupérer les produits
$result = $conn->query("SELECT * FROM products");

$products = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="cart.css">
    <link rel="stylesheet" href="product.css">
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
            <section class="filters">
                <h2>Filtres</h2>
                <form id="filter-form">
                    <label for="size">Taille :</label>
                    <select name="size" id="size">
                        <option value="">Toutes</option>
                        <option value="S">S</option>
                        <option value="M">M</option>
                        <option value="L">L</option>
                    </select><br>
                    <label for="color">Couleur :</label>
                    <select name="color" id="color">
                        <option value="">Toutes</option>
                        <option value="Red">Rouge</option>
                        <option value="Blue">Bleu</option>
                        <option value="Green">Vert</option>
                    </select><br>
                    <button type="button" onclick="applyFilters()">Appliquer</button>
                </form>
            </section>
            <section class="products">
                <h2>Articles</h2>
                <div class="product-list">
                    <?php foreach ($products as $product): ?>
                        <div class="product-item">
                            <a href="product.php?id=<?php echo $product['id']; ?>">
                                <img src="<?php echo $product['image_url']; ?>" alt="<?php echo $product['name']; ?>">
                                <h3><?php echo $product['name']; ?></h3>
                                <p><?php echo $product['base_price']; ?>€</p>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        </main>
        <footer class="footer">
            <p>&copy; 2024 Le crochet sur mesure. Tous droits réservés.</p>
        </footer>
    </div>
    <script src="scripts.js"></script>
</body>
</html>
