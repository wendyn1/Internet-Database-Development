<?php
/**
 * 项目：2026世界杯球队信息系统
 * 文件职责：后台本届阵容CRUD（B5）：已有比赛统计的阵容修改受模型锁定规则限制
 * 作者：王乐妍（2413423）
 * 主要修改者：王乐妍（2413423）
 */
namespace backend\controllers;

use Yii;
use common\models\Player;
use common\models\Team;
use common\models\TeamPlayer;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

class TeamPlayerController extends AdminController
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
            'query' => TeamPlayer::find()->with('player', 'team'),
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
        $model = new TeamPlayer();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', '阵容记录已创建。');
            return $this->redirect(['view', 'id' => $model->id]);
        }
        return $this->render('create', ['model' => $model, 'lists' => $this->dropdownLists()]);
    }

    public function actionUpdate($id = null)
    {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', '阵容记录已更新。');
            return $this->redirect(['view', 'id' => $model->id]);
        }
        return $this->render('update', ['model' => $model, 'lists' => $this->dropdownLists()]);
    }

    public function actionDelete($id = null)
    {
        $model = $this->findModel($id);
        // 依赖C交付的PlayerMatchStat模型；交付前由数据库FK RESTRICT兜底拦截
        if (class_exists(\common\models\PlayerMatchStat::class)) {
            $statCount = \common\models\PlayerMatchStat::find()->where(['team_player_id' => $model->id])->count();
            if ($statCount > 0) {
                Yii::$app->session->setFlash('error',
                    "存在 $statCount 条球员比赛统计，删除前须先清除该场全部统计，不能直接删除该阵容。");
                return $this->redirect(['index']);
            }
        }
        if ($model->delete() === false) {
            Yii::$app->session->setFlash('error', '删除失败，可能存在关联的比赛统计，请重试。');
        } else {
            Yii::$app->session->setFlash('success', '阵容记录已删除。');
        }
        return $this->redirect(['index']);
    }

    protected function dropdownLists()
    {
        return [
            'teams' => ArrayHelper::map(Team::find()->orderBy('id')->all(), 'id', 'name_zh'),
            'players' => ArrayHelper::map(Player::find()->orderBy('id')->all(), 'id', 'name_zh'),
        ];
    }

    protected function findModel($id)
    {
        if ($id === null || !ctype_digit((string)$id) || (int)$id < 1) {
            throw new NotFoundHttpException('页面不存在。');
        }
        if (($model = TeamPlayer::findOne((int)$id)) === null) {
            throw new NotFoundHttpException('页面不存在。');
        }
        return $model;
    }
}
