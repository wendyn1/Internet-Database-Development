<?php
/**
 * 项目：2026世界杯球队信息系统
 * 文件职责：球队执教关系模型（B1）；同队同届仅一名主教练
 * 作者：王乐妍（2413423）
 * 创建日期：2026-11-22
 * 主要修改者：王乐妍（2413423）
 */
namespace common\models;

use yii\db\ActiveRecord;

class TeamCoach extends ActiveRecord
{
    const ROLE_HEAD_COACH = 'head_coach';

    public static function tableName()
    {
        return '{{%team_coach}}';
    }

    public function rules()
    {
        return [
            [['team_id', 'coach_id', 'tournament_year'], 'required'],
            ['tournament_year', 'default', 'value' => 2026],
            ['role', 'default', 'value' => self::ROLE_HEAD_COACH],
            [['team_id', 'coach_id', 'tournament_year'], 'integer', 'min' => 1],
            ['tournament_year', 'in', 'range' => [2026],
                'message' => '本站仅收录2026届赛事执教关系。'],
            ['role', 'string', 'max' => 30],
            ['role', 'in', 'range' => [self::ROLE_HEAD_COACH]],
            ['team_id', 'exist', 'skipOnError' => true,
                'targetClass' => Team::class, 'targetAttribute' => ['team_id' => 'id']],
            ['coach_id', 'exist', 'skipOnError' => true,
                'targetClass' => Coach::class, 'targetAttribute' => ['coach_id' => 'id']],
            [['team_id', 'tournament_year'], 'unique',
                'targetAttribute' => ['team_id', 'tournament_year'],
                'message' => '同一球队同届只能登记一名主教练。'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => '执教关系ID',
            'team_id' => '球队',
            'coach_id' => '教练',
            'tournament_year' => '赛事年份',
            'role' => '执教角色',
        ];
    }

    public function getTeam()
    {
        return $this->hasOne(Team::class, ['id' => 'team_id']);
    }

    public function getCoach()
    {
        return $this->hasOne(Coach::class, ['id' => 'coach_id']);
    }
}
