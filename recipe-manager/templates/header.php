<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title ?? 'Recipe Manager') ?> | Recipe Manager</title>
    <link rel="stylesheet" href="assets/style.css">
    <script src="assets/app.js" defer></script>
</head>
<body>
<a class="skip-link" href="#main">Skip to content</a>
<header class="site-header">
    <a class="brand" href="index.php">Recipe Manager</a>
    <nav aria-label="Main navigation">
        <?php if (!empty($_SESSION['user_id'])): ?>
            <a href="dashboard.php">My recipes</a>
            <a href="add.php">Add recipe</a>
            <form action="logout.php" method="post">
                <?php csrf_field(); ?>
                <button class="secondary" type="submit">Log out</button>
            </form>
        <?php else: ?>
            <a href="login.php">Log in</a>
            <a href="register.php">Register</a>
        <?php endif; ?>
    </nav>
</header>
<main id="main" class="container">
    <?php if (isset($_SESSION['message'])): ?>
        <p class="notice" role="status"><?= e($_SESSION['message']) ?></p>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>
