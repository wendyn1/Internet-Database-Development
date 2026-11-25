<?php
/**
 * 项目：2026世界杯球队信息系统
 * 文件职责：后台教练资料CRUD（B5）：有执教关系时拒绝删除
 * 作者：王乐妍（2413423）
 * 主要修改者：王乐妍（2413423）
 */
namespace backend\controllers;

use Yii;
use common\models\Coach;
use common\models\TeamCoach;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

class CoachController extends AdminController
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
            'query' => Coach::find(),
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
        $model = new Coach();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', '教练已创建。');
            return $this->redirect(['view', 'id' => $model->id]);
        }
        return $this->render('create', ['model' => $model]);
    }

    public function actionUpdate($id = null)
    {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', '教练已更新。');
            return $this->redirect(['view', 'id' => $model->id]);
        }
        return $this->render('update', ['model' => $model]);
    }

    public function actionDelete($id = null)
    {
        $model = $this->findModel($id);
        $relationCount = TeamCoach::find()->where(['coach_id' => $model->id])->count();
        if ($relationCount > 0) {
            Yii::$app->session->setFlash('error',
                "存在 $relationCount 条执教关系，不能删除该教练。");
        } elseif ($model->delete() === false) {
            Yii::$app->session->setFlash('error', '删除失败，请重试。');
        } else {
            Yii::$app->session->setFlash('success', '教练已删除。');
        }
        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if ($id === null || !ctype_digit((string)$id) || (int)$id < 1) {
            throw new NotFoundHttpException('页面不存在。');
        }
        if (($model = Coach::findOne((int)$id)) === null) {
            throw new NotFoundHttpException('页面不存在。');
        }
        return $model;
    }
}
