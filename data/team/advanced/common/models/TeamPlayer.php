<?php
/**
 * 项目：2026世界杯球队信息系统
 * 文件职责：球队本届阵容模型（B1）；同球员同届仅一队，同队同届号码唯一
 * 作者：王乐妍（2413423）
 * 创建日期：2026-11-22
 * 主要修改者：王乐妍（2413423）
 */
namespace common\models;

use yii\db\ActiveRecord;

class TeamPlayer extends ActiveRecord
{
    const POSITION_GK = 'GK';
    const POSITION_DF = 'DF';
    const POSITION_MF = 'MF';
    const POSITION_FW = 'FW';

    public static function tableName()
    {
        return '{{%team_player}}';
    }

    public function rules()
    {
        return [
            [['team_id', 'player_id', 'tournament_year', 'shirt_number', 'position_code'], 'required'],
            ['tournament_year', 'default', 'value' => 2026],
            [['team_id', 'player_id', 'tournament_year', 'shirt_number'], 'integer', 'min' => 1],
            ['tournament_year', 'in', 'range' => [2026],
                'message' => '本站仅收录2026届赛事阵容。'],
            ['shirt_number', 'integer', 'min' => 1, 'max' => 26],
            ['position_code', 'in', 'range' => [
                self::POSITION_GK, self::POSITION_DF, self::POSITION_MF, self::POSITION_FW,
            ]],
            ['team_id', 'exist', 'skipOnError' => true,
                'targetClass' => Team::class, 'targetAttribute' => ['team_id' => 'id']],
            ['player_id', 'exist', 'skipOnError' => true,
                'targetClass' => Player::class, 'targetAttribute' => ['player_id' => 'id']],
            [['player_id', 'tournament_year'], 'unique',
                'targetAttribute' => ['player_id', 'tournament_year'],
                'message' => '同一球员同届只能归属一支球队。'],
            [['team_id', 'tournament_year', 'shirt_number'], 'unique',
                'targetAttribute' => ['team_id', 'tournament_year', 'shirt_number'],
                'message' => '同一球队同届号码必须唯一。'],
            [['team_id', 'player_id', 'tournament_year'], 'validateNotLocked', 'skipOnError' => false],
        ];
    }

    /**
     * 已有比赛统计的阵容不得修改球队、球员或年份（基线锁定规则）。
     */
    public function validateNotLocked($attribute, $params)
    {
        if ($this->getIsNewRecord()) {
            return;
        }
        $lockedFields = ['team_id', 'player_id', 'tournament_year'];
        $changed = [];
        foreach ($lockedFields as $field) {
            if ($this->isAttributeChanged($field)) {
                $changed[] = $field;
            }
        }
        if ($changed === []) {
            return;
        }
        // 依赖C交付的PlayerMatchStat模型，交付前跳过锁定校验（无统计即不锁定）
        if (!class_exists(PlayerMatchStat::class)) {
            return;
        }
        if (PlayerMatchStat::find()->where(['team_player_id' => $this->id])->exists()) {
            foreach ($changed as $field) {
                $this->addError($field, '该阵容已存在比赛统计，不能修改球队、球员或赛事年份。');
            }
        }
    }

    public function attributeLabels()
    {
        return [
            'id' => '阵容ID',
            'team_id' => '球队',
            'player_id' => '球员',
            'tournament_year' => '赛事年份',
            'shirt_number' => '球衣号码',
            'position_code' => '位置',
        ];
    }

    public function getTeam()
    {
        return $this->hasOne(Team::class, ['id' => 'team_id']);
    }

    public function getPlayer()
    {
        return $this->hasOne(Player::class, ['id' => 'player_id']);
    }

    // 关联C负责的球员比赛统计表；模型由C交付，交付前禁止调用本方法
    public function getPlayerMatchStats()
    {
        return $this->hasMany(PlayerMatchStat::class, ['team_player_id' => 'id']);
    }
}
