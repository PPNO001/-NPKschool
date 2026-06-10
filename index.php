<?php

require_once __DIR__ . '/includes/auth.php';
require_login();

$pageTitle = 'Dashboard';
$activePage = 'dashboard';

$studentCount = (int) $pdo->query('SELECT COUNT(*) FROM students')->fetchColumn();
$courseCount = (int) $pdo->query('SELECT COUNT(*) FROM courses')->fetchColumn();
$today = date('Y-m-d');

$statement = $pdo->prepare('
    SELECT status, COUNT(*) AS total
    FROM attendance
    WHERE attendance_date = ?
    GROUP BY status
');
$statement->execute([$today]);
$todayStats = ['present' => 0, 'late' => 0, 'absent' => 0, 'leave' => 0];
foreach ($statement->fetchAll() as $row) {
    $todayStats[$row['status']] = (int) $row['total'];
}

$recent = $pdo->query('
    SELECT a.attendance_date, a.status, s.student_code, s.first_name, s.last_name, c.name AS course_name
    FROM attendance a
    JOIN students s ON s.id = a.student_id
    JOIN courses c ON c.id = a.course_id
    ORDER BY a.attendance_date DESC, a.id DESC
    LIMIT 8
')->fetchAll();

require __DIR__ . '/includes/header.php';
?>
<section class="grid stats-grid">
  <article class="card stat-card">
    <span>นักเรียนทั้งหมด</span>
    <strong><?= $studentCount ?></strong>
  </article>
  <article class="card stat-card">
    <span>รายวิชา</span>
    <strong><?= $courseCount ?></strong>
  </article>
  <article class="card stat-card">
    <span>มาเรียนวันนี้</span>
    <strong><?= $todayStats['present'] ?></strong>
  </article>
  <article class="card stat-card">
    <span>ขาดเรียนวันนี้</span>
    <strong><?= $todayStats['absent'] ?></strong>
  </article>
</section>

<section class="card" style="margin-top: 16px;">
  <h2>รายการเช็คชื่อล่าสุด</h2>
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>วันที่</th>
          <th>รหัส</th>
          <th>นักเรียน</th>
          <th>รายวิชา</th>
          <th>สถานะ</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($recent as $item): ?>
          <tr>
            <td><?= h($item['attendance_date']) ?></td>
            <td><?= h($item['student_code']) ?></td>
            <td><?= h($item['first_name'] . ' ' . $item['last_name']) ?></td>
            <td><?= h($item['course_name']) ?></td>
            <td><span class="badge <?= status_class($item['status']) ?>"><?= status_label($item['status']) ?></span></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
