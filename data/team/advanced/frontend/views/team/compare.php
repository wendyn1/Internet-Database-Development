<?php
/**
 * 项目：2026世界杯球队信息系统
 * 文件职责：前台两队对比页（C6）：资料并排、胜平负饼图、零场空状态
 * 作者：邓佳怡（学号 2412612）
 * 主要修改者：邓佳怡（学号 2412612）
 */
use frontend\assets\TeamStatsAsset;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $submitted bool */
/* @var $errors array */
/* @var $teamA common\models\Team|null */
/* @var $teamB common\models\Team|null */
/* @var $statsA array|null */
/* @var $statsB array|null */
/* @var $teamOptions array */
/* @var $selectedA string */
/* @var $selectedB string */

TeamStatsAsset::register($this);

$this->title = '球队对比';
$this->params['breadcrumbs'][] = ['label' => '球队列表', 'url' => ['/team/index']];
$this->params['breadcrumbs'][] = $this->title;

$renderCard = function ($side, $team, $stats) {
    if ($team === null) {
        return;
    }
    $stats = $stats ?: [
        'played' => 0, 'wins' => 0, 'draws' => 0, 'losses' => 0,
        'goals_for' => 0, 'goals_against' => 0,
    ];
    ?>
    <div class="col-md-6">
        <div class="panel panel-default compare-card" data-side="<?= Html::encode($side) ?>"
             data-played="<?= (int) $stats['played'] ?>"
             data-wins="<?= (int) $stats['wins'] ?>"
             data-draws="<?= (int) $stats['draws'] ?>"
             data-losses="<?= (int) $stats['losses'] ?>"
             data-goals-for="<?= (int) $stats['goals_for'] ?>"
             data-goals-against="<?= (int) $stats['goals_against'] ?>">
            <div class="panel-heading">
                <h2 class="compare-team-name" style="margin:0;font-size:22px">
                    <?= Html::a(Html::encode($team->name_zh), ['/team/view', 'id' => $team->id]) ?>
                </h2>
            </div>
            <div class="panel-body">
                <table class="table table-condensed">
                    <tr><th>英文名</th><td class="info-name-en"><?= Html::encode($team->name_en) ?></td></tr>
                    <tr><th>FIFA三字码</th><td class="info-fifa-code"><?= Html::encode($team->fifa_code) ?></td></tr>
                    <tr><th>国家/地区</th><td class="info-country"><?= Html::encode($team->country) ?></td></tr>
                    <tr><th>洲</th><td class="info-continent"><?= Html::encode($team->continent) ?></td></tr>
                </table>

                <h4>本站已录入战绩</h4>
                <table class="table table-bordered">
                    <tr>
                        <th>场次</th><td class="stat-played"><?= (int) $stats['played'] ?></td>
                        <th>胜</th><td class="stat-wins"><?= (int) $stats['wins'] ?></td>
                    </tr>
                    <tr>
                        <th>平</th><td class="stat-draws"><?= (int) $stats['draws'] ?></td>
                        <th>负</th><td class="stat-losses"><?= (int) $stats['losses'] ?></td>
                    </tr>
                    <tr>
                        <th>进球</th><td class="stat-goals-for"><?= (int) $stats['goals_for'] ?></td>
                        <th>失球</th><td class="stat-goals-against"><?= (int) $stats['goals_against'] ?></td>
                    </tr>
                </table>

                <div class="team-chart" style="text-align:center">
                    <canvas class="team-pie" width="180" height="180"></canvas>
                    <p class="team-pie-empty text-muted"></p>
                    <div class="team-pie-error alert alert-danger" style="display:none">
                        <span class="team-pie-error-text"></span>
                        <button type="button" class="btn btn-xs btn-default team-pie-retry">重试</button>
                    </div>
                    <p class="text-muted" style="font-size:12px">绿色=胜，黄色=平，红色=负；仅统计本站已录入且已结束的比赛。</p>
                </div>
            </div>
        </div>
    </div>
    <?php
};
?>
<div class="team-compare" id="team-compare" data-stats-url="<?= Html::encode(Url::to(['/team/stats'])) ?>" data-team-info="<?= Html::encode(json_encode($teamInfo, JSON_UNESCAPED_UNICODE)) ?>">
    <h1><?= Html::encode($this->title) ?></h1>
    <p class="text-muted">
        进失球与胜平负仅覆盖本站已录入且已结束的2026世界杯比赛，不代表完整赛事成绩；
        加时计入，点球大战不计入，点球决胜的平局仍计平局。
    </p>

    <div class="panel panel-default">
        <div class="panel-body">
            <?= Html::beginForm(['/team/compare'], 'get', ['class' => 'form-inline']) ?>
            <div class="form-group">
                <label for="team-a">球队A</label>
                <?= Html::dropDownList('team_a_id', $selectedA, $teamOptions, [
                    'id' => 'team-a', 'class' => 'form-control', 'prompt' => '请选择球队',
                    'data-compare-select' => '1', 'data-side' => 'a',
                ]) ?>
            </div>
            <div class="form-group">
                <label for="team-b">球队B</label>
                <?= Html::dropDownList('team_b_id', $selectedB, $teamOptions, [
                    'id' => 'team-b', 'class' => 'form-control', 'prompt' => '请选择球队',
                    'data-compare-select' => '1', 'data-side' => 'b',
                ]) ?>
            </div>
            <?= Html::submitButton('对比', ['class' => 'btn btn-primary']) ?>
            <?= Html::a('重置', ['/team/compare'], ['class' => 'btn btn-default']) ?>
            <?= Html::endForm() ?>
        </div>
    </div>

    <?php if ($errors !== []): ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $error): ?>
                <div><?= Html::encode($error) ?></div>
            <?php endforeach; ?>
        </div>
    <?php elseif ($submitted && ($teamA === null || $teamB === null)): ?>
        <div class="alert alert-warning">请选择两支球队后再进行对比。</div>
    <?php endif; ?>

    <?php if ($teamA !== null && $teamB !== null && $statsA !== null && $statsB !== null): ?>
        <div class="row">
            <?php $renderCard('a', $teamA, $statsA); ?>
            <?php $renderCard('b', $teamB, $statsB); ?>
        </div>
    <?php endif; ?>
</div>
