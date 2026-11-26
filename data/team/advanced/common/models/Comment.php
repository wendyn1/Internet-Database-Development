<?php
/**
 * 项目：2026世界杯球队信息系统
 * 文件职责：留言模型，三个目标外键恰好填写一个，公开查询仅返回已通过记录
 * 作者：牛雨萱（学号 2411061）
 * 创建日期：2026-11-22
 * 主要修改者：牛雨萱（学号 2411061）
 */
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

    public static function statusLabels()
    {
        return [
            self::STATUS_PENDING => '待审核',
            self::STATUS_APPROVED => '已通过',
            self::STATUS_REJECTED => '未通过',
            self::STATUS_DELETED => '已删除',
        ];
    }

    public function getStatusLabel()
    {
        return self::statusLabels()[$this->status] ?? $this->status;
    }

    public function getTeam()
    {
        return $this->hasOne(Team::class, ['id' => 'team_id']);
    }

    public function getPlayer()
    {
        return $this->hasOne(Player::class, ['id' => 'player_id']);
    }

    public function getMatch()
    {
        return $this->hasOne(FootballMatch::class, ['id' => 'match_id']);
    }

    /** 留言对象的可读描述，对象资料被删除时同样给出说明。 */
    public function getTargetLabel()
    {
        if ($this->team_id !== null) {
            return '球队：' . ($this->team === null ? '（记录已删除）' : $this->team->name_zh);
        }
        if ($this->player_id !== null) {
            return '球员：' . ($this->player === null ? '（记录已删除）' : $this->player->name_zh);
        }
        if ($this->match_id !== null) {
            return '比赛：' . ($this->match === null
                ? '（记录已删除）'
                : $this->match->home_team_name . ' vs ' . $this->match->away_team_name);
        }
        return '未关联对象';
    }
}
