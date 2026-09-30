<?php require __DIR__ . '/errors.php'; ?>
<form method="post" class="recipe-form">
    <?php csrf_field(); ?>
    <label for="name">Recipe name</label>
    <input id="name" name="name" maxlength="150" value="<?= e($recipe['name']) ?>" required>
    <div class="form-grid">
        <?php foreach (['category' => CATEGORIES, 'difficulty' => DIFFICULTIES, 'status' => STATUSES] as $field => $options): ?>
            <div>
                <label for="<?= e($field) ?>"><?= e(ucfirst($field)) ?></label>
                <select id="<?= e($field) ?>" name="<?= e($field) ?>" required>
                    <?php foreach ($options as $option): ?>
                        <option value="<?= e($option) ?>" <?= $recipe[$field] === $option ? 'selected' : '' ?>><?= e($option) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        <?php endforeach; ?>
        <div>
            <label for="preparation_time">Preparation time (minutes)</label>
            <input id="preparation_time" name="preparation_time" type="number" min="1" max="1440" step="1" value="<?= e($recipe['preparation_time']) ?>" required>
        </div>
    </div>
    <label for="ingredients">Ingredients</label>
    <small id="ingredients-help">Write one ingredient and its quantity on each line.</small>
    <textarea id="ingredients" name="ingredients" rows="7" maxlength="10000" aria-describedby="ingredients-help" required><?= e($recipe['ingredients']) ?></textarea>
    <label for="instructions">Instructions</label>
    <small id="instructions-help">Write the cooking steps in order.</small>
    <textarea id="instructions" name="instructions" rows="9" maxlength="10000" aria-describedby="instructions-help" required><?= e($recipe['instructions']) ?></textarea>
    <div class="actions">
        <button type="submit"><?= e($buttonText) ?></button>
        <a href="dashboard.php">Cancel</a>
    </div>
</form>
