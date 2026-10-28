-- 团队胜平负及进失球：仅已导入且已结束、比分完整的2026比赛；含库外对手。
SELECT t.id, t.name_zh,
 COUNT(m.id) AS played,
 COALESCE(SUM(CASE WHEN (m.home_team_id=t.id AND m.home_score>m.away_score) OR (m.away_team_id=t.id AND m.away_score>m.home_score) THEN 1 ELSE 0 END),0) AS wins,
 COALESCE(SUM(CASE WHEN m.home_score=m.away_score THEN 1 ELSE 0 END),0) AS draws,
 COALESCE(SUM(CASE WHEN (m.home_team_id=t.id AND m.home_score<m.away_score) OR (m.away_team_id=t.id AND m.away_score<m.home_score) THEN 1 ELSE 0 END),0) AS losses,
 COALESCE(SUM(CASE WHEN m.home_team_id=t.id THEN m.home_score WHEN m.away_team_id=t.id THEN m.away_score ELSE 0 END),0) AS goals_for,
 COALESCE(SUM(CASE WHEN m.home_team_id=t.id THEN m.away_score WHEN m.away_team_id=t.id THEN m.home_score ELSE 0 END),0) AS goals_against
FROM team t LEFT JOIN football_match m
 ON (m.home_team_id=t.id OR m.away_team_id=t.id)
 AND m.tournament_year=2026 AND m.status='finished'
 AND m.home_score IS NOT NULL AND m.away_score IS NOT NULL
GROUP BY t.id,t.name_zh ORDER BY t.id;

-- 球员资料与已收录统计；没有统计记录时场次/进球保持NULL，页面显示“未收录”。
SELECT p.id,p.name_zh,t.name_zh AS team_name,tp.shirt_number,tp.position_code,
 s.recorded_matches,s.recorded_appearances,s.recorded_goals
FROM player p JOIN team_player tp ON tp.player_id=p.id AND tp.tournament_year=2026
JOIN team t ON t.id=tp.team_id
LEFT JOIN (
 SELECT ps.team_player_id,COUNT(*) AS recorded_matches,SUM(ps.appeared) AS recorded_appearances,SUM(ps.goals) AS recorded_goals
 FROM player_match_stat ps JOIN football_match m ON m.id=ps.match_id
 WHERE m.tournament_year=2026 AND m.status='finished'
 GROUP BY ps.team_player_id
) s ON s.team_player_id=tp.id ORDER BY p.id;
