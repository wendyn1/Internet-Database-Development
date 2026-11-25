-- 2026世界杯球队信息系统 业务表与样本数据安装脚本
-- 适用前提：同一数据库已通过 Yii 初始迁移创建 user 表（php yii migrate）。
-- 本脚本不创建管理员账号，请随后执行 php yii admin/init。
-- 本脚本不是幂等安装器，不要在已有业务表的数据库上重复执行。
-- 生成日期：2026-11-25
-- Prepared only; NOT executed. Existing user/admin must be preserved.
SET NAMES utf8mb4;
SET SESSION sql_mode = 'STRICT_TRANS_TABLES,NO_BACKSLASH_ESCAPES,NO_ENGINE_SUBSTITUTION';
CREATE TABLE `team` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name_zh` VARCHAR(80) NOT NULL,
  `name_en` VARCHAR(80) NOT NULL,
  `fifa_code` VARCHAR(3) NOT NULL,
  `country` VARCHAR(80) NOT NULL,
  `continent` VARCHAR(30) NOT NULL,
  `introduction` TEXT NULL DEFAULT NULL,
  `logo_path` VARCHAR(500) NULL DEFAULT NULL,
  CONSTRAINT `uq_team_1` UNIQUE (`fifa_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE INDEX `ix_team_1` ON `team` (`name_zh`);

