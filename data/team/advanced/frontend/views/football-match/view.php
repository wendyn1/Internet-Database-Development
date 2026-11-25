<?php
/**
 * 项目：2026世界杯球队信息系统
 * 文件职责：前台比赛详情视图（C4）：比赛资料、球员统计、A统一留言组件挂载（C8）
 * 作者：邓佳怡（学号 2412612）
 * 主要修改者：邓佳怡（学号 2412612）
 */
use common\models\FootballMatch;
use common\models\PlayerMatchStat;
use frontend\widgets\CommentWidget;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model FootballMatch */

$this->title = $model->home_team_name . ' vs ' . $model->away_team_name;
$this->params['breadcrumbs'][] = ['label' => '比赛列表', 'url' => ['/football-match/index']];
$this->params['breadcrumbs'][] = $this->title;

$homeName = Html::encode($model->home_team_name);
$awayName = Html::encode($model->away_team_name);
if ($model->home_team_id !== null && $model->homeTeam !== null) {
    $homeName = Html::a($homeName, ['/team/view', 'id' => $model->home_team_id]);
}
if ($model->away_team_id !== null && $model->awayTeam !== null) {
    $awayName = Html::a($awayName, ['/team/view', 'id' => $model->away_team_id]);
}

$stats = $model->playerMatchStats;
usort($stats, function (PlayerMatchStat $a, PlayerMatchStat $b) {
    $teamCompare = (int) $a->teamPlayer->team_id <=> (int) $b->teamPlayer->team_id;
    return $teamCompare !== 0 ? $teamCompare : ((int) $a->team_player_id <=> (int) $b->team_player_id);
});
?>
<div class="football-match-view">
    <h1><?= $homeName ?> <span class="text-muted">vs</span> <?= $awayName ?></h1>
    <p class="text-muted">本站精选比赛样本，库外对手只显示名称，不提供球队详情链接。</p>

    <table class="table table-bordered">
        <tr>
            <th style="width:160px">北京日期</th>
            <td><?= Html::encode($model->match_date_beijing) ?></td>
            <th style="width:160px">比赛阶段</th>
            <td><?= Html::encode($model->getStageLabel()) ?></td>
        </tr>
        <tr>
            <th>比赛状态</th>
            <td><?= Html::encode($model->getStatusLabel()) ?></td>
            <th>官方比赛编号</th>
            <td><?= $model->fifa_match_number === null ? '未录入' : (int) $model->fifa_match_number ?></td>
        </tr>
        <tr>
            <th>比赛场馆</th>
            <td colspan="3">
                <?php if ($model->venue === null): ?>
                    未录入
                <?php else: ?>
                    <?= Html::encode($model->venue->name_zh) ?>
                    （<?= Html::encode($model->venue->name_en) ?>，<?= Html::encode($model->venue->city) ?>，
                    <?= Html::encode($model->venue->country) ?>）
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <th>比分</th>
            <td colspan="3">
                <?php if ($model->isFinished()): ?>
                    <strong><?= (int) $model->home_score ?> : <?= (int) $model->away_score ?></strong>
                    <?php if ((int) $model->after_extra_time === 1): ?>
                        <span class="text-muted">（含加时；点球大战不计入比分）</span>
                    <?php endif; ?>
                <?php else: ?>
                    <span class="text-muted">尚未产生比分</span>
                <?php endif; ?>
            </td>
        </tr>
    </table>

    <h2>球员统计</h2>
    <?php if ($stats === []): ?>
        <p class="text-muted">本场暂未录入球员统计；未录入不代表未出场或0进球。</p>
    <?php else: ?>
        <table class="table table-striped">
            <tr><th>球队</th><th>球员</th><th>出场</th><th>进球</th><th>助攻</th></tr>
            <?php foreach ($stats as $stat): ?>
                <?php $roster = $stat->teamPlayer; ?>
                <tr>
                    <td><?= $roster === null ? '—' : Html::encode($roster->team->name_zh) ?></td>
                    <td>
                        <?php if ($roster === null || $roster->player === null): ?>
                            —
                        <?php else: ?>
                            <?= Html::a(Html::encode($roster->player->name_zh),
                                ['/player/view', 'id' => $roster->player_id]) ?>
                        <?php endif; ?>
                    </td>
                    <td><?= Html::encode(PlayerMatchStat::appearedLabels()[(int) $stat->appeared] ?? '—') ?></td>
                    <td><?= (int) $stat->goals ?></td>
                    <td><?= $stat->assists === null ? '未核实' : (int) $stat->assists ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

    <?= CommentWidget::widget([
        'targetType' => 'match',
        'targetId' => $model->id,
    ]) ?>
</div>
