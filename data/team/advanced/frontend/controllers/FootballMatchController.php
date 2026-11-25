<?php
/**
 * 项目：2026世界杯球队信息系统
 * 文件职责：前台比赛列表与详情控制器（C3/C4）；详情留言通过A的统一CommentController提交（C8）
 * 作者：邓佳怡（学号 2412612）
 * 创建日期：2026-11-25
 * 主要修改者：邓佳怡（学号 2412612）
 */
namespace frontend\controllers;

use Yii;
use common\models\FootballMatch;
use frontend\models\FootballMatchSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class FootballMatchController extends Controller
{
    public function actionIndex()
    {
        $searchModel = new FootballMatchSearch();
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
     * 非法或缺失详情ID一律404；预加载场馆、双方球队与球员统计。
     */
    protected function findModel($id)
    {
        if ($id === null || !ctype_digit((string) $id) || (int) $id < 1) {
            throw new NotFoundHttpException('页面不存在。');
        }
        $model = FootballMatch::find()
            ->with([
                'venue',
                'homeTeam',
                'awayTeam',
                'playerMatchStats.teamPlayer.player',
                'playerMatchStats.teamPlayer.team',
            ])
            ->where(['{{%football_match}}.id' => (int) $id])
            ->one();
        if ($model === null) {
            throw new NotFoundHttpException('页面不存在。');
        }
        return $model;
    }
}
