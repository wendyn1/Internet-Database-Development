-- 2026世界杯精选数据：仅向新建空数据库导入。无DROP、无默认密码。

-- 创建12张表；不创建管理员、不导入虚构留言。

-- MySQL 5.7/8.x常用语法；未在真实MySQL服务执行，已做SQLite关系镜像检查。

SET NAMES utf8mb4;

CREATE TABLE `user` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(255) NOT NULL,
  `auth_key` VARCHAR(32) NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `password_reset_token` VARCHAR(255) NULL,
  `email` VARCHAR(255) NOT NULL,
  `status` SMALLINT NOT NULL,
  `created_at` INT NOT NULL,
  `updated_at` INT NOT NULL,
  `verification_token` VARCHAR(255) NULL,
  UNIQUE (`username`),
  UNIQUE (`email`),
  UNIQUE (`password_reset_token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `team` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name_zh` VARCHAR(80) NOT NULL,
  `name_en` VARCHAR(80) NOT NULL,
  `fifa_code` VARCHAR(3) NOT NULL,
  `country` VARCHAR(80) NOT NULL,
  `continent` VARCHAR(30) NOT NULL,
  `introduction` TEXT NOT NULL,
  `logo_path` VARCHAR(500) NULL,
  `source_ids` VARCHAR(255) NOT NULL,
  `source_page` INT NOT NULL,
  UNIQUE (`fifa_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `player` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name_zh` VARCHAR(80) NOT NULL,
  `name_en` VARCHAR(100) NOT NULL,
  `nationality` VARCHAR(80) NOT NULL,
  `birth_date` DATE NOT NULL,
  `photo_path` VARCHAR(500) NULL,
  `source_ids` VARCHAR(255) NOT NULL,
  `source_page` INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `team_player` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `team_id` INT NOT NULL,
  `player_id` INT NOT NULL,
  `tournament_year` INT NOT NULL,
  `shirt_number` INT NOT NULL,
  `position_code` VARCHAR(2) NOT NULL,
  `source_ids` VARCHAR(255) NOT NULL,
  UNIQUE (`team_id`, `player_id`, `tournament_year`),
  UNIQUE (`team_id`, `tournament_year`, `shirt_number`),
  FOREIGN KEY (`team_id`) REFERENCES `team` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  FOREIGN KEY (`player_id`) REFERENCES `player` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `coach` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name_zh` VARCHAR(80) NOT NULL,
  `name_en` VARCHAR(100) NOT NULL,
  `nationality` VARCHAR(80) NOT NULL,
  `introduction` TEXT NOT NULL,
  `photo_path` VARCHAR(500) NULL,
  `source_ids` VARCHAR(255) NOT NULL,
  `source_page` INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `team_coach` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `team_id` INT NOT NULL,
  `coach_id` INT NOT NULL,
  `tournament_year` INT NOT NULL,
  `role` VARCHAR(30) NOT NULL,
  `source_ids` VARCHAR(255) NOT NULL,
  UNIQUE (`team_id`, `coach_id`, `tournament_year`),
  FOREIGN KEY (`team_id`) REFERENCES `team` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  FOREIGN KEY (`coach_id`) REFERENCES `coach` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `venue` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name_zh` VARCHAR(80) NOT NULL,
  `name_en` VARCHAR(100) NOT NULL,
  `city` VARCHAR(80) NOT NULL,
  `country` VARCHAR(80) NOT NULL,
  `timezone` VARCHAR(50) NOT NULL,
  `source_ids` VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `football_match` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `fifa_match_number` INT NOT NULL,
  `tournament_year` INT NOT NULL,
  `stage` VARCHAR(30) NOT NULL,
  `venue_id` INT NOT NULL,
  `home_team_id` INT NULL,
  `away_team_id` INT NULL,
  `home_team_name` VARCHAR(80) NOT NULL,
  `away_team_name` VARCHAR(80) NOT NULL,
  `local_match_date` DATE NOT NULL,
  `kickoff_utc` DATETIME NOT NULL,
  `match_date_beijing` DATE NOT NULL,
  `status` VARCHAR(20) NOT NULL,
  `home_score` INT NULL,
  `away_score` INT NULL,
  `after_extra_time` SMALLINT NOT NULL,
  `source_ids` VARCHAR(255) NOT NULL,
  UNIQUE (`fifa_match_number`),
  FOREIGN KEY (`venue_id`) REFERENCES `venue` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  FOREIGN KEY (`home_team_id`) REFERENCES `team` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  FOREIGN KEY (`away_team_id`) REFERENCES `team` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `player_match_stat` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `match_id` INT NOT NULL,
  `team_player_id` INT NOT NULL,
  `appeared` SMALLINT NOT NULL,
  `goals` INT NOT NULL,
  `assists` INT NULL,
  `source_ids` VARCHAR(255) NOT NULL,
  UNIQUE (`match_id`, `team_player_id`),
  FOREIGN KEY (`match_id`) REFERENCES `football_match` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  FOREIGN KEY (`team_player_id`) REFERENCES `team_player` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `honor` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(80) NOT NULL,
  `competition` VARCHAR(80) NOT NULL,
  `source_ids` VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `team_honor` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `team_id` INT NOT NULL,
  `honor_id` INT NOT NULL,
  `year` INT NOT NULL,
  `description` VARCHAR(255) NOT NULL,
  `source_ids` VARCHAR(255) NOT NULL,
  UNIQUE (`team_id`, `honor_id`, `year`),
  FOREIGN KEY (`team_id`) REFERENCES `team` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  FOREIGN KEY (`honor_id`) REFERENCES `honor` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `comment` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `team_id` INT NULL,
  `player_id` INT NULL,
  `match_id` INT NULL,
  `nickname` VARCHAR(30) NOT NULL,
  `content` TEXT NOT NULL,
  `status` VARCHAR(20) NOT NULL,
  `created_at` DATETIME NOT NULL,
  `reviewed_by` INT NULL,
  `reviewed_at` DATETIME NULL,
  FOREIGN KEY (`team_id`) REFERENCES `team` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  FOREIGN KEY (`player_id`) REFERENCES `player` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  FOREIGN KEY (`match_id`) REFERENCES `football_match` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  FOREIGN KEY (`reviewed_by`) REFERENCES `user` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

START TRANSACTION;

INSERT INTO `team` (`id`, `name_zh`, `name_en`, `fifa_code`, `country`, `continent`, `introduction`, `logo_path`, `source_ids`, `source_page`) VALUES (1, '阿根廷', 'Argentina', 'ARG', '阿根廷', '南美洲', '传统世界杯冠军球队，适合展示球员资料与冠军荣誉。', NULL, 'S01', 2);

INSERT INTO `team` (`id`, `name_zh`, `name_en`, `fifa_code`, `country`, `continent`, `introduction`, `logo_path`, `source_ids`, `source_page`) VALUES (2, '法国', 'France', 'FRA', '法国', '欧洲', '拥有世界杯冠军历史，适合展示球员与比赛关联。', NULL, 'S01', 19);

INSERT INTO `team` (`id`, `name_zh`, `name_en`, `fifa_code`, `country`, `continent`, `introduction`, `logo_path`, `source_ids`, `source_page`) VALUES (3, '西班牙', 'Spain', 'ESP', '西班牙', '欧洲', '以国家队阵容、比赛与历史荣誉为核心展示内容。', NULL, 'S01', 41);

INSERT INTO `team` (`id`, `name_zh`, `name_en`, `fifa_code`, `country`, `continent`, `introduction`, `logo_path`, `source_ids`, `source_page`) VALUES (4, '巴西', 'Brazil', 'BRA', '巴西', '南美洲', '具有丰富世界杯冠军记录，适合荣誉与战绩图表。', NULL, 'S01', 7);

INSERT INTO `team` (`id`, `name_zh`, `name_en`, `fifa_code`, `country`, `continent`, `introduction`, `logo_path`, `source_ids`, `source_page`) VALUES (5, '葡萄牙', 'Portugal', 'POR', '葡萄牙', '欧洲', '展示球队阵容、球星资料与欧洲杯冠军荣誉。', NULL, 'S01', 35);

INSERT INTO `team` (`id`, `name_zh`, `name_en`, `fifa_code`, `country`, `continent`, `introduction`, `logo_path`, `source_ids`, `source_page`) VALUES (6, '英格兰', 'England', 'ENG', '英格兰', '欧洲', '具有世界杯冠军历史，适合球员资料和比赛展示。', NULL, 'S01', 18);

INSERT INTO `player` (`id`, `name_zh`, `name_en`, `nationality`, `birth_date`, `photo_path`, `source_ids`, `source_page`) VALUES (1, '埃米利亚诺·马丁内斯', 'Emiliano Martínez', '阿根廷', '1992-09-02', NULL, 'S01', 2);

INSERT INTO `player` (`id`, `name_zh`, `name_en`, `nationality`, `birth_date`, `photo_path`, `source_ids`, `source_page`) VALUES (2, '克里斯蒂安·罗梅罗', 'Cristian Romero', '阿根廷', '1998-04-27', NULL, 'S01', 2);

INSERT INTO `player` (`id`, `name_zh`, `name_en`, `nationality`, `birth_date`, `photo_path`, `source_ids`, `source_page`) VALUES (3, '恩佐·费尔南德斯', 'Enzo Fernández', '阿根廷', '2001-01-17', NULL, 'S01', 2);

INSERT INTO `player` (`id`, `name_zh`, `name_en`, `nationality`, `birth_date`, `photo_path`, `source_ids`, `source_page`) VALUES (4, '利昂内尔·梅西', 'Lionel Messi', '阿根廷', '1987-06-24', NULL, 'S01', 2);

INSERT INTO `player` (`id`, `name_zh`, `name_en`, `nationality`, `birth_date`, `photo_path`, `source_ids`, `source_page`) VALUES (5, '迈克·迈尼昂', 'Mike Maignan', '法国', '1995-07-03', NULL, 'S01', 19);

INSERT INTO `player` (`id`, `name_zh`, `name_en`, `nationality`, `birth_date`, `photo_path`, `source_ids`, `source_page`) VALUES (6, '朱尔·孔德', 'Jules Koundé', '法国', '1998-11-12', NULL, 'S01', 19);

INSERT INTO `player` (`id`, `name_zh`, `name_en`, `nationality`, `birth_date`, `photo_path`, `source_ids`, `source_page`) VALUES (7, '奥雷利安·楚阿梅尼', 'Aurélien Tchouaméni', '法国', '2000-01-27', NULL, 'S01', 19);

INSERT INTO `player` (`id`, `name_zh`, `name_en`, `nationality`, `birth_date`, `photo_path`, `source_ids`, `source_page`) VALUES (8, '基利安·姆巴佩', 'Kylian Mbappé', '法国', '1998-12-20', NULL, 'S01', 19);

INSERT INTO `player` (`id`, `name_zh`, `name_en`, `nationality`, `birth_date`, `photo_path`, `source_ids`, `source_page`) VALUES (9, '乌奈·西蒙', 'Unai Simón', '西班牙', '1997-06-11', NULL, 'S01', 41);

INSERT INTO `player` (`id`, `name_zh`, `name_en`, `nationality`, `birth_date`, `photo_path`, `source_ids`, `source_page`) VALUES (10, '保·库巴西', 'Pau Cubarsí', '西班牙', '2007-01-22', NULL, 'S01', 41);

INSERT INTO `player` (`id`, `name_zh`, `name_en`, `nationality`, `birth_date`, `photo_path`, `source_ids`, `source_page`) VALUES (11, '佩德里', 'Pedri', '西班牙', '2002-11-25', NULL, 'S01', 41);

INSERT INTO `player` (`id`, `name_zh`, `name_en`, `nationality`, `birth_date`, `photo_path`, `source_ids`, `source_page`) VALUES (12, '拉明·亚马尔', 'Lamine Yamal', '西班牙', '2007-07-13', NULL, 'S01', 41);

INSERT INTO `player` (`id`, `name_zh`, `name_en`, `nationality`, `birth_date`, `photo_path`, `source_ids`, `source_page`) VALUES (13, '阿利松·贝克尔', 'Alisson Becker', '巴西', '1992-10-02', NULL, 'S01', 7);

INSERT INTO `player` (`id`, `name_zh`, `name_en`, `nationality`, `birth_date`, `photo_path`, `source_ids`, `source_page`) VALUES (14, '马尔基尼奥斯', 'Marquinhos', '巴西', '1994-05-14', NULL, 'S01', 7);

INSERT INTO `player` (`id`, `name_zh`, `name_en`, `nationality`, `birth_date`, `photo_path`, `source_ids`, `source_page`) VALUES (15, '布鲁诺·吉马良斯', 'Bruno Guimarães', '巴西', '1997-11-16', NULL, 'S01', 7);

INSERT INTO `player` (`id`, `name_zh`, `name_en`, `nationality`, `birth_date`, `photo_path`, `source_ids`, `source_page`) VALUES (16, '维尼修斯·儒尼奥尔', 'Vinícius Júnior', '巴西', '2000-07-12', NULL, 'S01', 7);

INSERT INTO `player` (`id`, `name_zh`, `name_en`, `nationality`, `birth_date`, `photo_path`, `source_ids`, `source_page`) VALUES (17, '迪奥戈·科斯塔', 'Diogo Costa', '葡萄牙', '1999-09-19', NULL, 'S01', 35);

INSERT INTO `player` (`id`, `name_zh`, `name_en`, `nationality`, `birth_date`, `photo_path`, `source_ids`, `source_page`) VALUES (18, '鲁本·迪亚斯', 'Rúben Dias', '葡萄牙', '1997-05-14', NULL, 'S01', 35);

INSERT INTO `player` (`id`, `name_zh`, `name_en`, `nationality`, `birth_date`, `photo_path`, `source_ids`, `source_page`) VALUES (19, '布鲁诺·费尔南德斯', 'Bruno Fernandes', '葡萄牙', '1994-09-08', NULL, 'S01', 35);

INSERT INTO `player` (`id`, `name_zh`, `name_en`, `nationality`, `birth_date`, `photo_path`, `source_ids`, `source_page`) VALUES (20, '克里斯蒂亚诺·罗纳尔多', 'Cristiano Ronaldo', '葡萄牙', '1985-02-05', NULL, 'S01', 35);

INSERT INTO `player` (`id`, `name_zh`, `name_en`, `nationality`, `birth_date`, `photo_path`, `source_ids`, `source_page`) VALUES (21, '乔丹·皮克福德', 'Jordan Pickford', '英格兰', '1994-03-07', NULL, 'S01', 18);

INSERT INTO `player` (`id`, `name_zh`, `name_en`, `nationality`, `birth_date`, `photo_path`, `source_ids`, `source_page`) VALUES (22, '约翰·斯通斯', 'John Stones', '英格兰', '1994-05-28', NULL, 'S01', 18);

INSERT INTO `player` (`id`, `name_zh`, `name_en`, `nationality`, `birth_date`, `photo_path`, `source_ids`, `source_page`) VALUES (23, '裘德·贝林厄姆', 'Jude Bellingham', '英格兰', '2003-06-29', NULL, 'S01', 18);

INSERT INTO `player` (`id`, `name_zh`, `name_en`, `nationality`, `birth_date`, `photo_path`, `source_ids`, `source_page`) VALUES (24, '哈里·凯恩', 'Harry Kane', '英格兰', '1993-07-28', NULL, 'S01', 18);

INSERT INTO `team_player` (`id`, `team_id`, `player_id`, `tournament_year`, `shirt_number`, `position_code`, `source_ids`) VALUES (1, 1, 1, 2026, 23, 'GK', 'S01');

INSERT INTO `team_player` (`id`, `team_id`, `player_id`, `tournament_year`, `shirt_number`, `position_code`, `source_ids`) VALUES (2, 1, 2, 2026, 13, 'DF', 'S01');

INSERT INTO `team_player` (`id`, `team_id`, `player_id`, `tournament_year`, `shirt_number`, `position_code`, `source_ids`) VALUES (3, 1, 3, 2026, 24, 'MF', 'S01');

INSERT INTO `team_player` (`id`, `team_id`, `player_id`, `tournament_year`, `shirt_number`, `position_code`, `source_ids`) VALUES (4, 1, 4, 2026, 10, 'FW', 'S01');

INSERT INTO `team_player` (`id`, `team_id`, `player_id`, `tournament_year`, `shirt_number`, `position_code`, `source_ids`) VALUES (5, 2, 5, 2026, 16, 'GK', 'S01');

INSERT INTO `team_player` (`id`, `team_id`, `player_id`, `tournament_year`, `shirt_number`, `position_code`, `source_ids`) VALUES (6, 2, 6, 2026, 5, 'DF', 'S01');

INSERT INTO `team_player` (`id`, `team_id`, `player_id`, `tournament_year`, `shirt_number`, `position_code`, `source_ids`) VALUES (7, 2, 7, 2026, 8, 'MF', 'S01');

INSERT INTO `team_player` (`id`, `team_id`, `player_id`, `tournament_year`, `shirt_number`, `position_code`, `source_ids`) VALUES (8, 2, 8, 2026, 10, 'FW', 'S01');

INSERT INTO `team_player` (`id`, `team_id`, `player_id`, `tournament_year`, `shirt_number`, `position_code`, `source_ids`) VALUES (9, 3, 9, 2026, 23, 'GK', 'S01');

INSERT INTO `team_player` (`id`, `team_id`, `player_id`, `tournament_year`, `shirt_number`, `position_code`, `source_ids`) VALUES (10, 3, 10, 2026, 22, 'DF', 'S01');

INSERT INTO `team_player` (`id`, `team_id`, `player_id`, `tournament_year`, `shirt_number`, `position_code`, `source_ids`) VALUES (11, 3, 11, 2026, 20, 'MF', 'S01');

INSERT INTO `team_player` (`id`, `team_id`, `player_id`, `tournament_year`, `shirt_number`, `position_code`, `source_ids`) VALUES (12, 3, 12, 2026, 19, 'FW', 'S01');

INSERT INTO `team_player` (`id`, `team_id`, `player_id`, `tournament_year`, `shirt_number`, `position_code`, `source_ids`) VALUES (13, 4, 13, 2026, 1, 'GK', 'S01');

INSERT INTO `team_player` (`id`, `team_id`, `player_id`, `tournament_year`, `shirt_number`, `position_code`, `source_ids`) VALUES (14, 4, 14, 2026, 4, 'DF', 'S01');

INSERT INTO `team_player` (`id`, `team_id`, `player_id`, `tournament_year`, `shirt_number`, `position_code`, `source_ids`) VALUES (15, 4, 15, 2026, 8, 'MF', 'S01');

INSERT INTO `team_player` (`id`, `team_id`, `player_id`, `tournament_year`, `shirt_number`, `position_code`, `source_ids`) VALUES (16, 4, 16, 2026, 7, 'FW', 'S01');

INSERT INTO `team_player` (`id`, `team_id`, `player_id`, `tournament_year`, `shirt_number`, `position_code`, `source_ids`) VALUES (17, 5, 17, 2026, 1, 'GK', 'S01');

INSERT INTO `team_player` (`id`, `team_id`, `player_id`, `tournament_year`, `shirt_number`, `position_code`, `source_ids`) VALUES (18, 5, 18, 2026, 3, 'DF', 'S01');

INSERT INTO `team_player` (`id`, `team_id`, `player_id`, `tournament_year`, `shirt_number`, `position_code`, `source_ids`) VALUES (19, 5, 19, 2026, 8, 'MF', 'S01');

INSERT INTO `team_player` (`id`, `team_id`, `player_id`, `tournament_year`, `shirt_number`, `position_code`, `source_ids`) VALUES (20, 5, 20, 2026, 7, 'FW', 'S01');

INSERT INTO `team_player` (`id`, `team_id`, `player_id`, `tournament_year`, `shirt_number`, `position_code`, `source_ids`) VALUES (21, 6, 21, 2026, 1, 'GK', 'S01');

INSERT INTO `team_player` (`id`, `team_id`, `player_id`, `tournament_year`, `shirt_number`, `position_code`, `source_ids`) VALUES (22, 6, 22, 2026, 5, 'DF', 'S01');

INSERT INTO `team_player` (`id`, `team_id`, `player_id`, `tournament_year`, `shirt_number`, `position_code`, `source_ids`) VALUES (23, 6, 23, 2026, 10, 'MF', 'S01');

INSERT INTO `team_player` (`id`, `team_id`, `player_id`, `tournament_year`, `shirt_number`, `position_code`, `source_ids`) VALUES (24, 6, 24, 2026, 9, 'FW', 'S01');

INSERT INTO `coach` (`id`, `name_zh`, `name_en`, `nationality`, `introduction`, `photo_path`, `source_ids`, `source_page`) VALUES (1, '利昂内尔·斯卡洛尼', 'Lionel Scaloni', '阿根廷', '2026世界杯期间阿根廷国家队主教练；本记录不代表2026年10月现任情况。', NULL, 'S01', 2);

INSERT INTO `coach` (`id`, `name_zh`, `name_en`, `nationality`, `introduction`, `photo_path`, `source_ids`, `source_page`) VALUES (2, '迪迪埃·德尚', 'Didier Deschamps', '法国', '2026世界杯期间法国国家队主教练；本记录不代表2026年10月现任情况。', NULL, 'S01', 19);

INSERT INTO `coach` (`id`, `name_zh`, `name_en`, `nationality`, `introduction`, `photo_path`, `source_ids`, `source_page`) VALUES (3, '路易斯·德拉富恩特', 'Luis de la Fuente', '西班牙', '2026世界杯期间西班牙国家队主教练；本记录不代表2026年10月现任情况。', NULL, 'S01', 41);

INSERT INTO `coach` (`id`, `name_zh`, `name_en`, `nationality`, `introduction`, `photo_path`, `source_ids`, `source_page`) VALUES (4, '卡洛·安切洛蒂', 'Carlo Ancelotti', '意大利', '2026世界杯期间巴西国家队主教练；本记录不代表2026年10月现任情况。', NULL, 'S01', 7);

INSERT INTO `coach` (`id`, `name_zh`, `name_en`, `nationality`, `introduction`, `photo_path`, `source_ids`, `source_page`) VALUES (5, '罗伯托·马丁内斯', 'Roberto Martínez', '西班牙', '2026世界杯期间葡萄牙国家队主教练；本记录不代表2026年10月现任情况。', NULL, 'S01', 35);

INSERT INTO `coach` (`id`, `name_zh`, `name_en`, `nationality`, `introduction`, `photo_path`, `source_ids`, `source_page`) VALUES (6, '托马斯·图赫尔', 'Thomas Tuchel', '德国', '2026世界杯期间英格兰国家队主教练；本记录不代表2026年10月现任情况。', NULL, 'S01', 18);

INSERT INTO `team_coach` (`id`, `team_id`, `coach_id`, `tournament_year`, `role`, `source_ids`) VALUES (1, 1, 1, 2026, 'head_coach', 'S01');

INSERT INTO `team_coach` (`id`, `team_id`, `coach_id`, `tournament_year`, `role`, `source_ids`) VALUES (2, 2, 2, 2026, 'head_coach', 'S01');

INSERT INTO `team_coach` (`id`, `team_id`, `coach_id`, `tournament_year`, `role`, `source_ids`) VALUES (3, 3, 3, 2026, 'head_coach', 'S01');

INSERT INTO `team_coach` (`id`, `team_id`, `coach_id`, `tournament_year`, `role`, `source_ids`) VALUES (4, 4, 4, 2026, 'head_coach', 'S01');

INSERT INTO `team_coach` (`id`, `team_id`, `coach_id`, `tournament_year`, `role`, `source_ids`) VALUES (5, 5, 5, 2026, 'head_coach', 'S01');

INSERT INTO `team_coach` (`id`, `team_id`, `coach_id`, `tournament_year`, `role`, `source_ids`) VALUES (6, 6, 6, 2026, 'head_coach', 'S01');

INSERT INTO `venue` (`id`, `name_zh`, `name_en`, `city`, `country`, `timezone`, `source_ids`) VALUES (1, '纽约新泽西体育场', 'New York New Jersey Stadium', 'East Rutherford', '美国', 'America/New_York', 'S03;S08;S15');

INSERT INTO `venue` (`id`, `name_zh`, `name_en`, `city`, `country`, `timezone`, `source_ids`) VALUES (2, '休斯敦体育场', 'Houston Stadium', 'Houston', '美国', 'America/Chicago', 'S05;S06');

INSERT INTO `venue` (`id`, `name_zh`, `name_en`, `city`, `country`, `timezone`, `source_ids`) VALUES (3, '达拉斯体育场', 'Dallas Stadium', 'Arlington', '美国', 'America/Chicago', 'S09;S11;S12');

INSERT INTO `venue` (`id`, `name_zh`, `name_en`, `city`, `country`, `timezone`, `source_ids`) VALUES (4, '亚特兰大体育场', 'Atlanta Stadium', 'Atlanta', '美国', 'America/New_York', 'S13');

INSERT INTO `venue` (`id`, `name_zh`, `name_en`, `city`, `country`, `timezone`, `source_ids`) VALUES (5, '迈阿密体育场', 'Miami Stadium', 'Miami Gardens', '美国', 'America/New_York', 'S14');

INSERT INTO `football_match` (`id`, `fifa_match_number`, `tournament_year`, `stage`, `venue_id`, `home_team_id`, `away_team_id`, `home_team_name`, `away_team_name`, `local_match_date`, `kickoff_utc`, `match_date_beijing`, `status`, `home_score`, `away_score`, `after_extra_time`, `source_ids`) VALUES (7, 7, 2026, 'group', 1, 4, NULL, '巴西', '摩洛哥', '2026-06-13', '2026-06-13 22:00:00', '2026-06-14', 'finished', 1, 1, 0, 'S03;S04');

INSERT INTO `football_match` (`id`, `fifa_match_number`, `tournament_year`, `stage`, `venue_id`, `home_team_id`, `away_team_id`, `home_team_name`, `away_team_name`, `local_match_date`, `kickoff_utc`, `match_date_beijing`, `status`, `home_score`, `away_score`, `after_extra_time`, `source_ids`) VALUES (76, 76, 2026, 'round_of_32', 2, 4, NULL, '巴西', '日本', '2026-06-29', '2026-06-29 17:00:00', '2026-06-30', 'finished', 2, 1, 0, 'S05;S06');

INSERT INTO `football_match` (`id`, `fifa_match_number`, `tournament_year`, `stage`, `venue_id`, `home_team_id`, `away_team_id`, `home_team_name`, `away_team_name`, `local_match_date`, `kickoff_utc`, `match_date_beijing`, `status`, `home_score`, `away_score`, `after_extra_time`, `source_ids`) VALUES (91, 91, 2026, 'round_of_16', 1, 4, NULL, '巴西', '挪威', '2026-07-05', '2026-07-05 20:00:00', '2026-07-06', 'finished', 1, 2, 0, 'S07;S08');

INSERT INTO `football_match` (`id`, `fifa_match_number`, `tournament_year`, `stage`, `venue_id`, `home_team_id`, `away_team_id`, `home_team_name`, `away_team_name`, `local_match_date`, `kickoff_utc`, `match_date_beijing`, `status`, `home_score`, `away_score`, `after_extra_time`, `source_ids`) VALUES (93, 93, 2026, 'round_of_16', 3, 5, 3, '葡萄牙', '西班牙', '2026-07-06', '2026-07-06 19:00:00', '2026-07-07', 'finished', 0, 1, 0, 'S09;S10;S02');

INSERT INTO `football_match` (`id`, `fifa_match_number`, `tournament_year`, `stage`, `venue_id`, `home_team_id`, `away_team_id`, `home_team_name`, `away_team_name`, `local_match_date`, `kickoff_utc`, `match_date_beijing`, `status`, `home_score`, `away_score`, `after_extra_time`, `source_ids`) VALUES (101, 101, 2026, 'semi_final', 3, 2, 3, '法国', '西班牙', '2026-07-14', '2026-07-14 19:00:00', '2026-07-15', 'finished', 0, 2, 0, 'S11;S12');

INSERT INTO `football_match` (`id`, `fifa_match_number`, `tournament_year`, `stage`, `venue_id`, `home_team_id`, `away_team_id`, `home_team_name`, `away_team_name`, `local_match_date`, `kickoff_utc`, `match_date_beijing`, `status`, `home_score`, `away_score`, `after_extra_time`, `source_ids`) VALUES (102, 102, 2026, 'semi_final', 4, 6, 1, '英格兰', '阿根廷', '2026-07-15', '2026-07-15 19:00:00', '2026-07-16', 'finished', 1, 2, 0, 'S13');

INSERT INTO `football_match` (`id`, `fifa_match_number`, `tournament_year`, `stage`, `venue_id`, `home_team_id`, `away_team_id`, `home_team_name`, `away_team_name`, `local_match_date`, `kickoff_utc`, `match_date_beijing`, `status`, `home_score`, `away_score`, `after_extra_time`, `source_ids`) VALUES (103, 103, 2026, 'third_place', 5, 2, 6, '法国', '英格兰', '2026-07-18', '2026-07-18 21:00:00', '2026-07-19', 'finished', 4, 6, 0, 'S14;S02');

INSERT INTO `football_match` (`id`, `fifa_match_number`, `tournament_year`, `stage`, `venue_id`, `home_team_id`, `away_team_id`, `home_team_name`, `away_team_name`, `local_match_date`, `kickoff_utc`, `match_date_beijing`, `status`, `home_score`, `away_score`, `after_extra_time`, `source_ids`) VALUES (104, 104, 2026, 'final', 1, 3, 1, '西班牙', '阿根廷', '2026-07-19', '2026-07-19 19:00:00', '2026-07-20', 'finished', 1, 0, 1, 'S15;S16;S02');

INSERT INTO `player_match_stat` (`id`, `match_id`, `team_player_id`, `appeared`, `goals`, `assists`, `source_ids`) VALUES (1, 102, 1, 1, 0, NULL, 'S13');

INSERT INTO `player_match_stat` (`id`, `match_id`, `team_player_id`, `appeared`, `goals`, `assists`, `source_ids`) VALUES (2, 102, 2, 1, 0, NULL, 'S13');

INSERT INTO `player_match_stat` (`id`, `match_id`, `team_player_id`, `appeared`, `goals`, `assists`, `source_ids`) VALUES (3, 102, 3, 1, 1, NULL, 'S13');

INSERT INTO `player_match_stat` (`id`, `match_id`, `team_player_id`, `appeared`, `goals`, `assists`, `source_ids`) VALUES (4, 102, 4, 1, 0, NULL, 'S13');

INSERT INTO `player_match_stat` (`id`, `match_id`, `team_player_id`, `appeared`, `goals`, `assists`, `source_ids`) VALUES (5, 101, 5, 1, 0, NULL, 'S12');

INSERT INTO `player_match_stat` (`id`, `match_id`, `team_player_id`, `appeared`, `goals`, `assists`, `source_ids`) VALUES (6, 101, 6, 1, 0, NULL, 'S12');

INSERT INTO `player_match_stat` (`id`, `match_id`, `team_player_id`, `appeared`, `goals`, `assists`, `source_ids`) VALUES (7, 101, 7, 1, 0, NULL, 'S12');

INSERT INTO `player_match_stat` (`id`, `match_id`, `team_player_id`, `appeared`, `goals`, `assists`, `source_ids`) VALUES (8, 101, 8, 1, 0, NULL, 'S12');

INSERT INTO `player_match_stat` (`id`, `match_id`, `team_player_id`, `appeared`, `goals`, `assists`, `source_ids`) VALUES (9, 104, 9, 1, 0, NULL, 'S16');

INSERT INTO `player_match_stat` (`id`, `match_id`, `team_player_id`, `appeared`, `goals`, `assists`, `source_ids`) VALUES (10, 104, 10, 1, 0, NULL, 'S16');

INSERT INTO `player_match_stat` (`id`, `match_id`, `team_player_id`, `appeared`, `goals`, `assists`, `source_ids`) VALUES (11, 104, 11, 1, 0, NULL, 'S16');

INSERT INTO `player_match_stat` (`id`, `match_id`, `team_player_id`, `appeared`, `goals`, `assists`, `source_ids`) VALUES (12, 104, 12, 1, 0, NULL, 'S16');

INSERT INTO `player_match_stat` (`id`, `match_id`, `team_player_id`, `appeared`, `goals`, `assists`, `source_ids`) VALUES (13, 7, 13, 1, 0, NULL, 'S03;S04');

INSERT INTO `player_match_stat` (`id`, `match_id`, `team_player_id`, `appeared`, `goals`, `assists`, `source_ids`) VALUES (14, 7, 14, 1, 0, NULL, 'S03;S04');

INSERT INTO `player_match_stat` (`id`, `match_id`, `team_player_id`, `appeared`, `goals`, `assists`, `source_ids`) VALUES (15, 7, 15, 1, 0, NULL, 'S03;S04');

INSERT INTO `player_match_stat` (`id`, `match_id`, `team_player_id`, `appeared`, `goals`, `assists`, `source_ids`) VALUES (16, 7, 16, 1, 1, NULL, 'S03;S04');

INSERT INTO `player_match_stat` (`id`, `match_id`, `team_player_id`, `appeared`, `goals`, `assists`, `source_ids`) VALUES (17, 93, 17, 1, 0, NULL, 'S10');

INSERT INTO `player_match_stat` (`id`, `match_id`, `team_player_id`, `appeared`, `goals`, `assists`, `source_ids`) VALUES (18, 93, 18, 1, 0, NULL, 'S10');

INSERT INTO `player_match_stat` (`id`, `match_id`, `team_player_id`, `appeared`, `goals`, `assists`, `source_ids`) VALUES (19, 93, 19, 1, 0, NULL, 'S10');

INSERT INTO `player_match_stat` (`id`, `match_id`, `team_player_id`, `appeared`, `goals`, `assists`, `source_ids`) VALUES (20, 93, 20, 1, 0, NULL, 'S10');

INSERT INTO `player_match_stat` (`id`, `match_id`, `team_player_id`, `appeared`, `goals`, `assists`, `source_ids`) VALUES (21, 102, 21, 1, 0, NULL, 'S13');

INSERT INTO `player_match_stat` (`id`, `match_id`, `team_player_id`, `appeared`, `goals`, `assists`, `source_ids`) VALUES (22, 102, 22, 1, 0, NULL, 'S13');

INSERT INTO `player_match_stat` (`id`, `match_id`, `team_player_id`, `appeared`, `goals`, `assists`, `source_ids`) VALUES (23, 102, 23, 1, 0, NULL, 'S13');

INSERT INTO `player_match_stat` (`id`, `match_id`, `team_player_id`, `appeared`, `goals`, `assists`, `source_ids`) VALUES (24, 102, 24, 1, 0, NULL, 'S13');

INSERT INTO `honor` (`id`, `name`, `competition`, `source_ids`) VALUES (1, '世界杯冠军', 'FIFA World Cup', 'S17;S18');

INSERT INTO `honor` (`id`, `name`, `competition`, `source_ids`) VALUES (2, '欧洲杯冠军', 'UEFA EURO', 'S19');

INSERT INTO `team_honor` (`id`, `team_id`, `honor_id`, `year`, `description`, `source_ids`) VALUES (1, 1, 1, 1978, '1978年世界杯冠军', 'S20');

INSERT INTO `team_honor` (`id`, `team_id`, `honor_id`, `year`, `description`, `source_ids`) VALUES (2, 1, 1, 1986, '1986年世界杯冠军', 'S20');

INSERT INTO `team_honor` (`id`, `team_id`, `honor_id`, `year`, `description`, `source_ids`) VALUES (3, 1, 1, 2022, '2022年世界杯冠军', 'S20');

INSERT INTO `team_honor` (`id`, `team_id`, `honor_id`, `year`, `description`, `source_ids`) VALUES (4, 2, 1, 1998, '1998年世界杯冠军', 'S17;S18');

INSERT INTO `team_honor` (`id`, `team_id`, `honor_id`, `year`, `description`, `source_ids`) VALUES (5, 2, 1, 2018, '2018年世界杯冠军', 'S17;S18');

INSERT INTO `team_honor` (`id`, `team_id`, `honor_id`, `year`, `description`, `source_ids`) VALUES (6, 3, 1, 2010, '2010年世界杯冠军', 'S17;S18');

INSERT INTO `team_honor` (`id`, `team_id`, `honor_id`, `year`, `description`, `source_ids`) VALUES (7, 3, 1, 2026, '2026年世界杯冠军', 'S21');

INSERT INTO `team_honor` (`id`, `team_id`, `honor_id`, `year`, `description`, `source_ids`) VALUES (8, 4, 1, 1958, '1958年世界杯冠军', 'S17;S18');

INSERT INTO `team_honor` (`id`, `team_id`, `honor_id`, `year`, `description`, `source_ids`) VALUES (9, 4, 1, 1962, '1962年世界杯冠军', 'S17;S18');

INSERT INTO `team_honor` (`id`, `team_id`, `honor_id`, `year`, `description`, `source_ids`) VALUES (10, 4, 1, 1970, '1970年世界杯冠军', 'S17;S18');

INSERT INTO `team_honor` (`id`, `team_id`, `honor_id`, `year`, `description`, `source_ids`) VALUES (11, 4, 1, 1994, '1994年世界杯冠军', 'S17;S18');

INSERT INTO `team_honor` (`id`, `team_id`, `honor_id`, `year`, `description`, `source_ids`) VALUES (12, 4, 1, 2002, '2002年世界杯冠军', 'S17;S18');

INSERT INTO `team_honor` (`id`, `team_id`, `honor_id`, `year`, `description`, `source_ids`) VALUES (13, 6, 1, 1966, '1966年世界杯冠军', 'S17;S18');

INSERT INTO `team_honor` (`id`, `team_id`, `honor_id`, `year`, `description`, `source_ids`) VALUES (14, 5, 2, 2016, '2016年欧洲杯冠军', 'S19');

COMMIT;
