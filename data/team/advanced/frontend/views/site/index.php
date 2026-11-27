<?php
/**
 * 项目：2026世界杯球队信息系统
 * 文件职责：前台首页，展示网站主题、精选球队入口与最近比赛
 * 作者：牛雨萱（学号 2411061）
 * 创建日期：2026-11-22
 * 主要修改者：牛雨萱（学号 2411061）
 */
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $teams common\models\Team[] */
/* @var $recentMatches common\models\FootballMatch[] */

$this->title = '首页';
$heroImage = Yii::getAlias('@web/images/hero-stadium.jpg');
?>
<div class="site-index">

    <div class="jumbotron" style="
        background-color:#16281c;
        background-image:linear-gradient(rgba(0,0,0,0.55), rgba(0,0,0,0.55)), url('<?= Html::encode($heroImage) ?>');
        background-size:cover;
        background-position:center;
        color:#fff;
        padding:60px 40px;
        border-radius:6px;">
        <h1 style="text-shadow:0 2px 8px rgba(0,0,0,0.8);">2026世界杯球队信息系统</h1>
        <p class="lead" style="text-shadow:0 1px 6px rgba(0,0,0,0.8);">
            精选6支球队，集中展示球队、球员、教练、比赛与荣誉资料，
            支持球队对比、胜平负图表与访客留言。
        </p>
        <p>
            <?= Html::a('浏览球队', ['/team/index'], ['class' => 'btn btn-default btn-lg']) ?>
            <?= Html::a('查看比赛', ['/football-match/index'], ['class' => 'btn btn-primary btn-lg']) ?>
            <?= Html::a('球队对比', ['/team/compare'], ['class' => 'btn btn-default btn-lg']) ?>
        </p>
        <p class="text-right" style="font-size:12px;text-shadow:0 1px 4px rgba(0,0,0,0.8);">
            本站为课程作业样本库，所收录比赛与统计仅代表已录入数据。
        </p>
    </div>

    <h2>精选球队</h2>
    <?php if ($teams === []): ?>
        <p class="text-muted">暂无球队资料。</p>
    <?php else: ?>
        <div class="row team-cards">
            <?php foreach ($teams as $team): ?>
                <div class="col-md-4">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <?php if (!empty($team->logo_path)): ?>
                                <?= Html::img('@web/' . ltrim($team->logo_path, '/'), [
                                    'alt' => $team->name_zh,
                                    'style' => 'height:64px;max-width:64px;object-fit:contain;',
                                ]) ?>
                            <?php else: ?>
                                <div style="width:64px;height:64px;line-height:64px;border-radius:50%;
                                            background:#eeeeee;text-align:center;font-weight:bold;">
                                    <?= Html::encode($team->fifa_code) ?>
                                </div>
                            <?php endif; ?>
                            <h3 style="margin-top:12px;">
                                <?= Html::a(Html::encode($team->name_zh), ['/team/view', 'id' => $team->id]) ?>
                            </h3>
                            <p class="text-muted">
                                <?= Html::encode($team->country) ?> · <?= Html::encode($team->continent) ?>
                            </p>
                            <?php $intro = (string) $team->introduction; ?>
                            <p>
                                <?= Html::encode(mb_substr($intro, 0, 50, 'UTF-8')) ?><?= mb_strlen($intro, 'UTF-8') > 50 ? '…' : '' ?>
                            </p>
                            <?= Html::a('查看球队', ['/team/view', 'id' => $team->id],
                                ['class' => 'btn btn-primary btn-sm']) ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <h2>最近比赛</h2>
    <?php if ($recentMatches === []): ?>
        <p class="text-muted">暂无比赛记录。</p>
    <?php else: ?>
        <table class="table table-striped">
            <thead>
            <tr>
                <th>北京日期</th>
                <th>阶段</th>
                <th>对阵</th>
                <th>比分</th>
                <th>状态</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($recentMatches as $match): ?>
                <tr>
                    <td><?= Html::encode($match->match_date_beijing) ?></td>
                    <td><?= Html::encode($match->getStageLabel()) ?></td>
                    <td>
                        <?= Html::a(
                            Html::encode($match->home_team_name) . ' vs ' . Html::encode($match->away_team_name),
                            ['/football-match/view', 'id' => $match->id]
                        ) ?>
                    </td>
                    <td>
                        <?= $match->isFinished()
                            ? (int) $match->home_score . ' : ' . (int) $match->away_score
                            : '未公布' ?>
                    </td>
                    <td><?= Html::encode($match->getStatusLabel()) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <p><?= Html::a('查看全部比赛 »', ['/football-match/index']) ?></p>
    <?php endif; ?>

</div>
