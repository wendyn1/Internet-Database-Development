<?php
/**
 * 项目：2026世界杯球队信息系统
 * 文件职责：后台比赛CRUD（C5）：比分状态校验、统计存在时字段锁定、依赖删除限制
 * 作者：邓佳怡（学号 2412612）
 * 创建日期：2026-11-25
 * 主要修改者：邓佳怡（学号 2412612）
 */
namespace backend\controllers;

use Yii;
use common\models\Comment;
use common\models\FootballMatch;
use common\models\PlayerMatchStat;
use common\models\Venue;
use yii\data\ActiveDataProvider;
use yii\db\IntegrityException;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

class FootballMatchController extends AdminController
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
            'query' => FootballMatch::find()->with(['venue', 'homeTeam', 'awayTeam']),
            'pagination' => ['pageSize' => 10, 'pageParam' => 'page'],
            'sort' => [
                'defaultOrder' => ['match_date_beijing' => SORT_DESC, 'id' => SORT_DESC],
                'attributes' => ['match_date_beijing', 'id'],
            ],
        ]);
        return $this->render('index', ['dataProvider' => $dataProvider]);
    }

    public function actionView($id = null)
    {
        $model = $this->findModel($id);
        return $this->render('view', [
            'model' => $model,
            'statCount' => PlayerMatchStat::find()->where(['match_id' => $model->id])->count(),
        ]);
    }

    public function actionCreate()
    {
        $model = new FootballMatch();
        if ($this->saveModel($model)) {
            Yii::$app->session->setFlash('success', '比赛已创建。');
            return $this->redirect(['view', 'id' => $model->id]);
        }
        return $this->render('create', ['model' => $model, 'venues' => $this->venueOptions()]);
    }

    public function actionUpdate($id = null)
    {
        $model = $this->findModel($id);
        if ($this->saveModel($model)) {
            Yii::$app->session->setFlash('success', '比赛已更新。');
            return $this->redirect(['view', 'id' => $model->id]);
        }
        return $this->render('update', [
            'model' => $model,
            'venues' => $this->venueOptions(),
            'hasStatistics' => $model->hasStatistics(),
        ]);
    }

    public function actionDelete($id = null)
    {
        $model = $this->findModel($id);
        $statCount = PlayerMatchStat::find()->where(['match_id' => $model->id])->count();
        if ($statCount > 0) {
            Yii::$app->session->setFlash('error', "该比赛已有 $statCount 条球员统计，请先在“球员统计”中删除后再删除比赛。");
            return $this->redirect(['index']);
        }
        $commentCount = Comment::find()->where(['match_id' => $model->id])->count();
        if ($commentCount > 0) {
            Yii::$app->session->setFlash('error', "该比赛已被 $commentCount 条留言引用，不能删除。");
            return $this->redirect(['index']);
        }
        try {
            if ($model->delete() === false) {
                Yii::$app->session->setFlash('error', '删除失败，请重试。');
            } else {
                Yii::$app->session->setFlash('success', '比赛已删除。');
            }
        } catch (IntegrityException $exception) {
            Yii::$app->session->setFlash('error', '该比赛仍被其他记录引用，不能删除。');
        }
        return $this->redirect(['index']);
    }

    /**
     * 在事务中校验并保存比赛，避免跨表校验与写入之间的并发不一致。
     */
    protected function saveModel(FootballMatch $model)
    {
        if (!Yii::$app->request->isPost || !$model->load(Yii::$app->request->post())) {
            return false;
        }
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$model->save()) {
                $transaction->rollBack();
                return false;
            }
            $transaction->commit();
            return true;
        } catch (\Throwable $exception) {
            $transaction->rollBack();
            Yii::error($exception->getMessage(), __METHOD__);
            $model->addError('status', '保存失败，请检查输入后重试。');
            return false;
        }
    }

    protected function venueOptions()
    {
        $options = [];
        foreach (Venue::find()->orderBy(['id' => SORT_ASC])->all() as $venue) {
            $options[$venue->id] = $venue->name_zh . '（' . $venue->city . '）';
        }
        return $options;
    }

    protected function findModel($id)
    {
        if ($id === null || !ctype_digit((string) $id) || (int) $id < 1) {
            throw new NotFoundHttpException('页面不存在。');
        }
        $model = FootballMatch::find()
            ->with(['venue', 'homeTeam', 'awayTeam', 'playerMatchStats.teamPlayer.player', 'playerMatchStats.teamPlayer.team'])
            ->where(['id' => (int) $id])
            ->one();
        if ($model === null) {
            throw new NotFoundHttpException('页面不存在。');
        }
        return $model;
    }
}
