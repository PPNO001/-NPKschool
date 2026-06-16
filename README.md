# School Management System

ระบบเช็คชื่อเข้าเรียนสำหรับโรงเรียน พัฒนาด้วย PHP และ MySQL ใช้ PDO prepared statements และรองรับการรันแบบ local, Docker, shared hosting/cPanel และ Railway

## ใช้งานออนไลน์ทันที

เปิดเวอร์ชัน GitHub Pages ได้ที่:

https://ppno001.github.io/-NPKschool/

เวอร์ชันนี้อยู่ใน `docs/index.html` และเก็บข้อมูลใน browser/localStorage เหมาะสำหรับใช้งานทันทีหรือสาธิตระบบ ถ้าต้องการข้อมูลกลางหลายเครื่องให้ deploy เวอร์ชัน PHP/MySQL ด้วย Railway หรือ hosting ที่รองรับ PHP

## ฟีเจอร์

- เข้าสู่ระบบผู้ดูแล
- จัดการข้อมูลนักเรียน
- จัดการข้อมูลรายวิชา
- เช็คชื่อเข้าเรียนรายวันตามรายวิชา
- บันทึกสถานะ มาเรียน มาสาย ขาดเรียน และลา
- ดูรายงานสรุปการเข้าเรียนตามช่วงวันที่

## โครงสร้างโปรเจกต์

```text
assets/css/styles.css
config/database.php
database/schema.sql
includes/
scripts/init-database.php
attendance.php
courses.php
index.php
login.php
logout.php
reports.php
students.php
```

## บัญชีเริ่มต้น

```text
Email: admin@example.com
Password: admin123
```

หลัง deploy ออนไลน์แล้วควรตั้ง `ADMIN_PASSWORD` เป็นรหัสใหม่ และเปลี่ยนรหัสผ่านเริ่มต้นทันที

## ตั้งค่า Environment

แอปรับค่าฐานข้อมูลจาก environment variables หรือไฟล์ `.env`

```env
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=school_management_system
DB_USER=root
DB_PASSWORD=
DB_CHARSET=utf8mb4
```

รองรับตัวแปรของ Railway MySQL โดยตรง: `MYSQLHOST`, `MYSQLPORT`, `MYSQLUSER`, `MYSQLPASSWORD`, `MYSQLDATABASE`, `MYSQL_URL`

## รันด้วย Docker

```bash
docker compose up --build
```

เปิดเว็บที่ `http://localhost:8080`

ถ้าต้องการเปิด phpMyAdmin:

```bash
docker compose --profile tools up -d phpmyadmin
```

เปิด phpMyAdmin ที่ `http://localhost:8081`

## Deploy ออนไลน์บน Railway

โปรเจกต์นี้มี `Dockerfile` และ `railway.json` พร้อม deploy แล้ว

1. สร้าง Project ใหม่บน Railway แล้วเลือก Deploy from GitHub Repo
2. เลือก repo `PPNO001/-NPKschool`
3. เพิ่ม MySQL service ใน project เดียวกัน
4. ใน service ของ PHP app ให้เพิ่ม variable references จาก MySQL service หรือกรอกค่าต่อไปนี้ให้ตรงกับ MySQL:

```env
MYSQL_URL=<reference จาก MySQL service>
DB_AUTO_MIGRATE=true
DB_WAIT_SECONDS=90
ADMIN_EMAIL=admin@example.com
ADMIN_PASSWORD=<ตั้งรหัสผ่านจริง>
```

หรือใช้แบบแยกตัวแปร:

```env
MYSQLHOST=<host>
MYSQLPORT=<port>
MYSQLUSER=<user>
MYSQLPASSWORD=<password>
MYSQLDATABASE=<database>
DB_AUTO_MIGRATE=true
```

เมื่อ `DB_AUTO_MIGRATE=true` container จะรัน `scripts/init-database.php` ตอน start เพื่อสร้างตารางและข้อมูลเริ่มต้นให้อัตโนมัติ

## Deploy บน shared hosting/cPanel

1. อัปโหลดไฟล์ทั้งหมดไปยัง public directory ของเว็บ
2. สร้าง MySQL database และ user ใน cPanel
3. Import `database/schema.sql` เข้า database ที่สร้างไว้
4. สร้างไฟล์ `.env` ตามตัวอย่าง `.env.example` แล้วใส่ค่า database ของ hosting
5. เปิดโดเมน แล้วเข้าสู่ระบบด้วยบัญชีเริ่มต้นหรือบัญชีที่ตั้งผ่าน `ADMIN_EMAIL`/`ADMIN_PASSWORD`

## รันบน XAMPP

1. วางโฟลเดอร์โปรเจกต์ไว้ใน `htdocs`
2. Start `Apache` และ `MySQL`
3. สร้าง database ชื่อ `school_management_system`
4. Import `database/schema.sql`
5. ตั้งค่า `.env` ถ้าค่า database ไม่ใช่ `root`/รหัสว่าง
6. เปิด `http://localhost/<ชื่อโฟลเดอร์โปรเจกต์>`
