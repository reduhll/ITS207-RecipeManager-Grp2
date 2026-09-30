<?php
require_once __DIR__ . '/../app/bootstrap.php';
if (!empty($_SESSION['user_id'])) {
    redirect('dashboard.php');
}
$errors = [];
$name = $email = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $name = input($_POST, 'name');
    $email = strtolower(input($_POST, 'email'));
    // Do not trim passwords: spaces may be intentional.
    $password = is_string($_POST['password'] ?? null) ? $_POST['password'] : '';
    $confirmation = is_string($_POST['confirmation'] ?? null) ? $_POST['confirmation'] : '';
    if ($name === '' || text_length($name) > 100) {
        $errors[] = 'Enter a name of up to 100 characters.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 190) {
        $errors[] = 'Enter a valid email address of up to 190 characters.';
    }
    if (text_length($password) < 8 || strlen($password) > 72 || strpos($password, "\0") !== false) {
        $errors[] = 'Use at least 8 characters. Keep longer passwords within 72 English letters, numbers or punctuation marks.';
    }
    if ($password !== $confirmation) {
        $errors[] = 'The passwords do not match.';
    }
    if (!$errors) {
        try {
            $statement = db()->prepare('INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)');
            $statement->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT)]);
            flash('Account created. You can now log in.');
            redirect('login.php');
        } catch (PDOException $error) {
            if ((int) ($error->errorInfo[1] ?? 0) === 1062) {
                $errors[] = 'That email address is already registered.';
            } else {
                throw $error;
            }
        }
    }
}
$title = 'Create an account';
require __DIR__ . '/../templates/header.php';
?>
<section class="panel narrow">
    <h1>Create an account</h1>
    <p>Start your own recipe collection.</p>
    <?php require __DIR__ . '/../templates/errors.php'; ?>
    <form method="post">
        <?php csrf_field(); ?>
        <label for="name">Your name</label>
        <input id="name" name="name" value="<?= e($name) ?>" maxlength="100" autocomplete="name" required>
        <label for="email">Email</label>
        <input id="email" name="email" type="email" value="<?= e($email) ?>" maxlength="190" autocomplete="email" required>
        <label for="password">Password</label>
        <input id="password" name="password" type="password" minlength="8" maxlength="72" autocomplete="new-password" aria-describedby="password-help" required>
        <small id="password-help">Use 8–72 characters. Some symbols count as more than one toward the maximum.</small>
        <label for="confirmation">Confirm password</label>
        <input id="confirmation" name="confirmation" type="password" minlength="8" maxlength="72" autocomplete="new-password" required>
        <button type="submit">Register</button>
    </form>
    <p>Already registered? <a href="login.php">Log in</a>.</p>
</section>
<?php require __DIR__ . '/../templates/footer.php'; ?>
