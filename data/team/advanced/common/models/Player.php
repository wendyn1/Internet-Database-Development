<?php
/**
 * 项目：2026世界杯球队信息系统
 * 文件职责：球员基础资料模型（B1）
 * 作者：王乐妍（2413423）
 * 创建日期：2026-11-22
 * 主要修改者：王乐妍（2413423）
 */
namespace common\models;

use yii\db\ActiveRecord;

class Player extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%player}}';
    }

    public function rules()
    {
        return [
            [['name_zh', 'name_en', 'nationality'], 'trim'],
            [['birth_date', 'photo_path'], 'default', 'value' => null],
            [['name_zh', 'name_en', 'nationality'], 'required'],
            ['name_zh', 'string', 'max' => 80],
            ['name_en', 'string', 'max' => 100],
            ['nationality', 'string', 'max' => 80],
            ['birth_date', 'date', 'format' => 'php:Y-m-d'],
            ['birth_date', 'validateBirthDateNotFuture'],
            ['photo_path', 'string', 'max' => 500],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => '球员ID',
            'name_zh' => '中文译名',
            'name_en' => '英文常用名',
            'nationality' => '国籍',
            'birth_date' => '出生日期',
            'photo_path' => '照片路径',
        ];
    }

    /**
     * 出生日期不得晚于当天（数据库基线约束）。
     */
    public function validateBirthDateNotFuture($attribute, $params)
    {
        if ($this->$attribute === null || $this->$attribute === '') {
            return;
        }
        $birth = strtotime($this->$attribute);
        if ($birth !== false && $birth > strtotime(date('Y-m-d'))) {
            $this->addError($attribute, '出生日期不能晚于当天。');
        }
    }

    public function getTeamPlayers()
    {
        return $this->hasMany(TeamPlayer::class, ['player_id' => 'id']);
    }

    public function getComments()
    {
        return $this->hasMany(Comment::class, ['player_id' => 'id']);
    }
}
