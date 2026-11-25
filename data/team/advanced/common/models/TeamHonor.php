<?php
/**
 * 项目：2026世界杯球队信息系统
 * 文件职责：球队获得荣誉记录模型（B1）
 * 作者：王乐妍（2413423）
 * 主要修改者：王乐妍（2413423）
 */
namespace common\models;

use yii\db\ActiveRecord;

class TeamHonor extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%team_honor}}';
    }

    public function rules()
    {
        return [
            [['team_id', 'honor_id', 'year', 'description'], 'required'],
            [['team_id', 'honor_id', 'year'], 'integer', 'min' => 1],
            ['year', 'integer', 'min' => 1900, 'max' => 2100],
            ['description', 'string', 'max' => 255],
            ['team_id', 'exist', 'skipOnError' => true,
                'targetClass' => Team::class, 'targetAttribute' => ['team_id' => 'id']],
            ['honor_id', 'exist', 'skipOnError' => true,
                'targetClass' => Honor::class, 'targetAttribute' => ['honor_id' => 'id']],
            [['team_id', 'honor_id', 'year'], 'unique',
                'targetAttribute' => ['team_id', 'honor_id', 'year'],
                'message' => '同一球队同一年份不能重复登记同一荣誉。'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => '荣誉记录ID',
            'team_id' => '球队',
            'honor_id' => '荣誉',
            'year' => '获得年份',
            'description' => '荣誉说明',
        ];
    }

    public function getTeam()
    {
        return $this->hasOne(Team::class, ['id' => 'team_id']);
    }

    public function getHonor()
    {
        return $this->hasOne(Honor::class, ['id' => 'honor_id']);
    }
}
