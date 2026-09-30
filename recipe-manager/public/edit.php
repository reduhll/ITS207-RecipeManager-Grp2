<?php
require_once __DIR__ . '/../app/recipes.php';
$userId = require_login();
$savedRecipe = owned_recipe($userId, $_GET['id'] ?? '');
$recipe = $savedRecipe;
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $recipe = recipe_data($_POST);
    $errors = recipe_errors($recipe);
    if (!$errors) {
        $statement = db()->prepare('UPDATE recipes SET name = ?, category = ?, ingredients = ?,
            instructions = ?, difficulty = ?, preparation_time = ?, status = ? WHERE id = ? AND user_id = ?');
        $statement->execute(array_merge(recipe_values($recipe), [$savedRecipe['id'], $userId]));
        flash('Recipe updated.');
        redirect('view.php?id=' . $savedRecipe['id']);
    }
}
$title = 'Edit recipe';
$buttonText = 'Save changes';
require __DIR__ . '/../templates/header.php';
?>
<section class="panel"><h1>Edit recipe</h1>
    <?php require __DIR__ . '/../templates/recipe-form.php'; ?>
</section>
<?php require __DIR__ . '/../templates/footer.php'; ?>
