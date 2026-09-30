<?php
require_once __DIR__ . '/../app/recipes.php';
$userId = require_login();
$recipe = owned_recipe($userId, $_GET['id'] ?? '');
$nextStatus = $recipe['status'] === 'Cooked' ? 'Not Cooked' : 'Cooked';
$title = $recipe['name'];
require __DIR__ . '/../templates/header.php';
?>
<article class="panel">
    <p class="eyebrow"><?= e($recipe['category']) ?> &middot; <?= e($recipe['difficulty']) ?></p>
    <h1><?= e($recipe['name']) ?></h1>
    <p>Preparation: <?= (int) $recipe['preparation_time'] ?> minutes</p>
    <p class="badge"><?= e($recipe['status']) ?></p>
    <div class="recipe-sections">
        <section><h2>Ingredients</h2><p class="multiline"><?= e($recipe['ingredients']) ?></p></section>
        <section><h2>Instructions</h2><p class="multiline"><?= e($recipe['instructions']) ?></p></section>
    </div>
    <div class="actions">
        <a class="button" href="edit.php?id=<?= (int) $recipe['id'] ?>">Edit recipe</a>
        <form action="status.php" method="post">
            <?php csrf_field(); ?>
            <input type="hidden" name="id" value="<?= (int) $recipe['id'] ?>">
            <input type="hidden" name="status" value="<?= e($nextStatus) ?>">
            <button class="secondary" type="submit">Mark as <?= e(strtolower($nextStatus)) ?></button>
        </form>
        <a class="danger-link" href="delete.php?id=<?= (int) $recipe['id'] ?>">Delete</a>
        <a href="dashboard.php">Back to recipes</a>
    </div>
</article>
<?php require __DIR__ . '/../templates/footer.php'; ?>
