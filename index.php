<?php
require "./config.php";

function chargerProduits($pdo) {
    $query = $pdo->query("SELECT * FROM produits");
    return $query->fetchAll(PDO::FETCH_ASSOC);
}

function chargerCategories($pdo) {
    $query = $pdo->query("SELECT * FROM categories");
    return $query->fetchAll(PDO::FETCH_ASSOC);
}

$produits = chargerProduits($pdo);
$categories = chargerCategories($pdo);
$message = '';

// Ajouter, modifier ou supprimer un produit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom = $_POST['nom'] ?? '';
    $categorie = $_POST['categorie'] ?? '';
    $prix = $_POST['prix'] ?? 0;
    $quantite = $_POST['quantite'] ?? 0;
    $description = $_POST['description'] ?? '';
    $produit_id = $_POST['produit_id'] ?? null;

    if (isset($_POST['ajouter'])) {
        if ($nom && $categorie && is_numeric($prix) && is_numeric($quantite)) {
            $stmt = $pdo->prepare("INSERT INTO produits (nom, categorie, prix, quantite, description) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$nom, $categorie, $prix, $quantite, $description]);
            $message = "Produit ajouté avec succès !";
        } else {
            $message = "Erreur : veuillez remplir tous les champs correctement.";
        }
    } elseif (isset($_POST['supprimer']) && isset($_POST['produit_id'])) {
        $stmt = $pdo->prepare("DELETE FROM produits WHERE id = ?");
        $stmt->execute([$_POST['produit_id']]);
        $message = "Produit supprimé avec succès !";
    } elseif (isset($_POST['modifier']) && $produit_id) {
        $stmt = $pdo->prepare("UPDATE produits SET nom = ?, categorie = ?, prix = ?, quantite = ?, description = ? WHERE id = ?");
        $stmt->execute([$nom, $categorie, $prix, $quantite, $description, $produit_id]);
        $message = "Produit modifié avec succès !";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Produits</title>
    <style>
        /* Votre style ici */
    </style>
    <script>
        function toggleForm(formId) {
            var forms = document.querySelectorAll('.modifierForm, .supprimerForm');
            forms.forEach(function(form) {
                form.style.display = 'none'; // Masquer tous les formulaires
            });
            document.getElementById(formId).style.display = 'block'; // Afficher le formulaire spécifié
        }
    </script>
</head>
<link rel="stylesheet" href="styles.css">
<body>
<div class="container">
    <h1>Gestion de Produits</h1>
    <?php if ($message): ?>
        <div class="message"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <div class="form" id="ajoutProduitForm">
        <h2>Ajouter un Produit</h2>
        <form method="POST">
            <input type="text" name="nom" placeholder="Nom du produit" required>
            <select name="categorie" required>
                <option value="">Selectionnez une categorie</option>
                <?php foreach ($categories as $categorie): ?>
                    <option value="<?= htmlspecialchars($categorie['id']); ?>"><?= htmlspecialchars($categorie['nom']); ?></option>
                <?php endforeach; ?>
            </select>
            <input type="number" name="prix" placeholder="Prix" required>
            <input type="number" name="quantite" placeholder="Quantite" required>
            <input type="text" name="description" placeholder="Description" required>
            <button type="submit" name="ajouter">Ajouter le produit</button>
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>Nom</th>
                <th>Categorie</th>
                <th>Prix</th>
                <th>Quantite</th>
                <th>Description</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($produits as $produit): ?>
                <tr>
                    <td><?php echo htmlspecialchars($produit['nom']); ?></td>
                    <td><?php echo htmlspecialchars($produit['categorie']); ?></td>
                    <td><?php echo htmlspecialchars($produit['prix']); ?></td>
                    <td><?php echo htmlspecialchars($produit['quantite']); ?></td>
                    <td><?php echo htmlspecialchars($produit['description']); ?></td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="produit_id" value="<?= $produit['id']; ?>">
                            <button type="submit" name="supprimer" onclick="toggleForm('supprimerForm<?= $produit['id']; ?>'); return false;">Supprimer</button>
                        </form>
                        <button onclick="toggleForm('modifierForm<?= $produit['id']; ?>');">Modifier</button>
                    </td>
                </tr>
                <tr id="modifierForm<?= $produit['id']; ?>" class="modifierForm" style="display:none;">
                    <td colspan="6">
                        <form method="POST">
                            <input type="hidden" name="produit_id" value="<?= $produit['id']; ?>">
                            <input type="text" name="nom" value="<?= htmlspecialchars($produit['nom']); ?>" required>
                            <select name="categorie" required>
                                <option value="">Sélectionnez une catégorie</option>
                                <?php foreach ($categories as $categorie): ?>
                                    <option value="<?= htmlspecialchars($categorie['id']); ?>" <?= $categorie['id'] == $produit['categorie'] ? 'selected' : ''; ?>><?= htmlspecialchars($categorie['nom']); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <input type="number" name="prix" value="<?= htmlspecialchars($produit['prix']); ?>" required>
                            <input type="number" name="quantite" value="<?= htmlspecialchars($produit['quantite']); ?>" required>
                            <input type="text" name="description" value="<?= htmlspecialchars($produit['description']); ?>" required>
                            <button type="submit" name="modifier">Modifier le produit</button>
                        </form>
                    </td>
                </tr>
                <tr id="supprimerForm<?= $produit['id']; ?>" class="supprimerForm" style="display:none;">
                    <td colspan="6">
                        <form method="POST">
                            <input type="hidden" name="produit_id" value="<?= $produit['id']; ?>">
                            <p>etes-vous sur de vouloir supprimer ce produit ?</p>
                            <button type="submit" name="supprimer">Oui, supprimer</button>
                            <button type="button" onclick="toggleForm('supprimerForm<?= $produit['id']; ?>');">Annuler</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>