<?php
/** 留言持久化模型：三个目标外键恰好填写一个，公开查询仅返回已通过记录。 */
namespace common\models;

use yii\db\ActiveRecord;

class Comment extends ActiveRecord
{
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_DELETED = 'deleted';

    public static function tableName()
    {
        return '{{%comment}}';
    }

    public function rules()
    {
        return [
            [['team_id', 'player_id', 'match_id'], 'default', 'value' => null],
            [['team_id', 'player_id', 'match_id'], 'integer', 'min' => 1],
            ['team_id', 'validateSingleTarget', 'skipOnEmpty' => false],
            [['nickname', 'content'], 'trim'],
            [['nickname', 'content', 'status', 'created_at'], 'required'],
            ['nickname', 'string', 'max' => 30],
            ['content', 'string', 'max' => 1000],
            ['status', 'in', 'range' => [self::STATUS_PENDING, self::STATUS_APPROVED,
                self::STATUS_REJECTED, self::STATUS_DELETED]],
        ];
    }

    public function validateSingleTarget($attribute, $params)
    {
        $count = 0;
        foreach (['team_id', 'player_id', 'match_id'] as $field) {
            if ($this->$field !== null && $this->$field !== '') {
                ++$count;
            }
        }
        if ($count !== 1) {
            $this->addError($attribute, '留言必须且只能关联一个对象。');
        }
    }

    public static function approvedFor($field, $id)
    {
        if (!in_array($field, ['team_id', 'player_id', 'match_id'], true)) {
            throw new \InvalidArgumentException('Invalid comment target field');
        }
        return static::find()->where([$field => $id, 'status' => self::STATUS_APPROVED])
            ->orderBy(['created_at' => SORT_DESC, 'id' => SORT_DESC]);
    }
}
