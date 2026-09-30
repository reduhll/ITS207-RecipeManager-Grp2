<?php
require_once __DIR__ . '/../app/recipes.php';
$userId = require_login();
$recipe = owned_recipe($userId, $_GET['id'] ?? '');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $statement = db()->prepare('DELETE FROM recipes WHERE id = ? AND user_id = ?');
    $statement->execute([$recipe['id'], $userId]);
    flash('Recipe deleted.');
    redirect('dashboard.php');
}
$title = 'Delete recipe';
require __DIR__ . '/../templates/header.php';
?>
<section class="panel narrow">
    <h1>Delete this recipe?</h1>
    <p>You are about to delete <strong><?= e($recipe['name']) ?></strong>. This cannot be undone.</p>
    <form method="post">
        <?php csrf_field(); ?>
        <div class="actions">
            <button class="danger" type="submit">Yes, delete recipe</button>
            <a href="view.php?id=<?= (int) $recipe['id'] ?>">Cancel</a>
        </div>
    </form>
</section>
<?php require __DIR__ . '/../templates/footer.php'; ?>
