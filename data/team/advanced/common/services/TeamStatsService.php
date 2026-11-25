<?php
/**
 * 项目：2026世界杯球队信息系统
 * 文件职责：统一球队战绩统计服务（C2）：按比赛计算，不存冗余汇总，不关联球员统计行
 * 作者：邓佳怡（学号 2412612）
 * 创建日期：2026-11-25
 * 主要修改者：邓佳怡（学号 2412612）
 */
namespace common\services;

use Yii;
use common\models\Team;

class TeamStatsService
{
    const SUPPORTED_YEAR = 2026;

    /**
     * 统计只覆盖本站已录入、已结束且双方比分完整的2026届比赛；每场只计一次。
     * 比分含加时、不含点球大战，点球决胜的平局仍按平局计算。
     *
     * @param int|string $teamId 有效球队ID
     * @param int $year 首版仅支持2026
     * @return array
     * @throws \InvalidArgumentException 参数非法
     * @throws \DomainException 球队不存在
     */
    public function getSummary($teamId, $year = self::SUPPORTED_YEAR)
    {
        $id = filter_var($teamId, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if ($id === false) {
            throw new \InvalidArgumentException('球队ID必须是正整数。');
        }
        if (!is_int($year) && !ctype_digit((string) $year)) {
            throw new \InvalidArgumentException('赛事年份必须是整数。');
        }
        $year = (int) $year;
        if ($year !== self::SUPPORTED_YEAR) {
            throw new \InvalidArgumentException('本站仅支持2026届赛事统计。');
        }
        if (!Team::find()->where(['id' => $id])->exists()) {
            throw new \DomainException('球队不存在。');
        }

        $table = Yii::$app->db->tablePrefix . 'football_match';

        $sql = <<<SQL
SELECT
    COUNT(*) AS played,
    COALESCE(SUM(CASE
        WHEN (home_team_id = :id_win_home AND home_score > away_score)
          OR (away_team_id = :id_win_away AND away_score > home_score)
        THEN 1 ELSE 0 END), 0) AS wins,
    COALESCE(SUM(CASE WHEN home_score = away_score THEN 1 ELSE 0 END), 0) AS draws,
    COALESCE(SUM(CASE
        WHEN (home_team_id = :id_loss_home AND home_score < away_score)
          OR (away_team_id = :id_loss_away AND away_score < home_score)
        THEN 1 ELSE 0 END), 0) AS losses,
    COALESCE(SUM(CASE WHEN home_team_id = :id_gf_home THEN home_score ELSE away_score END), 0) AS goals_for,
    COALESCE(SUM(CASE WHEN home_team_id = :id_ga_home THEN away_score ELSE home_score END), 0) AS goals_against
FROM `{$table}`
WHERE tournament_year = :year
  AND status = 'finished'
  AND home_score IS NOT NULL
  AND away_score IS NOT NULL
  AND (home_team_id = :id_team_home OR away_team_id = :id_team_away)
SQL;

        $row = Yii::$app->db->createCommand($sql, [
            ':id_win_home' => $id,
            ':id_win_away' => $id,
            ':id_loss_home' => $id,
            ':id_loss_away' => $id,
            ':id_gf_home' => $id,
            ':id_ga_home' => $id,
            ':id_team_home' => $id,
            ':id_team_away' => $id,
            ':year' => $year,
        ])->queryOne();

        $summary = [
            'team_id' => (int) $id,
            'tournament_year' => $year,
            'played' => (int) ($row['played'] ?? 0),
            'wins' => (int) ($row['wins'] ?? 0),
            'draws' => (int) ($row['draws'] ?? 0),
            'losses' => (int) ($row['losses'] ?? 0),
            'goals_for' => (int) ($row['goals_for'] ?? 0),
            'goals_against' => (int) ($row['goals_against'] ?? 0),
        ];
        $summary['played'] = $summary['wins'] + $summary['draws'] + $summary['losses'];

        return $summary;
    }

    public static function message()
    {
        return '统计仅覆盖本站已录入的2026世界杯已结束比赛';
    }
}
