<?php
/**
 * 项目：2026世界杯球队信息系统
 * 文件职责：球队基础资料模型（B1）
 * 作者：王乐妍（2413423）
 * 主要修改者：王乐妍（2413423）
 */
namespace common\models;

use yii\db\ActiveRecord;

class Team extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%team}}';
    }

    public function rules()
    {
        return [
            [['name_zh', 'name_en', 'fifa_code', 'country', 'continent'], 'trim'],
            [['introduction', 'logo_path'], 'default', 'value' => null],
            [['name_zh', 'name_en', 'fifa_code', 'country', 'continent'], 'required'],
            ['name_zh', 'string', 'max' => 80],
            ['name_en', 'string', 'max' => 80],
            ['fifa_code', 'string', 'max' => 3],
            ['fifa_code', 'match', 'pattern' => '/^[A-Za-z]{3}$/',
                'message' => 'FIFA三字码必须为3个字母。'],
            ['country', 'string', 'max' => 80],
            ['continent', 'string', 'max' => 30],
            ['introduction', 'string'],
            ['logo_path', 'string', 'max' => 500],
            ['fifa_code', 'unique'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => '球队ID',
            'name_zh' => '中文球队名',
            'name_en' => '英文球队名',
            'fifa_code' => 'FIFA三字码',
            'country' => '国家或地区',
            'continent' => '洲',
            'introduction' => '球队简介',
            'logo_path' => '队徽路径',
        ];
    }

    public function getTeamPlayers()
    {
        return $this->hasMany(TeamPlayer::class, ['team_id' => 'id']);
    }

    public function getTeamCoaches()
    {
        return $this->hasMany(TeamCoach::class, ['team_id' => 'id']);
    }

    public function getTeamHonors()
    {
        return $this->hasMany(TeamHonor::class, ['team_id' => 'id']);
    }

    public function getComments()
    {
        return $this->hasMany(Comment::class, ['team_id' => 'id']);
    }
}
