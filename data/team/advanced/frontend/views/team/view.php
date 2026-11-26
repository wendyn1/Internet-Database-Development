<?php
/**
 * 项目：2026世界杯球队信息系统
 * 文件职责：球队详情视图（B4）：资料、本届阵容、教练、荣誉、战绩统计（B8）与留言组件；
 * 作者：王乐妍（2413423）
 * 创建日期：2026-11-22
 * 主要修改者：王乐妍（2413423）
 */
use frontend\widgets\CommentWidget;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Team */
/* @var $stats array|null */

$positionLabels = [
    common\models\TeamPlayer::POSITION_GK => '门将',
    common\models\TeamPlayer::POSITION_DF => '后卫',
    common\models\TeamPlayer::POSITION_MF => '中场',
    common\models\TeamPlayer::POSITION_FW => '前锋',
];

$this->title = $model->name_zh;
$this->params['breadcrumbs'][] = ['label' => '球队列表', 'url' => ['/team/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="team-view">
    <div class="media">
        <div class="media-left">
            <?php if ($model->logo_path !== null && $model->logo_path !== ''): ?>
                <?= Html::img(Html::encode($model->logo_path),
                    ['alt' => Html::encode($model->name_zh), 'class' => 'media-object', 'style' => 'width:96px']) ?>
            <?php else: ?>
                <div class="media-object text-center"
                     style="width:96px;height:96px;line-height:96px;background:#eee;color:#999">
                    暂无队徽
                </div>
            <?php endif; ?>
        </div>
        <div class="media-body">
            <h1><?= Html::encode($model->name_zh) ?></h1>
            <p>
                <?= Html::encode($model->name_en) ?>（<?= Html::encode($model->fifa_code) ?>）
                ｜<?= Html::encode($model->country) ?>｜<?= Html::encode($model->continent) ?>
            </p>
        </div>
    </div>

    <?php if ($model->introduction !== null && $model->introduction !== ''): ?>
        <p><?= Html::encode($model->introduction) ?></p>
    <?php endif; ?>

    <h2>2026届阵容</h2>
    <?php $roster = $model->teamPlayers; usort($roster,
        function ($a, $b) { return $a->shirt_number <=> $b->shirt_number; }) ?>
    <?php if ($roster === []): ?>
        <p class="text-muted">暂未收录本届阵容。</p>
    <?php else: ?>
        <table class="table table-striped">
            <tr><th>号码</th><th>位置</th><th>球员</th><th>国籍</th></tr>
            <?php foreach ($roster as $entry): ?>
                <tr>
                    <td><?= (int)$entry->shirt_number ?></td>
                    <td><?= Html::encode($positionLabels[$entry->position_code] ?? $entry->position_code) ?></td>
                    <td>
                        <?= Html::a(Html::encode($entry->player->name_zh),
                            ['/player/view', 'id' => $entry->player_id]) ?>
                    </td>
                    <td><?= Html::encode($entry->player->nationality) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

    <h2>主教练</h2>
    <?php if ($model->teamCoaches === []): ?>
        <p class="text-muted">暂未收录本届主教练。</p>
    <?php else: ?>
        <ul>
            <?php foreach ($model->teamCoaches as $relation): ?>
                <li>
                    <?= Html::a(Html::encode($relation->coach->name_zh),
                        ['/coach/view', 'id' => $relation->coach_id]) ?>
                    （<?= Html::encode($relation->coach->nationality) ?>）
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <h2>荣誉记录</h2>
    <?php if ($model->teamHonors === []): ?>
        <p class="text-muted">暂未收录荣誉记录。</p>
    <?php else: ?>
        <ul>
            <?php foreach ($model->teamHonors as $record): ?>
                <li>
                    <?= Html::encode($record->honor->name) ?>
                    （<?= (int)$record->year ?>，<?= Html::encode($record->honor->competition) ?>）
                    —— <?= Html::encode($record->description) ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <h2>2026届战绩（本站已收录比赛）</h2>
    <?php if ($stats === null): ?>
        <p class="text-muted">战绩统计服务暂时不可用，请稍后重试。</p>
    <?php elseif ($stats['played'] === 0): ?>
        <p class="text-muted">本站暂未收录该队已结束且比分完整的比赛，暂无战绩统计。</p>
    <?php else: ?>
        <table class="table table-bordered">
            <tr>
                <th>场次</th><td><?= (int)$stats['played'] ?></td>
                <th>胜</th><td><?= (int)$stats['wins'] ?></td>
                <th>平</th><td><?= (int)$stats['draws'] ?></td>
                <th>负</th><td><?= (int)$stats['losses'] ?></td>
            </tr>
            <tr>
                <th>进球</th><td><?= (int)$stats['goals_for'] ?></td>
                <th>失球</th><td><?= (int)$stats['goals_against'] ?></td>
                <th colspan="4"></th>
            </tr>
        </table>
        <p class="text-muted" style="font-size:12px">
            仅统计本站已录入且已结束的2026世界杯比赛；加时计入，点球大战不计入，点球决胜的平局仍计平局，不代表完整赛事成绩。
        </p>
    <?php endif; ?>
    <p>
        <?= Html::a('查看该队已收录比赛',
            ['/football-match/index', 'FootballMatchSearch' => ['team_id' => $model->id]]) ?>
        ｜
        <?= Html::a('与其他球队对比',
            ['/team/compare', 'team_a_id' => $model->id]) ?>
    </p>

    <?= CommentWidget::widget([
        'targetType' => 'team',
        'targetId' => $model->id,
    ]) ?>
</div>
