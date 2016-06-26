-- phpMyAdmin SQL Dump
-- version 4.5.4.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: 2016 年 6 月 26 日 17:50
-- サーバのバージョン： 5.6.30
-- PHP Version: 7.0.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `kakeibou`
--

-- --------------------------------------------------------

--
-- テーブルの構造 `accounts`
--

CREATE TABLE `accounts` (
  `aid` tinyint(4) NOT NULL,
  `name` varchar(128) NOT NULL,
  `description` varchar(256) NOT NULL COMMENT 'Ghi chú cho tài khoản'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Phân loại các tài khoản ngân hàng';

--
-- テーブルのデータのダンプ `accounts`
--

INSERT INTO `accounts` (`aid`, `name`, `description`) VALUES
(1, 'Tiền mặt', ''),
(2, 'Yucho', '');

-- --------------------------------------------------------

--
-- テーブルの構造 `bottles`
--

CREATE TABLE `bottles` (
  `bid` tinyint(4) NOT NULL,
  `name` varchar(128) NOT NULL,
  `ratio` tinyint(3) UNSIGNED NOT NULL COMMENT 'tỷ lệ phân chia mặc định'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- テーブルのデータのダンプ `bottles`
--

INSERT INTO `bottles` (`bid`, `name`, `ratio`) VALUES
(0, 'null', 0),
(1, 'Nhu cầu thiết yếu', 10),
(2, 'Tiết kiệm dài hạn', 10),
(3, 'Giáo dục đào tạo', 10),
(4, 'Quỹ tự do tài chính', 55),
(5, 'Hưởng thụ', 10),
(6, 'Giúp đỡ người khác', 5);

-- --------------------------------------------------------

--
-- テーブルの構造 `bottle_records`
--

CREATE TABLE `bottle_records` (
  `iorid` int(11) NOT NULL COMMENT 'id của record thu nhập',
  `bottle_id` tinyint(4) NOT NULL COMMENT 'id của từng bottle',
  `amount` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='ghi lại tỷ lệ phân phối thu nhập vào các lọ';

-- --------------------------------------------------------

--
-- テーブルの構造 `categories`
--

CREATE TABLE `categories` (
  `cid` tinyint(4) NOT NULL,
  `name` varchar(128) NOT NULL,
  `sort` tinyint(3) UNSIGNED NOT NULL,
  `inout_type_id` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'id quy định loại thu, chi, mượn, cho mượn',
  `bottle_id` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'Phân loại sẵn lọ cho danh mục',
  `restrict_delete` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'không cho delete'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- テーブルのデータのダンプ `categories`
--

INSERT INTO `categories` (`cid`, `name`, `sort`, `inout_type_id`, `bottle_id`, `restrict_delete`) VALUES
(1, 'Rút tiền từ tài khoản*', 1, 2, 0, 1),
(2, 'Rút tiền từ tài khoản*', 1, 1, 0, 1),
(3, 'Nạp tiền vô tài khoản*', 2, 1, 0, 1),
(4, 'Nạp tiền vô tài khoản*', 2, 2, 0, 1),
(5, 'Chuyển tiền qua tay*', 3, 2, 0, 1),
(6, 'Chuyển tiền qua tay*', 3, 1, 0, 1),
(21, 'Ăn uống', 0, 2, 0, 0),
(22, 'Đi lại', 0, 2, 0, 0),
(23, 'Giải trí', 0, 2, 0, 0),
(24, 'Góp tiền hàng tháng', 0, 1, 0, 0),
(25, 'Khác', 0, 2, 0, 0),
(26, 'Khác', 0, 1, 0, 0);

-- --------------------------------------------------------

--
-- テーブルの構造 `inout_records`
--

CREATE TABLE `inout_records` (
  `iorid` int(11) NOT NULL,
  `inout_type_id` tinyint(1) UNSIGNED NOT NULL,
  `account_id` tinyint(4) NOT NULL,
  `category_id` tinyint(4) NOT NULL,
  `pair_id` varchar(32) NOT NULL COMMENT 'Chứa unique string có chiều dài 32 ký tự',
  `player` tinyint(4) NOT NULL COMMENT 'id người phụ trách',
  `cash_flow` varchar(24) NOT NULL,
  `amount` int(11) NOT NULL,
  `memo` varchar(128) NOT NULL,
  `date` date NOT NULL,
  `created_on` datetime NOT NULL,
  `created_by` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='ghi chép thu chi';

--
-- テーブルのデータのダンプ `inout_records`
--

INSERT INTO `inout_records` (`iorid`, `inout_type_id`, `account_id`, `category_id`, `pair_id`, `player`, `cash_flow`, `amount`, `memo`, `date`, `created_on`, `created_by`) VALUES
(1, 2, 1, 21, '', 2, 'outgo', -10000, '', '2016-06-26', '2016-06-26 15:20:16', 2),
(2, 1, 2, 3, '490ca58a974f4925347e7484b1c05049', 2, 'deposit', 5000, '', '2016-06-26', '2016-06-26 15:20:30', 2),
(3, 2, 1, 4, '490ca58a974f4925347e7484b1c05049', 2, 'deposit', -5000, '', '2016-06-26', '2016-06-26 15:20:30', 2),
(4, 1, 1, 24, '', 1, 'income', 5000, '', '2016-06-26', '2016-06-26 15:47:15', 2),
(5, 2, 2, 1, 'b3f51eb0e13a3ce9e520c2960104c687', 2, 'drawer', -10000, '', '2016-06-26', '2016-06-26 16:41:16', 2),
(6, 1, 1, 2, 'b3f51eb0e13a3ce9e520c2960104c687', 2, 'drawer', 10000, '', '2016-06-26', '2016-06-26 16:41:16', 2),
(7, 2, 2, 1, '02686317605a218c0d2c1a8136bd3c48', 2, 'drawer', -10000, '', '2016-06-26', '2016-06-26 16:41:40', 2),
(8, 1, 1, 2, '02686317605a218c0d2c1a8136bd3c48', 2, 'drawer', 10000, '', '2016-06-26', '2016-06-26 16:41:40', 2),
(9, 2, 1, 21, '', 2, 'outgo', -40000, '', '2016-06-26', '2016-06-26 16:42:39', 2);

-- --------------------------------------------------------

--
-- テーブルの構造 `inout_types`
--

CREATE TABLE `inout_types` (
  `iotid` tinyint(1) UNSIGNED NOT NULL,
  `name` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Chi tiết phân loại thu chi';

--
-- テーブルのデータのダンプ `inout_types`
--

INSERT INTO `inout_types` (`iotid`, `name`) VALUES
(2, 'Chi'),
(1, 'Thu');

-- --------------------------------------------------------

--
-- テーブルの構造 `settings`
--

CREATE TABLE `settings` (
  `item` varchar(128) NOT NULL,
  `name` varchar(128) NOT NULL,
  `value` varchar(512) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='lưu các thiết lập hệ thống';

--
-- テーブルのデータのダンプ `settings`
--

INSERT INTO `settings` (`item`, `name`, `value`) VALUES
('month_outgo_plans', 'Số tiền dự định chi trong tháng', '"500000"');

-- --------------------------------------------------------

--
-- テーブルの構造 `users`
--

CREATE TABLE `users` (
  `uid` tinyint(4) NOT NULL,
  `username` varchar(32) NOT NULL,
  `fullname` varchar(128) NOT NULL,
  `label` varchar(50) NOT NULL COMMENT 'Class HTML',
  `password` varchar(256) NOT NULL COMMENT 'sha512'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- テーブルのデータのダンプ `users`
--

INSERT INTO `users` (`uid`, `username`, `fullname`, `label`, `password`) VALUES
(1, 'bach', 'Bách', 'label-info', '94bc0c204d0f1ed072f1b20fba90c698caaf70cbb5ec64d8538f8cc00c5dc287a90c654c91282265454c4d13e8b156fb2a296e253e3f546b3786193cbea19ecf'),
(2, 'hiep', 'Hiệp', 'label-warning', '94bc0c204d0f1ed072f1b20fba90c698caaf70cbb5ec64d8538f8cc00c5dc287a90c654c91282265454c4d13e8b156fb2a296e253e3f546b3786193cbea19ecf');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`aid`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `bottles`
--
ALTER TABLE `bottles`
  ADD PRIMARY KEY (`bid`);

--
-- Indexes for table `bottle_records`
--
ALTER TABLE `bottle_records`
  ADD PRIMARY KEY (`iorid`,`bottle_id`),
  ADD UNIQUE KEY `prid` (`iorid`),
  ADD KEY `bid` (`bottle_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`cid`),
  ADD KEY `bid` (`bottle_id`),
  ADD KEY `iotid` (`inout_type_id`);

--
-- Indexes for table `inout_records`
--
ALTER TABLE `inout_records`
  ADD PRIMARY KEY (`iorid`),
  ADD KEY `category_id` (`category_id`) USING BTREE,
  ADD KEY `account_id` (`account_id`) USING BTREE,
  ADD KEY `inout_type_id` (`inout_type_id`) USING BTREE,
  ADD KEY `user_id` (`player`) USING BTREE,
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `inout_types`
--
ALTER TABLE `inout_types`
  ADD PRIMARY KEY (`iotid`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`item`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`uid`),
  ADD UNIQUE KEY `name` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `aid` tinyint(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `bottles`
--
ALTER TABLE `bottles`
  MODIFY `bid` tinyint(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `cid` tinyint(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;
--
-- AUTO_INCREMENT for table `inout_records`
--
ALTER TABLE `inout_records`
  MODIFY `iorid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT for table `inout_types`
--
ALTER TABLE `inout_types`
  MODIFY `iotid` tinyint(1) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `uid` tinyint(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- ダンプしたテーブルの制約
--

--
-- テーブルの制約 `bottle_records`
--
ALTER TABLE `bottle_records`
  ADD CONSTRAINT `bottle_records_ibfk_1` FOREIGN KEY (`iorid`) REFERENCES `inout_records` (`iorid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `bottle_records_ibfk_2` FOREIGN KEY (`bottle_id`) REFERENCES `bottles` (`bid`) ON UPDATE CASCADE;

--
-- テーブルの制約 `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`bottle_id`) REFERENCES `bottles` (`bid`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `categories_ibfk_2` FOREIGN KEY (`inout_type_id`) REFERENCES `inout_types` (`iotid`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- テーブルの制約 `inout_records`
--
ALTER TABLE `inout_records`
  ADD CONSTRAINT `inout_records_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`cid`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `inout_records_ibfk_2` FOREIGN KEY (`player`) REFERENCES `users` (`uid`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `inout_records_ibfk_3` FOREIGN KEY (`inout_type_id`) REFERENCES `inout_types` (`iotid`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `inout_records_ibfk_6` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`aid`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `inout_records_ibfk_7` FOREIGN KEY (`created_by`) REFERENCES `users` (`uid`) ON DELETE NO ACTION ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
