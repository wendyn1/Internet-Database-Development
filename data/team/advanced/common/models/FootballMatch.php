<?php
/**
 * 项目：2026世界杯球队信息系统
 * 文件职责：比赛模型（C1）：比分与状态一致性、库外对手、统计存在时的字段锁定
 * 作者：邓佳怡（学号 2412612）
 * 主要修改者：邓佳怡（学号 2412612）
 */
namespace common\models;

use yii\db\ActiveRecord;

class FootballMatch extends ActiveRecord
{
    const STAGE_GROUP = 'group';
    const STAGE_ROUND_OF_32 = 'round_of_32';
    const STAGE_ROUND_OF_16 = 'round_of_16';
    const STAGE_QUARTER_FINAL = 'quarter_final';
    const STAGE_SEMI_FINAL = 'semi_final';
    const STAGE_THIRD_PLACE = 'third_place';
    const STAGE_FINAL = 'final';

    const STATUS_NOT_STARTED = 'not_started';
    const STATUS_LIVE = 'live';
    const STATUS_FINISHED = 'finished';
    const STATUS_POSTPONED = 'postponed';
    const STATUS_CANCELLED = 'cancelled';

    /** 任意球员统计存在时禁止修改的字段（基线锁定规则）。 */
    const LOCKED_ATTRIBUTES = [
        'tournament_year', 'home_team_id', 'away_team_id',
        'home_team_name', 'away_team_name', 'status', 'home_score', 'away_score',
    ];

    public static function tableName()
    {
        return '{{%football_match}}';
    }

    public function rules()
    {
        return [
            [['fifa_match_number', 'home_team_id', 'away_team_id',
                'home_score', 'away_score'], 'default', 'value' => null],
            ['tournament_year', 'default', 'value' => 2026],
            ['after_extra_time', 'default', 'value' => 0],
            [['stage', 'venue_id', 'home_team_name', 'away_team_name',
                'match_date_beijing', 'status'], 'required'],
            [['fifa_match_number', 'tournament_year', 'venue_id',
                'home_team_id', 'away_team_id', 'after_extra_time'], 'integer'],
            ['fifa_match_number', 'integer', 'min' => 1],
            ['tournament_year', 'in', 'range' => [2026],
                'message' => '本站仅收录2026届赛事。'],
            ['venue_id', 'integer', 'min' => 1],
            [['home_team_id', 'away_team_id'], 'integer', 'min' => 1],
            [['home_team_name', 'away_team_name'], 'string', 'max' => 80],
            [['home_team_name', 'away_team_name'], 'trim'],
            ['match_date_beijing', 'date', 'format' => 'php:Y-m-d',
                'message' => '比赛日期必须为 YYYY-MM-DD 格式。'],
            ['stage', 'in', 'range' => array_keys(self::stageLabels()),
                'message' => '比赛阶段不在允许范围内。'],
            ['status', 'in', 'range' => array_keys(self::statusLabels()),
                'message' => '比赛状态不在允许范围内。'],
            [['home_score', 'away_score'], 'integer', 'min' => 0],
            ['after_extra_time', 'in', 'range' => [0, 1]],
            ['venue_id', 'exist', 'skipOnError' => true,
                'targetClass' => Venue::class, 'targetAttribute' => ['venue_id' => 'id']],
            ['home_team_id', 'exist', 'skipOnError' => true, 'skipOnEmpty' => true,
                'targetClass' => Team::class, 'targetAttribute' => ['home_team_id' => 'id']],
            ['away_team_id', 'exist', 'skipOnError' => true, 'skipOnEmpty' => true,
                'targetClass' => Team::class, 'targetAttribute' => ['away_team_id' => 'id']],
            ['fifa_match_number', 'unique',
                'targetAttribute' => ['fifa_match_number', 'tournament_year'],
                'skipOnEmpty' => true,
                'message' => '同一届赛事的官方比赛编号必须唯一。'],
            ['home_team_id', 'validateTeams',
                'skipOnError' => true, 'skipOnEmpty' => false],
            [['status'], 'validateScoreState', 'skipOnError' => true],
            [['status'], 'validateLockedAttributes'],
        ];
    }

