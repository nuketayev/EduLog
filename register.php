<?php
// register.php
require 'lib/common.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm = $_POST['password_confirm']; // confirm password
    
    $users = get_users();

    // 1. Validation
    if ($password !== $confirm) {
        set_flash('error', 'Hesla se neshodují.');
    } else {
        // 2. Check duplicate email
        $exists = false;
        foreach ($users as $u) {
            if ($u['email'] === $email) {
                $exists = true;
                break;
            }
        }

        if ($exists) {
            set_flash('error', 'Tento email je již registrován.');
        } else {
            // 3. Create User
            $users[] = [
                'id' => get_next_id($users),
                'email' => $email,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'role' => 'user'
            ];
            save_users($users);
            
            set_flash('success', 'Registrace úspěšná. Nyní se můžete přihlásit.');
            header("Location: login.php");
            exit();
        }
    }
}

include 'templates/header.php';
?>

<div class="card" style="max-width: 400px; margin: 0 auto;">
    <h2 style="text-align:center">Registrace</h2>
    <form method="post">
        <div class="form-group">
            <label>Email:</label>
            <input type="email" name="email" required value="<?= h($email ?? '') ?>">
        </div>
        <div class="form-group">
            <label>Heslo:</label>
            <input type="password" name="password" required minlength="5">
        </div>
        <div class="form-group">
            <label>Potvrzení hesla:</label>
            <input type="password" name="password_confirm" required minlength="5">
        </div>
        
        <button type="submit" class="btn btn-primary" style="width:100%">Vytvořit účet</button>
    </form>
    
    <div style="text-align:center; margin-top:15px;">
        <p>Již máte účet? <a href="login.php">Přihlaste se zde</a>.</p>
    </div>
</div>

<?php include 'templates/footer.php'; ?>