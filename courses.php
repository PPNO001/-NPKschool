<?php

require_once __DIR__ . '/includes/auth.php';
require_login();

$pageTitle = 'จัดการรายวิชา';
$activePage = 'courses';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        $statement = $pdo->prepare('INSERT INTO courses (code, name, teacher_name) VALUES (?, ?, ?)');
        $statement->execute([
            trim($_POST['code'] ?? ''),
            trim($_POST['name'] ?? ''),
            trim($_POST['teacher_name'] ?? ''),
        ]);
        $message = 'เพิ่มรายวิชาเรียบร้อยแล้ว';
    }

    if ($action === 'delete') {
        $statement = $pdo->prepare('DELETE FROM courses WHERE id = ?');
        $statement->execute([(int) ($_POST['id'] ?? 0)]);
        $message = 'ลบรายวิชาเรียบร้อยแล้ว';
    }
}

$courses = $pdo->query('SELECT * FROM courses ORDER BY code')->fetchAll();

require __DIR__ . '/includes/header.php';
?>
<?php if ($message): ?><div class="notice"><?= h($message) ?></div><?php endif; ?>

<section class="grid content-grid">
  <article class="card">
    <h2>เพิ่มรายวิชา</h2>
    <form class="form-stack" method="post">
      <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
      <input type="hidden" name="action" value="create">
      <label>รหัสวิชา <input name="code" required></label>
      <label>ชื่อวิชา <input name="name" required></label>
      <label>ครูผู้สอน <input name="teacher_name" required></label>
      <button class="button" type="submit">บันทึกรายวิชา</button>
    </form>
  </article>

  <article class="card">
    <h2>รายการรายวิชา</h2>
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>รหัส</th>
            <th>ชื่อวิชา</th>
            <th>ครูผู้สอน</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($courses as $course): ?>
            <tr>
              <td><?= h($course['code']) ?></td>
              <td><?= h($course['name']) ?></td>
              <td><?= h($course['teacher_name']) ?></td>
              <td>
                <form method="post" onsubmit="return confirm('ยืนยันการลบรายวิชา?')">
                  <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="id" value="<?= (int) $course['id'] ?>">
                  <button class="button danger" type="submit">ลบ</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </article>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
