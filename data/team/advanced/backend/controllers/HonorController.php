<?php
/**
 * 项目：2026世界杯球队信息系统
 * 文件职责：后台荣誉定义CRUD（B5）：有球队荣誉记录引用时拒绝删除
 * 作者：王乐妍（2413423）
 * 主要修改者：王乐妍（2413423）
 */
namespace backend\controllers;

use Yii;
use common\models\Honor;
use common\models\TeamHonor;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

class HonorController extends AdminController
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
            'query' => Honor::find(),
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
        $model = new Honor();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', '荣誉已创建。');
            return $this->redirect(['view', 'id' => $model->id]);
        }
        return $this->render('create', ['model' => $model]);
    }

    public function actionUpdate($id = null)
    {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', '荣誉已更新。');
            return $this->redirect(['view', 'id' => $model->id]);
        }
        return $this->render('update', ['model' => $model]);
    }

    public function actionDelete($id = null)
    {
        $model = $this->findModel($id);
        $recordCount = TeamHonor::find()->where(['honor_id' => $model->id])->count();
        if ($recordCount > 0) {
            Yii::$app->session->setFlash('error', "存在 $recordCount 条球队荣誉记录，不能删除该荣誉定义。");
        } elseif ($model->delete() === false) {
            Yii::$app->session->setFlash('error', '删除失败，请重试。');
        } else {
            Yii::$app->session->setFlash('success', '荣誉已删除。');
        }
        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if ($id === null || !ctype_digit((string)$id) || (int)$id < 1) {
            throw new NotFoundHttpException('页面不存在。');
        }
        if (($model = Honor::findOne((int)$id)) === null) {
            throw new NotFoundHttpException('页面不存在。');
        }
        return $model;
    }
}
