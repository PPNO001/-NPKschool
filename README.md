# School Management System

ระบบเช็คชื่อเข้าเรียนสำหรับโรงเรียน พัฒนาด้วย PHP และ MySQL ใช้งานง่าย เหมาะสำหรับนำไปต่อยอดเป็นระบบบริหารจัดการโรงเรียน

## ฟีเจอร์

- เข้าสู่ระบบผู้ดูแล
- จัดการข้อมูลนักเรียน
- จัดการข้อมูลรายวิชา
- เช็คชื่อเข้าเรียนรายวันตามรายวิชา
- บันทึกสถานะ มาเรียน มาสาย ขาดเรียน และลา
- ดูรายงานสรุปการเข้าเรียนตามช่วงวันที่
- ใช้ PDO prepared statements เพื่อความปลอดภัยในการ query

## โครงสร้างโปรเจกต์

```text
school-management-system/
  assets/css/styles.css
  config/database.php
  database/schema.sql
  includes/
  attendance.php
  courses.php
  index.php
  login.php
  logout.php
  reports.php
  students.php
```

## วิธีติดตั้งบน XAMPP

1. คัดลอกโฟลเดอร์โปรเจกต์ไปไว้ที่ `htdocs/school-management-system`
2. เปิด XAMPP แล้ว start `Apache` และ `MySQL`
3. เข้า `http://localhost/phpmyadmin`
4. Import ไฟล์ `database/schema.sql`
5. ตรวจสอบ config ฐานข้อมูลที่ `config/database.php`
6. เปิด `http://localhost/school-management-system`

## บัญชีเริ่มต้น

```text
Email: admin@example.com
Password: admin123
```

## การตั้งค่าฐานข้อมูล

ค่าเริ่มต้นใน `config/database.php`

```php
$dbHost = '127.0.0.1';
$dbName = 'school_management_system';
$dbUser = 'root';
$dbPass = '';
```

ถ้า hosting หรือเครื่องของคุณใช้ user/password อื่น ให้แก้เฉพาะไฟล์นี้

## Repository

https://github.com/kengiit8-collab/school-management-system
