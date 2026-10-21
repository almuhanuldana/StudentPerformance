<?php
session_start();
?>
<!doctype html>
<html lang="kk">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Студент үлгерімі жүйесі</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<header class="topbar">
    <a class="brand" href="index.php">📘 StudentTrack</a>
    <nav><a href="login.php">Кіру</a><a class="nav-button" href="register.php">Тіркелу</a></nav>
</header>
<main class="hero">
    <section class="hero-card">
        <span class="eyebrow">ОҚУ ҮЛГЕРІМІН БАСҚАРУ</span>
        <h1>Студенттердің үлгерімін есепке алу жүйесі</h1>
        <p>Студенттерді, пәндерді және бағаларды бір жерде ыңғайлы басқаруға арналған веб-жоба.</p>
        <div class="actions"><a class="button" href="register.php">Жұмысты бастау</a><a class="button secondary" href="login.php">Жүйеге кіру</a></div>
    </section>
    <section class="feature-grid">
        <article class="feature"><div class="icon">👩‍🎓</div><h3>Студенттер</h3><p>Студенттер туралы ақпаратты сақтау және басқару.</p></article>
        <article class="feature"><div class="icon">📚</div><h3>Пәндер</h3><p>Пәндер тізімін бір жерден жүргізу.</p></article>
        <article class="feature"><div class="icon">📊</div><h3>Бағалар</h3><p>Бағаларды тіркеп, үлгерімді бақылау.</p></article>
    </section>
</main>
<footer>© <?= date('Y') ?> StudentTrack · Оқу жобасы</footer>
</body>
</html>
