-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: profess0r_shop
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `pp_addon`
--

DROP TABLE IF EXISTS `pp_addon`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pp_addon` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `addon_id` varchar(15) NOT NULL,
  `slug` varchar(40) NOT NULL DEFAULT '--',
  `name` text DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_date` varchar(20) NOT NULL,
  `updated_date` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `addon_id` (`addon_id`,`status`,`created_date`,`updated_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pp_addon`
--

LOCK TABLES `pp_addon` WRITE;
/*!40000 ALTER TABLE `pp_addon` DISABLE KEYS */;
/*!40000 ALTER TABLE `pp_addon` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pp_addon_parameter`
--

DROP TABLE IF EXISTS `pp_addon_parameter`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pp_addon_parameter` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `addon_id` varchar(15) NOT NULL,
  `option_name` varchar(50) NOT NULL,
  `value` text NOT NULL,
  `created_date` varchar(20) NOT NULL,
  `updated_date` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `addon_id` (`addon_id`,`option_name`,`created_date`,`updated_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pp_addon_parameter`
--

LOCK TABLES `pp_addon_parameter` WRITE;
/*!40000 ALTER TABLE `pp_addon_parameter` DISABLE KEYS */;
/*!40000 ALTER TABLE `pp_addon_parameter` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pp_admin`
--

DROP TABLE IF EXISTS `pp_admin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pp_admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `a_id` varchar(15) NOT NULL,
  `full_name` text NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` text NOT NULL,
  `temp_password` text DEFAULT NULL,
  `reset_limit` varchar(10) NOT NULL DEFAULT '3',
  `status` enum('active','suspend') NOT NULL DEFAULT 'active',
  `role` enum('admin','staff') NOT NULL DEFAULT 'admin',
  `2fa_status` enum('enable','disable') NOT NULL DEFAULT 'disable',
  `2fa_secret` varchar(20) NOT NULL DEFAULT '--',
  `created_date` varchar(20) NOT NULL,
  `updated_date` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `a_id` (`a_id`,`email`),
  KEY `username` (`username`),
  KEY `created_date` (`created_date`,`updated_date`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pp_admin`
--

LOCK TABLES `pp_admin` WRITE;
/*!40000 ALTER TABLE `pp_admin` DISABLE KEYS */;
INSERT INTO `pp_admin` VALUES (1,'0242809184','sakib','sakib','sakibulhasantalukder2006@gmail.com','$2y$10$pfR7stYIXVqSUYVJ72OcD.CLXJnWmtt.uUmEbFVwmC0hviV2VShQ2','$2y$10$41EkUqh9yKRs89LxcoTh4eTveGJMAtWMGKTMVlvibpa7BC7A3Kf6G','3','active','admin','disable','SBDNQG4MGPEPNJMA','2026-06-23 14:26:36','2026-06-28 05:18:28');
/*!40000 ALTER TABLE `pp_admin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pp_api`
--

DROP TABLE IF EXISTS `pp_api`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pp_api` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `brand_id` varchar(15) NOT NULL,
  `name` text NOT NULL,
  `api_key` varchar(60) NOT NULL,
  `expired_date` text DEFAULT NULL,
  `api_scopes` text NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_date` varchar(20) NOT NULL,
  `updated_date` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `brand_id` (`brand_id`,`api_key`,`created_date`,`updated_date`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pp_api`
--

LOCK TABLES `pp_api` WRITE;
/*!40000 ALTER TABLE `pp_api` DISABLE KEYS */;
INSERT INTO `pp_api` VALUES (1,'9675068878','topup','f4efbef4260036c20081e31529dbf266679982aa0b8d9bfa41','--','[\"create_payment\",\"verify_payment\",\"refund_payment\"]','active','2026-07-01 12:29:41','2026-07-01 12:29:41');
/*!40000 ALTER TABLE `pp_api` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pp_balance_verification`
--

DROP TABLE IF EXISTS `pp_balance_verification`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pp_balance_verification` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `device_id` varchar(15) NOT NULL,
  `sender_key` varchar(15) NOT NULL,
  `type` enum('Personal','Agent','Merchant') NOT NULL DEFAULT 'Personal',
  `current_balance` decimal(20,8) NOT NULL,
  `simslot` varchar(6) NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_date` varchar(20) NOT NULL,
  `updated_date` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `device_id` (`device_id`,`sender_key`,`type`,`created_date`,`updated_date`),
  KEY `simslot` (`simslot`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pp_balance_verification`
--

LOCK TABLES `pp_balance_verification` WRITE;
/*!40000 ALTER TABLE `pp_balance_verification` DISABLE KEYS */;
/*!40000 ALTER TABLE `pp_balance_verification` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pp_brands`
--

DROP TABLE IF EXISTS `pp_brands`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pp_brands` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `brand_id` varchar(15) NOT NULL,
  `favicon` text DEFAULT NULL,
  `logo` text DEFAULT NULL,
  `identify_name` varchar(50) NOT NULL DEFAULT 'Default',
  `name` text DEFAULT NULL,
  `support_email_address` text DEFAULT NULL,
  `support_phone_number` text DEFAULT NULL,
  `support_website` text DEFAULT NULL,
  `whatsapp_number` text DEFAULT NULL,
  `telegram` text DEFAULT NULL,
  `facebook_messenger` text DEFAULT NULL,
  `facebook_page` text DEFAULT NULL,
  `theme` varchar(120) NOT NULL DEFAULT 'twenty-six',
  `street_address` text DEFAULT NULL,
  `city_town` text DEFAULT NULL,
  `postal_code` text DEFAULT NULL,
  `country` text DEFAULT NULL,
  `timezone` varchar(150) NOT NULL DEFAULT 'Asia/Dhaka',
  `language` varchar(150) NOT NULL DEFAULT 'en',
  `currency_code` varchar(150) NOT NULL DEFAULT 'BDT',
  `autoExchange` enum('disabled','enabled') NOT NULL DEFAULT 'disabled',
  `payment_tolerance` varchar(150) NOT NULL DEFAULT '0',
  `created_date` varchar(20) NOT NULL DEFAULT '--',
  `updated_date` varchar(20) NOT NULL DEFAULT '--',
  `redirect_url` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `brand_id` (`brand_id`),
  KEY `created_date` (`created_date`,`updated_date`),
  KEY `identify_name` (`identify_name`),
  KEY `autoExchange` (`autoExchange`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pp_brands`
--

LOCK TABLES `pp_brands` WRITE;
/*!40000 ALTER TABLE `pp_brands` DISABLE KEYS */;
INSERT INTO `pp_brands` VALUES (1,'9675068878','http://localhost/piprapay/pp-media/storage/KliRmfmpC7F5sp5raNuuT8afmilzSM.png','http://localhost/piprapay/pp-media/storage/m7rHcOBT0mrrcihOokxdOE5TBcrYW2.png','Default','Profess0r Shop','sakibulhasantalukder2006@gmail.com','--','https://profess0r-null.xyz','97431394274','profess0r_null','--','--','twenty-six','--','--','--','--','Asia/Dhaka','en','BDT','disabled','0','2026-06-23 14:26:36','2026-07-01 19:05:35','https://youtube.com');
/*!40000 ALTER TABLE `pp_brands` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pp_browser_log`
--

DROP TABLE IF EXISTS `pp_browser_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pp_browser_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `a_id` varchar(15) NOT NULL,
  `cookie` varchar(40) NOT NULL,
  `browser` varchar(10) NOT NULL,
  `device` varchar(10) NOT NULL,
  `ip` varchar(15) NOT NULL,
  `status` enum('active','expired') NOT NULL DEFAULT 'active',
  `created_date` varchar(20) NOT NULL,
  `updated_date` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `a_id` (`a_id`,`cookie`,`created_date`,`updated_date`),
  KEY `created_date` (`created_date`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pp_browser_log`
--

LOCK TABLES `pp_browser_log` WRITE;
/*!40000 ALTER TABLE `pp_browser_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `pp_browser_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pp_currency`
--

DROP TABLE IF EXISTS `pp_currency`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pp_currency` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `brand_id` varchar(15) NOT NULL,
  `code` varchar(6) NOT NULL,
  `symbol` varchar(5) NOT NULL,
  `rate` decimal(20,8) NOT NULL DEFAULT 0.00000000,
  `created_date` varchar(20) NOT NULL,
  `updated_date` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `brand_id` (`brand_id`,`code`,`symbol`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pp_currency`
--

LOCK TABLES `pp_currency` WRITE;
/*!40000 ALTER TABLE `pp_currency` DISABLE KEYS */;
INSERT INTO `pp_currency` VALUES (1,'9675068878','BDT','a��',0.00000000,'2026-06-23 14:26:36','2026-06-23 14:26:36');
/*!40000 ALTER TABLE `pp_currency` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pp_customer`
--

DROP TABLE IF EXISTS `pp_customer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pp_customer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ref` varchar(15) NOT NULL,
  `brand_id` varchar(15) NOT NULL,
  `name` text NOT NULL,
  `email` varchar(100) NOT NULL,
  `mobile` varchar(15) NOT NULL,
  `status` enum('active','suspend') NOT NULL DEFAULT 'active',
  `suspend_reason` text DEFAULT NULL,
  `inserted_via` enum('manual','checkout') NOT NULL DEFAULT 'manual',
  `created_date` varchar(20) NOT NULL,
  `updated_date` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ref` (`ref`,`brand_id`,`email`,`mobile`),
  KEY `created_date` (`created_date`,`updated_date`),
  KEY `status` (`status`,`inserted_via`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pp_customer`
--

LOCK TABLES `pp_customer` WRITE;
/*!40000 ALTER TABLE `pp_customer` DISABLE KEYS */;
INSERT INTO `pp_customer` VALUES (1,'6743910805','9675068878','Sakibul Haasan','sakibulhasantalukder2006@gmail.com','01603067785','active','','manual','2026-06-27 07:18:54','2026-06-27 07:18:54'),(2,'0486219671','9675068878','Test','test@test.com','01700','active','--','manual','2026-07-01 12:45:12','2026-07-01 12:45:12');
/*!40000 ALTER TABLE `pp_customer` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pp_device`
--

DROP TABLE IF EXISTS `pp_device`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pp_device` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `d_id` varchar(40) NOT NULL,
  `device_id` varchar(15) NOT NULL,
  `otp` varchar(15) NOT NULL,
  `name` text DEFAULT NULL,
  `model` text DEFAULT NULL,
  `android_level` text DEFAULT NULL,
  `app_version` text DEFAULT NULL,
  `status` enum('processing','used') NOT NULL DEFAULT 'processing',
  `created_date` varchar(20) NOT NULL,
  `updated_date` varchar(20) NOT NULL,
  `last_sync` varchar(20) NOT NULL DEFAULT '--',
  PRIMARY KEY (`id`),
  KEY `device_id` (`device_id`),
  KEY `created_date` (`created_date`,`updated_date`),
  KEY `a_id` (`d_id`),
  KEY `otp` (`otp`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pp_device`
--

LOCK TABLES `pp_device` WRITE;
/*!40000 ALTER TABLE `pp_device` DISABLE KEYS */;
INSERT INTO `pp_device` VALUES (1,'0a5c59a1fb52b6f140fac981b4e5bc91','0473042201','1566915994','--','--','--','--','processing','2026-06-23 15:01:57','2026-06-24 11:57:19','--');
/*!40000 ALTER TABLE `pp_device` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pp_domain`
--

DROP TABLE IF EXISTS `pp_domain`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pp_domain` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `domain` varchar(50) NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_date` varchar(20) NOT NULL,
  `updated_date` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `domain` (`domain`),
  KEY `created_date` (`created_date`,`updated_date`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pp_domain`
--

LOCK TABLES `pp_domain` WRITE;
/*!40000 ALTER TABLE `pp_domain` DISABLE KEYS */;
INSERT INTO `pp_domain` VALUES (3,'localhost','active','2026-07-01 15:00:40','2026-07-01 15:00:40');
/*!40000 ALTER TABLE `pp_domain` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pp_env`
--

DROP TABLE IF EXISTS `pp_env`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pp_env` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `brand_id` varchar(15) NOT NULL DEFAULT 'both',
  `option_name` varchar(50) NOT NULL,
  `value` text NOT NULL,
  `created_date` varchar(20) NOT NULL,
  `updated_date` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `option_name` (`option_name`),
  KEY `brand_id` (`brand_id`),
  KEY `created_date` (`created_date`,`updated_date`)
) ENGINE=InnoDB AUTO_INCREMENT=73 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pp_env`
--

LOCK TABLES `pp_env` WRITE;
/*!40000 ALTER TABLE `pp_env` DISABLE KEYS */;
INSERT INTO `pp_env` VALUES (1,'both','geneal-application-settings-paymentPath','--','2026-06-23 14:26:38','2026-06-23 14:26:38'),(2,'both','geneal-application-settings-invoicePath','--','2026-06-23 14:26:38','2026-06-23 14:26:38'),(3,'both','geneal-application-settings-paymentLinkPath','--','2026-06-23 14:26:38','2026-06-23 14:26:38'),(4,'both','geneal-application-settings-adminPath','--','2026-06-23 14:26:38','2026-06-23 14:26:38'),(5,'both','geneal-application-settings-cronPath','--','2026-06-23 14:26:38','2026-06-23 14:26:38'),(6,'both','geneal-application-settings-homepageRedirect','--','2026-06-23 14:26:39','2026-06-23 14:26:39'),(7,'both','last-cron-invocation','--','2026-06-23 14:29:21','2026-06-23 14:29:21'),(8,'9675068878','payment-link-default-currency','BDT','2026-06-23 14:30:14','2026-07-02 05:31:38'),(9,'9675068878','twenty-six-enable_bg_image','--','2026-06-23 14:30:25','2026-06-23 14:30:25'),(10,'9675068878','twenty-six-background_image','--','2026-06-23 14:30:25','2026-06-23 14:30:25'),(11,'9675068878','twenty-six-watermark_text','--','2026-06-23 14:30:25','2026-06-23 14:30:25'),(12,'9675068878','twenty-six-seo_title','--','2026-06-23 14:30:25','2026-06-23 14:30:25'),(13,'9675068878','twenty-six-seo_description','--','2026-06-23 14:30:25','2026-06-23 14:30:25'),(14,'9675068878','twenty-six-seo_keywords','--','2026-06-23 14:30:25','2026-06-23 14:30:25'),(15,'9675068878','twenty-six-analytics_code','--','2026-06-23 14:30:25','2026-06-23 14:30:25'),(16,'9675068878','twenty-six-primary_color','--','2026-06-23 14:30:25','2026-06-23 14:30:25'),(17,'9675068878','twenty-six-text_color','--','2026-06-23 14:30:25','2026-06-23 14:30:25'),(18,'both','binance-personal-pp_amount821057890218797514891909','0.08','2026-06-25 12:56:54','2026-06-25 12:56:54'),(19,'both','binance-personal-pp_amount821057890218797514891909','--','2026-06-25 12:57:29','2026-06-25 12:57:29'),(20,'both','binance-personal-pp_amount821057890218797514891909','--','2026-06-25 12:57:32','2026-06-25 12:57:32'),(21,'both','binance-personal-pp_amount821057890218797514891909','--','2026-06-25 12:57:35','2026-06-25 12:57:35'),(22,'both','binance-personal-pp_amount821057890218797514891909','--','2026-06-25 12:57:38','2026-06-25 12:57:38'),(23,'both','binance-personal-pp_amount821057890218797514891909','0.08','2026-06-25 13:02:44','2026-06-25 13:02:44'),(24,'both','binance-personal-pp_amount821057890218797514891909','--','2026-06-25 13:02:52','2026-06-25 13:02:52'),(25,'both','binance-personal-pp_amount821057890218797514891909','0.08','2026-06-25 13:03:39','2026-06-25 13:03:39'),(26,'both','binance-personal-pp_amount821057890218797514891909','0.08','2026-06-25 13:03:42','2026-06-25 13:03:42'),(27,'both','binance-personal-pp_amount821057890218797514891909','--','2026-06-25 13:03:48','2026-06-25 13:03:48'),(28,'both','binance-personal-pp_amount821057890218797514891909','--','2026-06-25 13:03:54','2026-06-25 13:03:54'),(29,'both','binance-personal-pp_amount821057890218797514891909','--','2026-06-25 13:03:57','2026-06-25 13:03:57'),(30,'both','binance-personal-pp_amount821057890218797514891909','0.08','2026-06-25 13:09:26','2026-06-25 13:09:26'),(31,'both','binance-personal-pp_amount821057890218797514891909','--','2026-06-25 13:09:31','2026-06-25 13:09:31'),(32,'both','binance-personal-pp_amount821057890218797514891909','--','2026-06-25 13:09:34','2026-06-25 13:09:34'),(33,'both','binance-personal-pp_amount821057890218797514891909','0.08','2026-06-25 13:09:36','2026-06-25 13:09:36'),(34,'both','binance-personal-pp_amount821057890218797514891909','0.08','2026-06-25 13:09:36','2026-06-25 13:09:36'),(35,'both','binance-personal-pp_amount821057890218797514891909','0.08','2026-06-25 13:09:37','2026-06-25 13:09:37'),(36,'both','binance-personal-pp_amount883754898893744340600845','0.77','2026-06-25 13:09:44','2026-06-25 13:09:44'),(37,'both','binance-personal-pp_amount883754898893744340600845','--','2026-06-25 13:09:50','2026-06-25 13:09:50'),(38,'both','binance-personal-pp_amount883754898893744340600845','0.77','2026-06-25 13:10:11','2026-06-25 13:10:11'),(39,'both','binance-personal-pp_amount883754898893744340600845','--','2026-06-25 13:10:15','2026-06-25 13:10:15'),(40,'both','binance-personal-pp_amount883754898893744340600845','--','2026-06-25 13:10:43','2026-06-25 13:10:43'),(41,'both','binance-personal-pp_amount883754898893744340600845','--','2026-06-25 13:10:46','2026-06-25 13:10:46'),(42,'both','binance-personal-pp_amount883754898893744340600845','0.77','2026-06-25 13:12:14','2026-06-25 13:12:14'),(43,'both','binance-personal-pp_amount883754898893744340600845','0.77','2026-06-25 13:14:51','2026-06-25 13:14:51'),(44,'both','binance-personal-pp_amount883754898893744340600845','--','2026-06-25 13:15:01','2026-06-25 13:15:01'),(45,'both','binance-personal-pp_amount883754898893744340600845','0.77','2026-06-25 13:15:04','2026-06-25 13:15:04'),(46,'both','binance-personal-pp_amount883754898893744340600845','--','2026-06-25 13:15:06','2026-06-25 13:15:06'),(47,'both','binance-personal-pp_amount452006586917461507765172','0.77','2026-06-25 13:21:09','2026-06-25 13:21:09'),(48,'both','binance-personal-pp_amount452006586917461507765172','--','2026-06-25 13:21:16','2026-06-25 13:21:16'),(49,'both','binance-personal-pp_amount942665562609718207250295','0.77','2026-06-25 13:29:32','2026-06-25 13:29:32'),(50,'both','binance-personal-pp_amount942665562609718207250295','--','2026-06-25 13:29:35','2026-06-25 13:29:35'),(51,'both','binance-personal-pp_amount299053952784694967750179','0.77','2026-06-25 13:36:21','2026-06-25 13:36:21'),(52,'both','binance-personal-pp_amount299053952784694967750179','--','2026-06-25 13:36:28','2026-06-25 13:36:28'),(53,'both','binance-personal-pp_amount299053952784694967750179','0.77','2026-06-25 13:46:37','2026-06-25 13:46:37'),(54,'both','binance-personal-pp_amount299053952784694967750179','--','2026-06-25 13:46:40','2026-06-25 13:46:40'),(55,'both','binance-personal-pp_amount948155904792587142776219','0.08','2026-06-25 13:49:53','2026-06-25 13:49:53'),(56,'both','binance-personal-pp_amount948155904792587142776219','--','2026-06-25 13:49:58','2026-06-25 13:49:58'),(57,'both','binance-personal-pp_amount948155904792587142776219','--','2026-06-25 13:50:02','2026-06-25 13:50:02'),(58,'both','geneal-application-settings-default_timezone','--','2026-06-27 17:21:10','2026-06-27 17:21:10'),(59,'both','geneal-application-settings-webhook_attempts_limit','--','2026-06-27 17:21:10','2026-06-27 17:21:10'),(60,'both','system-settings-update_channel','--','2026-06-27 17:23:06','2026-06-27 17:23:06'),(61,'both','last-update-version-name','--','2026-06-27 17:23:06','2026-06-27 17:23:06'),(62,'both','last-update-version','--','2026-06-27 17:23:06','2026-06-27 17:23:06'),(63,'both','last-auto-update-check','2026-06-27 17:23:11','2026-06-27 17:23:06','2026-06-27 17:23:11'),(64,'both','system-settings-automatic_update','--','2026-06-27 17:23:06','2026-06-27 17:23:06'),(65,'both','system-settings-create_backup','--','2026-06-27 17:23:06','2026-06-27 17:23:06'),(66,'both','payment-link-default-logo','--','2026-06-28 04:31:46','2026-06-28 04:31:46'),(67,'9675068878','payment-link-default-logo','http://localhost/piprapay/pp-media/storage/c2R8GjRf9cwN5j1mhLya2gsJ5hPiK7.jpg','2026-06-28 04:32:10','2026-06-29 12:46:59'),(68,'both','payment-link-default-logo','--','2026-06-28 04:32:34','2026-06-28 04:32:34'),(69,'both','payment-link-default-logo','--','2026-06-28 04:32:35','2026-06-28 04:32:35'),(70,'both','payment-link-default-logo','--','2026-06-28 04:32:37','2026-06-28 04:32:37'),(71,'both','payment-link-default-logo','--','2026-06-28 04:32:57','2026-06-28 04:32:57'),(72,'both','payment-link-default-logo','--','2026-06-28 04:32:57','2026-06-28 04:32:57');
/*!40000 ALTER TABLE `pp_env` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pp_faq`
--

DROP TABLE IF EXISTS `pp_faq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pp_faq` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `brand_id` varchar(15) NOT NULL,
  `title` text NOT NULL,
  `description` text NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_date` varchar(20) NOT NULL,
  `updated_date` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `brand_id` (`brand_id`,`created_date`,`updated_date`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pp_faq`
--

LOCK TABLES `pp_faq` WRITE;
/*!40000 ALTER TABLE `pp_faq` DISABLE KEYS */;
INSERT INTO `pp_faq` VALUES (1,'9675068878','what is its name','sakib','active','2026-07-01 14:58:18','2026-07-01 14:58:18');
/*!40000 ALTER TABLE `pp_faq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pp_gateways`
--

DROP TABLE IF EXISTS `pp_gateways`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pp_gateways` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gateway_id` varchar(15) NOT NULL,
  `brand_id` varchar(15) NOT NULL,
  `slug` varchar(40) NOT NULL DEFAULT '--',
  `name` text DEFAULT NULL,
  `display` text DEFAULT NULL,
  `logo` text DEFAULT NULL,
  `currency` varchar(6) NOT NULL,
  `min_allow` decimal(20,8) NOT NULL DEFAULT 0.00000000,
  `max_allow` decimal(20,8) NOT NULL DEFAULT 0.00000000,
  `fixed_discount` decimal(20,8) NOT NULL DEFAULT 0.00000000,
  `percentage_discount` decimal(20,8) NOT NULL DEFAULT 0.00000000,
  `fixed_charge` decimal(20,8) NOT NULL DEFAULT 0.00000000,
  `percentage_charge` decimal(20,8) NOT NULL DEFAULT 0.00000000,
  `primary_color` text DEFAULT NULL,
  `text_color` text DEFAULT NULL,
  `btn_color` text DEFAULT NULL,
  `btn_text_color` text DEFAULT NULL,
  `tab` enum('mfs','bank','global') NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_date` varchar(20) NOT NULL,
  `updated_date` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `brand_id` (`brand_id`,`slug`),
  KEY `g_id` (`gateway_id`),
  KEY `created_date` (`created_date`,`updated_date`),
  KEY `tab` (`tab`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pp_gateways`
--

LOCK TABLES `pp_gateways` WRITE;
/*!40000 ALTER TABLE `pp_gateways` DISABLE KEYS */;
INSERT INTO `pp_gateways` VALUES (1,'6020478430','9675068878','bkash-personal','Bkash Personal','Bkash Personal','http://localhost/profess0r-shop/pp-content/pp-modules/pp-gateways/bkash-personal/assets/logo.jpg','BDT',1.00000000,1000000.00000000,0.00000000,0.00000000,0.00000000,0.00000000,'#d12053','#ffffff','#d12053','#ffffff','mfs','active','2026-06-23 14:59:12','2026-06-27 11:22:07'),(3,'1903454180','9675068878','nagad-personal','Nagad Personal','Nagad Personal','http://localhost/profess0r-shop/pp-content/pp-modules/pp-gateways/nagad-personal/assets/logo.jpg','BDT',0.00000000,0.00000000,0.00000000,0.00000000,0.00000000,0.00000000,'#ed1c24','#ffffff','#ed1c24','#ffffff','mfs','active','2026-06-23 16:40:28','2026-06-29 20:21:06'),(4,'4434196823','9675068878','rocket-personal','Rocket Personal','Rocket Personal','http://localhost/profess0r-shop/pp-content/pp-modules/pp-gateways/rocket-personal/assets/logo.jpg','BDT',0.00000000,0.00000000,0.00000000,0.00000000,0.00000000,0.00000000,'#8b3392','#ffffff','#8b3392','#ffffff','mfs','active','2026-06-23 19:18:41','2026-06-25 11:28:04'),(5,'9414935650','9675068878','binance-personal','Binance Personal','Binance Personal','http://localhost/profess0r-shop/pp-content/pp-modules/pp-gateways/binance-personal/assets/logo.jpg','USDT',0.00000000,0.00000000,0.00000000,0.00000000,0.00000000,0.00000000,'#f0b90b','#000000','#f0b90b','#000000','global','active','2026-06-25 12:55:53','2026-07-01 20:15:30'),(8,'1351760010','9675068878','--','City Bank','City Bank PLC','http://localhost/piprapay/pp-media/storage/5YOPXTVZtFRxmysPEurjVDj0cdJEtm.jpg','BDT',0.00000000,0.00000000,0.00000000,0.00000000,0.00000000,0.00000000,'#000000','#000000','#000000','#000000','bank','active','2026-06-25 13:28:42','2026-06-28 03:59:59'),(9,'7107235275','9675068878','bkash-personal','Bkash Personal','Bkash Personal','http://localhost/piprapay/pp-content/pp-modules/pp-gateways/bkash-personal/assets/images/bkash.png','BDT',0.00000000,0.00000000,0.00000000,0.00000000,0.00000000,0.00000000,'#d12053','#ffffff','#d12053','#ffffff','mfs','active','2026-06-27 08:15:35','2026-06-27 10:40:59'),(10,'6745315581','9675068878','pathaopay-personal','PathaoPay Personal','PathaoPay Personal','http://localhost/piprapay/pp-content/pp-modules/pp-gateways/pathaopay-personal/assets/logo.jpg','BDT',0.00000000,0.00000000,0.00000000,0.00000000,0.00000000,0.00000000,'#3b82de','#ffffff','#3b82de','#ffffff','mfs','inactive','2026-06-27 11:11:32','2026-06-28 04:37:04'),(11,'1043698564','9675068878','cellfin-personal','Cellfin Personal','Cellfin Personal','http://localhost/piprapay/pp-content/pp-modules/pp-gateways/cellfin-personal/assets/logo.jpg','BDT',0.00000000,0.00000000,0.00000000,0.00000000,0.00000000,0.00000000,'#00803d','#ffffff','#00803d','#ffffff','mfs','active','2026-06-29 13:28:46','2026-06-29 13:29:14'),(13,'3458563186','9675068878','upay-personal','Upay Personal','Upay Personal','http://localhost/piprapay/pp-content/pp-modules/pp-gateways/upay-personal/assets/logo.jpg','BDT',0.00000000,0.00000000,0.00000000,0.00000000,0.00000000,0.00000000,'#0d56a3','#ffffff','#0d56a3','#ffffff','mfs','active','2026-06-29 13:29:59','2026-06-30 05:19:21');
/*!40000 ALTER TABLE `pp_gateways` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pp_gateways_parameter`
--

DROP TABLE IF EXISTS `pp_gateways_parameter`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pp_gateways_parameter` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `brand_id` varchar(15) NOT NULL,
  `gateway_id` varchar(15) NOT NULL,
  `option_name` varchar(50) NOT NULL,
  `value` text NOT NULL,
  `created_date` varchar(20) NOT NULL,
  `updated_date` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `slug` (`gateway_id`,`option_name`),
  KEY `brand_id` (`brand_id`),
  KEY `created_date` (`created_date`,`updated_date`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pp_gateways_parameter`
--

LOCK TABLES `pp_gateways_parameter` WRITE;
/*!40000 ALTER TABLE `pp_gateways_parameter` DISABLE KEYS */;
INSERT INTO `pp_gateways_parameter` VALUES (1,'9675068878','6020478430','mobile_number','01633110861','2026-06-23 14:59:51','2026-06-27 11:22:07'),(2,'9675068878','6020478430','pending_payment','disable','2026-06-23 14:59:51','2026-06-27 11:22:07'),(9,'9675068878','1903454180','mobile_number','01603067785','2026-06-23 16:40:39','2026-06-29 20:21:06'),(10,'9675068878','1903454180','pending_payment','disable','2026-06-23 16:40:39','2026-06-29 20:21:06'),(11,'9675068878','4434196823','mobile_number','01603067785','2026-06-23 19:19:00','2026-06-25 11:28:04'),(12,'9675068878','4434196823','pending_payment','disable','2026-06-23 19:19:00','2026-06-25 11:28:04'),(13,'9675068878','4434196823','verification_method','trxid','2026-06-23 19:49:30','2026-06-25 11:28:04'),(14,'9675068878','6020478430','verification_method','number_amount','2026-06-23 20:30:07','2026-06-27 11:22:07'),(15,'9675068878','1903454180','verification_method','number_amount','2026-06-24 08:32:00','2026-06-29 20:21:06'),(16,'9675068878','6020478430','qr_code','http://localhost/profess0r-shop/pp-media/storage/DCkHwjSEDdY65BoiMP7QO4FgkBzXu1.jpg','2026-06-24 18:36:07','2026-06-24 18:51:06'),(17,'9675068878','9414935650','binance_uid','1204323901','2026-06-25 12:56:45','2026-06-26 16:42:18'),(18,'9675068878','9414935650','api_key','DUMMY_BINANCE_API_KEY_HERE','2026-06-25 12:56:46','2026-06-26 16:42:18'),(19,'9675068878','9414935650','secret_key','DUMMY_BINANCE_SECRET_KEY_HERE','2026-06-25 12:56:46','2026-06-26 16:42:18'),(20,'9675068878','9414935650','conversion_rate','130','2026-06-25 12:56:46','2026-06-26 16:42:18'),(21,'9675068878','9414935650','qr_code','http://localhost/piprapay/pp-media/storage/yr9Z0Lo0yJCE3Cd5GI46iNO1Kr0ixJ.png','2026-06-25 12:56:46','2026-06-26 16:42:18'),(28,'9675068878','1351760010','bank_name','City Bank PLC','2026-06-25 13:28:42','2026-06-28 03:59:59'),(29,'9675068878','1351760010','account_holder_name','Md Sakibul Hasan Talukder','2026-06-25 13:28:42','2026-06-28 03:59:59'),(30,'9675068878','1351760010','account_number','DUMMY_BANK_ACCOUNT','2026-06-25 13:28:42','2026-06-28 03:59:59'),(31,'9675068878','1351760010','branch_name','CUMILLA BRANCH','2026-06-25 13:28:42','2026-06-28 03:59:59'),(32,'9675068878','1351760010','routing_number','225191152','2026-06-25 13:28:42','2026-06-28 03:59:59'),(33,'9675068878','1351760010','swift_code','--','2026-06-25 13:28:42','2026-06-28 03:59:59'),(34,'9675068878','7107235275','verification_method','number_amount','2026-06-27 08:16:08','2026-06-27 08:16:08'),(35,'9675068878','7107235275','mobile_number','01603067785','2026-06-27 08:16:08','2026-06-27 08:16:08'),(36,'9675068878','7107235275','pending_payment','disable','2026-06-27 08:16:08','2026-06-27 08:16:08'),(37,'9675068878','6745315581','verification_method','trxid','2026-06-27 11:11:52','2026-06-27 11:11:52'),(38,'9675068878','6745315581','mobile_number','01603067785','2026-06-27 11:11:52','2026-06-27 11:11:52'),(39,'9675068878','6745315581','pending_payment','disable','2026-06-27 11:11:52','2026-06-27 11:11:52'),(40,'9675068878','1043698564','verification_method','trxid','2026-06-29 13:29:14','2026-06-29 13:29:14'),(41,'9675068878','1043698564','mobile_number','01603067785','2026-06-29 13:29:14','2026-06-29 13:29:14'),(42,'9675068878','1043698564','pending_payment','disable','2026-06-29 13:29:14','2026-06-29 13:29:14'),(43,'9675068878','3458563186','verification_method','number_amount','2026-06-29 13:30:15','2026-06-30 05:19:21'),(44,'9675068878','3458563186','mobile_number','01603067785','2026-06-29 13:30:15','2026-06-30 05:19:21'),(45,'9675068878','3458563186','pending_payment','disable','2026-06-29 13:30:15','2026-06-30 05:19:21');
/*!40000 ALTER TABLE `pp_gateways_parameter` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pp_invoice`
--

DROP TABLE IF EXISTS `pp_invoice`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pp_invoice` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ref` varchar(30) NOT NULL,
  `brand_id` varchar(15) NOT NULL,
  `customer_info` text DEFAULT NULL,
  `gateway_id` varchar(15) NOT NULL DEFAULT '--',
  `currency` text NOT NULL,
  `due_date` text DEFAULT NULL,
  `shipping` varchar(250) NOT NULL DEFAULT '0',
  `status` enum('paid','unpaid','refunded','canceled') NOT NULL,
  `note` text DEFAULT NULL,
  `private_note` text DEFAULT NULL,
  `created_date` varchar(20) NOT NULL,
  `updated_date` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ref` (`ref`,`brand_id`),
  KEY `created_date` (`created_date`,`updated_date`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pp_invoice`
--

LOCK TABLES `pp_invoice` WRITE;
/*!40000 ALTER TABLE `pp_invoice` DISABLE KEYS */;
/*!40000 ALTER TABLE `pp_invoice` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pp_invoice_items`
--

DROP TABLE IF EXISTS `pp_invoice_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pp_invoice_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `brand_id` varchar(15) NOT NULL,
  `invoice_id` varchar(30) NOT NULL,
  `description` text DEFAULT NULL,
  `amount` decimal(20,8) NOT NULL DEFAULT 0.00000000,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `discount` decimal(20,8) NOT NULL DEFAULT 0.00000000,
  `vat` decimal(20,8) NOT NULL DEFAULT 0.00000000,
  `created_date` varchar(20) NOT NULL,
  `updated_date` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `invoice_id` (`invoice_id`),
  KEY `brand_id` (`brand_id`),
  KEY `created_date` (`created_date`,`updated_date`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pp_invoice_items`
--

LOCK TABLES `pp_invoice_items` WRITE;
/*!40000 ALTER TABLE `pp_invoice_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `pp_invoice_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pp_payment_link`
--

DROP TABLE IF EXISTS `pp_payment_link`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pp_payment_link` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ref` varchar(30) NOT NULL,
  `brand_id` varchar(15) NOT NULL,
  `product_info` text NOT NULL,
  `amount` decimal(20,8) NOT NULL DEFAULT 0.00000000,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `currency` text NOT NULL,
  `expired_date` text NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_date` varchar(20) NOT NULL,
  `updated_date` varchar(20) NOT NULL,
  `return_url` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ref` (`ref`,`brand_id`,`created_date`,`updated_date`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pp_payment_link`
--

LOCK TABLES `pp_payment_link` WRITE;
/*!40000 ALTER TABLE `pp_payment_link` DISABLE KEYS */;
/*!40000 ALTER TABLE `pp_payment_link` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pp_payment_link_field`
--

DROP TABLE IF EXISTS `pp_payment_link_field`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pp_payment_link_field` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `paymentLinkID` varchar(30) NOT NULL,
  `formType` text NOT NULL,
  `fieldName` text NOT NULL,
  `value` text NOT NULL,
  `required` enum('true','false') NOT NULL DEFAULT 'true',
  `created_date` varchar(20) NOT NULL,
  `updated_date` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `paymentLinkID` (`paymentLinkID`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pp_payment_link_field`
--

LOCK TABLES `pp_payment_link_field` WRITE;
/*!40000 ALTER TABLE `pp_payment_link_field` DISABLE KEYS */;
/*!40000 ALTER TABLE `pp_payment_link_field` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pp_permission`
--

DROP TABLE IF EXISTS `pp_permission`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pp_permission` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `brand_id` varchar(15) NOT NULL,
  `a_id` varchar(15) NOT NULL,
  `permission` text NOT NULL,
  `status` enum('active','suspend') NOT NULL DEFAULT 'active',
  `created_date` varchar(20) NOT NULL,
  `updated_date` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `brand_id` (`brand_id`,`a_id`,`created_date`,`updated_date`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pp_permission`
--

LOCK TABLES `pp_permission` WRITE;
/*!40000 ALTER TABLE `pp_permission` DISABLE KEYS */;
INSERT INTO `pp_permission` VALUES (1,'9675068878','0242809184','{\"resources\":{\"customers\":{\"create\":true,\"edit\":true,\"delete\":true},\"transaction\":{\"edit\":true,\"delete\":true,\"approve\":true,\"cancel\":true,\"refund\":true,\"send_ipn\":true},\"invoice\":{\"create\":true,\"edit\":true,\"delete\":true},\"payment_link\":{\"create\":true,\"edit\":true,\"delete\":true},\"gateways\":{\"create\":true,\"edit\":true,\"delete\":true},\"addons\":{\"create\":true,\"edit\":true,\"delete\":true},\"brand_settings\":{\"view\":true,\"edit\":true},\"api_settings\":{\"view\":true,\"create\":true,\"edit\":true,\"delete\":true},\"theme_settings\":{\"view\":true,\"edit\":true},\"faq_settings\":{\"view\":true,\"create\":true,\"edit\":true,\"delete\":true},\"currency_settings\":{\"view\":true,\"sync_rate\":true,\"import\":true,\"edit\":true},\"sms_data\":{\"create\":true,\"edit\":true,\"delete\":true},\"device\":{\"connect\":true,\"delete\":true,\"balance_verification_for\":true},\"brands\":{\"create\":true,\"edit\":true,\"delete\":true},\"staff\":{\"create\":true,\"edit\":true,\"delete\":true,\"assign_brand_to\":true,\"edit_permission\":true,\"view_permission_list\":true,\"delete_permission_of\":true},\"domains\":{\"whitelist\":true,\"edit\":true,\"delete\":true},\"system_settings\":{\"manage_general\":true,\"manage_cron\":true,\"manage_update\":true,\"manage_import\":true}},\"pages\":{\"dashboard\":true,\"reports\":true,\"customers\":true,\"transaction\":true,\"invoice\":true,\"payment_link\":true,\"gateways\":true,\"addons\":true,\"brand_settings\":true,\"sms_data\":true,\"device\":true,\"brands\":true,\"staff_management\":true,\"domains\":true,\"system_settings\":true}}','active','2026-06-23 14:26:36','2026-06-23 14:26:36');
/*!40000 ALTER TABLE `pp_permission` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pp_sms_data`
--

DROP TABLE IF EXISTS `pp_sms_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pp_sms_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `source` enum('app','web') NOT NULL DEFAULT 'web',
  `device_id` varchar(15) NOT NULL,
  `sender` varchar(15) NOT NULL DEFAULT '--',
  `sender_key` varchar(15) NOT NULL,
  `simslot` text DEFAULT NULL,
  `number` varchar(20) NOT NULL DEFAULT '--',
  `amount` decimal(20,8) NOT NULL DEFAULT 0.00000000,
  `currency` varchar(10) NOT NULL DEFAULT '--',
  `trx_id` varchar(100) NOT NULL DEFAULT '--',
  `balance` varchar(70) NOT NULL DEFAULT '--',
  `message` text DEFAULT NULL,
  `reason` text DEFAULT NULL,
  `type` enum('Personal','Agent','Merchant') NOT NULL DEFAULT 'Personal',
  `entry_type` enum('manual','automatic') NOT NULL DEFAULT 'automatic',
  `edit_status` enum('done','pending') NOT NULL DEFAULT 'pending',
  `status` enum('approved','awaiting-review','used','error') NOT NULL DEFAULT 'approved',
  `created_date` varchar(20) NOT NULL,
  `updated_date` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `device_id` (`sender_key`,`amount`,`trx_id`),
  KEY `created_date` (`created_date`,`updated_date`),
  KEY `number` (`number`),
  KEY `balance` (`balance`),
  KEY `device_id_2` (`device_id`),
  KEY `sender` (`sender`),
  KEY `source` (`source`),
  KEY `type` (`type`,`entry_type`,`edit_status`,`status`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pp_sms_data`
--

LOCK TABLES `pp_sms_data` WRITE;
/*!40000 ALTER TABLE `pp_sms_data` DISABLE KEYS */;
INSERT INTO `pp_sms_data` VALUES (1,'web','--','--','bkash','--','01712345678',25.00000000,'BDT','ABC123XYZ5','1025.00','You have received Tk 25.00 from 01712345678. Ref . Fee Tk 0.00. Balance Tk 1025.00. TrxID ABC123XYZ5 at 25/06/2026 12:54','--','Personal','automatic','pending','approved','2026-06-25 06:54:03','2026-06-25 06:54:03'),(3,'web','--','--','bkash','--','01712345675',25.00000000,'BDT','BBC123XYZ5','1025.00','You have received Tk 25.00 from 01712345675. Ref . Fee Tk 0.00. Balance Tk 1025.00. TrxID BBC123XYZ5 at 25/06/2026 12:54','--','Personal','automatic','pending','awaiting-review','2026-06-25 11:04:05','2026-06-25 11:04:05');
/*!40000 ALTER TABLE `pp_sms_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pp_transaction`
--

DROP TABLE IF EXISTS `pp_transaction`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pp_transaction` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `brand_id` varchar(15) NOT NULL,
  `source` enum('invoice','payment-link','payment-link-default','api') NOT NULL DEFAULT 'api',
  `ref` varchar(30) NOT NULL,
  `customer_info` text NOT NULL,
  `amount` decimal(20,8) NOT NULL DEFAULT 0.00000000,
  `processing_fee` decimal(20,8) NOT NULL DEFAULT 0.00000000,
  `discount_amount` decimal(20,8) NOT NULL DEFAULT 0.00000000,
  `local_net_amount` decimal(20,8) NOT NULL DEFAULT 0.00000000,
  `currency` text DEFAULT NULL,
  `local_currency` text DEFAULT NULL,
  `sender` varchar(50) NOT NULL DEFAULT '--',
  `trx_id` varchar(70) NOT NULL DEFAULT '--',
  `trx_slip` text DEFAULT NULL,
  `gateway_id` varchar(50) NOT NULL DEFAULT '--',
  `sender_key` varchar(50) NOT NULL DEFAULT '--',
  `sender_type` varchar(11) NOT NULL,
  `source_info` text DEFAULT NULL,
  `metadata` text DEFAULT NULL,
  `status` enum('completed','pending','refunded','initiated','canceled') NOT NULL DEFAULT 'initiated',
  `return_url` text DEFAULT NULL,
  `webhook_url` text DEFAULT NULL,
  `created_date` varchar(20) NOT NULL,
  `updated_date` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `brand_id` (`brand_id`,`ref`,`trx_id`),
  KEY `payment_method_id` (`gateway_id`,`sender_key`),
  KEY `gateway_slug` (`sender_key`),
  KEY `created_date` (`created_date`,`updated_date`),
  KEY `sender` (`sender`),
  KEY `source` (`source`,`status`),
  KEY `sender_type` (`sender_type`)
) ENGINE=InnoDB AUTO_INCREMENT=246 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pp_transaction`
--

LOCK TABLES `pp_transaction` WRITE;
/*!40000 ALTER TABLE `pp_transaction` DISABLE KEYS */;
/*!40000 ALTER TABLE `pp_transaction` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pp_webhook_log`
--

DROP TABLE IF EXISTS `pp_webhook_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pp_webhook_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ref` varchar(15) NOT NULL,
  `brand_id` varchar(15) NOT NULL,
  `payload` text NOT NULL,
  `url` text NOT NULL,
  `attempts` int(11) NOT NULL DEFAULT 0,
  `response_body` text DEFAULT NULL,
  `http_code` text DEFAULT NULL,
  `status` enum('completed','pending','canceled') NOT NULL DEFAULT 'pending',
  `created_date` varchar(20) NOT NULL,
  `updated_date` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ref` (`ref`),
  KEY `brand_id` (`brand_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pp_webhook_log`
--

LOCK TABLES `pp_webhook_log` WRITE;
/*!40000 ALTER TABLE `pp_webhook_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `pp_webhook_log` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-07-02 11:42:44



-- Auto Updater Tables
CREATE TABLE IF NOT EXISTS `pp_update_logs` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `version` VARCHAR(50) NOT NULL,
    `status` VARCHAR(50) NOT NULL,
    `log` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `pp_migrations` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `migration` VARCHAR(255) NOT NULL,
    `executed_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `pp_env` (`brand_id`, `option_name`, `value`, `created_date`) VALUES ('both', 'pp_version', '1.3.0', NOW());

--
-- Table structure for table `pp_notifications`
--

DROP TABLE IF EXISTS `pp_notifications`;
CREATE TABLE `pp_notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `type` enum('success','warning','error','info') NOT NULL DEFAULT 'info',
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

