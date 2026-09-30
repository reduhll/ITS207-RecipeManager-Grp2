<?php
require_once __DIR__ . '/bootstrap.php';

const CATEGORIES = ['Breakfast', 'Lunch', 'Dinner', 'Dessert'];
const DIFFICULTIES = ['Easy', 'Medium', 'Hard'];
const STATUSES = ['Not Cooked', 'Cooked'];

function recipe_data(array $source): array
{
    $data = [];
    foreach (['name', 'category', 'ingredients', 'instructions', 'difficulty', 'preparation_time', 'status'] as $field) {
        $data[$field] = input($source, $field);
    }
    return $data;
}

function recipe_errors(array $recipe): array
{
    $errors = [];
    if ($recipe['name'] === '' || text_length($recipe['name']) > 150) {
        $errors[] = 'Enter a recipe name of up to 150 characters.';
    }
    if (!in_array($recipe['category'], CATEGORIES, true)) {
        $errors[] = 'Choose a valid category.';
    }
    if (!in_array($recipe['difficulty'], DIFFICULTIES, true)) {
        $errors[] = 'Choose a valid difficulty.';
    }
    if (!in_array($recipe['status'], STATUSES, true)) {
        $errors[] = 'Choose a valid cooking status.';
    }
    foreach (['ingredients', 'instructions'] as $field) {
        if ($recipe[$field] === '' || text_length($recipe[$field]) > 10000) {
            $errors[] = 'Enter ' . $field . ' using no more than 10,000 characters.';
        }
    }
    if (filter_var($recipe['preparation_time'], FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 1, 'max_range' => 1440],
    ]) === false) {
        $errors[] = 'Preparation time must be a whole number from 1 to 1440 minutes.';
    }
    return $errors;
}

function owned_recipe(int $userId, $id): array
{
    if (!is_string($id) || !ctype_digit($id) || (int) $id < 1) {
        http_response_code(404);
        exit('Recipe not found.');
    }
    // Both conditions matter: knowing a recipe ID must not grant access.
    $statement = db()->prepare('SELECT * FROM recipes WHERE id = ? AND user_id = ?');
    $statement->execute([$id, $userId]);
    $recipe = $statement->fetch();
    if (!$recipe) {
        http_response_code(404);
        exit('Recipe not found.');
    }
    return $recipe;
}

function recipe_values(array $recipe): array
{
    return [$recipe['name'], $recipe['category'], $recipe['ingredients'],
        $recipe['instructions'], $recipe['difficulty'],
        $recipe['preparation_time'], $recipe['status']];
}
