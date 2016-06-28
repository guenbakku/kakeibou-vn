-- phpMyAdmin SQL Dump
-- version 4.0.10.7
-- http://www.phpmyadmin.net
--
-- ホスト: localhost
-- 生成日時: 2016 年 6 月 28 日 04:04
-- サーバのバージョン: 5.5.45-cll-lve
-- PHP のバージョン: 5.4.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- データベース: `nvb-online_kakeibou`
--

-- --------------------------------------------------------

--
-- テーブルの構造 `accounts`
--

CREATE TABLE IF NOT EXISTS `accounts` (
  `aid` tinyint(4) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `description` varchar(256) NOT NULL COMMENT 'Ghi chú cho tài khoản',
  PRIMARY KEY (`aid`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Phân loại các tài khoản ngân hàng' AUTO_INCREMENT=3 ;

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

CREATE TABLE IF NOT EXISTS `bottles` (
  `bid` tinyint(4) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `ratio` tinyint(3) unsigned NOT NULL COMMENT 'tỷ lệ phân chia mặc định',
  PRIMARY KEY (`bid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

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

CREATE TABLE IF NOT EXISTS `bottle_records` (
  `iorid` int(11) NOT NULL COMMENT 'id của record thu nhập',
  `bottle_id` tinyint(4) NOT NULL COMMENT 'id của từng bottle',
  `amount` int(11) NOT NULL,
  PRIMARY KEY (`iorid`,`bottle_id`),
  UNIQUE KEY `prid` (`iorid`),
  KEY `bid` (`bottle_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='ghi lại tỷ lệ phân phối thu nhập vào các lọ';

-- --------------------------------------------------------

--
-- テーブルの構造 `categories`
--

CREATE TABLE IF NOT EXISTS `categories` (
  `cid` tinyint(4) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `sort` tinyint(3) unsigned NOT NULL,
  `inout_type_id` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'id quy định loại thu, chi, mượn, cho mượn',
  `bottle_id` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'Phân loại sẵn lọ cho danh mục',
  `restrict_delete` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'không cho delete',
  PRIMARY KEY (`cid`),
  KEY `bid` (`bottle_id`),
  KEY `iotid` (`inout_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=33 ;

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
(25, 'Điện, nước, gas, internet', 0, 2, 0, 0),
(26, 'Khác', 0, 1, 0, 0),
(27, 'Đồ gia dụng lặt vặt', 0, 2, 0, 0),
(28, 'Trả thẻ credit', 0, 2, 0, 0),
(29, 'Áo quần, giày dép', 0, 2, 0, 0),
(30, 'Điện thoại', 0, 2, 0, 0),
(31, 'Bảo hiểm', 0, 2, 0, 0),
(32, 'Khác', 0, 2, 0, 0);

-- --------------------------------------------------------

--
-- テーブルの構造 `inout_records`
--

CREATE TABLE IF NOT EXISTS `inout_records` (
  `iorid` int(11) NOT NULL AUTO_INCREMENT,
  `inout_type_id` tinyint(1) unsigned NOT NULL,
  `account_id` tinyint(4) NOT NULL,
  `category_id` tinyint(4) NOT NULL,
  `pair_id` varchar(32) NOT NULL COMMENT 'Chứa unique string có chiều dài 32 ký tự',
  `player` tinyint(4) NOT NULL COMMENT 'id người phụ trách',
  `cash_flow` varchar(24) NOT NULL,
  `amount` int(11) NOT NULL,
  `memo` varchar(128) NOT NULL,
  `date` date NOT NULL,
  `created_on` datetime NOT NULL,
  `created_by` tinyint(4) NOT NULL,
  PRIMARY KEY (`iorid`),
  KEY `category_id` (`category_id`) USING BTREE,
  KEY `account_id` (`account_id`) USING BTREE,
  KEY `inout_type_id` (`inout_type_id`) USING BTREE,
  KEY `user_id` (`player`) USING BTREE,
  KEY `created_by` (`created_by`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='ghi chép thu chi' AUTO_INCREMENT=18 ;

--
-- テーブルのデータのダンプ `inout_records`
--

INSERT INTO `inout_records` (`iorid`, `inout_type_id`, `account_id`, `category_id`, `pair_id`, `player`, `cash_flow`, `amount`, `memo`, `date`, `created_on`, `created_by`) VALUES
(12, 1, 2, 26, '', 2, 'income', 274848, 'Khởi tạo', '2016-06-26', '2016-06-26 23:26:28', 2),
(13, 2, 1, 32, '', 1, 'outgo', -1594, 'Khởi tạo', '2016-06-26', '2016-06-26 23:32:05', 1),
(14, 2, 1, 21, '', 1, 'outgo', -500, '', '2016-06-27', '2016-06-27 12:42:11', 1),
(15, 1, 1, 26, '', 2, 'income', 182, 'Khởi tạo', '2016-06-26', '2016-06-27 18:55:33', 1),
(16, 2, 2, 28, '', 1, 'outgo', -69492, '', '2016-06-27', '2016-06-27 21:18:00', 1),
(17, 2, 1, 21, '', 1, 'outgo', -350, '', '2016-06-28', '2016-06-28 12:08:15', 1);

-- --------------------------------------------------------

--
-- テーブルの構造 `inout_types`
--

CREATE TABLE IF NOT EXISTS `inout_types` (
  `iotid` tinyint(1) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  PRIMARY KEY (`iotid`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Chi tiết phân loại thu chi' AUTO_INCREMENT=3 ;

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

CREATE TABLE IF NOT EXISTS `settings` (
  `item` varchar(128) NOT NULL,
  `name` varchar(128) NOT NULL,
  `value` varchar(512) NOT NULL,
  PRIMARY KEY (`item`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='lưu các thiết lập hệ thống';

--
-- テーブルのデータのダンプ `settings`
--

INSERT INTO `settings` (`item`, `name`, `value`) VALUES
('month_outgo_plans', 'Số tiền dự định chi trong tháng', '"7000"');

-- --------------------------------------------------------

--
-- テーブルの構造 `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `uid` tinyint(4) NOT NULL AUTO_INCREMENT,
  `username` varchar(32) NOT NULL,
  `fullname` varchar(128) NOT NULL,
  `label` varchar(50) NOT NULL COMMENT 'Class HTML',
  `password` varchar(256) NOT NULL COMMENT 'sha512',
  PRIMARY KEY (`uid`),
  UNIQUE KEY `name` (`username`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- テーブルのデータのダンプ `users`
--

INSERT INTO `users` (`uid`, `username`, `fullname`, `label`, `password`) VALUES
(1, 'bach', 'Bách', 'label-info', '94bc0c204d0f1ed072f1b20fba90c698caaf70cbb5ec64d8538f8cc00c5dc287a90c654c91282265454c4d13e8b156fb2a296e253e3f546b3786193cbea19ecf'),
(2, 'hiep', 'Hiệp', 'label-warning', '94bc0c204d0f1ed072f1b20fba90c698caaf70cbb5ec64d8538f8cc00c5dc287a90c654c91282265454c4d13e8b156fb2a296e253e3f546b3786193cbea19ecf');

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