    /** 至少一侧为本库球队，且两侧均在本库时不得相同。库外对手只保存名称。 */
    public function validateTeams($attribute, $params)
    {
        $home = $this->home_team_id;
        $away = $this->away_team_id;
        if (($home === null || $home === '') && ($away === null || $away === '')) {
            $this->addError('home_team_id', '比赛至少需要一侧关联本库球队，库外对手请只填写名称。');
            return;
        }
        if ($home !== null && $home !== '' && $away !== null && $away !== ''
            && (int) $home === (int) $away) {
            $this->addError('away_team_id', '同一场比赛的主客队不能是同一支球队。');
        }
    }

    /** finished 必须双方非负比分；其他状态双方比分必须为 NULL。 */
    public function validateScoreState($attribute, $params)
    {
        $home = $this->home_score;
        $away = $this->away_score;
        if ($this->status === self::STATUS_FINISHED) {
            if ($home === null || $home === '' || $away === null || $away === '') {
                $this->addError('home_score', '已结束的比赛必须填写双方比分。');
                $this->addError('away_score', '已结束的比赛必须填写双方比分。');
            }
            return;
        }
        if ($home !== null && $home !== '') {
            $this->addError('home_score', '未结束的比赛不能填写比分。');
        }
        if ($away !== null && $away !== '') {
            $this->addError('away_score', '未结束的比赛不能填写比分。');
        }
    }

    /** 已存在任意球员统计时，锁定双方、名称快照、年份、状态与比分。 */
    public function validateLockedAttributes($attribute, $params)
    {
        if ($this->getIsNewRecord() || !$this->hasStatistics()) {
            return;
        }
        foreach (self::LOCKED_ATTRIBUTES as $field) {
            if ($this->isAttributeChanged($field)) {
                $this->addError($field, '该比赛已存在球员统计，双方球队、名称、年份、状态与比分不能修改。');
            }
        }
    }

    public function hasStatistics()
    {
        if ($this->getIsNewRecord()) {
            return false;
        }
        return PlayerMatchStat::find()->where(['match_id' => $this->id])->exists();
    }

    public function attributeLabels()
    {
        return [
            'id' => '比赛ID',
            'fifa_match_number' => '官方比赛编号',
            'tournament_year' => '赛事年份',
            'stage' => '比赛阶段',
            'venue_id' => '比赛场馆',
            'home_team_id' => '主队（本库）',
            'away_team_id' => '客队（本库）',
            'home_team_name' => '主队展示名称',
            'away_team_name' => '客队展示名称',
            'match_date_beijing' => '北京日期',
            'status' => '比赛状态',
            'home_score' => '主队比分',
            'away_score' => '客队比分',
            'after_extra_time' => '是否含加时',
        ];
    }

    public static function stageLabels()
    {
        return [
            self::STAGE_GROUP => '小组赛',
            self::STAGE_ROUND_OF_32 => '32强赛',
            self::STAGE_ROUND_OF_16 => '16强赛',
            self::STAGE_QUARTER_FINAL => '四分之一决赛',
            self::STAGE_SEMI_FINAL => '半决赛',
            self::STAGE_THIRD_PLACE => '季军赛',
            self::STAGE_FINAL => '决赛',
        ];
    }

    public static function statusLabels()
    {
        return [
            self::STATUS_NOT_STARTED => '未开始',
            self::STATUS_LIVE => '进行中',
            self::STATUS_FINISHED => '已结束',
            self::STATUS_POSTPONED => '延期',
            self::STATUS_CANCELLED => '取消',
        ];
    }

    public function getStageLabel()
    {
        return self::stageLabels()[$this->stage] ?? $this->stage;
    }

    public function getStatusLabel()
    {
        return self::statusLabels()[$this->status] ?? $this->status;
    }

    public function isFinished()
    {
        return $this->status === self::STATUS_FINISHED;
    }

    public function getVenue()
    {
        return $this->hasOne(Venue::class, ['id' => 'venue_id']);
    }

    public function getHomeTeam()
    {
        return $this->hasOne(Team::class, ['id' => 'home_team_id']);
    }

    public function getAwayTeam()
    {
        return $this->hasOne(Team::class, ['id' => 'away_team_id']);
    }

    public function getPlayerMatchStats()
    {
        return $this->hasMany(PlayerMatchStat::class, ['match_id' => 'id']);
    }

    public function getComments()
    {
        return $this->hasMany(Comment::class, ['match_id' => 'id']);
    }
}
