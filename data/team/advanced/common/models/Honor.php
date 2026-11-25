<?php
/**
 * 项目：2026世界杯球队信息系统
 * 文件职责：荣誉定义模型（B1）
 * 作者：王乐妍（2413423）
 * 主要修改者：王乐妍（2413423）
 */
namespace common\models;

use yii\db\ActiveRecord;

class Honor extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%honor}}';
    }

    public function rules()
    {
        return [
            [['name', 'competition'], 'trim'],
            [['name', 'competition'], 'required'],
            ['name', 'string', 'max' => 80],
            ['competition', 'string', 'max' => 80],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => '荣誉ID',
            'name' => '荣誉名称',
            'competition' => '所属赛事',
        ];
    }

    public function getTeamHonors()
    {
        return $this->hasMany(TeamHonor::class, ['honor_id' => 'id']);
    }
}
