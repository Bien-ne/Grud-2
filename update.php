<?php
require "./config.php";

$index = $_GET['index'] ?? null;
$query = $pdo->query("SELECT * FROM produits WHERE id = $index");
$produit = $query->fetch(PDO::FETCH_ASSOC);
$categories = chargerCategories($pdo);

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom = $_POST['nom'] ?? '';
    $categorie = $_POST['categorie'] ?? '';
    $prix = $_POST['prix'] ?? 0;
    $quantite = $_POST['quantite'] ?? 0;
    $description = $_POST['description'] ?? '';

    if (isset($_POST['modifier']) && $index !== null) {
        if ($nom && $categorie && is_numeric($prix) && is_numeric($quantite)) {
            $stmt = $pdo->prepare("UPDATE produits SET nom = ?, categorie = ?, prix = ?, quantite = ?, description = ? WHERE id = ?");
            $stmt->execute([$nom, $categorie, $prix, $quantite, $description, $index]);
            $message = "Produit modifié avec succès !";
            header("Location: index.php?message=" . urlencode($message));
            exit;
        } else {
            $message = "Erreur : veuillez remplir tous les champs correctement.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Produit</title>
    <style>
        /* Styles similaires à index.php */
    </style>
</head>
<body>
<div class="container">
    <h1>Modifier le Produit</h1>
    <?php if ($message): ?>
        <div class="message"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <form method="POST">
        <input type="text" name="nom" placeholder="Nom du produit" value="<?= htmlspecialchars($produit['nom']); ?>" required>
        <select name="categorie" required>
            <?php foreach ($categories as $categorie): ?>
                <option value="<?= htmlspecialchars($categorie['id']); ?>" <?= $produit['categorie'] == $categorie['id'] ? 'selected' : ''; ?>>
                    <?= htmlspecialchars($categorie['nom']); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <input type="number" name="prix" placeholder="Prix" value="<?= htmlspecialchars($produit['prix']); ?>" required>
        <input type="number" name="quantite" placeholder="Quantité" value="<?= htmlspecialchars($produit['quantite']); ?>" required>
        <input type="text" name="description" placeholder="Description" value="<?= htmlspecialchars($produit['description']); ?>" required>
        <button type="submit" name="modifier">Sauvegarder les modifications</button>
    </form>
</div>
</body>
</html>
