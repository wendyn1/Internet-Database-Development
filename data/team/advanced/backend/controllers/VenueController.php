<?php
/**
 * 项目：2026世界杯球队信息系统
 * 文件职责：后台场馆CRUD（C5）：被比赛引用时拒绝删除
 * 作者：邓佳怡（学号 2412612）
 * 创建日期：2026-11-25
 * 主要修改者：邓佳怡（学号 2412612）
 */
namespace backend\controllers;

use Yii;
use common\models\FootballMatch;
use common\models\Venue;
use yii\data\ActiveDataProvider;
use yii\db\IntegrityException;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

class VenueController extends AdminController
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
            'query' => Venue::find(),
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
        $model = new Venue();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', '场馆已创建。');
            return $this->redirect(['view', 'id' => $model->id]);
        }
        return $this->render('create', ['model' => $model]);
    }

    public function actionUpdate($id = null)
    {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', '场馆已更新。');
            return $this->redirect(['view', 'id' => $model->id]);
        }
        return $this->render('update', ['model' => $model]);
    }

    public function actionDelete($id = null)
    {
        $model = $this->findModel($id);
        $count = FootballMatch::find()->where(['venue_id' => $model->id])->count();
        if ($count > 0) {
            Yii::$app->session->setFlash('error', "该场馆已被 $count 场比赛引用，不能删除。");
            return $this->redirect(['index']);
        }
        try {
            if ($model->delete() === false) {
                Yii::$app->session->setFlash('error', '删除失败，请重试。');
            } else {
                Yii::$app->session->setFlash('success', '场馆已删除。');
            }
        } catch (IntegrityException $exception) {
            Yii::$app->session->setFlash('error', '该场馆仍被其他记录引用，不能删除。');
        }
        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if ($id === null || !ctype_digit((string) $id) || (int) $id < 1) {
            throw new NotFoundHttpException('页面不存在。');
        }
        if (($model = Venue::findOne((int) $id)) === null) {
            throw new NotFoundHttpException('页面不存在。');
        }
        return $model;
    }
}
