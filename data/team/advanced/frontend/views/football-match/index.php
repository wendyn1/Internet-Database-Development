<?php
/**
 * 项目：2026世界杯球队信息系统
 * 文件职责：前台比赛列表视图（C4）：筛选、分页、比分与库外对手展示
 * 作者：邓佳怡（学号 2412612）
 * 创建日期：2026-11-25
 * 主要修改者：邓佳怡（学号 2412612）
 */
use common\models\FootballMatch;
use common\models\Team;
use frontend\models\FootballMatchSearch;
use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel FootballMatchSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '比赛列表';
$this->params['breadcrumbs'][] = $this->title;

$teams = Team::find()->orderBy(['id' => SORT_ASC])->all();
$teamOptions = [];
foreach ($teams as $team) {
    $teamOptions[$team->id] = $team->name_zh;
}
?>
<div class="football-match-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <p class="text-muted">本站仅收录2026世界杯精选比赛样本，统计与比分不代表完整赛事记录。</p>

    <div class="panel panel-default">
        <div class="panel-body">
            <?= Html::beginForm(['/football-match/index'], 'get', ['class' => 'form-inline']) ?>
            <div class="form-group">
                <?= Html::activeLabel($searchModel, 'team_id') ?>
                <?= Html::activeDropDownList($searchModel, 'team_id', $teamOptions, [
                    'class' => 'form-control', 'prompt' => '全部球队',
                ]) ?>
            </div>
            <div class="form-group">
                <?= Html::activeLabel($searchModel, 'date') ?>
                <?= Html::activeInput('date', $searchModel, 'date', ['class' => 'form-control']) ?>
            </div>
            <div class="form-group">
                <?= Html::activeLabel($searchModel, 'stage') ?>
                <?= Html::activeDropDownList($searchModel, 'stage', FootballMatch::stageLabels(), [
                    'class' => 'form-control', 'prompt' => '全部阶段',
                ]) ?>
            </div>
            <div class="form-group">
                <?= Html::activeLabel($searchModel, 'status') ?>
                <?= Html::activeDropDownList($searchModel, 'status', FootballMatch::statusLabels(), [
                    'class' => 'form-control', 'prompt' => '全部状态',
                ]) ?>
            </div>
            <?= Html::submitButton('筛选', ['class' => 'btn btn-primary']) ?>
            <?= Html::a('重置', ['/football-match/index'], ['class' => 'btn btn-default']) ?>
            <?= Html::endForm() ?>
        </div>
    </div>

    <?php if ($searchModel->hasErrors()): ?>
        <div class="alert alert-danger">
            <?= Html::encode(implode('；', $searchModel->getFirstErrors())) ?>
        </div>
    <?php endif; ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['attribute' => 'match_date_beijing', 'label' => '北京日期'],
            ['attribute' => 'stage', 'label' => '阶段',
                'value' => function ($model) { return $model->getStageLabel(); }],
            [
                'label' => '对阵',
                'format' => 'raw',
                'value' => function (FootballMatch $model) {
                    return Html::a(Html::encode($model->home_team_name), ['/football-match/view', 'id' => $model->id])
                        . ' vs ' . Html::encode($model->away_team_name);
                },
            ],
            [
                'label' => '比分',
                'value' => function (FootballMatch $model) {
                    return $model->isFinished()
                        ? ((int) $model->home_score . ' : ' . (int) $model->away_score)
                        : '—';
                },
            ],
            ['attribute' => 'status', 'label' => '状态',
                'value' => function ($model) { return $model->getStatusLabel(); }],
            ['label' => '场馆', 'value' => function (FootballMatch $model) {
                return $model->venue === null ? '—' : $model->venue->name_zh;
            }],
            ['class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
                'buttons' => [
                    'view' => function ($url, $model) {
                        return Html::a('详情', ['/football-match/view', 'id' => $model->id]);
                    },
                ]],
        ],
    ]) ?>
</div>
