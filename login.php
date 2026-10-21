<?php
session_start();
require_once __DIR__ . '/config/database.php';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'] ?? '';
    if (!$email || $password === '') {
        $error = 'Email мен құпиясөзді дұрыс енгізіңіз.';
    } else {
        $stmt = $pdo->prepare('SELECT id, name, password FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = (int)$user['id'];
            $_SESSION['user_name'] = $user['name'];
            header('Location: dashboard.php');
            exit;
        }
        $error = 'Email немесе құпиясөз қате.';
    }
}
?>
<!doctype html>
<html lang="kk"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Кіру</title><link rel="stylesheet" href="assets/css/style.css"></head>
<body><header class="topbar"><a class="brand" href="index.php">📘 StudentTrack</a><nav><a href="register.php">Тіркелу</a></nav></header>
<main class="form-wrap"><form class="form-card" method="post">
<h1>Жүйеге кіру</h1><p class="muted">Жалғастыру үшін аккаунтыңызға кіріңіз.</p>
<?php if ($error): ?><div class="alert"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
<label>Email<input type="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"></label>
<label>Құпиясөз<input type="password" name="password" required></label>
<button class="button full" type="submit">Кіру</button><p class="muted center">Аккаунтыңыз жоқ па? <a href="register.php">Тіркелу</a></p>
</form></main></body></html>
