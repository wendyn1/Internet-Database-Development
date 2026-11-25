<?php
/**
 * 项目：2026世界杯球队信息系统
 * 文件职责：比赛场馆模型（C1）
 * 作者：邓佳怡（学号 2412612）
 * 创建日期：2026-11-25
 * 主要修改者：邓佳怡（学号 2412612）
 */
namespace common\models;

use yii\db\ActiveRecord;

class Venue extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%venue}}';
    }

    public function rules()
    {
        return [
            [['name_zh', 'name_en', 'city', 'country'], 'trim'],
            [['name_zh', 'name_en', 'city', 'country'], 'required'],
            ['name_zh', 'string', 'max' => 80],
            ['name_en', 'string', 'max' => 100],
            ['city', 'string', 'max' => 80],
            ['country', 'string', 'max' => 80],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => '场馆ID',
            'name_zh' => '中文场馆名',
            'name_en' => '英文场馆名',
            'city' => '城市',
            'country' => '国家',
        ];
    }

    public function getFootballMatches()
    {
        return $this->hasMany(FootballMatch::class, ['venue_id' => 'id']);
    }
}
