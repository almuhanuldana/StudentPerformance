<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }
require_once __DIR__ . '/config/database.php';
function e($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'delete') {
        $stmt = $pdo->prepare('DELETE FROM subjects WHERE id=?');
        try { $stmt->execute([(int)($_POST['id'] ?? 0)]); header('Location: subjects.php'); exit; }
        catch (PDOException $ex) { $error = 'Бұл пәнге баға тіркелген. Алдымен бағаларын жойыңыз.'; }
    } else {
        $name = trim($_POST['subject_name'] ?? '');
        $code = trim($_POST['subject_code'] ?? '');
        if ($name === '') $error = 'Пән атауын енгізіңіз.';
        else {
            try {
                if ($action === 'update') {
                    $stmt = $pdo->prepare('UPDATE subjects SET subject_name=?, subject_code=? WHERE id=?');
                    $stmt->execute([$name, $code ?: null, (int)$_POST['id']]);
                } else {
                    $stmt = $pdo->prepare('INSERT INTO subjects (subject_name, subject_code) VALUES (?, ?)');
                    $stmt->execute([$name, $code ?: null]);
                }
                header('Location: subjects.php'); exit;
            } catch (PDOException $ex) { $error = 'Пән коды қайталануы мүмкін.'; }
        }
    }
}
$edit = null;
if (isset($_GET['edit'])) { $st=$pdo->prepare('SELECT * FROM subjects WHERE id=?'); $st->execute([(int)$_GET['edit']]); $edit=$st->fetch() ?: null; }
$subjects = $pdo->query('SELECT s.*, (SELECT COUNT(*) FROM grades g WHERE g.subject_id=s.id) AS grade_count FROM subjects s ORDER BY subject_name')->fetchAll();
?>
<!doctype html><html lang="kk"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Пәндер</title><link rel="stylesheet" href="assets/css/style.css"></head><body>
<header class="topbar"><a class="brand" href="dashboard.php">📘 StudentTrack</a><nav><a href="dashboard.php">Басқару панелі</a><a href="logout.php">Шығу</a></nav></header>
<main class="page"><div class="page-heading"><div><span class="eyebrow">ДЕРЕКТЕР</span><h1>Пәндер</h1><p class="muted">Оқу пәндерін басқарыңыз.</p></div></div><?php if ($error): ?><div class="alert"><?= e($error) ?></div><?php endif; ?>
<div class="content-grid"><form class="form-card compact" method="post"><h2><?= $edit ? 'Пәнді өңдеу' : 'Жаңа пән қосу' ?></h2><input type="hidden" name="action" value="<?= $edit ? 'update' : 'create' ?>"><?php if ($edit): ?><input type="hidden" name="id" value="<?= (int)$edit['id'] ?>"><?php endif; ?>
<label>Пән атауы<input name="subject_name" required maxlength="100" value="<?= e($edit['subject_name'] ?? '') ?>"></label><label>Пән коды<input name="subject_code" maxlength="30" value="<?= e($edit['subject_code'] ?? '') ?>"></label><button class="button full" type="submit"><?= $edit ? 'Сақтау' : 'Пән қосу' ?></button><?php if ($edit): ?><a class="cancel-link" href="subjects.php">Болдырмау</a><?php endif; ?></form>
<section class="table-card"><div class="table-scroll"><table><thead><tr><th>Пән атауы</th><th>Коды</th><th>Бағалар саны</th><th>Әрекет</th></tr></thead><tbody><?php foreach ($subjects as $s): ?><tr><td><?= e($s['subject_name']) ?></td><td><?= e($s['subject_code'] ?? '—') ?></td><td><?= (int)$s['grade_count'] ?></td><td class="row-actions"><a href="subjects.php?edit=<?= (int)$s['id'] ?>">Өңдеу</a><form method="post" onsubmit="return confirm('Пәнді жоясыз ба?')"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int)$s['id'] ?>"><button class="text-danger" type="submit">Жою</button></form></td></tr><?php endforeach; ?><?php if (!$subjects): ?><tr><td colspan="4" class="empty">Әзірге пән жоқ.</td></tr><?php endif; ?></tbody></table></div></section></div></main></body></html>
