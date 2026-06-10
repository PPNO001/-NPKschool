<?php
$activePage = $activePage ?? '';
$pageTitle = $pageTitle ?? 'School Attendance';
?>
<!doctype html>
<html lang="th">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($pageTitle) ?> | School Management System</title>
    <link rel="stylesheet" href="assets/css/styles.css">
  </head>
  <body>
    <div class="app-shell">
      <aside class="sidebar">
        <div class="brand">
          <span>SMS</span>
          <strong>School System</strong>
        </div>
        <nav>
          <a class="<?= $activePage === 'dashboard' ? 'active' : '' ?>" href="index.php">Dashboard</a>
          <a class="<?= $activePage === 'attendance' ? 'active' : '' ?>" href="attendance.php">เช็คชื่อเข้าเรียน</a>
          <a class="<?= $activePage === 'reports' ? 'active' : '' ?>" href="reports.php">รายงาน</a>
          <a class="<?= $activePage === 'students' ? 'active' : '' ?>" href="students.php">นักเรียน</a>
          <a class="<?= $activePage === 'courses' ? 'active' : '' ?>" href="courses.php">รายวิชา</a>
        </nav>
      </aside>
      <main class="main-content">
        <header class="topbar">
          <div>
            <p class="eyebrow">ระบบเช็คชื่อเข้าเรียน</p>
            <h1><?= h($pageTitle) ?></h1>
          </div>
          <div class="user-menu">
            <span><?= h(current_user()['name'] ?? '') ?></span>
            <a href="logout.php">ออกจากระบบ</a>
          </div>
        </header>
