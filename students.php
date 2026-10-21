<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }
require_once __DIR__ . '/config/database.php';
function e($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'delete') {
        $stmt = $pdo->prepare('DELETE FROM students WHERE id = ?');
        $stmt->execute([(int)($_POST['id'] ?? 0)]);
        header('Location: students.php'); exit;
    }
    $name = trim($_POST['full_name'] ?? '');
    $number = trim($_POST['student_number'] ?? '');
    $group = trim($_POST['group_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    if ($name === '' || $number === '' || $group === '' || ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL))) {
        $error = 'Аты-жөні, студент нөмірі және топты толтырыңыз. Email дұрыс болуы керек.';
    } else {
        try {
            if ($action === 'update') {
                $stmt = $pdo->prepare('UPDATE students SET full_name=?, student_number=?, group_name=?, email=? WHERE id=?');
                $stmt->execute([$name, $number, $group, $email ?: null, (int)$_POST['id']]);
            } else {
                $stmt = $pdo->prepare('INSERT INTO students (full_name, student_number, group_name, email) VALUES (?, ?, ?, ?)');
                $stmt->execute([$name, $number, $group, $email ?: null]);
            }
            header('Location: students.php'); exit;
        } catch (PDOException $ex) { $error = 'Студент нөмірі қайталануы мүмкін. Мәліметтерді тексеріңіз.'; }
    }
}
$edit = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare('SELECT * FROM students WHERE id=?');
    $stmt->execute([(int)$_GET['edit']]);
    $edit = $stmt->fetch() ?: null;
}
$q = trim($_GET['q'] ?? '');
$stmt = $pdo->prepare('SELECT * FROM students WHERE full_name LIKE ? OR student_number LIKE ? OR group_name LIKE ? ORDER BY full_name');
$like = "%{$q}%"; $stmt->execute([$like, $like, $like]); $students = $stmt->fetchAll();
?>
<!doctype html><html lang="kk"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Студенттер</title><link rel="stylesheet" href="assets/css/style.css"></head><body>
<header class="topbar"><a class="brand" href="dashboard.php">📘 StudentTrack</a><nav><a href="dashboard.php">Басқару панелі</a><a href="logout.php">Шығу</a></nav></header>
<main class="page"><div class="page-heading"><div><span class="eyebrow">ДЕРЕКТЕР</span><h1>Студенттер</h1><p class="muted">Студенттерді қосыңыз, іздеңіз және өңдеңіз.</p></div></div>
<?php if ($error): ?><div class="alert"><?= e($error) ?></div><?php endif; ?>
<div class="content-grid"><form class="form-card compact" method="post"><h2><?= $edit ? 'Студентті өңдеу' : 'Жаңа студент қосу' ?></h2>
<input type="hidden" name="action" value="<?= $edit ? 'update' : 'create' ?>"><?php if ($edit): ?><input type="hidden" name="id" value="<?= (int)$edit['id'] ?>"><?php endif; ?>
<label>Аты-жөні<input name="full_name" required maxlength="150" value="<?= e($edit['full_name'] ?? '') ?>"></label>
<label>Студент нөмірі<input name="student_number" required maxlength="30" value="<?= e($edit['student_number'] ?? '') ?>"></label>
<label>Тобы<input name="group_name" required maxlength="50" value="<?= e($edit['group_name'] ?? '') ?>"></label>
<label>Email (міндетті емес)<input type="email" name="email" value="<?= e($edit['email'] ?? '') ?>"></label>
<button class="button full" type="submit"><?= $edit ? 'Сақтау' : 'Студент қосу' ?></button><?php if ($edit): ?><a class="cancel-link" href="students.php">Болдырмау</a><?php endif; ?></form>
<section class="table-card"><form class="search" method="get"><input name="q" placeholder="Аты, нөмірі немесе топ бойынша іздеу" value="<?= e($q) ?>"><button class="button" type="submit">Іздеу</button></form>
<div class="table-scroll"><table><thead><tr><th>Аты-жөні</th><th>Нөмірі</th><th>Тобы</th><th>Email</th><th>Әрекет</th></tr></thead><tbody>
<?php foreach ($students as $s): ?><tr><td><?= e($s['full_name']) ?></td><td><?= e($s['student_number']) ?></td><td><?= e($s['group_name']) ?></td><td><?= e($s['email'] ?? '') ?></td><td class="row-actions"><a href="students.php?edit=<?= (int)$s['id'] ?>">Өңдеу</a><form method="post" onsubmit="return confirm('Студентті жоясыз ба?')"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int)$s['id'] ?>"><button class="text-danger" type="submit">Жою</button></form></td></tr><?php endforeach; ?>
<?php if (!$students): ?><tr><td colspan="5" class="empty">Студенттер табылмады.</td></tr><?php endif; ?></tbody></table></div></section></div></main></body></html>
