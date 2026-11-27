<?php
/**
 * 项目：2026世界杯球队信息系统
 * 文件职责：前台球队列表与详情控制器（B3）；详情挂载留言与战绩统计（B7/B8），含C的对比与统计JSON动作
 * 作者：王乐妍（2413423）
 * 创建日期：2026-11-22
 * 主要修改者：王乐妍（2413423）
 */
namespace frontend\controllers;

use Yii;
use common\models\Team;
use common\services\TeamStatsService;
use frontend\models\TeamSearch;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

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

    /**
     * 详情页同时挂载战绩统计（B8）：只调用C的统一服务，不复制统计SQL；
     * 服务异常时页面降级为提示，不影响资料与留言展示。
     */
    public function actionView($id = null)
    {
        $model = $this->findModel($id);
        $stats = null;
        try {
            $stats = (new TeamStatsService())->getSummary($model->id);
        } catch (\Throwable $exception) {
            Yii::error($exception->getMessage(), __METHOD__);
        }
        return $this->render('view', ['model' => $model, 'stats' => $stats]);
    }

    /**
     * 两队对比（C6/C7）：参数 team_a_id、team_b_id，按选择顺序左右展示。
     * 非法ID、同队、球队不存在时显示明确错误，不生成有效比较。
     */
    public function actionCompare()
    {
        $request = Yii::$app->request;
        $rawA = $request->get('team_a_id');
        $rawB = $request->get('team_b_id');

        $errors = [];
        $submitted = ($rawA !== null && $rawA !== '') || ($rawB !== null && $rawB !== '');
        $teamA = null;
        $teamB = null;
        $statsA = null;
        $statsB = null;

        if ($submitted) {
            [$aId, $aInvalid] = $this->parseTeamId($rawA);
            [$bId, $bInvalid] = $this->parseTeamId($rawB);
            if ($aInvalid) {
                $errors[] = '球队A的ID必须是正整数。';
            }
            if ($bInvalid) {
                $errors[] = '球队B的ID必须是正整数。';
            }
            if (!$aInvalid && !$bInvalid) {
                if ($aId === null || $bId === null) {
                    $errors[] = '请选择两支球队后再进行对比。';
                } elseif ($aId === $bId) {
                    $errors[] = '不能选择同一支球队进行对比。';
                } else {
                    $teamA = Team::findOne($aId);
                    $teamB = Team::findOne($bId);
                    if ($teamA === null || $teamB === null) {
                        $errors[] = '所选球队不存在，请重新选择。';
                        $teamA = $teamB = null;
                    }
                }
            }
            if ($errors === [] && $teamA !== null && $teamB !== null) {
                $service = new TeamStatsService();
                try {
                    $statsA = $service->getSummary($teamA->id);
                    $statsB = $service->getSummary($teamB->id);
                } catch (\Throwable $exception) {
                    Yii::error($exception->getMessage(), __METHOD__);
                    $errors[] = '统计服务暂时不可用，请稍后重试。';
                    $statsA = $statsB = null;
                }
            }
        }

        $teamInfo = [];
        foreach (Team::find()->orderBy(['id' => SORT_ASC])->all() as $team) {
            $teamInfo[$team->id] = [
                'name_zh' => (string) $team->name_zh,
                'name_en' => (string) $team->name_en,
                'fifa_code' => (string) $team->fifa_code,
                'country' => (string) $team->country,
                'continent' => (string) $team->continent,
                'url' => Url::to(['/team/view', 'id' => $team->id]),
            ];
        }

        return $this->render('compare', [
            'submitted' => $submitted,
            'errors' => $errors,
            'teamA' => $teamA,
            'teamB' => $teamB,
            'statsA' => $statsA,
            'statsB' => $statsB,
            'teamOptions' => $this->teamOptions(),
            'teamInfo' => $teamInfo,
            'selectedA' => is_scalar($rawA) ? (string) $rawA : '',
            'selectedB' => is_scalar($rawB) ? (string) $rawB : '',
        ]);
    }

    /**
     * 公开只读战绩JSON接口（C7）：200成功、400无效参数、404球队不存在、500服务异常。
     */
    public function actionStats()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $summary = (new TeamStatsService())->getSummary(Yii::$app->request->get('team_id'));
            return [
                'success' => true,
                'data' => $summary,
                'message' => TeamStatsService::message(),
            ];
        } catch (\InvalidArgumentException $exception) {
            Yii::$app->response->statusCode = 400;
            return ['success' => false, 'data' => null, 'message' => '球队ID参数无效。'];
        } catch (\DomainException $exception) {
            Yii::$app->response->statusCode = 404;
            return ['success' => false, 'data' => null, 'message' => '球队不存在。'];
        } catch (\Throwable $exception) {
            Yii::error($exception->getMessage(), __METHOD__);
            Yii::$app->response->statusCode = 500;
            return ['success' => false, 'data' => null, 'message' => '统计服务暂时不可用，请稍后重试。'];
        }
    }

    /**
     * @return array [int|null 合法ID, bool 是否非法]
     */
    protected function parseTeamId($value)
    {
        if ($value === null || $value === '') {
            return [null, false];
        }
        if (!is_scalar($value) || !ctype_digit((string) $value) || (int) $value < 1) {
            return [null, true];
        }
        return [(int) $value, false];
    }

    protected function teamOptions()
    {
        $options = [];
        foreach (Team::find()->orderBy(['id' => SORT_ASC])->all() as $team) {
            $options[$team->id] = $team->name_zh . '（ID ' . $team->id . '）';
        }
        return $options;
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