CREATE TABLE `player` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name_zh` VARCHAR(80) NOT NULL,
  `name_en` VARCHAR(100) NOT NULL,
  `nationality` VARCHAR(80) NOT NULL,
  `birth_date` DATE NULL DEFAULT NULL,
  `photo_path` VARCHAR(500) NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE INDEX `ix_player_1` ON `player` (`name_zh`);

CREATE TABLE `team_player` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `team_id` INT NOT NULL,
  `player_id` INT NOT NULL,
  `tournament_year` INT NOT NULL DEFAULT 2026,
  `shirt_number` INT NOT NULL,
  `position_code` VARCHAR(2) NOT NULL,
  CONSTRAINT `uq_team_player_1` UNIQUE (`player_id`, `tournament_year`),
  CONSTRAINT `uq_team_player_2` UNIQUE (`team_id`, `tournament_year`, `shirt_number`),
  CONSTRAINT `fk_team_player_team_id` FOREIGN KEY (`team_id`) REFERENCES `team` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_team_player_player_id` FOREIGN KEY (`player_id`) REFERENCES `player` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `ck_team_player_1` CHECK (tournament_year = 2026),
  CONSTRAINT `ck_team_player_2` CHECK (shirt_number BETWEEN 1 AND 26),
  CONSTRAINT `ck_team_player_3` CHECK (position_code IN ('GK','DF','MF','FW'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `coach` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name_zh` VARCHAR(80) NOT NULL,
  `name_en` VARCHAR(100) NOT NULL,
  `nationality` VARCHAR(80) NOT NULL,
  `introduction` TEXT NULL DEFAULT NULL,
  `photo_path` VARCHAR(500) NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE INDEX `ix_coach_1` ON `coach` (`name_zh`);

CREATE TABLE `team_coach` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `team_id` INT NOT NULL,
  `coach_id` INT NOT NULL,
  `tournament_year` INT NOT NULL DEFAULT 2026,
  `role` VARCHAR(30) NOT NULL DEFAULT 'head_coach',
  CONSTRAINT `uq_team_coach_1` UNIQUE (`team_id`, `tournament_year`),
  CONSTRAINT `fk_team_coach_team_id` FOREIGN KEY (`team_id`) REFERENCES `team` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_team_coach_coach_id` FOREIGN KEY (`coach_id`) REFERENCES `coach` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `ck_team_coach_1` CHECK (tournament_year = 2026),
  CONSTRAINT `ck_team_coach_2` CHECK (role = 'head_coach')
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE INDEX `ix_team_coach_1` ON `team_coach` (`coach_id`);

CREATE TABLE `venue` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name_zh` VARCHAR(80) NOT NULL,
  `name_en` VARCHAR(100) NOT NULL,
  `city` VARCHAR(80) NOT NULL,
  `country` VARCHAR(80) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `football_match` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `fifa_match_number` INT NULL DEFAULT NULL,
  `tournament_year` INT NOT NULL DEFAULT 2026,
  `stage` VARCHAR(30) NOT NULL,
  `venue_id` INT NOT NULL,
  `home_team_id` INT NULL DEFAULT NULL,
  `away_team_id` INT NULL DEFAULT NULL,
  `home_team_name` VARCHAR(80) NOT NULL,
  `away_team_name` VARCHAR(80) NOT NULL,
  `match_date_beijing` DATE NOT NULL,
  `status` VARCHAR(20) NOT NULL,
  `home_score` INT NULL DEFAULT NULL,
  `away_score` INT NULL DEFAULT NULL,
  `after_extra_time` SMALLINT NOT NULL DEFAULT 0,
  CONSTRAINT `uq_football_match_1` UNIQUE (`tournament_year`, `fifa_match_number`),
  CONSTRAINT `fk_football_match_venue_id` FOREIGN KEY (`venue_id`) REFERENCES `venue` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_football_match_home_team_id` FOREIGN KEY (`home_team_id`) REFERENCES `team` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_football_match_away_team_id` FOREIGN KEY (`away_team_id`) REFERENCES `team` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `ck_football_match_1` CHECK (tournament_year = 2026),
  CONSTRAINT `ck_football_match_2` CHECK (fifa_match_number IS NULL OR fifa_match_number > 0),
  CONSTRAINT `ck_football_match_3` CHECK (home_team_id IS NOT NULL OR away_team_id IS NOT NULL),
  CONSTRAINT `ck_football_match_4` CHECK (home_team_id IS NULL OR away_team_id IS NULL OR home_team_id <> away_team_id),
  CONSTRAINT `ck_football_match_5` CHECK (stage IN ('group','round_of_32','round_of_16','quarter_final','semi_final','third_place','final')),
  CONSTRAINT `ck_football_match_6` CHECK (status IN ('not_started','live','finished','postponed','cancelled')),
  CONSTRAINT `ck_football_match_7` CHECK ((status = 'finished' AND home_score IS NOT NULL AND away_score IS NOT NULL AND home_score >= 0 AND away_score >= 0) OR (status <> 'finished' AND home_score IS NULL AND away_score IS NULL)),
  CONSTRAINT `ck_football_match_8` CHECK (after_extra_time IN (0,1))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE INDEX `ix_football_match_1` ON `football_match` (`venue_id`);

CREATE INDEX `ix_football_match_2` ON `football_match` (`home_team_id`, `tournament_year`, `status`);

CREATE INDEX `ix_football_match_3` ON `football_match` (`away_team_id`, `tournament_year`, `status`);

CREATE INDEX `ix_football_match_4` ON `football_match` (`match_date_beijing`);

CREATE TABLE `player_match_stat` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `match_id` INT NOT NULL,
  `team_player_id` INT NOT NULL,
  `appeared` SMALLINT NOT NULL,
  `goals` INT NOT NULL,
  `assists` INT NULL DEFAULT NULL,
  CONSTRAINT `uq_player_match_stat_1` UNIQUE (`match_id`, `team_player_id`),
  CONSTRAINT `fk_player_match_stat_match_id` FOREIGN KEY (`match_id`) REFERENCES `football_match` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_player_match_stat_team_player_id` FOREIGN KEY (`team_player_id`) REFERENCES `team_player` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `ck_player_match_stat_1` CHECK (appeared IN (0,1)),
  CONSTRAINT `ck_player_match_stat_2` CHECK (goals >= 0),
  CONSTRAINT `ck_player_match_stat_3` CHECK (assists IS NULL OR assists >= 0),
  CONSTRAINT `ck_player_match_stat_4` CHECK (appeared = 1 OR (goals = 0 AND (assists IS NULL OR assists = 0)))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE INDEX `ix_player_match_stat_1` ON `player_match_stat` (`team_player_id`);

CREATE TABLE `honor` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(80) NOT NULL,
  `competition` VARCHAR(80) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `team_honor` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `team_id` INT NOT NULL,
  `honor_id` INT NOT NULL,
  `year` INT NOT NULL,
  `description` VARCHAR(255) NOT NULL,
  CONSTRAINT `uq_team_honor_1` UNIQUE (`team_id`, `honor_id`, `year`),
  CONSTRAINT `fk_team_honor_team_id` FOREIGN KEY (`team_id`) REFERENCES `team` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_team_honor_honor_id` FOREIGN KEY (`honor_id`) REFERENCES `honor` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `ck_team_honor_1` CHECK (year > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE INDEX `ix_team_honor_1` ON `team_honor` (`honor_id`);

CREATE TABLE `comment` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `team_id` INT NULL DEFAULT NULL,
  `player_id` INT NULL DEFAULT NULL,
  `match_id` INT NULL DEFAULT NULL,
  `nickname` VARCHAR(30) NOT NULL,
  `content` TEXT NOT NULL,
  `status` VARCHAR(20) NOT NULL DEFAULT 'pending',
  `created_at` DATETIME NOT NULL,
  `reviewed_by` INT NULL DEFAULT NULL,
  `reviewed_at` DATETIME NULL DEFAULT NULL,
  CONSTRAINT `fk_comment_team_id` FOREIGN KEY (`team_id`) REFERENCES `team` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_comment_player_id` FOREIGN KEY (`player_id`) REFERENCES `player` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_comment_match_id` FOREIGN KEY (`match_id`) REFERENCES `football_match` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_comment_reviewed_by` FOREIGN KEY (`reviewed_by`) REFERENCES `user` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `ck_comment_1` CHECK ((CASE WHEN team_id IS NULL THEN 0 ELSE 1 END + CASE WHEN player_id IS NULL THEN 0 ELSE 1 END + CASE WHEN match_id IS NULL THEN 0 ELSE 1 END) = 1),
  CONSTRAINT `ck_comment_2` CHECK (status IN ('pending','approved','rejected','deleted')),
  CONSTRAINT `ck_comment_3` CHECK (CHAR_LENGTH(TRIM(nickname)) BETWEEN 1 AND 30),
  CONSTRAINT `ck_comment_4` CHECK (CHAR_LENGTH(TRIM(content)) BETWEEN 1 AND 1000)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE INDEX `ix_comment_1` ON `comment` (`team_id`, `status`, `created_at`, `id`);

CREATE INDEX `ix_comment_2` ON `comment` (`player_id`, `status`, `created_at`, `id`);

CREATE INDEX `ix_comment_3` ON `comment` (`match_id`, `status`, `created_at`, `id`);

CREATE INDEX `ix_comment_4` ON `comment` (`status`, `created_at`, `id`);

CREATE INDEX `ix_comment_5` ON `comment` (`reviewed_by`);


-- Prepared only; NOT executed. Existing user/admin must be preserved.
SET NAMES utf8mb4;
SET SESSION sql_mode = 'STRICT_TRANS_TABLES,NO_BACKSLASH_ESCAPES,NO_ENGINE_SUBSTITUTION';

START TRANSACTION;
INSERT INTO `team` (`id`, `name_zh`, `name_en`, `fifa_code`, `country`, `continent`, `introduction`, `logo_path`) VALUES (1, '阿根廷', 'Argentina', 'ARG', '阿根廷', '南美洲', '传统世界杯冠军球队，适合展示球员资料与冠军荣誉。', NULL);
INSERT INTO `team` (`id`, `name_zh`, `name_en`, `fifa_code`, `country`, `continent`, `introduction`, `logo_path`) VALUES (2, '法国', 'France', 'FRA', '法国', '欧洲', '拥有世界杯冠军历史，适合展示球员与比赛关联。', NULL);
INSERT INTO `team` (`id`, `name_zh`, `name_en`, `fifa_code`, `country`, `continent`, `introduction`, `logo_path`) VALUES (3, '西班牙', 'Spain', 'ESP', '西班牙', '欧洲', '以国家队阵容、比赛与历史荣誉为核心展示内容。', NULL);
INSERT INTO `team` (`id`, `name_zh`, `name_en`, `fifa_code`, `country`, `continent`, `introduction`, `logo_path`) VALUES (4, '巴西', 'Brazil', 'BRA', '巴西', '南美洲', '具有丰富世界杯冠军记录，适合荣誉与战绩图表。', NULL);
INSERT INTO `team` (`id`, `name_zh`, `name_en`, `fifa_code`, `country`, `continent`, `introduction`, `logo_path`) VALUES (5, '葡萄牙', 'Portugal', 'POR', '葡萄牙', '欧洲', '展示球队阵容、球星资料与欧洲杯冠军荣誉。', NULL);
INSERT INTO `team` (`id`, `name_zh`, `name_en`, `fifa_code`, `country`, `continent`, `introduction`, `logo_path`) VALUES (6, '英格兰', 'England', 'ENG', '英格兰', '欧洲', '具有世界杯冠军历史，适合球员资料和比赛展示。', NULL);
INSERT INTO `player` (`id`, `name_zh`, `name_en`, `nationality`, `birth_date`, `photo_path`) VALUES (1, '埃米利亚诺·马丁内斯', 'Emiliano Martínez', '阿根廷', '1992-09-02', NULL);
INSERT INTO `player` (`id`, `name_zh`, `name_en`, `nationality`, `birth_date`, `photo_path`) VALUES (2, '克里斯蒂安·罗梅罗', 'Cristian Romero', '阿根廷', '1998-04-27', NULL);
INSERT INTO `player` (`id`, `name_zh`, `name_en`, `nationality`, `birth_date`, `photo_path`) VALUES (3, '恩佐·费尔南德斯', 'Enzo Fernández', '阿根廷', '2001-01-17', NULL);
INSERT INTO `player` (`id`, `name_zh`, `name_en`, `nationality`, `birth_date`, `photo_path`) VALUES (4, '利昂内尔·梅西', 'Lionel Messi', '阿根廷', '1987-06-24', NULL);
INSERT INTO `player` (`id`, `name_zh`, `name_en`, `nationality`, `birth_date`, `photo_path`) VALUES (5, '迈克·迈尼昂', 'Mike Maignan', '法国', '1995-07-03', NULL);
INSERT INTO `player` (`id`, `name_zh`, `name_en`, `nationality`, `birth_date`, `photo_path`) VALUES (6, '朱尔·孔德', 'Jules Koundé', '法国', '1998-11-12', NULL);
INSERT INTO `player` (`id`, `name_zh`, `name_en`, `nationality`, `birth_date`, `photo_path`) VALUES (7, '奥雷利安·楚阿梅尼', 'Aurélien Tchouaméni', '法国', '2000-01-27', NULL);
INSERT INTO `player` (`id`, `name_zh`, `name_en`, `nationality`, `birth_date`, `photo_path`) VALUES (8, '基利安·姆巴佩', 'Kylian Mbappé', '法国', '1998-12-20', NULL);
INSERT INTO `player` (`id`, `name_zh`, `name_en`, `nationality`, `birth_date`, `photo_path`) VALUES (9, '乌奈·西蒙', 'Unai Simón', '西班牙', '1997-06-11', NULL);
INSERT INTO `player` (`id`, `name_zh`, `name_en`, `nationality`, `birth_date`, `photo_path`) VALUES (10, '保·库巴西', 'Pau Cubarsí', '西班牙', '2007-01-22', NULL);
INSERT INTO `player` (`id`, `name_zh`, `name_en`, `nationality`, `birth_date`, `photo_path`) VALUES (11, '佩德里', 'Pedri', '西班牙', '2002-11-25', NULL);
INSERT INTO `player` (`id`, `name_zh`, `name_en`, `nationality`, `birth_date`, `photo_path`) VALUES (12, '拉明·亚马尔', 'Lamine Yamal', '西班牙', '2007-07-13', NULL);
INSERT INTO `player` (`id`, `name_zh`, `name_en`, `nationality`, `birth_date`, `photo_path`) VALUES (13, '阿利松·贝克尔', 'Alisson Becker', '巴西', '1992-10-02', NULL);
INSERT INTO `player` (`id`, `name_zh`, `name_en`, `nationality`, `birth_date`, `photo_path`) VALUES (14, '马尔基尼奥斯', 'Marquinhos', '巴西', '1994-05-14', NULL);
INSERT INTO `player` (`id`, `name_zh`, `name_en`, `nationality`, `birth_date`, `photo_path`) VALUES (15, '布鲁诺·吉马良斯', 'Bruno Guimarães', '巴西', '1997-11-16', NULL);
INSERT INTO `player` (`id`, `name_zh`, `name_en`, `nationality`, `birth_date`, `photo_path`) VALUES (16, '维尼修斯·儒尼奥尔', 'Vinícius Júnior', '巴西', '2000-07-12', NULL);
INSERT INTO `player` (`id`, `name_zh`, `name_en`, `nationality`, `birth_date`, `photo_path`) VALUES (17, '迪奥戈·科斯塔', 'Diogo Costa', '葡萄牙', '1999-09-19', NULL);
INSERT INTO `player` (`id`, `name_zh`, `name_en`, `nationality`, `birth_date`, `photo_path`) VALUES (18, '鲁本·迪亚斯', 'Rúben Dias', '葡萄牙', '1997-05-14', NULL);
INSERT INTO `player` (`id`, `name_zh`, `name_en`, `nationality`, `birth_date`, `photo_path`) VALUES (19, '布鲁诺·费尔南德斯', 'Bruno Fernandes', '葡萄牙', '1994-09-08', NULL);
INSERT INTO `player` (`id`, `name_zh`, `name_en`, `nationality`, `birth_date`, `photo_path`) VALUES (20, '克里斯蒂亚诺·罗纳尔多', 'Cristiano Ronaldo', '葡萄牙', '1985-02-05', NULL);
INSERT INTO `player` (`id`, `name_zh`, `name_en`, `nationality`, `birth_date`, `photo_path`) VALUES (21, '乔丹·皮克福德', 'Jordan Pickford', '英格兰', '1994-03-07', NULL);
INSERT INTO `player` (`id`, `name_zh`, `name_en`, `nationality`, `birth_date`, `photo_path`) VALUES (22, '约翰·斯通斯', 'John Stones', '英格兰', '1994-05-28', NULL);
INSERT INTO `player` (`id`, `name_zh`, `name_en`, `nationality`, `birth_date`, `photo_path`) VALUES (23, '裘德·贝林厄姆', 'Jude Bellingham', '英格兰', '2003-06-29', NULL);
INSERT INTO `player` (`id`, `name_zh`, `name_en`, `nationality`, `birth_date`, `photo_path`) VALUES (24, '哈里·凯恩', 'Harry Kane', '英格兰', '1993-07-28', NULL);
INSERT INTO `team_player` (`id`, `team_id`, `player_id`, `tournament_year`, `shirt_number`, `position_code`) VALUES (1, 1, 1, 2026, 23, 'GK');
INSERT INTO `team_player` (`id`, `team_id`, `player_id`, `tournament_year`, `shirt_number`, `position_code`) VALUES (2, 1, 2, 2026, 13, 'DF');
INSERT INTO `team_player` (`id`, `team_id`, `player_id`, `tournament_year`, `shirt_number`, `position_code`) VALUES (3, 1, 3, 2026, 24, 'MF');
INSERT INTO `team_player` (`id`, `team_id`, `player_id`, `tournament_year`, `shirt_number`, `position_code`) VALUES (4, 1, 4, 2026, 10, 'FW');
INSERT INTO `team_player` (`id`, `team_id`, `player_id`, `tournament_year`, `shirt_number`, `position_code`) VALUES (5, 2, 5, 2026, 16, 'GK');
INSERT INTO `team_player` (`id`, `team_id`, `player_id`, `tournament_year`, `shirt_number`, `position_code`) VALUES (6, 2, 6, 2026, 5, 'DF');
INSERT INTO `team_player` (`id`, `team_id`, `player_id`, `tournament_year`, `shirt_number`, `position_code`) VALUES (7, 2, 7, 2026, 8, 'MF');
INSERT INTO `team_player` (`id`, `team_id`, `player_id`, `tournament_year`, `shirt_number`, `position_code`) VALUES (8, 2, 8, 2026, 10, 'FW');
INSERT INTO `team_player` (`id`, `team_id`, `player_id`, `tournament_year`, `shirt_number`, `position_code`) VALUES (9, 3, 9, 2026, 23, 'GK');
INSERT INTO `team_player` (`id`, `team_id`, `player_id`, `tournament_year`, `shirt_number`, `position_code`) VALUES (10, 3, 10, 2026, 22, 'DF');
INSERT INTO `team_player` (`id`, `team_id`, `player_id`, `tournament_year`, `shirt_number`, `position_code`) VALUES (11, 3, 11, 2026, 20, 'MF');
INSERT INTO `team_player` (`id`, `team_id`, `player_id`, `tournament_year`, `shirt_number`, `position_code`) VALUES (12, 3, 12, 2026, 19, 'FW');
INSERT INTO `team_player` (`id`, `team_id`, `player_id`, `tournament_year`, `shirt_number`, `position_code`) VALUES (13, 4, 13, 2026, 1, 'GK');
INSERT INTO `team_player` (`id`, `team_id`, `player_id`, `tournament_year`, `shirt_number`, `position_code`) VALUES (14, 4, 14, 2026, 4, 'DF');
INSERT INTO `team_player` (`id`, `team_id`, `player_id`, `tournament_year`, `shirt_number`, `position_code`) VALUES (15, 4, 15, 2026, 8, 'MF');
INSERT INTO `team_player` (`id`, `team_id`, `player_id`, `tournament_year`, `shirt_number`, `position_code`) VALUES (16, 4, 16, 2026, 7, 'FW');
INSERT INTO `team_player` (`id`, `team_id`, `player_id`, `tournament_year`, `shirt_number`, `position_code`) VALUES (17, 5, 17, 2026, 1, 'GK');
INSERT INTO `team_player` (`id`, `team_id`, `player_id`, `tournament_year`, `shirt_number`, `position_code`) VALUES (18, 5, 18, 2026, 3, 'DF');
INSERT INTO `team_player` (`id`, `team_id`, `player_id`, `tournament_year`, `shirt_number`, `position_code`) VALUES (19, 5, 19, 2026, 8, 'MF');
INSERT INTO `team_player` (`id`, `team_id`, `player_id`, `tournament_year`, `shirt_number`, `position_code`) VALUES (20, 5, 20, 2026, 7, 'FW');
INSERT INTO `team_player` (`id`, `team_id`, `player_id`, `tournament_year`, `shirt_number`, `position_code`) VALUES (21, 6, 21, 2026, 1, 'GK');
INSERT INTO `team_player` (`id`, `team_id`, `player_id`, `tournament_year`, `shirt_number`, `position_code`) VALUES (22, 6, 22, 2026, 5, 'DF');
INSERT INTO `team_player` (`id`, `team_id`, `player_id`, `tournament_year`, `shirt_number`, `position_code`) VALUES (23, 6, 23, 2026, 10, 'MF');
INSERT INTO `team_player` (`id`, `team_id`, `player_id`, `tournament_year`, `shirt_number`, `position_code`) VALUES (24, 6, 24, 2026, 9, 'FW');
INSERT INTO `coach` (`id`, `name_zh`, `name_en`, `nationality`, `introduction`, `photo_path`) VALUES (1, '利昂内尔·斯卡洛尼', 'Lionel Scaloni', '阿根廷', '2026世界杯期间阿根廷国家队主教练；本记录不代表2026年10月现任情况。', NULL);
INSERT INTO `coach` (`id`, `name_zh`, `name_en`, `nationality`, `introduction`, `photo_path`) VALUES (2, '迪迪埃·德尚', 'Didier Deschamps', '法国', '2026世界杯期间法国国家队主教练；本记录不代表2026年10月现任情况。', NULL);
INSERT INTO `coach` (`id`, `name_zh`, `name_en`, `nationality`, `introduction`, `photo_path`) VALUES (3, '路易斯·德拉富恩特', 'Luis de la Fuente', '西班牙', '2026世界杯期间西班牙国家队主教练；本记录不代表2026年10月现任情况。', NULL);
INSERT INTO `coach` (`id`, `name_zh`, `name_en`, `nationality`, `introduction`, `photo_path`) VALUES (4, '卡洛·安切洛蒂', 'Carlo Ancelotti', '意大利', '2026世界杯期间巴西国家队主教练；本记录不代表2026年10月现任情况。', NULL);
INSERT INTO `coach` (`id`, `name_zh`, `name_en`, `nationality`, `introduction`, `photo_path`) VALUES (5, '罗伯托·马丁内斯', 'Roberto Martínez', '西班牙', '2026世界杯期间葡萄牙国家队主教练；本记录不代表2026年10月现任情况。', NULL);
INSERT INTO `coach` (`id`, `name_zh`, `name_en`, `nationality`, `introduction`, `photo_path`) VALUES (6, '托马斯·图赫尔', 'Thomas Tuchel', '德国', '2026世界杯期间英格兰国家队主教练；本记录不代表2026年10月现任情况。', NULL);
INSERT INTO `team_coach` (`id`, `team_id`, `coach_id`, `tournament_year`, `role`) VALUES (1, 1, 1, 2026, 'head_coach');
INSERT INTO `team_coach` (`id`, `team_id`, `coach_id`, `tournament_year`, `role`) VALUES (2, 2, 2, 2026, 'head_coach');
INSERT INTO `team_coach` (`id`, `team_id`, `coach_id`, `tournament_year`, `role`) VALUES (3, 3, 3, 2026, 'head_coach');
INSERT INTO `team_coach` (`id`, `team_id`, `coach_id`, `tournament_year`, `role`) VALUES (4, 4, 4, 2026, 'head_coach');
INSERT INTO `team_coach` (`id`, `team_id`, `coach_id`, `tournament_year`, `role`) VALUES (5, 5, 5, 2026, 'head_coach');
INSERT INTO `team_coach` (`id`, `team_id`, `coach_id`, `tournament_year`, `role`) VALUES (6, 6, 6, 2026, 'head_coach');
INSERT INTO `venue` (`id`, `name_zh`, `name_en`, `city`, `country`) VALUES (1, '纽约新泽西体育场', 'New York New Jersey Stadium', 'East Rutherford', '美国');
INSERT INTO `venue` (`id`, `name_zh`, `name_en`, `city`, `country`) VALUES (2, '休斯敦体育场', 'Houston Stadium', 'Houston', '美国');
INSERT INTO `venue` (`id`, `name_zh`, `name_en`, `city`, `country`) VALUES (3, '达拉斯体育场', 'Dallas Stadium', 'Arlington', '美国');
INSERT INTO `venue` (`id`, `name_zh`, `name_en`, `city`, `country`) VALUES (4, '亚特兰大体育场', 'Atlanta Stadium', 'Atlanta', '美国');
INSERT INTO `venue` (`id`, `name_zh`, `name_en`, `city`, `country`) VALUES (5, '迈阿密体育场', 'Miami Stadium', 'Miami Gardens', '美国');
INSERT INTO `football_match` (`id`, `fifa_match_number`, `tournament_year`, `stage`, `venue_id`, `home_team_id`, `away_team_id`, `home_team_name`, `away_team_name`, `match_date_beijing`, `status`, `home_score`, `away_score`, `after_extra_time`) VALUES (7, 7, 2026, 'group', 1, 4, NULL, '巴西', '摩洛哥', '2026-06-14', 'finished', 1, 1, 0);
INSERT INTO `football_match` (`id`, `fifa_match_number`, `tournament_year`, `stage`, `venue_id`, `home_team_id`, `away_team_id`, `home_team_name`, `away_team_name`, `match_date_beijing`, `status`, `home_score`, `away_score`, `after_extra_time`) VALUES (76, 76, 2026, 'round_of_32', 2, 4, NULL, '巴西', '日本', '2026-06-30', 'finished', 2, 1, 0);
INSERT INTO `football_match` (`id`, `fifa_match_number`, `tournament_year`, `stage`, `venue_id`, `home_team_id`, `away_team_id`, `home_team_name`, `away_team_name`, `match_date_beijing`, `status`, `home_score`, `away_score`, `after_extra_time`) VALUES (91, 91, 2026, 'round_of_16', 1, 4, NULL, '巴西', '挪威', '2026-07-06', 'finished', 1, 2, 0);
INSERT INTO `football_match` (`id`, `fifa_match_number`, `tournament_year`, `stage`, `venue_id`, `home_team_id`, `away_team_id`, `home_team_name`, `away_team_name`, `match_date_beijing`, `status`, `home_score`, `away_score`, `after_extra_time`) VALUES (93, 93, 2026, 'round_of_16', 3, 5, 3, '葡萄牙', '西班牙', '2026-07-07', 'finished', 0, 1, 0);
INSERT INTO `football_match` (`id`, `fifa_match_number`, `tournament_year`, `stage`, `venue_id`, `home_team_id`, `away_team_id`, `home_team_name`, `away_team_name`, `match_date_beijing`, `status`, `home_score`, `away_score`, `after_extra_time`) VALUES (101, 101, 2026, 'semi_final', 3, 2, 3, '法国', '西班牙', '2026-07-15', 'finished', 0, 2, 0);
INSERT INTO `football_match` (`id`, `fifa_match_number`, `tournament_year`, `stage`, `venue_id`, `home_team_id`, `away_team_id`, `home_team_name`, `away_team_name`, `match_date_beijing`, `status`, `home_score`, `away_score`, `after_extra_time`) VALUES (102, 102, 2026, 'semi_final', 4, 6, 1, '英格兰', '阿根廷', '2026-07-16', 'finished', 1, 2, 0);
INSERT INTO `football_match` (`id`, `fifa_match_number`, `tournament_year`, `stage`, `venue_id`, `home_team_id`, `away_team_id`, `home_team_name`, `away_team_name`, `match_date_beijing`, `status`, `home_score`, `away_score`, `after_extra_time`) VALUES (103, 103, 2026, 'third_place', 5, 2, 6, '法国', '英格兰', '2026-07-19', 'finished', 4, 6, 0);
INSERT INTO `football_match` (`id`, `fifa_match_number`, `tournament_year`, `stage`, `venue_id`, `home_team_id`, `away_team_id`, `home_team_name`, `away_team_name`, `match_date_beijing`, `status`, `home_score`, `away_score`, `after_extra_time`) VALUES (104, 104, 2026, 'final', 1, 3, 1, '西班牙', '阿根廷', '2026-07-20', 'finished', 1, 0, 1);
INSERT INTO `player_match_stat` (`id`, `match_id`, `team_player_id`, `appeared`, `goals`, `assists`) VALUES (1, 102, 1, 1, 0, NULL);
INSERT INTO `player_match_stat` (`id`, `match_id`, `team_player_id`, `appeared`, `goals`, `assists`) VALUES (2, 102, 2, 1, 0, NULL);
INSERT INTO `player_match_stat` (`id`, `match_id`, `team_player_id`, `appeared`, `goals`, `assists`) VALUES (3, 102, 3, 1, 1, NULL);
INSERT INTO `player_match_stat` (`id`, `match_id`, `team_player_id`, `appeared`, `goals`, `assists`) VALUES (4, 102, 4, 1, 0, NULL);
INSERT INTO `player_match_stat` (`id`, `match_id`, `team_player_id`, `appeared`, `goals`, `assists`) VALUES (5, 101, 5, 1, 0, NULL);
INSERT INTO `player_match_stat` (`id`, `match_id`, `team_player_id`, `appeared`, `goals`, `assists`) VALUES (6, 101, 6, 1, 0, NULL);
INSERT INTO `player_match_stat` (`id`, `match_id`, `team_player_id`, `appeared`, `goals`, `assists`) VALUES (7, 101, 7, 1, 0, NULL);
INSERT INTO `player_match_stat` (`id`, `match_id`, `team_player_id`, `appeared`, `goals`, `assists`) VALUES (8, 101, 8, 1, 0, NULL);
INSERT INTO `player_match_stat` (`id`, `match_id`, `team_player_id`, `appeared`, `goals`, `assists`) VALUES (9, 104, 9, 1, 0, NULL);
INSERT INTO `player_match_stat` (`id`, `match_id`, `team_player_id`, `appeared`, `goals`, `assists`) VALUES (10, 104, 10, 1, 0, NULL);
INSERT INTO `player_match_stat` (`id`, `match_id`, `team_player_id`, `appeared`, `goals`, `assists`) VALUES (11, 104, 11, 1, 0, NULL);
INSERT INTO `player_match_stat` (`id`, `match_id`, `team_player_id`, `appeared`, `goals`, `assists`) VALUES (12, 104, 12, 1, 0, NULL);
INSERT INTO `player_match_stat` (`id`, `match_id`, `team_player_id`, `appeared`, `goals`, `assists`) VALUES (13, 7, 13, 1, 0, NULL);
INSERT INTO `player_match_stat` (`id`, `match_id`, `team_player_id`, `appeared`, `goals`, `assists`) VALUES (14, 7, 14, 1, 0, NULL);
INSERT INTO `player_match_stat` (`id`, `match_id`, `team_player_id`, `appeared`, `goals`, `assists`) VALUES (15, 7, 15, 1, 0, NULL);
INSERT INTO `player_match_stat` (`id`, `match_id`, `team_player_id`, `appeared`, `goals`, `assists`) VALUES (16, 7, 16, 1, 1, NULL);
INSERT INTO `player_match_stat` (`id`, `match_id`, `team_player_id`, `appeared`, `goals`, `assists`) VALUES (17, 93, 17, 1, 0, NULL);
INSERT INTO `player_match_stat` (`id`, `match_id`, `team_player_id`, `appeared`, `goals`, `assists`) VALUES (18, 93, 18, 1, 0, NULL);
INSERT INTO `player_match_stat` (`id`, `match_id`, `team_player_id`, `appeared`, `goals`, `assists`) VALUES (19, 93, 19, 1, 0, NULL);
INSERT INTO `player_match_stat` (`id`, `match_id`, `team_player_id`, `appeared`, `goals`, `assists`) VALUES (20, 93, 20, 1, 0, NULL);
INSERT INTO `player_match_stat` (`id`, `match_id`, `team_player_id`, `appeared`, `goals`, `assists`) VALUES (21, 102, 21, 1, 0, NULL);
INSERT INTO `player_match_stat` (`id`, `match_id`, `team_player_id`, `appeared`, `goals`, `assists`) VALUES (22, 102, 22, 1, 0, NULL);
INSERT INTO `player_match_stat` (`id`, `match_id`, `team_player_id`, `appeared`, `goals`, `assists`) VALUES (23, 102, 23, 1, 0, NULL);
INSERT INTO `player_match_stat` (`id`, `match_id`, `team_player_id`, `appeared`, `goals`, `assists`) VALUES (24, 102, 24, 1, 0, NULL);
INSERT INTO `honor` (`id`, `name`, `competition`) VALUES (1, '世界杯冠军', 'FIFA World Cup');
INSERT INTO `honor` (`id`, `name`, `competition`) VALUES (2, '欧洲杯冠军', 'UEFA EURO');
INSERT INTO `team_honor` (`id`, `team_id`, `honor_id`, `year`, `description`) VALUES (1, 1, 1, 1978, '1978年世界杯冠军');
INSERT INTO `team_honor` (`id`, `team_id`, `honor_id`, `year`, `description`) VALUES (2, 1, 1, 1986, '1986年世界杯冠军');
INSERT INTO `team_honor` (`id`, `team_id`, `honor_id`, `year`, `description`) VALUES (3, 1, 1, 2022, '2022年世界杯冠军');
INSERT INTO `team_honor` (`id`, `team_id`, `honor_id`, `year`, `description`) VALUES (4, 2, 1, 1998, '1998年世界杯冠军');
INSERT INTO `team_honor` (`id`, `team_id`, `honor_id`, `year`, `description`) VALUES (5, 2, 1, 2018, '2018年世界杯冠军');
INSERT INTO `team_honor` (`id`, `team_id`, `honor_id`, `year`, `description`) VALUES (6, 3, 1, 2010, '2010年世界杯冠军');
INSERT INTO `team_honor` (`id`, `team_id`, `honor_id`, `year`, `description`) VALUES (7, 3, 1, 2026, '2026年世界杯冠军');
INSERT INTO `team_honor` (`id`, `team_id`, `honor_id`, `year`, `description`) VALUES (8, 4, 1, 1958, '1958年世界杯冠军');
INSERT INTO `team_honor` (`id`, `team_id`, `honor_id`, `year`, `description`) VALUES (9, 4, 1, 1962, '1962年世界杯冠军');
INSERT INTO `team_honor` (`id`, `team_id`, `honor_id`, `year`, `description`) VALUES (10, 4, 1, 1970, '1970年世界杯冠军');
INSERT INTO `team_honor` (`id`, `team_id`, `honor_id`, `year`, `description`) VALUES (11, 4, 1, 1994, '1994年世界杯冠军');
INSERT INTO `team_honor` (`id`, `team_id`, `honor_id`, `year`, `description`) VALUES (12, 4, 1, 2002, '2002年世界杯冠军');
INSERT INTO `team_honor` (`id`, `team_id`, `honor_id`, `year`, `description`) VALUES (13, 6, 1, 1966, '1966年世界杯冠军');
INSERT INTO `team_honor` (`id`, `team_id`, `honor_id`, `year`, `description`) VALUES (14, 5, 2, 2016, '2016年欧洲杯冠军');
COMMIT;
