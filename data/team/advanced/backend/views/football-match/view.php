<?php
/**
 * 项目：2026世界杯球队信息系统
 * 文件职责：后台比赛详情视图（C5）
 * 作者：邓佳怡（学号 2412612）
 * 主要修改者：邓佳怡（学号 2412612）
 */
use common\models\PlayerMatchStat;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\FootballMatch */
/* @var $statCount int */

$this->title = $model->home_team_name . ' vs ' . $model->away_team_name;
$this->params['breadcrumbs'][] = ['label' => '比赛管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="football-match-admin-view">
    <h1><?= Html::encode($this->title) ?></h1>
    <p>
        <?= Html::a('修改', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('删除', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => ['confirm' => '确定删除该比赛吗？', 'method' => 'post'],
        ]) ?>
        <?= Html::a('新增球员统计', ['/player-match-stat/create', 'match_id' => $model->id],
            ['class' => 'btn btn-default']) ?>
    </p>

    <?php if ($statCount > 0): ?>
        <div class="alert alert-info">
            该比赛已有 <?= (int) $statCount ?> 条球员统计，双方球队、名称、年份、状态与比分已锁定；如需修改，请先删除全部球员统计。
        </div>
    <?php endif; ?>

    <table class="table table-bordered">
        <tr><th style="width:180px">比赛ID</th><td><?= (int) $model->id ?></td></tr>
        <tr><th>官方比赛编号</th><td><?= $model->fifa_match_number === null ? '未录入' : (int) $model->fifa_match_number ?></td></tr>
        <tr><th>赛事年份</th><td><?= (int) $model->tournament_year ?></td></tr>
        <tr><th>比赛阶段</th><td><?= Html::encode($model->getStageLabel()) ?></td></tr>
        <tr><th>北京日期</th><td><?= Html::encode($model->match_date_beijing) ?></td></tr>
        <tr><th>比赛场馆</th><td><?= $model->venue === null ? '—' : Html::encode($model->venue->name_zh) ?></td></tr>
        <tr><th>主队</th><td><?= Html::encode($model->home_team_name) ?>
            <?= $model->home_team_id === null ? '（库外）' : '（本库ID：' . (int) $model->home_team_id . '）' ?></td></tr>
        <tr><th>客队</th><td><?= Html::encode($model->away_team_name) ?>
            <?= $model->away_team_id === null ? '（库外）' : '（本库ID：' . (int) $model->away_team_id . '）' ?></td></tr>
        <tr><th>比赛状态</th><td><?= Html::encode($model->getStatusLabel()) ?></td></tr>
        <tr><th>比分</th><td><?= $model->isFinished()
                ? ((int) $model->home_score . ' : ' . (int) $model->away_score)
                : '—' ?></td></tr>
        <tr><th>是否含加时</th><td><?= (int) $model->after_extra_time === 1 ? '是（点球大战不计入比分）' : '否' ?></td></tr>
    </table>

    <h2>球员统计</h2>
    <?php if ($model->playerMatchStats === []): ?>
        <p class="text-muted">暂无球员统计。</p>
    <?php else: ?>
        <table class="table table-striped">
            <tr><th>ID</th><th>球队</th><th>球员</th><th>出场</th><th>进球</th><th>助攻</th><th>操作</th></tr>
            <?php foreach ($model->playerMatchStats as $stat): ?>
                <?php $roster = $stat->teamPlayer; ?>
                <tr>
                    <td><?= (int) $stat->id ?></td>
                    <td><?= $roster === null ? '—' : Html::encode($roster->team->name_zh) ?></td>
                    <td><?= $roster === null || $roster->player === null ? '—' : Html::encode($roster->player->name_zh) ?></td>
                    <td><?= Html::encode(PlayerMatchStat::appearedLabels()[(int) $stat->appeared] ?? '—') ?></td>
                    <td><?= (int) $stat->goals ?></td>
                    <td><?= $stat->assists === null ? '未核实' : (int) $stat->assists ?></td>
                    <td>
                        <?= Html::a('查看', ['/player-match-stat/view', 'id' => $stat->id]) ?>
                        <?= Html::a('修改', ['/player-match-stat/update', 'id' => $stat->id]) ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</div>
