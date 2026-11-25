<?php
/**
 * 项目：2026世界杯球队信息系统
 * 文件职责：前台球员列表与详情控制器（B3）
 * 作者：王乐妍（2413423）
 * 主要修改者：王乐妍（2413423）
 */
namespace frontend\controllers;

use Yii;
use common\models\Player;
use frontend\models\PlayerSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class PlayerController extends Controller
{
    public function actionIndex()
    {
        $searchModel = new PlayerSearch();
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
     * 非法或缺失详情ID一律404；预加载2026届阵容与球队。
     */
    protected function findModel($id)
    {
        if ($id === null || !ctype_digit((string)$id) || (int)$id < 1) {
            throw new NotFoundHttpException('页面不存在。');
        }
        $model = Player::find()
            ->with([
                'teamPlayers' => function ($query) {
                    $query->andWhere(['{{%team_player}}.tournament_year' => 2026])->with('team');
                },
            ])
            ->where(['{{%player}}.id' => (int)$id])
            ->one();
        if ($model === null) {
            throw new NotFoundHttpException('页面不存在。');
        }
        return $model;
    }
}
