-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Dec 05, 2025 at 01:23 PM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `medixa`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

DROP TABLE IF EXISTS `admins`;
CREATE TABLE IF NOT EXISTS `admins` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_on` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `email`, `password`, `created_on`) VALUES
(3, 'Abdulwakeel', 'wakinojimoh@gmail.com', '$2y$10$SuP/29GkOQHHIF1S9n2lHu15TZbeV2YmaDU9PnuOpE27cRyBaX4tO', '2025-11-30 23:16:41');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE IF NOT EXISTS `categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `category_name` varchar(255) NOT NULL,
  `img` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `category_name`, `img`, `created_at`) VALUES
(2, 'Eye Care', 'Service_6930a22b281ec2.73811340.jpeg', '2025-12-03 21:48:43'),
(3, 'Dental Care', 'Service_6930a2397f6179.62980117.jpg', '2025-12-03 21:48:57'),
(4, 'Cardiothoracic Surgeon', 'Service_6930a24544c4e5.31275832.jpeg', '2025-12-03 21:49:09'),
(5, 'Pediatric Specialist', 'Service_6930a253759250.96413742.jpeg', '2025-12-03 21:49:23'),
(6, 'Gynecology', 'Service_6930a26f618d93.61227260.jpeg', '2025-12-03 21:49:51'),
(7, 'Associate Dentist', 'Service_6930a25d9058e8.44101866.jpg', '2025-12-03 21:49:33'),
(17, 'Entertainment', 'service692fef62124a56.64999234.jpeg', '2025-12-03 09:05:54');

-- --------------------------------------------------------

--
-- Table structure for table `contacts`
--

DROP TABLE IF EXISTS `contacts`;
CREATE TABLE IF NOT EXISTS `contacts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('new','read','replied') COLLATE utf8mb4_unicode_ci DEFAULT 'new',
  PRIMARY KEY (`id`),
  KEY `idx_created_at` (`created_at`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `doctors`
--

DROP TABLE IF EXISTS `doctors`;
CREATE TABLE IF NOT EXISTS `doctors` (
  `id` int NOT NULL AUTO_INCREMENT,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `twitter` varchar(255) NOT NULL,
  `linkedin` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `facebook` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `category_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_category_id` (`category_id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `doctors`
--

