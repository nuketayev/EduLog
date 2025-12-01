<?php
// login.php
require 'lib/common.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    $users = get_users();
    $found_user = null;

    // Search for user
    foreach ($users as $u) {
        if ($u['email'] === $email) {
            $found_user = $u;
            break;
        }
    }

    // Verify Password
    if ($found_user && password_verify($password, $found_user['password'])) {
        $_SESSION['user_id'] = $found_user['id'];
        $_SESSION['role'] = $found_user['role'];
        header("Location: index.php");
        exit();
    } else {
        set_flash('error', 'Nesprávný email nebo heslo.');
    }
}

include 'templates/header.php';
?>

<div class="card" style="max-width: 400px; margin: 0 auto;">
    <h2 style="text-align:center">Přihlášení</h2>
    
    <form method="post">
        <div class="form-group">
            <label>Email:</label>
            <input type="email" name="email" required value="<?= h($email ?? '') ?>">
        </div>
        <div class="form-group">
            <label>Heslo:</label>
            <input type="password" name="password" required>
        </div>
        
        <button type="submit" class="btn btn-primary" style="width:100%">Přihlásit se</button>
    </form>
    
    <div style="text-align:center; margin-top:15px;">
        <p>Nemáte účet? <a href="register.php">Registrujte se zde</a>.</p>
    </div>
</div>

<?php include 'templates/footer.php'; ?>