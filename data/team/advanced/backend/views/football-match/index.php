<?php
/**
 * 项目：2026世界杯球队信息系统
 * 文件职责：后台比赛列表视图（C5）
 * 作者：邓佳怡（学号 2412612）
 * 主要修改者：邓佳怡（学号 2412612）
 */
use common\models\FootballMatch;
use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '比赛管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="football-match-admin-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <p><?= Html::a('新建比赛', ['create'], ['class' => 'btn btn-success']) ?></p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'id',
            ['attribute' => 'match_date_beijing', 'label' => '北京日期'],
            [
                'label' => '对阵',
                'value' => function (FootballMatch $model) {
                    return $model->home_team_name . ' vs ' . $model->away_team_name;
                },
            ],
            ['attribute' => 'stage', 'label' => '阶段',
                'value' => function (FootballMatch $model) { return $model->getStageLabel(); }],
            [
                'label' => '比分',
                'value' => function (FootballMatch $model) {
                    return $model->isFinished()
                        ? ((int) $model->home_score . ' : ' . (int) $model->away_score)
                        : '—';
                },
            ],
            ['attribute' => 'status', 'label' => '状态',
                'value' => function (FootballMatch $model) { return $model->getStatusLabel(); }],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]) ?>
</div>
