<?php
require_once __DIR__ . '/../app/recipes.php';
$userId = require_login();
require_post();
$recipe = owned_recipe($userId, $_POST['id'] ?? '');
$status = input($_POST, 'status');
if (!in_array($status, STATUSES, true)) {
    http_response_code(422);
    exit('Choose a valid cooking status.');
}
$statement = db()->prepare('UPDATE recipes SET status = ? WHERE id = ? AND user_id = ?');
$statement->execute([$status, $recipe['id'], $userId]);
flash('Cooking status updated.');
redirect('view.php?id=' . $recipe['id']);
