<?php
/**
 * 项目：2026世界杯球队信息系统
 * 文件职责：后台球队资料CRUD（B5）：依赖（阵容/执教/荣誉/留言）存在时拒绝删除
 * 作者：王乐妍（2413423）
 * 主要修改者：王乐妍（2413423）
 */
namespace backend\controllers;

use Yii;
use common\models\Comment;
use common\models\Team;
use common\models\TeamCoach;
use common\models\TeamHonor;
use common\models\TeamPlayer;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

class TeamController extends AdminController
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'verbs' => ['class' => VerbFilter::class, 'actions' => ['delete' => ['post']]],
        ]);
    }

    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Team::find(),
            'pagination' => ['pageSize' => 10, 'pageParam' => 'page'],
            'sort' => ['defaultOrder' => ['id' => SORT_ASC], 'attributes' => ['id']],
        ]);
        return $this->render('index', ['dataProvider' => $dataProvider]);
    }

    public function actionView($id = null)
    {
        return $this->render('view', ['model' => $this->findModel($id)]);
    }

    public function actionCreate()
    {
        $model = new Team();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', '球队已创建。');
            return $this->redirect(['view', 'id' => $model->id]);
        }
        return $this->render('create', ['model' => $model]);
    }

    public function actionUpdate($id = null)
    {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', '球队已更新。');
            return $this->redirect(['view', 'id' => $model->id]);
        }
        return $this->render('update', ['model' => $model]);
    }

    public function actionDelete($id = null)
    {
        $model = $this->findModel($id);
        $dependencies = [
            '本届阵容' => TeamPlayer::find()->where(['team_id' => $model->id])->count(),
            '执教关系' => TeamCoach::find()->where(['team_id' => $model->id])->count(),
            '荣誉记录' => TeamHonor::find()->where(['team_id' => $model->id])->count(),
            '留言（含已删除）' => Comment::find()->where(['team_id' => $model->id])->count(),
        ];
        $existing = array_filter($dependencies, function ($count) { return $count > 0; });
        if ($existing !== []) {
            $parts = [];
            foreach ($existing as $label => $count) {
                $parts[] = "$label $count 条";
            }
            Yii::$app->session->setFlash('error', '存在关联记录（' . implode('、', $parts) . '），不能删除该球队。');
        } elseif ($model->delete() === false) {
            Yii::$app->session->setFlash('error', '删除失败，请重试。');
        } else {
            Yii::$app->session->setFlash('success', '球队已删除。');
        }
        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if ($id === null || !ctype_digit((string)$id) || (int)$id < 1) {
            throw new NotFoundHttpException('页面不存在。');
        }
        if (($model = Team::findOne((int)$id)) === null) {
            throw new NotFoundHttpException('页面不存在。');
        }
        return $model;
    }
}
