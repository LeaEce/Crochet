<?php
// Vérifier si un ID est passé dans l'URL
if (!isset($_GET['id'])) {
    die("Produit non trouvé.");
}

// Connexion à la base de données
$conn = new mysqli('localhost', 'root', '', 'crochet');

// Vérifier la connexion
if ($conn->connect_error) {
    die("Connexion échouée : " . $conn->connect_error);
}

// Récupérer les détails du produit
$product_id = $conn->real_escape_string($_GET['id']);
$result = $conn->query("SELECT * FROM products WHERE id = $product_id");

if ($result->num_rows == 0) {
    die("Produit non trouvé.");
}

$product = $result->fetch_assoc();

// Récupérer les variantes du produit (tailles, couleurs, etc.)
$variants = $conn->query("SELECT * FROM product_variants WHERE product_id = $product_id");

$sizeOptions = [];
$colorOptions = [];

while ($variant = $variants->fetch_assoc()) {
    if ($variant['type'] === 'size') {
        // Ajouter la variante de taille au tableau des options de taille
        $sizeOptions[] = $variant;
    } elseif (strpos($variant['type'], 'color') === 0) {
        // Ajouter la variante de couleur au tableau des options de couleur
        if (!isset($colorOptions[$variant['type']])) {
            $colorOptions[$variant['type']] = [];
        }
        $colorOptions[$variant['type']][] = $variant;
    }
}


