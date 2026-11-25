<?php
/**
 * 项目：2026世界杯球队信息系统
 * 文件职责：球员列表视图（B4）：展示2026届阵容归属的球队、位置与号码
 * 作者：王乐妍（2413423）
 * 创建日期：2026-11-22
 * 主要修改者：王乐妍（2413423）
 */
use common\models\TeamPlayer;
use frontend\models\PlayerSearch;
use common\models\Team;
use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel PlayerSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$positionLabels = [
    TeamPlayer::POSITION_GK => '门将',
    TeamPlayer::POSITION_DF => '后卫',
    TeamPlayer::POSITION_MF => '中场',
    TeamPlayer::POSITION_FW => '前锋',
];

$teamOptions = [];
foreach (Team::find()->orderBy('id')->all() as $team) {
    $teamOptions[$team->id] = $team->name_zh;
}

$this->title = '球员列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="player-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <p class="text-muted">本站为2026世界杯精选球员样本，球队与位置按2026届阵容过滤，不代表完整参赛名单。</p>

    <div class="panel panel-default">
        <div class="panel-body">
            <?= Html::beginForm(['/player/index'], 'get', ['class' => 'form-inline']) ?>
            <div class="form-group">
                <?= Html::activeLabel($searchModel, 'name') ?>
                <?= Html::activeTextInput($searchModel, 'name', ['class' => 'form-control']) ?>
            </div>
            <div class="form-group">
                <?= Html::activeLabel($searchModel, 'team_id') ?>
                <?= Html::activeDropDownList($searchModel, 'team_id', $teamOptions,
                    ['prompt' => '全部球队', 'class' => 'form-control']) ?>
            </div>
            <div class="form-group">
                <?= Html::activeLabel($searchModel, 'position') ?>
                <?= Html::activeDropDownList($searchModel, 'position',
                    array_merge(['' => '全部位置'], $positionLabels), ['class' => 'form-control']) ?>
            </div>
            <?= Html::submitButton('筛选', ['class' => 'btn btn-primary']) ?>
            <?= Html::a('重置', ['/player/index'], ['class' => 'btn btn-default']) ?>
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
            'id',
            [
                'attribute' => 'name_zh',
                'format' => 'raw',
                'value' => function ($model) {
                    return Html::a(Html::encode($model->name_zh), ['/player/view', 'id' => $model->id]);
                },
            ],
            'name_en',
            'nationality',
            [
                'label' => '本届球队',
                'format' => 'raw',
                'value' => function ($model) {
                    $entries = $model->teamPlayers;
                    if ($entries === []) {
                        return '';
                    }
                    return Html::a(Html::encode($entries[0]->team->name_zh),
                        ['/team/view', 'id' => $entries[0]->team_id]);
                },
            ],
            [
                'label' => '位置',
                'value' => function ($model) use ($positionLabels) {
                    $entries = $model->teamPlayers;
                    return $entries === [] ? ''
                        : ($positionLabels[$entries[0]->position_code] ?? $entries[0]->position_code);
                },
            ],
            [
                'label' => '号码',
                'value' => function ($model) {
                    $entries = $model->teamPlayers;
                    return $entries === [] ? '' : (int)$entries[0]->shirt_number;
                },
            ],
        ],
    ]) ?>
</div>
