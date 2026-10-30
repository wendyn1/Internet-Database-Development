<?php
/** 可复用留言组件：详情页传 targetType 和 targetId，未完成接口配置时不查询数据库。 */
namespace frontend\widgets;

use common\models\Comment;
use frontend\components\CommentTarget;
use frontend\models\CommentForm;
use yii\base\Widget;
use yii\data\ActiveDataProvider;
use yii\web\HttpException;

class CommentWidget extends Widget
{
    public $targetType;
    public $targetId;
    public $model;

    public function run()
    {
        try {
            $target = CommentTarget::resolve($this->targetType, $this->targetId);
        } catch (HttpException $exception) {
            if ($exception->statusCode !== 503) {
                throw $exception;
            }
            return '<section id="comments"><p>留言暂未开放。</p></section>';
        }
        $provider = new ActiveDataProvider([
            'query' => Comment::approvedFor($target['field'], $target['id']),
            'pagination' => ['pageSize' => 10, 'pageParam' => 'comment-page'],
            'sort' => false,
        ]);
        return $this->render('@frontend/views/comment/_panel', [
            'model' => $this->model ?: new CommentForm(), 'provider' => $provider,
            'targetType' => $this->targetType, 'targetId' => $target['id'],
        ]);
    }
}
