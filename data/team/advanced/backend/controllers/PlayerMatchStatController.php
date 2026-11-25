<?php
/**
 * 项目：2026世界杯球队信息系统
 * 文件职责：后台球员比赛统计CRUD（C5）：仅已结束比赛、阵容与球队匹配、进球不超比分
 * 作者：邓佳怡（学号 2412612）
 * 创建日期：2026-11-25
 * 主要修改者：邓佳怡（学号 2412612）
 */
namespace backend\controllers;

use Yii;
use common\models\FootballMatch;
use common\models\PlayerMatchStat;
use common\models\TeamPlayer;
use yii\data\ActiveDataProvider;
use yii\db\IntegrityException;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

class PlayerMatchStatController extends AdminController
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
            'query' => PlayerMatchStat::find()->with([
                'match', 'teamPlayer.player', 'teamPlayer.team',
            ]),
            'pagination' => ['pageSize' => 10, 'pageParam' => 'page'],
            'sort' => ['defaultOrder' => ['id' => SORT_ASC], 'attributes' => ['id']],
        ]);
        return $this->render('index', ['dataProvider' => $dataProvider]);
    }

    public function actionView($id = null)
    {
        $model = $this->findModel($id);
        return $this->render('view', ['model' => $model]);
    }

    public function actionCreate($match_id = null)
    {
        $model = new PlayerMatchStat();
        if ($match_id !== null && ctype_digit((string) $match_id)) {
            $model->match_id = (int) $match_id;
        }
        if ($this->saveModel($model)) {
            Yii::$app->session->setFlash('success', '球员统计已创建。');
            return $this->redirect(['view', 'id' => $model->id]);
        }
        return $this->render('create', [
            'model' => $model,
            'matchOptions' => $this->matchOptions(),
            'rosterOptions' => $this->rosterOptions(),
        ]);
    }

    public function actionUpdate($id = null)
    {
        $model = $this->findModel($id);
        if ($this->saveModel($model)) {
            Yii::$app->session->setFlash('success', '球员统计已更新。');
            return $this->redirect(['view', 'id' => $model->id]);
        }
        return $this->render('update', [
            'model' => $model,
            'matchOptions' => $this->matchOptions(),
            'rosterOptions' => $this->rosterOptions(),
        ]);
    }

    public function actionDelete($id = null)
    {
        $model = $this->findModel($id);
        try {
            if ($model->delete() === false) {
                Yii::$app->session->setFlash('error', '删除失败，请重试。');
            } else {
                Yii::$app->session->setFlash('success', '球员统计已删除。');
            }
        } catch (IntegrityException $exception) {
            Yii::$app->session->setFlash('error', '该统计仍被其他记录引用，不能删除。');
        }
        return $this->redirect(['index']);
    }

    /**
     * 事务内完成跨表校验与保存，避免与比赛/阵容修改并发冲突。
     */
    protected function saveModel(PlayerMatchStat $model)
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
            $model->addError('goals', '保存失败，请检查输入后重试。');
            return false;
        }
    }

    protected function matchOptions()
    {
        $options = [];
        $matches = FootballMatch::find()
            ->orderBy(['match_date_beijing' => SORT_DESC, 'id' => SORT_DESC])
            ->all();
        foreach ($matches as $match) {
            $options[$match->id] = $match->match_date_beijing . ' '
                . $match->home_team_name . ' vs ' . $match->away_team_name
                . '（' . $match->getStatusLabel() . '，ID ' . $match->id . '）';
        }
        return $options;
    }

    protected function rosterOptions()
    {
        $options = [];
        $rosters = TeamPlayer::find()
            ->with(['team', 'player'])
            ->orderBy(['team_id' => SORT_ASC, 'shirt_number' => SORT_ASC])
            ->all();
        foreach ($rosters as $roster) {
            $teamName = $roster->team === null ? '未知球队' : $roster->team->name_zh;
            $playerName = $roster->player === null ? '未知球员' : $roster->player->name_zh;
            $options[$roster->id] = $teamName . ' · ' . $playerName
                . '（#' . (int) $roster->shirt_number . '，阵容ID ' . $roster->id . '）';
        }
        return $options;
    }

    protected function findModel($id)
    {
        if ($id === null || !ctype_digit((string) $id) || (int) $id < 1) {
            throw new NotFoundHttpException('页面不存在。');
        }
        $model = PlayerMatchStat::find()
            ->with(['match', 'teamPlayer.player', 'teamPlayer.team'])
            ->where(['id' => (int) $id])
            ->one();
        if ($model === null) {
            throw new NotFoundHttpException('页面不存在。');
        }
        return $model;
    }
}
