-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.4.3 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.8.0.6908
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for nurse_pnnhbd
CREATE DATABASE IF NOT EXISTS `nurse_pnnhbd` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `nurse_pnnhbd`;

-- Dumping structure for table nurse_pnnhbd.banners
CREATE TABLE IF NOT EXISTS `banners` (
  `id` int NOT NULL AUTO_INCREMENT,
  `department_id` int DEFAULT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `subtitle` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `image_name` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `link_url` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `sort_order` int NOT NULL DEFAULT '1',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table nurse_pnnhbd.banners: ~1 rows (approximately)
INSERT INTO `banners` (`id`, `department_id`, `title`, `subtitle`, `image_name`, `link_url`, `sort_order`, `is_active`, `created_at`) VALUES
	(33, NULL, 'กลุ่มการพยาบาล', '', '1786092295_banner___________________________________________.png', NULL, 1, 1, '2026-08-07 08:44:55');

-- Dumping structure for table nurse_pnnhbd.departments
CREATE TABLE IF NOT EXISTS `departments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `link_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table nurse_pnnhbd.departments: ~21 rows (approximately)
INSERT INTO `departments` (`id`, `name`, `link_url`) VALUES
	(1, 'วิสัญญี', 'dept_anesthesia.php'),
	(2, 'อุบัติเหตุ-ฉุกเฉิน', 'dept_emergency.php'),
	(3, 'IC', 'dept_ic.php'),
	(4, 'อายุรกรรมหญิง', 'dept_medicine_female.php'),
	(5, 'อายุรกรรมชาย', 'dept_medicine_male.php'),
	(6, 'MICU', 'dept_micu.php'),
	(7, 'NICU', 'dept_nicu.php'),
	(8, 'สูติ-นรีเวช', 'dept_obgyn.php'),
	(9, 'OPD', 'dept_opd.php'),
	(10, 'ห้องผ่าตัด', 'dept_operating_room.php'),
	(11, 'ศัลยกรรมกระดูกและข้อ', 'dept_orthopedics.php'),
	(12, 'กุมารเวชกรรม', 'dept_pediatrics.php'),
	(13, 'ผู้คลอด', 'dept_postpartum.php'),
	(14, 'พิเศษชั้น 4', 'dept_private_floor4.php'),
	(15, 'พิเศษชั้น 5', 'dept_private_floor5.php'),
	(16, 'รักษ์จิต', 'dept_psychiatry.php'),
	(17, 'อุรเวชช์', 'dept_pulmonary.php'),
	(18, 'SICU', 'dept_sicu.php'),
	(19, 'ตรวจรักษาพิเศษ', 'dept_special_clinic.php'),
	(20, 'Stroke Unit', 'dept_stroke_unit.php'),
	(21, 'ศัลยกรรม', 'dept_surgery.php');

-- Dumping structure for table nurse_pnnhbd.department_contents
CREATE TABLE IF NOT EXISTS `department_contents` (
  `id` int NOT NULL AUTO_INCREMENT,
  `department_id` int DEFAULT NULL,
  `section` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `file_name` text COLLATE utf8mb4_general_ci,
  `link_url` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_department_section` (`department_id`,`section`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table nurse_pnnhbd.department_contents: ~0 rows (approximately)
INSERT INTO `department_contents` (`id`, `department_id`, `section`, `title`, `content`, `file_name`, `link_url`, `sort_order`, `created_at`) VALUES
	(4, 12, 'knowledge', 'test', '', '1786092759_0_dept_content.jpg', NULL, 1, '2026-08-07 08:52:39');

-- Dumping structure for table nurse_pnnhbd.events
CREATE TABLE IF NOT EXISTS `events` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `event_date` date NOT NULL,
  `image_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT 'default.jpg',
  `link_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table nurse_pnnhbd.events: ~0 rows (approximately)

-- Dumping structure for table nurse_pnnhbd.news
CREATE TABLE IF NOT EXISTS `news` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `image_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT 'default.jpg',
  `is_new` tinyint(1) DEFAULT '1',
  `link_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `created_at` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table nurse_pnnhbd.news: ~0 rows (approximately)
INSERT INTO `news` (`id`, `title`, `content`, `image_name`, `is_new`, `link_url`, `created_at`) VALUES
	(1, 'test', '', '1786092548_0_news_________________________________________________________________________________________________________________________________________________a4__2___1_.png', 0, NULL, '2026-08-07');

-- Dumping structure for table nurse_pnnhbd.personnel_directory
CREATE TABLE IF NOT EXISTS `personnel_directory` (
  `id` int NOT NULL AUTO_INCREMENT,
  `category` enum('head_nurse','dept_head','nurse') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'head_nurse=ทำเนียบหัวหน้าพยาบาล, dept_head=ทำเนียบหัวหน้ากลุ่มงาน, nurse=ทำเนียบพยาบาล',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'ชื่อ-นามสกุล',
  `position` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'ตำแหน่ง เช่น ผู้อำนวยการ, พยาบาลวิชาชีพ',
  `image_name` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'ชื่อไฟล์รูปภาพ',
  `sort_order` int NOT NULL DEFAULT '0' COMMENT 'ลำดับการแสดงผล (เลขน้อย=สำคัญกว่า)',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_category_sort` (`category`,`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table nurse_pnnhbd.personnel_directory: ~0 rows (approximately)

-- Dumping structure for table nurse_pnnhbd.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `password_hash` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `role` enum('main','dept') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'dept',
  `department_id` int DEFAULT NULL,
  `display_name` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  KEY `idx_username` (`username`),
  KEY `idx_role` (`role`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table nurse_pnnhbd.users: ~25 rows (approximately)
INSERT INTO `users` (`id`, `username`, `password_hash`, `role`, `department_id`, `display_name`, `created_at`) VALUES
	(1, 'admin', '240be518fabd2724ddb6f04eeb1da5967448d7e831c08c8fa822809f74c720a9', 'main', NULL, 'ผู้ดูแลระบบหลัก', '2026-07-02 03:47:35'),
	(2, 'กุมารเวช', 'ee79976c9380d5e337fc1c095ece8c8f22f91f306ceeb161fa51fecede2c4ba1', 'dept', 1, 'k', '2026-07-02 06:26:51'),
	(3, 'ตรวจรักษาพิเศษ', 'ef797c8118f02dfb649607dd5d3f8c7623048c9c063d532cc95c5ed7a898a64f', 'dept', 2, '้้hh', '2026-07-02 06:28:40'),
	(4, 'ผู้คลอด', '1f3ce40415a2081fa3eee75fc39fff8e56c22270d1a978a7249b592dcebd20b4', 'dept', 3, 'gh', '2026-07-02 06:40:11'),
	(7, 'pediatrics', '0a1edd9f8478a9371957e93af3055fc93d6526a32e463826c3d4b3682458bf88', 'dept', 1, 'pediatrics', '2026-07-13 03:37:41'),
	(8, 'specialclinic', 'e423cb0439e8e3984c7c06eb9abab1f09b8ee019235276ac823b3f370aeae6d5', 'dept', 2, 'specialclinic', '2026-07-13 03:38:02'),
	(9, 'laborroom', '413c619c5a02c1cdbdce95a4f3c5b1524e8bceaa969bd3a6f4038ecc14736d33', 'dept', 3, 'laborroom', '2026-07-13 03:38:19'),
	(10, 'ward4', '5add8df98952e7916d525dbac8045f2f23a43043f4bf41b11beec68666565d8f', 'dept', 4, 'ward4', '2026-07-13 03:38:38'),
	(11, 'ward5', '4baaed7a7d4c50f70716b8acbe92fa74a0f946547a6b89a98dfe6b3e37e35c21', 'dept', 5, 'ward5', '2026-07-13 03:38:51'),
	(12, 'psychiatry', '2fd0230976387d2a7f8e181a661d41bb739d4ab92b3466db5f5defe2a078770c', 'dept', 6, 'psychiatry', '2026-07-13 03:39:32'),
	(13, 'anesthesia', '8bc805598944e5a1b506110cc7da5a745e0c568aaafe9a9f1b3824635ff9b777', 'dept', 7, 'anesthesia', '2026-07-13 03:39:58'),
	(14, 'surgery', '20ad29a197dc2ee01f28016707ec4e1c70b808b7e36fbfe18e8fed6f1f99e6ae', 'dept', 8, 'surgery', '2026-07-13 03:40:14'),
	(15, 'orthopedics', '1f12af43a385b638034ff35b1135beda626d5552675aee90b6f85d79b3adadc4', 'dept', 9, 'orthopedics', '2026-07-13 03:41:36'),
	(16, 'obgyn', 'ee9fa0113e83e2ee359cfdf99acae9f72e9c104a0c12dea83b0e7869d7a4c5ab', 'dept', 10, 'obgyn', '2026-07-13 03:42:21'),
	(17, 'operatingroom', 'b634654b1ac1a7443d30bece9469390ec6defe9a2ad7bdff0be425965c38d6c1', 'dept', 11, 'operatingroom', '2026-07-13 03:42:37'),
	(18, 'medmale', 'ab56eac402b8d1da87121e8179250d36c76faebdee0e8babbba0f48f0a16ffdb', 'dept', 12, 'medmale', '2026-07-13 03:42:55'),
	(19, 'medfemale', 'b5ea13c3a8dde1427dd4ef023dd9463411213521750e4e6b075e37001de034ad', 'dept', 13, 'medfemale', '2026-07-13 03:43:11'),
	(20, 'emergency', '1b8a38f8889dc8d030d5d6ddf32d7194021a08a92ce17394f4282cf2daa9641c', 'dept', 14, 'emergency', '2026-07-13 03:43:29'),
	(21, 'urology', '660472397d8d931f3e30dce31f66bed7aab6a3af2cb7d4a52938228f7f464391', 'dept', 15, 'urology', '2026-07-13 03:43:48'),
	(22, 'ic', '84bd467a81a3608636e6057cd4e1deb659cd7274d2263746827107be287618c6', 'dept', 16, 'ic', '2026-07-13 03:44:01'),
	(23, 'micu', 'fa551907b4f0d10b1eb882ef6f24443767c442fcf1c4d26ab88f76f7d66e19f3', 'dept', 17, 'micu', '2026-07-13 03:44:14'),
	(24, 'nicu', '1c7b8893357b3b12bd86a949a63511fe032c62330fd6fd7592ad4393a86b34e0', 'dept', 18, 'nicu', '2026-07-13 03:44:31'),
	(25, 'opd', '71d4fd6c786f189fe238ec404cb1e27f93c7a6bbc2425c4e973b6b29ae55f8c8', 'dept', 19, 'opd', '2026-07-13 03:44:48'),
	(26, 'sicu', 'c2f29345b8ade104bdf71b794e777d3311cfa67f966bd6e7de501c93f696c231', 'dept', 20, 'sicu', '2026-07-13 03:45:01'),
	(27, 'strokeunit', '6be931f2b54bb035aff20537bfe09fdc9dbf1f37f845e4b1daaa37fe224b0d98', 'dept', 21, 'strokeunit', '2026-07-13 03:45:14');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
