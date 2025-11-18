<?php
require 'lib/common.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    $users = get_users();

    if ($action === 'register') {
        // 1. Check duplication
        foreach ($users as $u) {
            if ($u['email'] === $email) {
                set_flash('error', 'Tento email již existuje.');
                header("Location: login.php");
                exit();
            }
        }
        // 2. Create User
        $users[] = [
            'id' => get_next_id($users),
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'role' => 'user'
        ];
        save_users($users);
        set_flash('success', 'Registrace úspěšná. Přihlaste se.');
        header("Location: login.php");
        exit();

    } elseif ($action === 'login') {
        // 3. Login
        foreach ($users as $u) {
            if ($u['email'] === $email && password_verify($password, $u['password'])) {
                $_SESSION['user_id'] = $u['id'];
                $_SESSION['role'] = $u['role'];
                header("Location: index.php");
                exit();
            }
        }
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
            <input type="email" name="email" required>
        </div>
        <div class="form-group">
            <label>Heslo:</label>
            <input type="password" name="password" required>
        </div>
        <div style="display:flex; gap:10px; margin-top:20px;">
            <button type="submit" name="action" value="login" class="btn btn-primary" style="flex:1">Přihlásit</button>
            <button type="submit" name="action" value="register" class="btn" style="background:#6c757d; color:white; flex:1">Registrovat</button>
        </div>
    </form>
</div>

<?php include 'templates/footer.php'; ?>