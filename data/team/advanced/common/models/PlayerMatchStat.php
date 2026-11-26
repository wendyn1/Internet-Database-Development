<?php
/**
 * 项目：2026世界杯球队信息系统
 * 文件职责：球员比赛统计模型（C1）：仅已结束比赛、阵容与球队匹配、进球不超本队比分
 * 作者：邓佳怡（学号 2412612）
 * 主要修改者：邓佳怡（学号 2412612）
 */
namespace common\models;

use yii\db\ActiveRecord;

class PlayerMatchStat extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%player_match_stat}}';
    }

    public function rules()
    {
        return [
            ['assists', 'default', 'value' => null],
            [['match_id', 'team_player_id', 'appeared', 'goals'], 'required'],
            [['match_id', 'team_player_id'], 'integer', 'min' => 1],
            ['appeared', 'in', 'range' => [0, 1],
                'message' => '出场标记只能为0或1。'],
            ['goals', 'integer', 'min' => 0],
            ['assists', 'integer', 'min' => 0],
            ['match_id', 'exist', 'skipOnError' => true,
                'targetClass' => FootballMatch::class,
                'targetAttribute' => ['match_id' => 'id']],
            ['team_player_id', 'exist', 'skipOnError' => true,
                'targetClass' => TeamPlayer::class,
                'targetAttribute' => ['team_player_id' => 'id']],
            [['match_id', 'team_player_id'], 'unique',
                'targetAttribute' => ['match_id', 'team_player_id'],
                'message' => '同一场比赛的同一名球员只能有一条统计记录。'],
            ['match_id', 'validateMatchState', 'skipOnError' => true],
            ['team_player_id', 'validateRoster', 'skipOnError' => true],
            ['goals', 'validateGoals', 'skipOnError' => true],
            ['assists', 'validateAssists', 'skipOnError' => true],
        ];
    }

    /** 统计只允许关联已结束且双方比分完整的比赛。 */
    public function validateMatchState($attribute, $params)
    {
        $match = $this->getMatchModel();
        if ($match === null) {
            return;
        }
        if (!$match->isFinished()) {
            $this->addError('match_id', '只有已结束的比赛才能录入球员统计。');
        }
    }

    /** 阵容年份须与比赛一致，且该球员所属球队必须是本场参赛球队。 */
    public function validateRoster($attribute, $params)
    {
        $match = $this->getMatchModel();
        $roster = $this->getRosterModel();
        if ($match === null || $roster === null) {
            return;
        }
        if ((int) $roster->tournament_year !== (int) $match->tournament_year) {
            $this->addError('team_player_id', '阵容记录的赛事年份与比赛不一致。');
            return;
        }
        $teamId = (int) $roster->team_id;
        if ($teamId !== (int) $match->home_team_id && $teamId !== (int) $match->away_team_id) {
            $this->addError('team_player_id', '该球员所属球队不是本场比赛的参赛球队。');
        }
    }

    /** appeared=0 时进球必须为0；进球不得超过该队本场比分。 */
    public function validateGoals($attribute, $params)
    {
        $goals = (int) $this->goals;
        if ((int) $this->appeared === 0 && $goals !== 0) {
            $this->addError('goals', '未出场的球员进球数必须为0。');
            return;
        }
        $match = $this->getMatchModel();
        $roster = $this->getRosterModel();
        if ($match === null || $roster === null || !$match->isFinished()) {
            return;
        }
        if ((int) $roster->team_id === (int) $match->home_team_id) {
            $teamScore = (int) $match->home_score;
        } elseif ((int) $roster->team_id === (int) $match->away_team_id) {
            $teamScore = (int) $match->away_score;
        } else {
            return;
        }
        if ($goals > $teamScore) {
            $this->addError('goals', '进球数不能超过本队该场比分。');
        }
    }

    /** appeared=0 时已知助攻只能为0；NULL 表示未核实，不能当作0。 */
    public function validateAssists($attribute, $params)
    {
        if ($this->appeared === null || (int) $this->appeared !== 0) {
            return;
        }
        if ($this->assists !== null && (int) $this->assists !== 0) {
            $this->addError('assists', '未出场的球员助攻数只能为0或留空。');
        }
    }

    public function attributeLabels()
    {
        return [
            'id' => '统计ID',
            'match_id' => '比赛',
            'team_player_id' => '本届阵容',
            'appeared' => '是否出场',
            'goals' => '进球',
            'assists' => '助攻',
        ];
    }

    public static function appearedLabels()
    {
        return [1 => '已出场', 0 => '未出场'];
    }

    public function getMatch()
    {
        return $this->hasOne(FootballMatch::class, ['id' => 'match_id']);
    }

    public function getTeamPlayer()
    {
        return $this->hasOne(TeamPlayer::class, ['id' => 'team_player_id']);
    }

    protected function getMatchModel()
    {
        return $this->match ?: FootballMatch::findOne($this->match_id);
    }

    protected function getRosterModel()
    {
        return $this->teamPlayer ?: TeamPlayer::findOne($this->team_player_id);
    }
}
