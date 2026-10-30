<?php
/** 访客留言提交：POST及CSRF校验，成功PRG返回固定详情路由。 */
namespace frontend\controllers;

use Yii;
use common\models\Comment;
use frontend\components\CommentTarget;
use frontend\models\CommentForm;
use yii\filters\VerbFilter;
use yii\web\Controller;

class CommentController extends Controller
{
    public function behaviors()
    {
        return ['verbs' => ['class' => VerbFilter::class, 'actions' => ['create' => ['post']]]];
    }

    public function actionCreate($targetType = null, $targetId = null)
    {
        $target = CommentTarget::resolve($targetType, $targetId);
        $form = new CommentForm();
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            $comment = new Comment();
            $comment->team_id = $comment->player_id = $comment->match_id = null;
            $comment->{$target['field']} = $target['id'];
            $comment->nickname = $form->nickname;
            $comment->content = $form->content;
            $comment->status = Comment::STATUS_PENDING;
            $comment->created_at = date('Y-m-d H:i:s');
            $comment->reviewed_by = $comment->reviewed_at = null;
            try {
                if ($comment->save()) {
                    Yii::$app->session->setFlash('success', '留言已提交，审核通过后显示。');
                    return $this->redirect($target['url'], 303);
                }
                $form->addError('content', '留言未保存，请检查输入后重试。');
            } catch (\yii\db\IntegrityException $exception) {
                $form->addError('content', '留言对象可能已变更，请返回详情页确认后重试。');
            }
        } else {
            $form->validate();
        }
        Yii::$app->response->statusCode = 422;
        return $this->render('create', ['model' => $form,
            'targetType' => $targetType, 'targetId' => $target['id'], 'targetUrl' => $target['url']]);
    }
}
