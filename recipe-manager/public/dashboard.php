<?php
require_once __DIR__ . '/../app/recipes.php';
$userId = require_login();
$filters = [
    'q' => input($_GET, 'q'),
    'category' => input($_GET, 'category'),
    'difficulty' => input($_GET, 'difficulty'),
    'status' => input($_GET, 'status'),
];
$sql = 'SELECT * FROM recipes WHERE user_id = ?';
$values = [$userId];
// This also works when JavaScript is disabled.
if ($filters['q'] !== '') {
    $sql .= ' AND (LOCATE(?, name) > 0 OR LOCATE(?, ingredients) > 0)';
    $values[] = $filters['q'];
    $values[] = $filters['q'];
}
$choices = ['category' => CATEGORIES, 'difficulty' => DIFFICULTIES, 'status' => STATUSES];
foreach ($choices as $field => $options) {
    if (in_array($filters[$field], $options, true)) {
        // The column name comes from our fixed list, not from user input.
        $sql .= " AND $field = ?";
        $values[] = $filters[$field];
    } else {
        $filters[$field] = '';
    }
}
$statement = db()->prepare($sql . ' ORDER BY created_at DESC, id DESC');
$statement->execute($values);
$recipes = $statement->fetchAll();
$title = 'My recipes';
require __DIR__ . '/../templates/header.php';
?>
<div class="page-heading">
    <div><p class="eyebrow">Hello, <?= e($_SESSION['name']) ?></p><h1>Your recipe collection</h1></div>
    <a class="button" href="add.php">Add a recipe</a>
</div>
<form method="get" action="dashboard.php" id="filters" class="panel filters">
    <div>
        <label for="q">Search recipes or ingredients</label>
        <input id="q" name="q" type="search" value="<?= e($filters['q']) ?>" maxlength="150" placeholder="Try rice, momo or pasta">
    </div>
    <?php foreach ($choices as $field => $options): ?>
        <div>
            <label for="<?= e($field) ?>"><?= e(ucfirst($field)) ?></label>
            <select id="<?= e($field) ?>" name="<?= e($field) ?>">
                <option value="">All</option>
                <?php foreach ($options as $option): ?>
                    <option value="<?= e($option) ?>" <?= $filters[$field] === $option ? 'selected' : '' ?>><?= e($option) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    <?php endforeach; ?>
    <div class="actions"><button type="submit">Apply</button><a href="dashboard.php">Reset</a></div>
</form>
<p id="result-count" role="status" aria-live="polite"><?= count($recipes) ?> recipe(s) found.</p>
<section id="recipe-list" class="cards" aria-label="Your recipes">
    <?php foreach ($recipes as $recipe): ?>
        <article class="card">
            <p class="eyebrow"><?= e($recipe['category']) ?></p>
            <h2><a href="view.php?id=<?= (int) $recipe['id'] ?>"><?= e($recipe['name']) ?></a></h2>
            <p><?= e($recipe['difficulty']) ?> &middot; <?= (int) $recipe['preparation_time'] ?> minutes</p>
            <p class="badge"><?= e($recipe['status']) ?></p>
            <div class="actions">
                <a href="view.php?id=<?= (int) $recipe['id'] ?>">View recipe</a>
                <a href="edit.php?id=<?= (int) $recipe['id'] ?>">Edit</a>
            </div>
        </article>
    <?php endforeach; ?>
</section>
<p id="empty-state" class="panel" <?= $recipes ? 'hidden' : '' ?>>No recipes match. Try clearing your filters or add your first recipe.</p>
<?php require __DIR__ . '/../templates/footer.php'; ?>
