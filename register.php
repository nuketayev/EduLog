<?php
/**
 * Registration Page.
 * Create new account.
 */

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
        set_flash('error', 'Hesla se neshodují.');
    } else {
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

<div class="card card-narrow">
    <h2 class="text-center">Registrace</h2>
    <form method="post">
        <input type="hidden" name="csrf_token" value="<?= generate_csrf() ?>">
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" required value="<?= h($email) ?>">
        </div>
        <div class="form-group">
            <label for="password">Heslo:</label>
            <input type="password" name="password" id="password" required minlength="5">
        </div>
        <div class="form-group">
            <label for="password_confirm">Potvrzení hesla:</label>
            <input type="password" name="password_confirm" id="password_confirm" required minlength="5">
        </div>
        
        <button type="submit" class="btn btn-primary w-100">Vytvořit účet</button>
    </form>
    
    <div class="text-center mt-15">
        <p>Již máte účet? <a href="login.php">Přihlaste se zde</a>.</p>
    </div>
</div>

<?php include 'templates/footer.php'; ?>