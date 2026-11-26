<?php
/**
 * 项目：2026世界杯球队信息系统
 * 文件职责：访客留言表单，仅接受昵称和正文，不接受审核及目标字段
 * 作者：牛雨萱（学号 2411061）
 * 创建日期：2026-11-22
 * 主要修改者：牛雨萱（学号 2411061）
 */
namespace frontend\models;

use yii\base\Model;

class CommentForm extends Model
{
    public $nickname;
    public $content;

    public function rules()
    {
        return [
            [['nickname', 'content'], 'trim'],
            [['nickname', 'content'], 'required', 'message' => '{attribute}不能为空。'],
            ['nickname', 'string', 'max' => 30, 'tooLong' => '昵称最多30个字符。'],
            ['content', 'string', 'max' => 1000, 'tooLong' => '留言最多1000个字符。'],
        ];
    }

    public function attributeLabels()
    {
        return ['nickname' => '昵称', 'content' => '留言内容'];
    }
}
