<?php
// login page
require 'lib/common.php';

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$email = ''; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    $users = get_users();
    $found_user = null;

    foreach ($users as $u) {
        if ($u['email'] === $email) {
            $found_user = $u;
            break;
        }
    }

    if ($found_user && password_verify($password, $found_user['password'])) {
        $_SESSION['user_id'] = $found_user['id'];
        $_SESSION['role'] = $found_user['role'];
        header("Location: index.php");
        exit();
    } else {
        set_flash('error', 'Wrong email or password.');
    }
}

include 'templates/header.php';
?>

<div class="card card-narrow">
    <h2 class="text-center">Login</h2>
    
    <form method="post">
        <input type="hidden" name="csrf_token" value="<?= generate_csrf() ?>">
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" required value="<?= h($email) ?>">
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required>
        </div>
        
        <button type="submit" class="btn btn-primary w-100">Sign In</button>
    </form>
    
    <div class="text-center mt-15">
        <p>No account? <a href="register.php">Register here</a>.</p>
    </div>
</div>

<?php include 'templates/footer.php'; ?>