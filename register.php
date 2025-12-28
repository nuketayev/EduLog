<?php
// registration page
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
    $confirm = $_POST['password_confirm'];
    
    $users = get_users();

    if ($password !== $confirm) {
        set_flash('error', 'Passwords do not match.');
    } else {
        $exists = false;
        foreach ($users as $u) {
            if ($u['email'] === $email) {
                $exists = true;
                break;
            }
        }

        if ($exists) {
            set_flash('error', 'Email already registered.');
        } else {
            $users[] = [
                'id' => get_next_id($users),
                'email' => $email,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'role' => 'user'
            ];
            save_users($users);
            
            set_flash('success', 'Registration successful. Please login.');
            header("Location: login.php");
            exit();
        }
    }
}

include 'templates/header.php';
?>

<div class="card card-narrow">
    <h2 class="text-center">Register</h2>
    <form method="post">
        <input type="hidden" name="csrf_token" value="<?= generate_csrf() ?>">
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" required value="<?= h($email) ?>">
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required minlength="5">
        </div>
        <div class="form-group">
            <label for="password_confirm">Confirm Password:</label>
            <input type="password" name="password_confirm" id="password_confirm" required minlength="5">
        </div>
        
        <button type="submit" class="btn btn-primary w-100">Create Account</button>
    </form>
    
    <div class="text-center mt-15">
        <p>Already have an account? <a href="login.php">Login here</a>.</p>
    </div>
</div>

<?php include 'templates/footer.php'; ?>