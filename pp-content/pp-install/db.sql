-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Feb 16, 2026 at 02:44 PM
-- Server version: 10.9.8-MariaDB
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `professorpay`
--

-- --------------------------------------------------------

--
-- Table structure for table `pp_addon`
--

CREATE TABLE `pp_addon` (
  `id` int(11) NOT NULL,
  `addon_id` varchar(15) NOT NULL,
  `slug` varchar(40) NOT NULL DEFAULT '--',
  `name` text NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_date` varchar(20) NOT NULL,
  `updated_date` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pp_addon_parameter`
--

CREATE TABLE `pp_addon_parameter` (
  `id` int(11) NOT NULL,
  `addon_id` varchar(15) NOT NULL,
  `option_name` varchar(50) NOT NULL,
  `value` text NOT NULL,
  `created_date` varchar(20) NOT NULL,
  `updated_date` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pp_admin`
--

CREATE TABLE `pp_admin` (
  `id` int(11) NOT NULL,
  `a_id` varchar(15) NOT NULL,
  `full_name` text NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` text NOT NULL,
  `temp_password` text NULL,
  `reset_limit` varchar(10) NOT NULL DEFAULT '3',
  `status` enum('active','suspend') NOT NULL DEFAULT 'active',
  `role` enum('admin','staff') NOT NULL DEFAULT 'admin',
  `2fa_status` enum('enable','disable') NOT NULL DEFAULT 'disable',
  `2fa_secret` varchar(20) NOT NULL DEFAULT '--',
  `created_date` varchar(20) NOT NULL,
  `updated_date` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pp_api`
--

CREATE TABLE `pp_api` (
  `id` int(11) NOT NULL,
  `brand_id` varchar(15) NOT NULL,
  `name` text NOT NULL,
  `api_key` varchar(60) NOT NULL,
  `expired_date` text NULL,
  `api_scopes` text NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_date` varchar(20) NOT NULL,
  `updated_date` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pp_balance_verification`
--

CREATE TABLE `pp_balance_verification` (
  `id` int(11) NOT NULL,
  `device_id` varchar(15) NOT NULL,
  `sender_key` varchar(15) NOT NULL,
  `type` enum('Personal','Agent','Merchant') NOT NULL DEFAULT 'Personal',
  `current_balance` decimal(20,8) NOT NULL,
  `simslot` varchar(6) NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_date` varchar(20) NOT NULL,
  `updated_date` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pp_brands`
--

CREATE TABLE `pp_brands` (
  `id` int(11) NOT NULL,
  `brand_id` varchar(15) NOT NULL,
  `favicon` text NULL,
  `logo` text NULL,
  `identify_name` varchar(50) NOT NULL DEFAULT 'Default',
  `name` text NULL,
  `support_email_address` text NULL,
  `support_phone_number` text NULL,
  `support_website` text NULL,
  `whatsapp_number` text NULL,
  `telegram` text NULL,
  `facebook_messenger` text NULL,
  `facebook_page` text NULL,
  `theme` varchar(120) NOT NULL DEFAULT 'twenty-six',
  `street_address` text NULL,
  `city_town` text NULL,
  `postal_code` text NULL,
  `country` text NULL,
  `timezone` varchar(150) NOT NULL DEFAULT 'Asia/Dhaka',
  `language` varchar(150) NOT NULL DEFAULT 'en',
  `currency_code` varchar(150) NOT NULL DEFAULT 'BDT',
  `autoExchange` enum('disabled','enabled') NOT NULL DEFAULT 'disabled',
  `payment_tolerance` varchar(150) NOT NULL DEFAULT '0',
  `created_date` varchar(20) NOT NULL DEFAULT '--',
  `updated_date` varchar(20) NOT NULL DEFAULT '--'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pp_browser_log`
--

CREATE TABLE `pp_browser_log` (
  `id` int(11) NOT NULL,
  `a_id` varchar(15) NOT NULL,
  `cookie` varchar(40) NOT NULL,
  `browser` varchar(10) NOT NULL,
  `device` varchar(10) NOT NULL,
  `ip` varchar(15) NOT NULL,
  `status` enum('active','expired') NOT NULL DEFAULT 'active',
  `created_date` varchar(20) NOT NULL,
  `updated_date` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pp_currency`
--

CREATE TABLE `pp_currency` (
  `id` int(11) NOT NULL,
  `brand_id` varchar(15) NOT NULL,
  `code` varchar(6) NOT NULL,
  `symbol` varchar(5) NOT NULL,
  `rate` decimal(20,8) NOT NULL DEFAULT 0.00000000,
  `created_date` varchar(20) NOT NULL,
  `updated_date` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pp_customer`
--

CREATE TABLE `pp_customer` (
  `id` int(11) NOT NULL,
  `ref` varchar(15) NOT NULL,
  `brand_id` varchar(15) NOT NULL,
  `name` text NOT NULL,
  `email` varchar(100) NOT NULL,
  `mobile` varchar(15) NOT NULL,
  `status` enum('active','suspend') NOT NULL DEFAULT 'active',
  `suspend_reason` text NULL,
  `inserted_via` enum('manual','checkout') NOT NULL DEFAULT 'manual',
  `created_date` varchar(20) NOT NULL,
  `updated_date` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pp_device`
--

CREATE TABLE `pp_device` (
  `id` int(11) NOT NULL,
  `d_id` varchar(40) NOT NULL,
  `device_id` varchar(15) NOT NULL,
  `otp` varchar(15) NOT NULL,
  `name` text NULL,
  `model` text NULL,
  `android_level` text NULL,
  `app_version` text NULL,
  `status` enum('processing','used') NOT NULL DEFAULT 'processing',
  `created_date` varchar(20) NOT NULL,
  `updated_date` varchar(20) NOT NULL,
  `last_sync` varchar(20) NOT NULL DEFAULT '--'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pp_domain`
--

CREATE TABLE `pp_domain` (
  `id` int(11) NOT NULL,
  `domain` varchar(50) NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_date` varchar(20) NOT NULL,
  `updated_date` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pp_env`
--

CREATE TABLE `pp_env` (
  `id` int(11) NOT NULL,
  `brand_id` varchar(15) NOT NULL DEFAULT 'both',
  `option_name` varchar(50) NOT NULL,
  `value` text NOT NULL,
  `created_date` varchar(20) NOT NULL,
  `updated_date` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pp_faq`
--

CREATE TABLE `pp_faq` (
  `id` int(11) NOT NULL,
  `brand_id` varchar(15) NOT NULL,
  `title` text NOT NULL,
  `description` text NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_date` varchar(20) NOT NULL,
  `updated_date` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pp_gateways`
--

CREATE TABLE `pp_gateways` (
  `id` int(11) NOT NULL,
  `gateway_id` varchar(15) NOT NULL,
  `brand_id` varchar(15) NOT NULL,
  `slug` varchar(40) NOT NULL DEFAULT '--',
  `name` text NULL,
  `display` text NULL,
  `logo` text NULL,
  `currency` varchar(6) NOT NULL,
  `min_allow` decimal(20,8) NOT NULL DEFAULT 0.00000000,
  `max_allow` decimal(20,8) NOT NULL DEFAULT 0.00000000,
  `fixed_discount` decimal(20,8) NOT NULL DEFAULT 0.00000000,
  `percentage_discount` decimal(20,8) NOT NULL DEFAULT 0.00000000,
  `fixed_charge` decimal(20,8) NOT NULL DEFAULT 0.00000000,
  `percentage_charge` decimal(20,8) NOT NULL DEFAULT 0.00000000,
  `primary_color` text NULL,
  `text_color` text NULL,
  `btn_color` text NULL,
  `btn_text_color` text NULL,
  `tab` enum('mfs','bank','global') NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_date` varchar(20) NOT NULL,
  `updated_date` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pp_gateways_parameter`
--

CREATE TABLE `pp_gateways_parameter` (
  `id` int(11) NOT NULL,
  `brand_id` varchar(15) NOT NULL,
  `gateway_id` varchar(15) NOT NULL,
  `option_name` varchar(50) NOT NULL,
  `value` text NOT NULL,
  `created_date` varchar(20) NOT NULL,
  `updated_date` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pp_invoice`
--

CREATE TABLE `pp_invoice` (
  `id` int(11) NOT NULL,
  `ref` varchar(30) NOT NULL,
  `brand_id` varchar(15) NOT NULL,
  `customer_info` text NULL,
  `gateway_id` varchar(15) NOT NULL DEFAULT '--',
  `currency` text NOT NULL,
  `due_date` text NULL,
  `shipping` varchar(250) NOT NULL DEFAULT '0',
  `status` enum('paid','unpaid','refunded','canceled') NOT NULL,
  `note` text NULL,
  `private_note` text NULL,
  `created_date` varchar(20) NOT NULL,
  `updated_date` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pp_invoice_items`
--

CREATE TABLE `pp_invoice_items` (
  `id` int(11) NOT NULL,
  `brand_id` varchar(15) NOT NULL,
  `invoice_id` varchar(30) NOT NULL,
  `description` text NULL,
  `amount` decimal(20,8) NOT NULL DEFAULT 0.00000000,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `discount` decimal(20,8) NOT NULL DEFAULT 0.00000000,
  `vat` decimal(20,8) NOT NULL DEFAULT 0.00000000,
  `created_date` varchar(20) NOT NULL,
  `updated_date` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pp_payment_link`
--

CREATE TABLE `pp_payment_link` (
  `id` int(11) NOT NULL,
  `ref` varchar(30) NOT NULL,
  `brand_id` varchar(15) NOT NULL,
  `product_info` text NOT NULL,
  `amount` decimal(20,8) NOT NULL DEFAULT 0.00000000,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `currency` text NOT NULL,
  `expired_date` text NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_date` varchar(20) NOT NULL,
  `updated_date` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pp_payment_link_field`
--

CREATE TABLE `pp_payment_link_field` (
  `id` int(11) NOT NULL,
  `paymentLinkID` varchar(30) NOT NULL,
  `formType` text NOT NULL,
  `fieldName` text NOT NULL,
  `value` text NOT NULL,
  `required` enum('true','false') NOT NULL DEFAULT 'true',
  `created_date` varchar(20) NOT NULL,
  `updated_date` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pp_permission`
--

CREATE TABLE `pp_permission` (
  `id` int(11) NOT NULL,
  `brand_id` varchar(15) NOT NULL,
  `a_id` varchar(15) NOT NULL,
  `permission` text NOT NULL,
  `status` enum('active','suspend') NOT NULL DEFAULT 'active',
  `created_date` varchar(20) NOT NULL,
  `updated_date` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pp_sms_data`
--

CREATE TABLE `pp_sms_data` (
  `id` int(11) NOT NULL,
  `source` enum('app','web') NOT NULL DEFAULT 'web',
  `device_id` varchar(15) NOT NULL,
  `sender` varchar(15) NOT NULL DEFAULT '--',
  `sender_key` varchar(15) NOT NULL,
  `simslot` text NULL,
  `number` varchar(20) NOT NULL DEFAULT '--',
  `amount` decimal(20,8) NOT NULL DEFAULT 0.00000000,
  `currency` varchar(10) NOT NULL DEFAULT '--',
  `trx_id` varchar(100) NOT NULL DEFAULT '--',
  `balance` varchar(70) NOT NULL DEFAULT '--',
  `message` text NULL,
  `reason` text NULL,
  `type` enum('Personal','Agent','Merchant') NOT NULL DEFAULT 'Personal',
  `entry_type` enum('manual','automatic') NOT NULL DEFAULT 'automatic',
  `edit_status` enum('done','pending') NOT NULL DEFAULT 'pending',
  `status` enum('approved','awaiting-review','used','error') NOT NULL DEFAULT 'approved',
  `created_date` varchar(20) NOT NULL,
  `updated_date` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pp_transaction`
--

CREATE TABLE `pp_transaction` (
  `id` int(11) NOT NULL,
  `brand_id` varchar(15) NOT NULL,
  `source` enum('invoice','payment-link','payment-link-default','api') NOT NULL DEFAULT 'api',
  `ref` varchar(30) NOT NULL,
  `customer_info` text NOT NULL,
  `amount` decimal(20,8) NOT NULL DEFAULT 0.00000000,
  `processing_fee` decimal(20,8) NOT NULL DEFAULT 0.00000000,
  `discount_amount` decimal(20,8) NOT NULL DEFAULT 0.00000000,
  `local_net_amount` decimal(20,8) NOT NULL DEFAULT 0.00000000,
  `currency` text NULL,
  `local_currency` text NULL,
  `sender` varchar(50) NOT NULL DEFAULT '--',
  `trx_id` varchar(70) NOT NULL DEFAULT '--',
  `trx_slip` text NULL,
  `gateway_id` varchar(50) NOT NULL DEFAULT '--',
  `sender_key` varchar(50) NOT NULL DEFAULT '--',
  `sender_type` varchar(11) NOT NULL,
  `source_info` text NULL,
  `metadata` text NULL,
  `status` enum('completed','pending','refunded','initiated','canceled') NOT NULL DEFAULT 'initiated',
  `return_url` text NULL,
  `webhook_url` text NULL,
  `created_date` varchar(20) NOT NULL,
  `updated_date` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pp_webhook_log`
--

CREATE TABLE `pp_webhook_log` (
  `id` int(11) NOT NULL,
  `ref` varchar(15) NOT NULL,
  `brand_id` varchar(15) NOT NULL,
  `payload` text NOT NULL,
  `url` text NOT NULL,
  `attempts` int(11) NOT NULL DEFAULT 0,
  `response_body` text NULL,
  `http_code` text NULL,
  `status` enum('completed','pending','canceled') NOT NULL DEFAULT 'pending',
  `created_date` varchar(20) NOT NULL,
  `updated_date` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `pp_addon`
--
ALTER TABLE `pp_addon`
  ADD PRIMARY KEY (`id`),
  ADD KEY `addon_id` (`addon_id`,`status`,`created_date`,`updated_date`);

--
-- Indexes for table `pp_addon_parameter`
--
ALTER TABLE `pp_addon_parameter`
  ADD PRIMARY KEY (`id`),
  ADD KEY `addon_id` (`addon_id`,`option_name`,`created_date`,`updated_date`);

--
-- Indexes for table `pp_admin`
--
ALTER TABLE `pp_admin`
  ADD PRIMARY KEY (`id`),
  ADD KEY `a_id` (`a_id`,`email`),
  ADD KEY `username` (`username`),
  ADD KEY `created_date` (`created_date`,`updated_date`);

--
-- Indexes for table `pp_api`
--
ALTER TABLE `pp_api`
  ADD PRIMARY KEY (`id`),
  ADD KEY `brand_id` (`brand_id`,`api_key`,`created_date`,`updated_date`);

--
-- Indexes for table `pp_balance_verification`
--
ALTER TABLE `pp_balance_verification`
  ADD PRIMARY KEY (`id`),
  ADD KEY `device_id` (`device_id`,`sender_key`,`type`,`created_date`,`updated_date`),
  ADD KEY `simslot` (`simslot`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `pp_brands`
--
ALTER TABLE `pp_brands`
  ADD PRIMARY KEY (`id`),
  ADD KEY `brand_id` (`brand_id`),
  ADD KEY `created_date` (`created_date`,`updated_date`),
  ADD KEY `identify_name` (`identify_name`),
  ADD KEY `autoExchange` (`autoExchange`);

--
-- Indexes for table `pp_browser_log`
--
ALTER TABLE `pp_browser_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `a_id` (`a_id`,`cookie`,`created_date`,`updated_date`),
  ADD KEY `created_date` (`created_date`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `pp_currency`
--
ALTER TABLE `pp_currency`
  ADD PRIMARY KEY (`id`),
  ADD KEY `brand_id` (`brand_id`,`code`,`symbol`);

--
-- Indexes for table `pp_customer`
--
ALTER TABLE `pp_customer`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ref` (`ref`,`brand_id`,`email`,`mobile`),
  ADD KEY `created_date` (`created_date`,`updated_date`),
  ADD KEY `status` (`status`,`inserted_via`);

--
-- Indexes for table `pp_device`
--
ALTER TABLE `pp_device`
  ADD PRIMARY KEY (`id`),
  ADD KEY `device_id` (`device_id`),
  ADD KEY `created_date` (`created_date`,`updated_date`),
  ADD KEY `a_id` (`d_id`),
  ADD KEY `otp` (`otp`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `pp_domain`
--
ALTER TABLE `pp_domain`
  ADD PRIMARY KEY (`id`),
  ADD KEY `domain` (`domain`),
  ADD KEY `created_date` (`created_date`,`updated_date`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `pp_env`
--
ALTER TABLE `pp_env`
  ADD PRIMARY KEY (`id`),
  ADD KEY `option_name` (`option_name`),
  ADD KEY `brand_id` (`brand_id`),
  ADD KEY `created_date` (`created_date`,`updated_date`);

--
-- Indexes for table `pp_faq`
--
ALTER TABLE `pp_faq`
  ADD PRIMARY KEY (`id`),
  ADD KEY `brand_id` (`brand_id`,`created_date`,`updated_date`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `pp_gateways`
--
ALTER TABLE `pp_gateways`
  ADD PRIMARY KEY (`id`),
  ADD KEY `brand_id` (`brand_id`,`slug`),
  ADD KEY `g_id` (`gateway_id`),
  ADD KEY `created_date` (`created_date`,`updated_date`),
  ADD KEY `tab` (`tab`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `pp_gateways_parameter`
--
ALTER TABLE `pp_gateways_parameter`
  ADD PRIMARY KEY (`id`),
  ADD KEY `slug` (`gateway_id`,`option_name`),
  ADD KEY `brand_id` (`brand_id`),
  ADD KEY `created_date` (`created_date`,`updated_date`);

--
-- Indexes for table `pp_invoice`
--
ALTER TABLE `pp_invoice`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ref` (`ref`,`brand_id`),
  ADD KEY `created_date` (`created_date`,`updated_date`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `pp_invoice_items`
--
ALTER TABLE `pp_invoice_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `invoice_id` (`invoice_id`),
  ADD KEY `brand_id` (`brand_id`),
  ADD KEY `created_date` (`created_date`,`updated_date`);

--
-- Indexes for table `pp_payment_link`
--
ALTER TABLE `pp_payment_link`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ref` (`ref`,`brand_id`,`created_date`,`updated_date`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `pp_payment_link_field`
--
ALTER TABLE `pp_payment_link_field`
  ADD PRIMARY KEY (`id`),
  ADD KEY `paymentLinkID` (`paymentLinkID`);

--
-- Indexes for table `pp_permission`
--
ALTER TABLE `pp_permission`
  ADD PRIMARY KEY (`id`),
  ADD KEY `brand_id` (`brand_id`,`a_id`,`created_date`,`updated_date`);

--
-- Indexes for table `pp_sms_data`
--
ALTER TABLE `pp_sms_data`
  ADD PRIMARY KEY (`id`),
  ADD KEY `device_id` (`sender_key`,`amount`,`trx_id`),
  ADD KEY `created_date` (`created_date`,`updated_date`),
  ADD KEY `number` (`number`),
  ADD KEY `balance` (`balance`),
  ADD KEY `device_id_2` (`device_id`),
  ADD KEY `sender` (`sender`),
  ADD KEY `source` (`source`),
  ADD KEY `type` (`type`,`entry_type`,`edit_status`,`status`);

--
-- Indexes for table `pp_transaction`
--
ALTER TABLE `pp_transaction`
  ADD PRIMARY KEY (`id`),
  ADD KEY `brand_id` (`brand_id`,`ref`,`trx_id`),
  ADD KEY `payment_method_id` (`gateway_id`,`sender_key`),
  ADD KEY `gateway_slug` (`sender_key`),
  ADD KEY `created_date` (`created_date`,`updated_date`),
  ADD KEY `sender` (`sender`),
  ADD KEY `source` (`source`,`status`),
  ADD KEY `sender_type` (`sender_type`);

--
-- Indexes for table `pp_webhook_log`
--
ALTER TABLE `pp_webhook_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ref` (`ref`),
  ADD KEY `brand_id` (`brand_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `pp_addon`
--
ALTER TABLE `pp_addon`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pp_addon_parameter`
--
ALTER TABLE `pp_addon_parameter`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pp_admin`
--
ALTER TABLE `pp_admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pp_api`
--
ALTER TABLE `pp_api`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pp_balance_verification`
--
ALTER TABLE `pp_balance_verification`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pp_brands`
--
ALTER TABLE `pp_brands`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pp_browser_log`
--
ALTER TABLE `pp_browser_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pp_currency`
--
ALTER TABLE `pp_currency`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pp_customer`
--
ALTER TABLE `pp_customer`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pp_device`
--
ALTER TABLE `pp_device`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pp_domain`
--
ALTER TABLE `pp_domain`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pp_env`
--
ALTER TABLE `pp_env`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pp_faq`
--
ALTER TABLE `pp_faq`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pp_gateways`
--
ALTER TABLE `pp_gateways`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pp_gateways_parameter`
--
ALTER TABLE `pp_gateways_parameter`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pp_invoice`
--
ALTER TABLE `pp_invoice`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pp_invoice_items`
--
ALTER TABLE `pp_invoice_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pp_payment_link`
--
ALTER TABLE `pp_payment_link`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pp_payment_link_field`
--
ALTER TABLE `pp_payment_link_field`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pp_permission`
--
ALTER TABLE `pp_permission`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pp_sms_data`
--
ALTER TABLE `pp_sms_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pp_transaction`
--
ALTER TABLE `pp_transaction`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pp_webhook_log`
--
ALTER TABLE `pp_webhook_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