INSERT INTO `doctors` (`id`, `firstname`, `lastname`, `email`, `title`, `twitter`, `linkedin`, `facebook`, `image`, `created_at`, `category_id`) VALUES
(4, 'Abdulwakeel', 'Jimoh', 'wakinojimoh@gmail.com', '5', 'twitter@1Z', '22, Maryland estate Aleke, Ikorodu', '45544', 'DOCTOR_692daaf29a7b03.89675813.jpeg', '2025-12-01 15:49:22', NULL),
(3, 'Dr. Hannah', 'Park', 'wakinojimoh@gmail.com', '4', 'twitter@1', 'linkdin@1', 'facebook@', 'DOCTOR_692f41b986ddc8.08297781.jpeg', '2025-12-02 20:44:57', NULL),
(5, 'kelvin', 'johnson', 'kelvin223@gmail.com', '6', 'twitter@1Z', 'linkdin@1de', 'facebook@1d', 'DOCTOR_692f413ff2eef9.51977830.jpeg', '2025-12-02 20:42:55', NULL),
(6, 'bidex', 'habeeb', 'bidex@gmail.com', '4', 'twitter@1b', 'linkdin@1b', 'facebook@1b', 'DOCTOR_692f4198c56fa9.34180874.jpeg', '2025-12-02 20:44:24', NULL),
(7, 'Habeeb', 'Mariam', 'habeeb@gmail.com', '3', 'twitter@1', 'linkdin@1de', 'facebook@1d', 'DOCTOR_6931cb4c461473.85856905.jpg', '2025-12-04 18:56:28', NULL),
(8, 'Habeeb', 'Mariam', 'kelvin223@gmail.com', '4', 'twitter@1', 'linkdin@1', 'facebook@1', 'DOCTOR_6931c56731d992.96763351.jpg', '2025-12-04 18:31:19', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `patients`
--

DROP TABLE IF EXISTS `patients`;
CREATE TABLE IF NOT EXISTS `patients` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `department_id` int UNSIGNED NOT NULL,
  `doctor_id` int UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `appointment_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('pending','confirmed','cancelled','completed') DEFAULT 'pending',
  PRIMARY KEY (`id`),
  KEY `idx_department_id` (`department_id`),
  KEY `idx_doctor_id` (`doctor_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `patients`
--

INSERT INTO `patients` (`id`, `department_id`, `doctor_id`, `name`, `phone`, `appointment_date`, `created_at`, `status`) VALUES
(1, 17, 4, 'damola ayodele', '09056937682', '2025-12-08', '2025-12-04 12:47:24', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

DROP TABLE IF EXISTS `posts`;
CREATE TABLE IF NOT EXISTS `posts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `subtitle` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `body` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `category_id` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id`, `title`, `subtitle`, `body`, `image`, `category_id`, `created_at`) VALUES
(2, 'Improving Heart Health with Daily Habits', 'daily habits', 'Improving Heart Health with Daily Habits Improving Heart Health with Daily Habits', 'post_1.jpeg', '', '2025-12-02 22:12:56'),
(3, 'Radiology Advancements in Diagnostics', 'Diagnostics', 'Radiology Advancements in Diagnostics Radiology Advancements in DiagnosticsRadiology Advancements in DiagnosticsRadiology Advancements in Diagnostics', 'post_2.jpeg', '', '2025-12-02 22:13:36'),
(4, 'Child Wellness: Preventive Care Tips', 'preventive care tips', 'Child Wellness: Preventive Care Tips Child Wellness: Preventive Care Tips Child Wellness: Preventive Care Tips Child Wellness: Preventive Care Tips', 'post_3.jpeg', '', '2025-12-02 22:14:27');

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

DROP TABLE IF EXISTS `services`;
CREATE TABLE IF NOT EXISTS `services` (
  `id` int NOT NULL AUTO_INCREMENT,
  `service_name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `service_name`, `description`, `image`, `created_at`) VALUES
(1, 'Eye Care', 'eye care services with top professional Specilist', 'Service_6930b46e4a8289.93565708.jpeg', '2025-12-03 23:06:38'),
(2, 'Entertainment', 'entertainment services with top professional Specilist', 'Service_6930b5385415c0.35441926.jpeg', '2025-12-03 23:10:00'),
(3, 'Dental Care', 'dental care services with top professional Specilist', 'Service_6930b4a52fa707.78893061.jpg', '2025-12-03 23:07:33'),
(4, 'Personal Consultant', 'Personal Consultant services with top professional Specilist', 'Service_6930b523a02ce4.72196415.jpeg', '2025-12-03 23:09:39'),
(5, 'Heart Department', 'Heart Department services with top professional Specilist', 'Service_6930b50aa971c4.36508434.jpeg', '2025-12-03 23:09:14');

-- --------------------------------------------------------

--
-- Table structure for table `testimonials`
--

DROP TABLE IF EXISTS `testimonials`;
CREATE TABLE IF NOT EXISTS `testimonials` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `profession` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `testimonials`
--

INSERT INTO `testimonials` (`id`, `name`, `profession`, `message`, `image`, `created_at`) VALUES
(1, 'femi', 'mechanic', 'Professional and friendly staff. The care I received was exceptional.”', 'Testimonial_69317402cc2548.32123168.png', '2025-12-04 11:44:02'),
(2, 'lekan', 'Barber', 'Pain management was effective, and I was comfortablev during and after the treatment. The staff provided emotional support and made sure I understood every step of my care.', 'Testimonial_69318f51e33728.83927659.jpeg', '2025-12-04 13:40:33'),
(3, 'Hazan', 'Graphic Designer', 'Pain management was effective, and I was comfortable during and after the treatment. The staff provided emotional support and made sure I understood every step of my care.', 'Testimonial_69318fe4320734.47412842.jpeg', '2025-12-04 13:43:00'),
(4, 'John James', 'Web Developer', 'Pain management was effective, and I was comfortable during and after the treatment. The staff provided emotional support and made sure I understood every step of my care.', 'Testimonial_693190220e3789.20586705.jpeg', '2025-12-04 13:44:02'),
(5, 'Entertainment', 'Graphic Designer', 'Pain management was effective, and I was comfortable during and after the treatment. The staff provided emotional support and made sure I understood every step of my care.', 'Testimonial_693190610d2eb3.10815804.jpeg', '2025-12-04 13:45:05');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
