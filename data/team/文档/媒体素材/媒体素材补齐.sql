-- =============================================================================
-- 项目：2026世界杯球队信息系统
-- 文件职责：媒体素材补齐SQL（消除B4缺图占位显示）
-- 作者：王乐妍（2413423）
-- 创建日期：2026-11-22
--
-- 目的：为 team.logo_path、player.photo_path、coach.photo_path 补齐本地素材路径，
--       消除前台球队/球员/教练详情页的“暂无队徽/暂无照片”占位显示。
--
-- 素材文件位置：frontend/web/uploads/{team,player,coach}/<主键ID>.jpg
-- 路径约定：数据库中保存相对于 frontend/web 的相对路径（如 uploads/team/1.jpg），
--           与 frontend/views/{team,player,coach}/view.php 中 Html::img 的相对路径
--           用法一致，不写死本机端口。
--
-- 执行方式：仅在本人本机数据库执行一次（可重复执行，结果幂等）。
--           不修改组长的建表/样本脚本；正式 data/install.sql 由部署阶段汇总时再合并。
-- 来源记录：见同目录 媒体素材来源记录.md。
-- =============================================================================

-- ---------------------------------------------------------------- 球队队徽（6）
UPDATE `team` SET `logo_path` = 'uploads/team/1.jpg' WHERE `id` = 1;
UPDATE `team` SET `logo_path` = 'uploads/team/2.jpg' WHERE `id` = 2;
UPDATE `team` SET `logo_path` = 'uploads/team/3.jpg' WHERE `id` = 3;
UPDATE `team` SET `logo_path` = 'uploads/team/4.jpg' WHERE `id` = 4;
UPDATE `team` SET `logo_path` = 'uploads/team/5.jpg' WHERE `id` = 5;
UPDATE `team` SET `logo_path` = 'uploads/team/6.jpg' WHERE `id` = 6;

-- --------------------------------------------------------------- 球员照片（24）
UPDATE `player` SET `photo_path` = 'uploads/player/1.jpg'  WHERE `id` = 1;
UPDATE `player` SET `photo_path` = 'uploads/player/2.jpg'  WHERE `id` = 2;
UPDATE `player` SET `photo_path` = 'uploads/player/3.jpg'  WHERE `id` = 3;
UPDATE `player` SET `photo_path` = 'uploads/player/4.jpg'  WHERE `id` = 4;
UPDATE `player` SET `photo_path` = 'uploads/player/5.jpg'  WHERE `id` = 5;
UPDATE `player` SET `photo_path` = 'uploads/player/6.jpg'  WHERE `id` = 6;
UPDATE `player` SET `photo_path` = 'uploads/player/7.jpg'  WHERE `id` = 7;
UPDATE `player` SET `photo_path` = 'uploads/player/8.jpg'  WHERE `id` = 8;
UPDATE `player` SET `photo_path` = 'uploads/player/9.jpg'  WHERE `id` = 9;
UPDATE `player` SET `photo_path` = 'uploads/player/10.jpg' WHERE `id` = 10;
UPDATE `player` SET `photo_path` = 'uploads/player/11.jpg' WHERE `id` = 11;
UPDATE `player` SET `photo_path` = 'uploads/player/12.jpg' WHERE `id` = 12;
UPDATE `player` SET `photo_path` = 'uploads/player/13.jpg' WHERE `id` = 13;
UPDATE `player` SET `photo_path` = 'uploads/player/14.jpg' WHERE `id` = 14;
UPDATE `player` SET `photo_path` = 'uploads/player/15.jpg' WHERE `id` = 15;
UPDATE `player` SET `photo_path` = 'uploads/player/16.jpg' WHERE `id` = 16;
UPDATE `player` SET `photo_path` = 'uploads/player/17.jpg' WHERE `id` = 17;
UPDATE `player` SET `photo_path` = 'uploads/player/18.jpg' WHERE `id` = 18;
UPDATE `player` SET `photo_path` = 'uploads/player/19.jpg' WHERE `id` = 19;
UPDATE `player` SET `photo_path` = 'uploads/player/20.jpg' WHERE `id` = 20;
UPDATE `player` SET `photo_path` = 'uploads/player/21.jpg' WHERE `id` = 21;
UPDATE `player` SET `photo_path` = 'uploads/player/22.jpg' WHERE `id` = 22;
UPDATE `player` SET `photo_path` = 'uploads/player/23.jpg' WHERE `id` = 23;
UPDATE `player` SET `photo_path` = 'uploads/player/24.jpg' WHERE `id` = 24;

-- --------------------------------------------------------------- 教练照片（6）
UPDATE `coach` SET `photo_path` = 'uploads/coach/1.jpg' WHERE `id` = 1;
UPDATE `coach` SET `photo_path` = 'uploads/coach/2.jpg' WHERE `id` = 2;
UPDATE `coach` SET `photo_path` = 'uploads/coach/3.jpg' WHERE `id` = 3;
UPDATE `coach` SET `photo_path` = 'uploads/coach/4.jpg' WHERE `id` = 4;
UPDATE `coach` SET `photo_path` = 'uploads/coach/5.jpg' WHERE `id` = 5;
UPDATE `coach` SET `photo_path` = 'uploads/coach/6.jpg' WHERE `id` = 6;

-- =============================================================================
-- 回滚脚本（如需撤销，执行以下语句恢复为空路径，占位显示自动恢复）
-- =============================================================================
-- UPDATE `team`  SET `logo_path`  = NULL;
-- UPDATE `player` SET `photo_path` = NULL;
-- UPDATE `coach` SET `photo_path` = NULL;
