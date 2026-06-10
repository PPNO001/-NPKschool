<?php

require_once __DIR__ . '/includes/auth.php';

if (current_user()) {
    redirect('index.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (login($email, $password)) {
        redirect('index.php');
    }

    $error = 'อีเมลหรือรหัสผ่านไม่ถูกต้อง';
}
?>
<!doctype html>
<html lang="th">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ | School Management System</title>
    <link rel="stylesheet" href="assets/css/styles.css">
  </head>
  <body>
    <main class="login-page">
      <section class="card login-card">
        <p class="eyebrow">School Management System</p>
        <h1>เข้าสู่ระบบ</h1>
        <p class="muted">ระบบเช็คชื่อเข้าเรียนด้วย PHP และ MySQL</p>

        <?php if ($error): ?>
          <div class="notice error"><?= h($error) ?></div>
        <?php endif; ?>

        <form class="form-stack" method="post">
          <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
          <label>
            อีเมล
            <input type="email" name="email" value="admin@example.com" required>
          </label>
          <label>
            รหัสผ่าน
            <input type="password" name="password" value="admin123" required>
          </label>
          <button class="button" type="submit">เข้าสู่ระบบ</button>
        </form>
      </section>
    </main>
  </body>
</html>
