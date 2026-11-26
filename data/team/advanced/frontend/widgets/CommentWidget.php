<?php
/**
 * 项目：2026世界杯球队信息系统
 * 文件职责：可复用留言组件，详情页传目标类型和编号，未配置接口时不查询数据库
 * 作者：牛雨萱（学号 2411061）
 * 创建日期：2026-11-22
 * 主要修改者：牛雨萱（学号 2411061）
 */
namespace frontend\widgets;

use Yii;
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

        // 提交失败后回到详情页时，恢复错误提示和已填写内容
        $model = $this->model ?: new CommentForm();
        $errors = Yii::$app->session->getFlash('commentErrors', [], true);
        if (is_array($errors) && $errors !== []) {
            $model->addErrors($errors);
        }
        $old = Yii::$app->session->getFlash('commentOld', [], true);
        if (is_array($old)) {
            foreach (['nickname', 'content'] as $name) {
                if (isset($old[$name]) && is_scalar($old[$name])) {
                    $model->$name = (string) $old[$name];
                }
            }
        }

        return $this->render('@frontend/views/comment/_panel', [
            'model' => $model, 'provider' => $provider,
            'targetType' => $this->targetType, 'targetId' => $target['id'],
        ]);
    }
}
