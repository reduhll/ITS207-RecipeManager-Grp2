<?php
require_once __DIR__ . '/../app/recipes.php';
$userId = require_login();
$errors = [];
$recipe = ['name' => '', 'category' => 'Breakfast', 'ingredients' => '',
    'instructions' => '', 'difficulty' => 'Easy', 'preparation_time' => '', 'status' => 'Not Cooked'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $recipe = recipe_data($_POST);
    $errors = recipe_errors($recipe);
    if (!$errors) {
        $statement = db()->prepare('INSERT INTO recipes
            (name, category, ingredients, instructions, difficulty, preparation_time, status, user_id)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
        $statement->execute(array_merge(recipe_values($recipe), [$userId]));
        $id = db()->lastInsertId();
        flash('Recipe added.');
        redirect('view.php?id=' . $id);
    }
}
$title = 'Add recipe';
$buttonText = 'Save recipe';
require __DIR__ . '/../templates/header.php';
?>
<section class="panel"><h1>Add a recipe</h1>
    <?php require __DIR__ . '/../templates/recipe-form.php'; ?>
</section>
<?php require __DIR__ . '/../templates/footer.php'; ?>
