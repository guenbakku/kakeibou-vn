RENAME TABLE remember TO tokens;
ALTER TABLE `categories` ADD `month_estimated_inout` INT NOT NULL DEFAULT '0' COMMENT 'Dự định thu chi trong tháng' AFTER `inout_type_id`;