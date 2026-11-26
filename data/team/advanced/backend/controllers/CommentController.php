<?php
/**
 * 项目：2026世界杯球队信息系统
 * 文件职责：后台留言管理控制器，查看留言对象并执行通过、不通过、删除
 * 作者：牛雨萱（学号 2411061）
 * 创建日期：2026-11-22
 * 主要修改者：牛雨萱（学号 2411061）
 */
namespace backend\controllers;

use Yii;
use common\models\Comment;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

class CommentController extends AdminController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['verbs'] = [
            'class' => VerbFilter::class,
            'actions' => [
                'approve' => ['post'],
                'reject' => ['post'],
                'delete' => ['post'],
            ],
        ];
        return $behaviors;
    }

    public function actionIndex($status = '')
    {
        $allowed = array_keys(Comment::statusLabels());
        $status = is_string($status) && in_array($status, $allowed, true) ? $status : '';

        $query = Comment::find()->orderBy(['created_at' => SORT_DESC, 'id' => SORT_DESC]);
        if ($status !== '') {
            $query->andWhere(['status' => $status]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 20],
            'sort' => false,
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'status' => $status,
        ]);
    }

    public function actionView($id)
    {
        return $this->render('view', ['model' => $this->findModel($id)]);
    }

    public function actionApprove($id)
    {
        return $this->changeStatus((int) $id, Comment::STATUS_APPROVED, '留言已审核通过。');
    }

    public function actionReject($id)
    {
        return $this->changeStatus((int) $id, Comment::STATUS_REJECTED, '留言已标记为未通过。');
    }

    public function actionDelete($id)
    {
        return $this->changeStatus((int) $id, Comment::STATUS_DELETED, '留言已删除。');
    }

    protected function changeStatus($id, $status, $message)
    {
        $model = $this->findModel($id);
        if (!in_array($status, $this->allowedTransitions($model->status), true)) {
            Yii::$app->session->setFlash('error', '当前状态不允许该操作。');
            return $this->redirect(['index']);
        }

        $model->status = $status;
        $model->reviewed_by = Yii::$app->user->id;
        $model->reviewed_at = date('Y-m-d H:i:s');
        if ($model->save(false, ['status', 'reviewed_by', 'reviewed_at'])) {
            Yii::$app->session->setFlash('success', $message);
        } else {
            Yii::$app->session->setFlash('error', '操作未生效，请重试。');
        }
        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        $model = Comment::findOne((int) $id);
        if ($model === null) {
            throw new NotFoundHttpException('留言不存在。');
        }
        return $model;
    }

    /** 允许的状态流转：待审核可通过/拒绝/删除，已通过仅可删除，已拒绝仅可删除。 */
    protected function allowedTransitions($currentStatus)
    {
        switch ($currentStatus) {
            case Comment::STATUS_PENDING:
                return [Comment::STATUS_APPROVED, Comment::STATUS_REJECTED, Comment::STATUS_DELETED];
            case Comment::STATUS_APPROVED:
            case Comment::STATUS_REJECTED:
                return [Comment::STATUS_DELETED];
            default:
                return [];
        }
    }
}
