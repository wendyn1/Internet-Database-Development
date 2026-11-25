<?php
/**
 * 项目：2026世界杯球队信息系统
 * 文件职责：前台教练详情控制器（B3）：仅详情页，从球队详情进入
 * 作者：王乐妍（2413423）
 * 主要修改者：王乐妍（2413423）
 */
namespace frontend\controllers;

use common\models\Coach;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class CoachController extends Controller
{
    public function actionView($id = null)
    {
        if ($id === null || !ctype_digit((string)$id) || (int)$id < 1) {
            throw new NotFoundHttpException('页面不存在。');
        }
        $model = Coach::find()
            ->with([
                'teamCoaches' => function ($query) {
                    $query->andWhere(['{{%team_coach}}.tournament_year' => 2026])->with('team');
                },
            ])
            ->where(['{{%coach}}.id' => (int)$id])
            ->one();
        if ($model === null) {
            throw new NotFoundHttpException('页面不存在。');
        }
        return $this->render('view', ['model' => $model]);
    }
}
