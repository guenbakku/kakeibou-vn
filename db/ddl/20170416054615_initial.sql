-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- ホスト: mysql
-- 生成日時: 2024 年 6 月 08 日 04:53
-- サーバのバージョン： 5.6.49
-- PHP のバージョン: 7.4.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- --------------------------------------------------------

--
-- テーブルの構造 `accounts`
--

CREATE TABLE `accounts` (
  `id` tinyint(4) NOT NULL,
  `name` varchar(128) NOT NULL,
  `description` varchar(256) NOT NULL COMMENT 'Ghi chú cho tài khoản'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Phân loại các tài khoản ngân hàng';

--
-- テーブルのデータのダンプ `accounts`
--

INSERT INTO `accounts` (`id`, `name`, `description`) VALUES
(1, 'Tiền mặt', ''),
(2, 'Bank account', '');

-- --------------------------------------------------------

--
-- テーブルの構造 `categories`
--

CREATE TABLE `categories` (
  `id` tinyint(4) NOT NULL,
  `name` varchar(128) NOT NULL,
  `order_no` tinyint(3) UNSIGNED NOT NULL,
  `inout_type_id` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'id quy định loại thu, chi, mượn, cho mượn',
  `month_estimated_inout` int(11) NOT NULL DEFAULT '0' COMMENT 'Dự định thu chi trong tháng',
  `month_fixed_money` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Có phải là tiền cố định hàng tháng không',
  `restrict_delete` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'không cho delete'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- テーブルのデータのダンプ `categories`
--

INSERT INTO `categories` (`id`, `name`, `order_no`, `inout_type_id`, `month_estimated_inout`, `month_fixed_money`, `restrict_delete`) VALUES
(1, 'Rút tiền từ tài khoản*', 1, 2, 0, 0, 1),
(2, 'Rút tiền từ tài khoản*', 1, 1, 0, 0, 1),
(3, 'Nạp tiền vô tài khoản*', 2, 1, 0, 0, 1),
(4, 'Nạp tiền vô tài khoản*', 2, 2, 0, 0, 1),
(5, 'Chuyển tiền qua tay*', 3, 2, 0, 0, 1),
(6, 'Chuyển tiền qua tay*', 3, 1, 0, 0, 1),
(21, 'Ăn uống', 0, 2, 10000000, 0, 0),
(22, 'Đi lại', 11, 2, 0, 0, 0),
(23, 'Giải trí', 5, 2, 0, 0, 0),
(24, 'Góp tiền hàng tháng', 0, 1, 0, 0, 0),
(25, 'Điện, nước, gas, internet', 9, 2, 0, 1, 0),
(26, 'Khác', 1, 1, 0, 0, 0),
(27, 'Đồ gia dụng lặt vặt', 3, 2, 0, 0, 0),
(28, 'Trả thẻ credit', 1, 2, 0, 1, 0),
(29, 'Thời trang', 4, 2, 0, 0, 0),
(30, 'Điện thoại', 2, 2, 0, 1, 0),
(31, 'Bảo hiểm, thuế', 7, 2, 0, 1, 0),
(32, 'Khác', 12, 2, 0, 0, 0),
(36, 'Ngoại giao', 10, 2, 0, 0, 0),
(37, 'Nhà ở', 8, 2, 0, 1, 0),
(38, 'Y tế', 6, 2, 0, 0, 0);

-- --------------------------------------------------------

--
-- テーブルの構造 `inout_records`
--

CREATE TABLE `inout_records` (
  `id` int(11) NOT NULL,
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
  `modified_on` datetime DEFAULT NULL,
  `modified_by` tinyint(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='ghi chép thu chi';

-- --------------------------------------------------------

--
-- テーブルの構造 `inout_types`
--

CREATE TABLE `inout_types` (
  `id` tinyint(1) UNSIGNED NOT NULL,
  `name` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Chi tiết phân loại thu chi';

--
-- テーブルのデータのダンプ `inout_types`
--

INSERT INTO `inout_types` (`id`, `name`) VALUES
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

-- --------------------------------------------------------

--
-- テーブルの構造 `tokens`
--

CREATE TABLE `tokens` (
  `token` varchar(40) NOT NULL,
  `user_id` tinyint(4) NOT NULL,
  `user_agent` varchar(512) DEFAULT NULL,
  `expire_on` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_on` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_on` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- テーブルのデータのダンプ `tokens`
--

INSERT INTO `tokens` (`token`, `user_id`, `user_agent`, `expire_on`, `created_on`, `modified_on`) VALUES
('034eee96c15d97b2bd03a263b409bd69e4db94de', 2, 'Mozilla/5.0 (iPhone; CPU iPhone OS 10_3_1 like Mac OS X) AppleWebKit/603.1.30 (KHTML, like Gecko) Version/10.0 Mobile/14E304 Safari/602.1', '2018-03-20 09:27:09', '2017-03-20 09:27:09', '2017-04-15 16:37:22'),
('2f48797d75dbfe7695726049e09798d6b46bc1fd', 1, 'Mozilla/5.0 (iPhone; CPU iPhone OS 9_1 like Mac OS X) AppleWebKit/601.1.46 (KHTML, like Gecko) Version/9.0 Mobile/13B143 Safari/601.1', '2018-02-26 19:01:45', '2017-02-26 19:01:45', '2017-04-08 22:34:20'),
('675bd7cb35355097bc71e17634cde0ab5023c4ac', 1, 'Mozilla/5.0 (iPhone; CPU iPhone OS 9_3_2 like Mac OS X) AppleWebKit/601.1.46 (KHTML, like Gecko) Version/9.0 Mobile/13F69 Safari/601.1', '2018-04-08 19:32:16', '2017-04-08 19:32:16', '2017-04-16 13:34:08'),
('bdbc31b2c460f2979b291e361bd454dda02a203f', 1, 'Mozilla/5.0 (iPhone; CPU iPhone OS 9_3_2 like Mac OS X) AppleWebKit/601.1.46 (KHTML, like Gecko) Version/9.0 Mobile/13F69 Safari/601.1', '2018-02-19 19:11:14', '2017-02-19 19:11:14', '2017-02-23 08:44:34'),
('c3a0d1484e683d36cf8aa1ac7279377038717dc4', 2, 'Mozilla/5.0 (iPhone; CPU iPhone OS 9_3_2 like Mac OS X) AppleWebKit/601.1.46 (KHTML, like Gecko) Version/9.0 Mobile/13F69 Safari/601.1', '2018-02-26 20:53:04', '2017-02-26 20:53:04', '2017-03-19 17:13:33'),
('da868a960251ddf4b36c95adcd9dcb8d0376ef4e', 2, 'Mozilla/5.0 (iPhone; CPU iPhone OS 9_3_2 like Mac OS X) AppleWebKit/601.1.46 (KHTML, like Gecko) Version/9.0 Mobile/13F69 Safari/601.1', '2018-02-19 20:34:19', '2017-02-19 20:34:19', '2017-02-25 15:05:25');

-- --------------------------------------------------------

--
-- テーブルの構造 `users`
--

CREATE TABLE `users` (
  `id` tinyint(4) NOT NULL,
  `username` varchar(32) NOT NULL,
  `fullname` varchar(128) NOT NULL,
  `label` varchar(50) NOT NULL COMMENT 'Class HTML',
  `password` varchar(256) NOT NULL COMMENT 'blowfish',
  `locked_on` datetime DEFAULT NULL,
  `lock_duration` int(11) NOT NULL DEFAULT '0',
  `login_attemps` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- テーブルのデータのダンプ `users`
--

INSERT INTO `users` (`id`, `username`, `fullname`, `label`, `password`, `locked_on`, `lock_duration`, `login_attemps`) VALUES
(1, 'user1', 'Joe', 'label-info', '$2y$10$lEIu6RafTW2UFNoooR9wQeU/5nfxGQULvsvZXtT/7uZqFyUKzI7Fa', NULL, 0, 0),
(2, 'user2', 'Smith', 'label-warning', '$2y$10$lEIu6RafTW2UFNoooR9wQeU/5nfxGQULvsvZXtT/7uZqFyUKzI7Fa', NULL, 0, 0);

--
-- ダンプしたテーブルのインデックス
--

--
-- テーブルのインデックス `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- テーブルのインデックス `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `iotid` (`inout_type_id`);

--
-- テーブルのインデックス `inout_records`
--
ALTER TABLE `inout_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`) USING BTREE,
  ADD KEY `account_id` (`account_id`) USING BTREE,
  ADD KEY `user_id` (`player`) USING BTREE,
  ADD KEY `created_by` (`created_by`),
  ADD KEY `modified_by` (`modified_by`);

--
-- テーブルのインデックス `inout_types`
--
ALTER TABLE `inout_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- テーブルのインデックス `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`item`);

--
-- テーブルのインデックス `tokens`
--
ALTER TABLE `tokens`
  ADD PRIMARY KEY (`token`),
  ADD KEY `user_id` (`user_id`);

--
-- テーブルのインデックス `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`username`);

--
-- ダンプしたテーブルのAUTO_INCREMENT
--

--
-- テーブルのAUTO_INCREMENT `accounts`
--
ALTER TABLE `accounts`
  MODIFY `id` tinyint(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- テーブルのAUTO_INCREMENT `categories`
--
ALTER TABLE `categories`
  MODIFY `id` tinyint(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- テーブルのAUTO_INCREMENT `inout_records`
--
ALTER TABLE `inout_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- テーブルのAUTO_INCREMENT `inout_types`
--
ALTER TABLE `inout_types`
  MODIFY `id` tinyint(1) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- テーブルのAUTO_INCREMENT `users`
--
ALTER TABLE `users`
  MODIFY `id` tinyint(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- ダンプしたテーブルの制約
--

--
-- テーブルの制約 `inout_records`
--
ALTER TABLE `inout_records`
  ADD CONSTRAINT `inout_records_ibfk_1` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `inout_records_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `inout_records_ibfk_3` FOREIGN KEY (`player`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `inout_records_ibfk_4` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `inout_records_ibfk_5` FOREIGN KEY (`modified_by`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- テーブルの制約 `tokens`
--
ALTER TABLE `tokens`
  ADD CONSTRAINT `tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