// Récupérer les personnalisations spécifiques (dimensions, diamètre, etc.)
$customization = $conn->query("SELECT * FROM product_customizations WHERE product_id = $product_id")->fetch_assoc();

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?></title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="product.css">
    <link rel="stylesheet" href="cart.css">
    <script src="config.js"></script>
    <script src="scripts.js"></script>
    <script>
        function addToCart(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);

            fetch('add_to_cart.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    showPopup(data.message);
                } else {
                    showPopup(data.message, true);
                }
            })
            .catch(error => {
                showPopup('Erreur : ' + error.message, true);
            });
        }

        function showPopup(message, isError = false) {
            const popup = document.createElement('div');
            popup.className = 'popup' + (isError ? ' error' : '');
            popup.innerText = message;
            document.body.appendChild(popup);
            setTimeout(() => {
                popup.remove();
            }, 30000);
        }
        let basePrice = <?php echo json_encode($product['base_price']); ?>;

        function calculatePrice() {
            let totalPrice = parseFloat(basePrice);

            // Récupère chaque élément ayant un data-price-modifier
            document.querySelectorAll('[data-price-modifier]').forEach(function(element) {
                let priceModifier = parseFloat(element.getAttribute('data-price-modifier'));

                if ((element.type === 'checkbox' || element.type === 'radio') && element.checked) {
                    // Ajoute le priceModifier des checkboxes et radios cochés
                    totalPrice += priceModifier;
                if (element.tagName === 'SELECT' && element.value) {
                    let selectedOption = element.options[element.selectedIndex];
                    let priceModifier = parseFloat(selectedOption.getAttribute('data-price-modifier'));
                    totalPrice += priceModifier;
                } else if (element.tagName === 'INPUT' && element.type === 'number' && element.value) {
                    // Ajoute le priceModifier pour les inputs de type number
                    let quantity = parseInt(element.value, 10);
                    totalPrice += totalPrice * quantity;
                } else if (element.tagName === 'INPUT' && element.type === 'range') {
                    // Ajoute le priceModifier pour les inputs de type range
                    totalPrice += priceModifier;
                }
            });

            // Mise à jour de l'affichage du prix
            document.querySelector('.price').innerText = totalPrice.toFixed(2) + '€';
            document.querySelector('#price-input').value = totalPrice.toFixed(2);
        }

        // Appel de calculatePrice pour chaque changement d'option
        document.querySelectorAll('[data-price-modifier]').forEach(function(element) {
            element.addEventListener('change', calculatePrice);
        });

        // Appel initial pour calculer le prix au chargement de la page
        calculatePrice();


    </script>
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
            <section class="product-details">
                <div class="product-image-slider">
                    <div class="image-container">
                        <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="slider-image active">
                        <img src="<?php echo htmlspecialchars($product['image_url_2']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="slider-image">
                    </div>
                    <button class="prev" onclick="changeSlide(-1)">&#10094;</button>
                    <button class="next" onclick="changeSlide(1)">&#10095;</button>
                </div>
                <div class="product-info">
                    <h2><?php echo htmlspecialchars($product['name']); ?></h2>
                    <p><?php echo htmlspecialchars($product['description']); ?></p>
                    <div class="product-options">
                        <form onsubmit="addToCart(event)">
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                            <input type="hidden" name="image_url" value="<?php echo htmlspecialchars($product['image_url']); ?>">
                            <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($product['name']); ?>">

                            <!-- Options de taille -->
                            <?php if (count($sizeOptions) > 0): ?>
                                <label for="size">Taille :</label>
                                <select name="size" id="size" onchange="calculatePrice()">
                                    <option value="">Choisir une taille</option>
                                    <?php foreach ($sizeOptions as $size): ?>
                                        <option value="<?php echo htmlspecialchars($size['value']); ?>" data-price-modifier="<?php echo htmlspecialchars($size['price_modifier']); ?>">
                                            <?php echo htmlspecialchars($size['value']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            <?php endif; ?>

                            <!-- Options de couleur -->
                            <?php foreach ($colorOptions as $colorType => $colors): ?>
                                <label for="<?php echo $colorType; ?>">Couleur <?php echo ucfirst(substr($colorType, 6)); ?> :</label>
                                <div class="color-options">
                                    <?php foreach ($colors as $color): ?>
                                        <input type="radio" name="<?php echo $colorType; ?>" id="<?php echo $colorType; ?>-<?php echo strtolower(htmlspecialchars($color['value'])); ?>"
                                               value="<?php echo htmlspecialchars($color['value']); ?>"
                                               data-price-modifier="<?php echo htmlspecialchars($color['price_modifier']); ?>" onchange="calculatePrice()">
                                        <label for="<?php echo $colorType; ?>-<?php echo strtolower(htmlspecialchars($color['value'])); ?>" class="color-option <?php echo strtolower(htmlspecialchars($color['value'])); ?>">
                                            <span class="color-name"><?php echo htmlspecialchars($color['value']); ?></span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            <?php endforeach; ?>

                            <!-- Dimensions pour trousse/mitaines - affichées seulement si elles existent dans la base -->
                            <?php if (!empty($customization['dimension_1']) && !empty($customization['dimension_2'])): ?>
                                <label for="width">Largeur (5-25 cm) :</label>
                                <input type="range" name="width" id="width" min="5" max="25" value="13" 
                                       onchange="updateDimensions('width', this.value); calculatePrice()" data-price-modifier="0">
                                <span id="width-value" class="dimension-value">13 cm</span><br>

                                <label for="height">Hauteur (5-25 cm) :</label>
                                <input type="range" name="height" id="height" min="5" max="25" value="9" 
                                       onchange="updateDimensions('height', this.value); calculatePrice()" data-price-modifier="0">
                                <span id="height-value" class="dimension-value">9 cm</span><br>
                            <?php endif; ?>

                            <!-- Autres personnalisations spécifiques -->
                            <!-- Exemple pour le diamètre -->
                            <?php if ($customization['diameter']): ?>
                                <label for="diameter">Diamètre :</label>
                                <input type="number" name="diameter" id="diameter"
                                    value="0"
                                    onchange="calculatePrice()"
                                    data-price-modifier="2"> <!-- Supposons que chaque cm ajoute 2€ -->
                                <br>
                            <?php endif; ?>

                            <label for="quantity">Quantité :</label>
                            <input type="number" name="quantity" id="quantity" min="1" value="1"><br>
                            <p class="price"></p>
                            <input type="hidden" name="price" id="price-input">
                            <button type="submit" class="add-to-cart-button">Ajouter au panier</button>
                        </form>
                        <br>
                    </div>
                </div>
            </section>
            <a href="index.php" class="back-button">Retour à l'accueil</a>
        </main>
        <footer class="footer">
            <p>&copy; 2024 Le crochet sur mesure. Tous droits réservés.</p>
        </footer>
    </div>
</body>
</html>
