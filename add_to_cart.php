<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Vérifier que toutes les données nécessaires sont présentes
    if (isset($_POST['product_id']) && isset($_POST['quantity']) && isset($_POST['price']) && isset($_POST['image_url']) && isset($_POST['product_name'])) {
        // Créer un tableau pour l'élément du panier
        $item = [
            'product_id' => $_POST['product_id'],
            'size' => $_POST['size'] ?? null,
            'color_1' => $_POST['color_1'] ?? null,
            'color_2' => $_POST['color_2'] ?? null,
            'quantity' => (int)$_POST['quantity'],
            'price' => (float)$_POST['price'],
            'image_url' => $_POST['image_url'],
            'product_name' => $_POST['product_name']
        ];

        // Ajouter l'élément au panier (session)
        $_SESSION['cart'][] = $item;

        // Répondre avec un JSON
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'message' => 'Article ajouté au panier avec succès.']);
    } else {
        // Répondre avec une erreur
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Données manquantes.']);
    }
} else {
    // Répondre avec une erreur
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Méthode non autorisée.']);
}
