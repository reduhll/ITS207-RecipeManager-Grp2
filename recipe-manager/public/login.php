<?php
require_once __DIR__ . '/../app/bootstrap.php';
if (!empty($_SESSION['user_id'])) {
    redirect('dashboard.php');
}
$errors = [];
$email = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $email = strtolower(input($_POST, 'email'));
    $password = is_string($_POST['password'] ?? null) ? $_POST['password'] : '';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '' || strlen($password) > 72) {
        $errors[] = 'Enter a valid email and password.';
    } else {
        $statement = db()->prepare('SELECT * FROM users WHERE email = ?');
        $statement->execute([$email]);
        $user = $statement->fetch();
        if ($user && password_verify($password, $user['password_hash'])) {
            // Replace the session ID after successful authentication.
            session_regenerate_id(true);
            $_SESSION = ['user_id' => (int) $user['id'], 'name' => $user['name']];
            redirect('dashboard.php');
        }
        $errors[] = 'The email or password is incorrect.';
    }
}
$title = 'Log in';
require __DIR__ . '/../templates/header.php';
?>
<section class="panel narrow">
    <h1>Welcome back</h1>
    <p>Log in to your recipe collection.</p>
    <?php require __DIR__ . '/../templates/errors.php'; ?>
    <form method="post">
        <?php csrf_field(); ?>
        <label for="email">Email</label>
        <input id="email" name="email" type="email" value="<?= e($email) ?>" maxlength="190" autocomplete="username" required>
        <label for="password">Password</label>
        <input id="password" name="password" type="password" maxlength="72" autocomplete="current-password" required>
        <button type="submit">Log in</button>
    </form>
    <p>New here? <a href="register.php">Create an account</a>.</p>
</section>
<?php require __DIR__ . '/../templates/footer.php'; ?>
