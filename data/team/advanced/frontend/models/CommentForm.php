<?php
/** 访客留言表单：仅接受昵称和正文，不接受审核及目标字段。 */
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
