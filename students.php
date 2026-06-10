<?php

require_once __DIR__ . '/includes/auth.php';
require_login();

$pageTitle = 'จัดการนักเรียน';
$activePage = 'students';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        $statement = $pdo->prepare('
            INSERT INTO students (student_code, first_name, last_name, classroom, phone)
            VALUES (?, ?, ?, ?, ?)
        ');
        $statement->execute([
            trim($_POST['student_code'] ?? ''),
            trim($_POST['first_name'] ?? ''),
            trim($_POST['last_name'] ?? ''),
            trim($_POST['classroom'] ?? ''),
            trim($_POST['phone'] ?? ''),
        ]);
        $message = 'เพิ่มนักเรียนเรียบร้อยแล้ว';
    }

    if ($action === 'delete') {
        $statement = $pdo->prepare('DELETE FROM students WHERE id = ?');
        $statement->execute([(int) ($_POST['id'] ?? 0)]);
        $message = 'ลบนักเรียนเรียบร้อยแล้ว';
    }
}

$students = $pdo->query('SELECT * FROM students ORDER BY classroom, student_code')->fetchAll();

require __DIR__ . '/includes/header.php';
?>
<?php if ($message): ?><div class="notice"><?= h($message) ?></div><?php endif; ?>

<section class="grid content-grid">
  <article class="card">
    <h2>เพิ่มนักเรียน</h2>
    <form class="form-stack" method="post">
      <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
      <input type="hidden" name="action" value="create">
      <label>รหัสนักเรียน <input name="student_code" required></label>
      <label>ชื่อ <input name="first_name" required></label>
      <label>นามสกุล <input name="last_name" required></label>
      <label>ห้องเรียน <input name="classroom" placeholder="ม.6/1" required></label>
      <label>เบอร์โทร <input name="phone"></label>
      <button class="button" type="submit">บันทึกนักเรียน</button>
    </form>
  </article>

  <article class="card">
    <h2>รายชื่อนักเรียน</h2>
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>รหัส</th>
            <th>ชื่อ-นามสกุล</th>
            <th>ห้อง</th>
            <th>เบอร์โทร</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($students as $student): ?>
            <tr>
              <td><?= h($student['student_code']) ?></td>
              <td><?= h($student['first_name'] . ' ' . $student['last_name']) ?></td>
              <td><?= h($student['classroom']) ?></td>
              <td><?= h($student['phone']) ?></td>
              <td>
                <form method="post" onsubmit="return confirm('ยืนยันการลบนักเรียน?')">
                  <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="id" value="<?= (int) $student['id'] ?>">
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
