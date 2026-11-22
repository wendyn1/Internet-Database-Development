<?php
/**
 * 项目：2026世界杯球队信息系统
 * 文件职责：前台球队列表与详情控制器（B3）；详情是留言与统计的挂载目标（B7/B8待集成）
 * 作者：王乐妍（2413423）
 * 创建日期：2026-11-22
 * 主要修改者：王乐妍（2413423）
 */
namespace frontend\controllers;

use Yii;
use common\models\Team;
use frontend\models\TeamSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class TeamController extends Controller
{
    public function actionIndex()
    {
        $searchModel = new TeamSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id = null)
    {
        return $this->render('view', ['model' => $this->findModel($id)]);
    }

    /**
     * 非法或缺失详情ID一律404；预加载本届阵容、教练与荣誉。
     */
    protected function findModel($id)
    {
        if ($id === null || !ctype_digit((string)$id) || (int)$id < 1) {
            throw new NotFoundHttpException('页面不存在。');
        }
        $model = Team::find()
            ->with([
                'teamPlayers' => function ($query) {
                    $query->andWhere(['{{%team_player}}.tournament_year' => 2026])->with('player');
                },
                'teamCoaches' => function ($query) {
                    $query->andWhere(['{{%team_coach}}.tournament_year' => 2026])->with('coach');
                },
                'teamHonors.honor',
            ])
            ->where(['{{%team}}.id' => (int)$id])
            ->one();
        if ($model === null) {
            throw new NotFoundHttpException('页面不存在。');
        }
        return $model;
    }
}
