SET NAMES utf8mb4;
SET time_zone = '+00:00';

CREATE TABLE IF NOT EXISTS users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(190) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('admin', 'teacher') NOT NULL DEFAULT 'admin',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS students (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  student_code VARCHAR(50) NOT NULL UNIQUE,
  first_name VARCHAR(120) NOT NULL,
  last_name VARCHAR(120) NOT NULL,
  classroom VARCHAR(50) NOT NULL,
  phone VARCHAR(50) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS courses (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  code VARCHAR(50) NOT NULL UNIQUE,
  name VARCHAR(160) NOT NULL,
  teacher_name VARCHAR(120) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS attendance (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  student_id INT UNSIGNED NOT NULL,
  course_id INT UNSIGNED NOT NULL,
  attendance_date DATE NOT NULL,
  status ENUM('present', 'late', 'absent', 'leave') NOT NULL DEFAULT 'present',
  note VARCHAR(255) NULL,
  recorded_by INT UNSIGNED NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY unique_attendance (student_id, course_id, attendance_date),
  CONSTRAINT fk_attendance_student FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
  CONSTRAINT fk_attendance_course FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
  CONSTRAINT fk_attendance_user FOREIGN KEY (recorded_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO users (name, email, password_hash, role)
VALUES ('ผู้ดูแลระบบ', 'admin@example.com', '240be518fabd2724ddb6f04eeb1da5967448d7e831c08c8fa822809f74c720a9', 'admin')
ON DUPLICATE KEY UPDATE email = email;

INSERT INTO students (student_code, first_name, last_name, classroom, phone) VALUES
('S001', 'กิตติ', 'ใจดี', 'ม.6/1', '0800000001'),
('S002', 'มณีรัตน์', 'สุขใจ', 'ม.6/1', '0800000002'),
('S003', 'ธนากร', 'เรียนดี', 'ม.6/2', '0800000003')
ON DUPLICATE KEY UPDATE student_code = student_code;

INSERT INTO courses (code, name, teacher_name) VALUES
('TH101', 'ภาษาไทยพื้นฐาน', 'ครูสมชาย'),
('MA101', 'คณิตศาสตร์พื้นฐาน', 'ครูวรรณา'),
('SC101', 'วิทยาศาสตร์พื้นฐาน', 'ครูปรียา')
ON DUPLICATE KEY UPDATE code = code;
