<?php
/**
 * 项目：2026世界杯球队信息系统
 * 文件职责：访客留言提交控制器，POST及CSRF校验，成功进入待审核，失败回原详情页展示错误
 * 作者：牛雨萱（学号 2411061）
 * 创建日期：2026-11-22
 * 主要修改者：牛雨萱（学号 2411061）
 */
namespace frontend\controllers;

use Yii;
use common\models\Comment;
use frontend\components\CommentTarget;
use frontend\models\CommentForm;
use yii\filters\VerbFilter;
use yii\web\Controller;

class CommentController extends Controller
{
    /** 同一会话两次成功提交之间的最小间隔（秒）。 */
    const SUBMIT_INTERVAL = 60;

    public function behaviors()
    {
        return ['verbs' => ['class' => VerbFilter::class, 'actions' => ['create' => ['post']]]];
    }

    public function actionCreate($targetType = null, $targetId = null)
    {
        $target = CommentTarget::resolve($targetType, $targetId);
        $form = new CommentForm();
        // 失败（校验、限流、保存异常）统一回到原详情页展示错误，不再单独渲染结果页
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            $remaining = self::SUBMIT_INTERVAL
                - (time() - (int) Yii::$app->session->get('commentLastSubmitAt', 0));
            if ($remaining > 0) {
                // 连续重复提交：仅提示等待，不写入数据库。
                $form->addError('content', "提交过于频繁，请 {$remaining} 秒后再试。");
            } else {
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
                        Yii::$app->session->set('commentLastSubmitAt', time());
                        Yii::$app->session->setFlash('success', '留言已提交，审核通过后显示。');
                        return $this->redirect($target['url'], 303);
                    }
                    $form->addError('content', '留言未保存，请检查输入后重试。');
                } catch (\yii\db\IntegrityException $exception) {
                    $form->addError('content', '留言对象可能已变更，请返回详情页确认后重试。');
                }
            }
        } else {
            $form->validate();
        }
        // 带着错误和已填内容回到原详情页，由留言组件在原位展示。
        Yii::$app->session->setFlash('commentErrors', $form->getErrors());
        Yii::$app->session->setFlash('commentOld', [
            'nickname' => $form->nickname,
            'content' => $form->content,
        ]);
        return $this->redirect($target['url'] + ['#' => 'comments'], 303);
    }
}
