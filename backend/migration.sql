ALTER TABLE `e_learning` ADD `status` TINYINT NOT NULL DEFAULT '1' AFTER `speaker6`;

ALTER TABLE `member` ADD `remarks` VARCHAR(120) NULL AFTER `profile_img`;
ALTER TABLE `user_login` CHANGE `reset_password` `reset_password` TINYINT(4) NULL DEFAULT '0';