<?php
require_once __DIR__ . '/../app/bootstrap.php';
if (!empty($_SESSION['user_id'])) {
    redirect('dashboard.php');
}
$title = 'Welcome';
require __DIR__ . '/../templates/header.php';
?>
<section class="hero">
    <p class="eyebrow">A place for the food you love</p>
    <h1>Keep a little taste of home.</h1>
    <p>Save family favourites, organise recipes by meal and find your next dish.
       Your collection stays with your account.</p>
    <div class="actions">
        <a class="button" href="register.php">Create an account</a>
        <a class="button secondary" href="login.php">Log in</a>
    </div>
</section>
<section class="cards" aria-label="What you can do">
    <article class="card"><h2>Save your recipes</h2><p>Keep ingredients and cooking instructions together.</p></article>
    <article class="card"><h2>Find a favourite</h2><p>Search your collection and filter by meal, difficulty or cooking status.</p></article>
    <article class="card"><h2>Make it your own</h2><p>Update recipes as you cook and mark the dishes you have tried.</p></article>
</section>
<?php require __DIR__ . '/../templates/footer.php'; ?>
