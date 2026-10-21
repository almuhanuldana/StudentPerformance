<?php
session_start();
require_once __DIR__ . '/config/database.php';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'] ?? '';
    if ($name === '' || !$email || strlen($password) < 8) {
        $error = 'Атыңызды, дұрыс email және кемінде 8 таңбалы құпиясөз енгізіңіз.';
    } else {
        try {
            $stmt = $pdo->prepare('INSERT INTO users (name, email, password) VALUES (?, ?, ?)');
            $stmt->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT)]);
            $_SESSION['user_id'] = (int)$pdo->lastInsertId();
            $_SESSION['user_name'] = $name;
            header('Location: dashboard.php');
            exit;
        } catch (PDOException $e) {
            $error = 'Бұл email тіркелген болуы мүмкін. Басқа email қолданып көріңіз.';
        }
    }
}
?>
<!doctype html>
<html lang="kk"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Тіркелу</title><link rel="stylesheet" href="assets/css/style.css"></head>
<body><header class="topbar"><a class="brand" href="index.php">📘 StudentTrack</a><nav><a href="login.php">Кіру</a></nav></header>
<main class="form-wrap"><form class="form-card" method="post">
<h1>Тіркелу</h1><p class="muted">Жаңа аккаунт жасаңыз.</p>
<?php if ($error): ?><div class="alert"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
<label>Аты-жөні<input name="name" required maxlength="100" value="<?= htmlspecialchars($_POST['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"></label>
<label>Email<input type="email" name="email" required maxlength="150" value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"></label>
<label>Құпиясөз<input type="password" name="password" required minlength="8"></label>
<button class="button full" type="submit">Тіркелу</button><p class="muted center">Аккаунтыңыз бар ма? <a href="login.php">Кіру</a></p>
</form></main></body></html>
