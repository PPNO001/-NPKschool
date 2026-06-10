<?php

require_once __DIR__ . '/includes/auth.php';
require_login();

$pageTitle = 'รายงานการเข้าเรียน';
$activePage = 'reports';
$courseId = (int) ($_GET['course_id'] ?? 0);
$fromDate = $_GET['from_date'] ?? date('Y-m-01');
$toDate = $_GET['to_date'] ?? date('Y-m-d');

$courses = $pdo->query('SELECT id, code, name FROM courses ORDER BY code')->fetchAll();
$rows = [];

if ($courseId > 0) {
    $statement = $pdo->prepare('
        SELECT
            s.student_code,
            s.first_name,
            s.last_name,
            s.classroom,
            SUM(a.status = "present") AS present_count,
            SUM(a.status = "late") AS late_count,
            SUM(a.status = "absent") AS absent_count,
            SUM(a.status = "leave") AS leave_count,
            COUNT(a.id) AS total_records
        FROM students s
        LEFT JOIN attendance a
            ON a.student_id = s.id
            AND a.course_id = ?
            AND a.attendance_date BETWEEN ? AND ?
        GROUP BY s.id
        ORDER BY s.classroom, s.student_code
    ');
    $statement->execute([$courseId, $fromDate, $toDate]);
    $rows = $statement->fetchAll();
}

require __DIR__ . '/includes/header.php';
?>
<section class="card">
  <h2>ตัวกรองรายงาน</h2>
  <form class="form-stack" method="get">
    <label>
      รายวิชา
      <select name="course_id" required>
        <option value="">-- เลือกรายวิชา --</option>
        <?php foreach ($courses as $course): ?>
          <option value="<?= (int) $course['id'] ?>" <?= $courseId === (int) $course['id'] ? 'selected' : '' ?>>
            <?= h($course['code'] . ' - ' . $course['name']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </label>
    <label>ตั้งแต่วันที่ <input type="date" name="from_date" value="<?= h($fromDate) ?>" required></label>
    <label>ถึงวันที่ <input type="date" name="to_date" value="<?= h($toDate) ?>" required></label>
    <button class="button" type="submit">แสดงรายงาน</button>
  </form>
</section>

<?php if ($courseId > 0): ?>
  <section class="card" style="margin-top: 16px;">
    <h2>สรุปการเข้าเรียน</h2>
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>รหัส</th>
            <th>นักเรียน</th>
            <th>ห้อง</th>
            <th>มาเรียน</th>
            <th>สาย</th>
            <th>ขาด</th>
            <th>ลา</th>
            <th>คิดเป็น %</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($rows as $row): ?>
            <?php
              $total = (int) $row['total_records'];
              $attended = (int) $row['present_count'] + (int) $row['late_count'];
              $percent = $total > 0 ? round(($attended / $total) * 100, 2) : 0;
            ?>
            <tr>
              <td><?= h($row['student_code']) ?></td>
              <td><?= h($row['first_name'] . ' ' . $row['last_name']) ?></td>
              <td><?= h($row['classroom']) ?></td>
              <td class="success"><?= (int) $row['present_count'] ?></td>
              <td class="warning"><?= (int) $row['late_count'] ?></td>
              <td class="danger"><?= (int) $row['absent_count'] ?></td>
              <td class="info"><?= (int) $row['leave_count'] ?></td>
              <td><span class="badge"><?= $percent ?>%</span></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </section>
<?php endif; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
