<?php

require_once __DIR__ . '/includes/auth.php';
require_login();

$pageTitle = 'เช็คชื่อเข้าเรียน';
$activePage = 'attendance';
$message = '';
$selectedCourse = (int) ($_GET['course_id'] ?? $_POST['course_id'] ?? 0);
$selectedDate = $_GET['attendance_date'] ?? $_POST['attendance_date'] ?? date('Y-m-d');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'save_attendance') {
    verify_csrf();

    $selectedCourse = (int) $_POST['course_id'];
    $selectedDate = $_POST['attendance_date'];
    $statuses = $_POST['status'] ?? [];
    $notes = $_POST['note'] ?? [];

    $statement = $pdo->prepare('
        INSERT INTO attendance (student_id, course_id, attendance_date, status, note, recorded_by)
        VALUES (?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE status = VALUES(status), note = VALUES(note), recorded_by = VALUES(recorded_by)
    ');

    foreach ($statuses as $studentId => $status) {
        $statement->execute([
            (int) $studentId,
            $selectedCourse,
            $selectedDate,
            $status,
            trim($notes[$studentId] ?? ''),
            current_user()['id'],
        ]);
    }

    $message = 'บันทึกการเช็คชื่อเรียบร้อยแล้ว';
}

$courses = $pdo->query('SELECT id, code, name FROM courses ORDER BY code')->fetchAll();
$students = $pdo->query('SELECT * FROM students ORDER BY classroom, student_code')->fetchAll();
$existing = [];

if ($selectedCourse > 0) {
    $statement = $pdo->prepare('
        SELECT student_id, status, note
        FROM attendance
        WHERE course_id = ? AND attendance_date = ?
    ');
    $statement->execute([$selectedCourse, $selectedDate]);
    foreach ($statement->fetchAll() as $row) {
        $existing[(int) $row['student_id']] = $row;
    }
}

require __DIR__ . '/includes/header.php';
?>
<?php if ($message): ?><div class="notice"><?= h($message) ?></div><?php endif; ?>

<section class="card">
  <h2>เลือกวันที่และรายวิชา</h2>
  <form class="form-stack" method="get">
    <label>
      วันที่
      <input type="date" name="attendance_date" value="<?= h($selectedDate) ?>" required>
    </label>
    <label>
      รายวิชา
      <select name="course_id" required>
        <option value="">-- เลือกรายวิชา --</option>
        <?php foreach ($courses as $course): ?>
          <option value="<?= (int) $course['id'] ?>" <?= $selectedCourse === (int) $course['id'] ? 'selected' : '' ?>>
            <?= h($course['code'] . ' - ' . $course['name']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </label>
    <button class="button" type="submit">โหลดรายชื่อนักเรียน</button>
  </form>
</section>

<?php if ($selectedCourse > 0): ?>
  <section class="card" style="margin-top: 16px;">
    <h2>บันทึกสถานะเข้าเรียน</h2>
    <form method="post">
      <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
      <input type="hidden" name="action" value="save_attendance">
      <input type="hidden" name="course_id" value="<?= $selectedCourse ?>">
      <input type="hidden" name="attendance_date" value="<?= h($selectedDate) ?>">
      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>รหัส</th>
              <th>นักเรียน</th>
              <th>ห้อง</th>
              <th>สถานะ</th>
              <th>หมายเหตุ</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($students as $student): ?>
              <?php
                $studentId = (int) $student['id'];
                $current = $existing[$studentId]['status'] ?? 'present';
                $note = $existing[$studentId]['note'] ?? '';
              ?>
              <tr>
                <td><?= h($student['student_code']) ?></td>
                <td><?= h($student['first_name'] . ' ' . $student['last_name']) ?></td>
                <td><?= h($student['classroom']) ?></td>
                <td>
                  <select name="status[<?= $studentId ?>]">
                    <?php foreach (['present', 'late', 'absent', 'leave'] as $status): ?>
                      <option value="<?= $status ?>" <?= $current === $status ? 'selected' : '' ?>><?= status_label($status) ?></option>
                    <?php endforeach; ?>
                  </select>
                </td>
                <td><input name="note[<?= $studentId ?>]" value="<?= h($note) ?>" placeholder="หมายเหตุ"></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <div class="actions" style="margin-top: 16px;">
        <button class="button" type="submit">บันทึกการเช็คชื่อ</button>
      </div>
    </form>
  </section>
<?php endif; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
