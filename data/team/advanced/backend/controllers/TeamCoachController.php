<?php
/**
 * 项目：2026世界杯球队信息系统
 * 文件职责：后台球队执教关系CRUD（B5）：同队同届唯一约束由模型保证
 * 作者：王乐妍（2413423）
 * 创建日期：2026-11-22
 * 主要修改者：王乐妍（2413423）
 */
namespace backend\controllers;

use Yii;
use common\models\Coach;
use common\models\Team;
use common\models\TeamCoach;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

class TeamCoachController extends AdminController
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
            'query' => TeamCoach::find()->with('team', 'coach'),
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
        $model = new TeamCoach();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', '执教关系已创建。');
            return $this->redirect(['view', 'id' => $model->id]);
        }
        return $this->render('create', ['model' => $model, 'lists' => $this->dropdownLists()]);
    }

    public function actionUpdate($id = null)
    {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', '执教关系已更新。');
            return $this->redirect(['view', 'id' => $model->id]);
        }
        return $this->render('update', ['model' => $model, 'lists' => $this->dropdownLists()]);
    }

    public function actionDelete($id = null)
    {
        $model = $this->findModel($id);
        if ($model->delete() === false) {
            Yii::$app->session->setFlash('error', '删除失败，请重试。');
        } else {
            Yii::$app->session->setFlash('success', '执教关系已删除。');
        }
        return $this->redirect(['index']);
    }

    protected function dropdownLists()
    {
        return [
            'teams' => ArrayHelper::map(Team::find()->orderBy('id')->all(), 'id', 'name_zh'),
            'coaches' => ArrayHelper::map(Coach::find()->orderBy('id')->all(), 'id', 'name_zh'),
        ];
    }

    protected function findModel($id)
    {
        if ($id === null || !ctype_digit((string)$id) || (int)$id < 1) {
            throw new NotFoundHttpException('页面不存在。');
        }
        if (($model = TeamCoach::findOne((int)$id)) === null) {
            throw new NotFoundHttpException('页面不存在。');
        }
        return $model;
    }
}
