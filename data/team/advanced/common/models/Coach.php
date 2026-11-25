<?php
/**
 * 项目：2026世界杯球队信息系统
 * 文件职责：教练基础资料模型（B1）
 * 作者：王乐妍（2413423）
 * 主要修改者：王乐妍（2413423）
 */
namespace common\models;

use yii\db\ActiveRecord;

class Coach extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%coach}}';
    }

    public function rules()
    {
        return [
            [['name_zh', 'name_en', 'nationality'], 'trim'],
            [['introduction', 'photo_path'], 'default', 'value' => null],
            [['name_zh', 'name_en', 'nationality'], 'required'],
            ['name_zh', 'string', 'max' => 80],
            ['name_en', 'string', 'max' => 100],
            ['nationality', 'string', 'max' => 80],
            ['introduction', 'string'],
            ['photo_path', 'string', 'max' => 500],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => '教练ID',
            'name_zh' => '中文译名',
            'name_en' => '英文常用名',
            'nationality' => '国籍',
            'introduction' => '教练简介',
            'photo_path' => '照片路径',
        ];
    }

    public function getTeamCoaches()
    {
        return $this->hasMany(TeamCoach::class, ['coach_id' => 'id']);
    }
}
