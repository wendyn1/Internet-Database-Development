<?php
/**
 * 项目：2026世界杯球队信息系统
 * 文件职责：后台球员统计列表视图（C5）
 * 作者：邓佳怡（学号 2412612）
 * 创建日期：2026-11-25
 * 主要修改者：邓佳怡（学号 2412612）
 */
use common\models\PlayerMatchStat;
use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '球员统计管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="player-match-stat-admin-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <p><?= Html::a('新增球员统计', ['create'], ['class' => 'btn btn-success']) ?></p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'id',
            [
                'label' => '比赛',
                'value' => function (PlayerMatchStat $model) {
                    return $model->match === null ? '—'
                        : ($model->match->match_date_beijing . ' ' . $model->match->home_team_name
                            . ' vs ' . $model->match->away_team_name);
                },
            ],
            [
                'label' => '球队',
                'value' => function (PlayerMatchStat $model) {
                    return $model->teamPlayer === null || $model->teamPlayer->team === null
                        ? '—' : $model->teamPlayer->team->name_zh;
                },
            ],
            [
                'label' => '球员',
                'value' => function (PlayerMatchStat $model) {
                    return $model->teamPlayer === null || $model->teamPlayer->player === null
                        ? '—' : $model->teamPlayer->player->name_zh;
                },
            ],
            [
                'label' => '出场',
                'value' => function (PlayerMatchStat $model) {
                    return PlayerMatchStat::appearedLabels()[(int) $model->appeared] ?? '—';
                },
            ],
            'goals',
            [
                'label' => '助攻',
                'value' => function (PlayerMatchStat $model) {
                    return $model->assists === null ? '未核实' : (int) $model->assists;
                },
            ],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]) ?>
</div>
