<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }
require_once __DIR__ . '/config/database.php';
$studentsCount = (int)$pdo->query('SELECT COUNT(*) FROM students')->fetchColumn();
$subjectsCount = (int)$pdo->query('SELECT COUNT(*) FROM subjects')->fetchColumn();
$gradesCount = (int)$pdo->query('SELECT COUNT(*) FROM grades')->fetchColumn();
$average = $pdo->query('SELECT AVG(grade) FROM grades')->fetchColumn();
function e($value) { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); }
?>
<!doctype html><html lang="kk"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Басқару панелі</title><link rel="stylesheet" href="assets/css/style.css"></head>
<body><header class="topbar"><a class="brand" href="dashboard.php">📘 StudentTrack</a><nav><span class="welcome"><?= e($_SESSION['user_name'] ?? 'Пайдаланушы') ?></span><a href="logout.php">Шығу</a></nav></header>
<main class="page"><div class="page-heading"><div><span class="eyebrow">БАСҚАРУ ПАНЕЛІ</span><h1>Қош келдіңіз!</h1><p class="muted">Студенттердің оқу көрсеткіштеріне шолу.</p></div></div>
<section class="stats"><article class="stat"><span>Студенттер</span><strong><?= $studentsCount ?></strong></article><article class="stat"><span>Пәндер</span><strong><?= $subjectsCount ?></strong></article><article class="stat"><span>Бағалар</span><strong><?= $gradesCount ?></strong></article><article class="stat"><span>Орташа баға</span><strong><?= $average !== null ? number_format((float)$average, 1) : '—' ?></strong></article></section>
<section class="feature-grid dashboard-links"><a class="feature" href="students.php"><div class="icon">👩‍🎓</div><h3>Студенттерді басқару →</h3><p>Студенттерді қосу, іздеу және өңдеу.</p></a><a class="feature" href="subjects.php"><div class="icon">📚</div><h3>Пәндерді басқару →</h3><p>Пәндерді қосу және тізімді көру.</p></a><a class="feature" href="grades.php"><div class="icon">📊</div><h3>Бағаларды басқару →</h3><p>Бағаларды енгізу және үлгерімді бақылау.</p></a></section></main></body></html>
