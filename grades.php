<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }
require_once __DIR__ . '/config/database.php';
function e($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'delete') {
        $st = $pdo->prepare('DELETE FROM grades WHERE id=?'); $st->execute([(int)($_POST['id'] ?? 0)]);
        header('Location: grades.php'); exit;
    }
    $studentId = (int)($_POST['student_id'] ?? 0);
    $subjectId = (int)($_POST['subject_id'] ?? 0);
    $grade = filter_var($_POST['grade'] ?? '', FILTER_VALIDATE_FLOAT);
    $date = $_POST['assessment_date'] ?? '';
    if ($studentId < 1 || $subjectId < 1 || $grade === false || $grade < 0 || $grade > 100 || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
        $error = 'Студентті, пәнді, 0–100 аралығындағы бағаны және дұрыс күнді енгізіңіз.';
    } else {
        try {
            if ($action === 'update') {
                $st = $pdo->prepare('UPDATE grades SET student_id=?, subject_id=?, grade=?, assessment_date=? WHERE id=?');
                $st->execute([$studentId, $subjectId, $grade, $date, (int)$_POST['id']]);
            } else {
                $st = $pdo->prepare('INSERT INTO grades (student_id, subject_id, grade, assessment_date) VALUES (?, ?, ?, ?)');
                $st->execute([$studentId, $subjectId, $grade, $date]);
            }
            header('Location: grades.php'); exit;
        } catch (PDOException $ex) { $error = 'Бағаны сақтау кезінде қате пайда болды.'; }
    }
}
$edit = null;
if (isset($_GET['edit'])) { $st=$pdo->prepare('SELECT * FROM grades WHERE id=?'); $st->execute([(int)$_GET['edit']]); $edit=$st->fetch() ?: null; }
$students = $pdo->query('SELECT id, full_name, student_number FROM students ORDER BY full_name')->fetchAll();
$subjects = $pdo->query('SELECT id, subject_name FROM subjects ORDER BY subject_name')->fetchAll();
$q = trim($_GET['q'] ?? '');
$sql = 'SELECT g.*, s.full_name, s.student_number, sub.subject_name FROM grades g JOIN students s ON s.id=g.student_id JOIN subjects sub ON sub.id=g.subject_id WHERE s.full_name LIKE ? OR sub.subject_name LIKE ? ORDER BY g.assessment_date DESC, s.full_name';
$st=$pdo->prepare($sql); $like="%{$q}%"; $st->execute([$like,$like]); $grades=$st->fetchAll();
?>
<!doctype html><html lang="kk"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Бағалар</title><link rel="stylesheet" href="assets/css/style.css"></head><body>
<header class="topbar"><a class="brand" href="dashboard.php">📘 StudentTrack</a><nav><a href="dashboard.php">Басқару панелі</a><a href="logout.php">Шығу</a></nav></header>
<main class="page"><div class="page-heading"><div><span class="eyebrow">ҮЛГЕРІМ</span><h1>Бағалар</h1><p class="muted">Студенттердің пәндер бойынша бағаларын тіркеңіз.</p></div></div><?php if ($error): ?><div class="alert"><?= e($error) ?></div><?php endif; ?>
<div class="content-grid"><form class="form-card compact" method="post"><h2><?= $edit ? 'Бағаны өңдеу' : 'Баға қосу' ?></h2><input type="hidden" name="action" value="<?= $edit ? 'update' : 'create' ?>"><?php if ($edit): ?><input type="hidden" name="id" value="<?= (int)$edit['id'] ?>"><?php endif; ?>
<label>Студент<select name="student_id" required><option value="">Студентті таңдаңыз</option><?php foreach ($students as $s): ?><option value="<?= (int)$s['id'] ?>" <?= (int)($edit['student_id'] ?? 0)===(int)$s['id']?'selected':'' ?>><?= e($s['full_name']) ?> (<?= e($s['student_number']) ?>)</option><?php endforeach; ?></select></label>
<label>Пән<select name="subject_id" required><option value="">Пәнді таңдаңыз</option><?php foreach ($subjects as $s): ?><option value="<?= (int)$s['id'] ?>" <?= (int)($edit['subject_id'] ?? 0)===(int)$s['id']?'selected':'' ?>><?= e($s['subject_name']) ?></option><?php endforeach; ?></select></label>
<label>Баға (0–100)<input type="number" name="grade" min="0" max="100" step="0.01" required value="<?= e($edit['grade'] ?? '') ?>"></label><label>Бағалау күні<input type="date" name="assessment_date" required value="<?= e($edit['assessment_date'] ?? date('Y-m-d')) ?>"></label>
<button class="button full" type="submit"><?= $edit ? 'Сақтау' : 'Баға қосу' ?></button><?php if ($edit): ?><a class="cancel-link" href="grades.php">Болдырмау</a><?php endif; ?></form>
<section class="table-card"><form class="search" method="get"><input name="q" placeholder="Студент немесе пән бойынша іздеу" value="<?= e($q) ?>"><button class="button" type="submit">Іздеу</button></form><div class="table-scroll"><table><thead><tr><th>Студент</th><th>Пән</th><th>Баға</th><th>Күні</th><th>Әрекет</th></tr></thead><tbody><?php foreach ($grades as $g): ?><tr><td><?= e($g['full_name']) ?></td><td><?= e($g['subject_name']) ?></td><td><span class="grade-pill"><?= e($g['grade']) ?></span></td><td><?= e($g['assessment_date']) ?></td><td class="row-actions"><a href="grades.php?edit=<?= (int)$g['id'] ?>">Өңдеу</a><form method="post" onsubmit="return confirm('Бағаны жоясыз ба?')"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int)$g['id'] ?>"><button class="text-danger" type="submit">Жою</button></form></td></tr><?php endforeach; ?><?php if (!$grades): ?><tr><td colspan="5" class="empty">Бағалар табылмады.</td></tr><?php endif; ?></tbody></table></div></section></div></main></body></html>
