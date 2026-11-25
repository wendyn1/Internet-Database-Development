<?php
/**
 * 项目：2026世界杯球队信息系统
 * 文件职责：后台球员统计详情视图（C5）
 * 作者：邓佳怡（学号 2412612）
 * 创建日期：2026-11-25
 * 主要修改者：邓佳怡（学号 2412612）
 */
use common\models\PlayerMatchStat;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model PlayerMatchStat */

$this->title = '球员统计 #' . $model->id;
$this->params['breadcrumbs'][] = ['label' => '球员统计管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="player-match-stat-admin-view">
    <h1><?= Html::encode($this->title) ?></h1>
    <p>
        <?= Html::a('修改', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('删除', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => ['confirm' => '确定删除该条统计吗？', 'method' => 'post'],
        ]) ?>
    </p>
    <table class="table table-bordered">
        <tr><th style="width:180px">统计ID</th><td><?= (int) $model->id ?></td></tr>
        <tr><th>比赛</th><td>
            <?php if ($model->match === null): ?>
                —
            <?php else: ?>
                <?= Html::a(Html::encode($model->match->home_team_name . ' vs ' . $model->match->away_team_name),
                    ['/football-match/view', 'id' => $model->match->id]) ?>
                （<?= Html::encode($model->match->match_date_beijing) ?>，
                <?= Html::encode($model->match->getStatusLabel()) ?>）
            <?php endif; ?>
        </td></tr>
        <tr><th>球队</th><td><?= $model->teamPlayer === null || $model->teamPlayer->team === null
                ? '—' : Html::encode($model->teamPlayer->team->name_zh) ?></td></tr>
        <tr><th>球员</th><td><?= $model->teamPlayer === null || $model->teamPlayer->player === null
                ? '—' : Html::encode($model->teamPlayer->player->name_zh) ?></td></tr>
        <tr><th>是否出场</th><td><?= Html::encode(PlayerMatchStat::appearedLabels()[(int) $model->appeared] ?? '—') ?></td></tr>
        <tr><th>进球</th><td><?= (int) $model->goals ?></td></tr>
        <tr><th>助攻</th><td><?= $model->assists === null ? '未核实' : (int) $model->assists ?></td></tr>
    </table>
</div>
