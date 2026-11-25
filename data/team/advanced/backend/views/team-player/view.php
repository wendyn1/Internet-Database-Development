<?php
/**
 * 项目：2026世界杯球队信息系统
 * 文件职责：后台本届阵容详情视图（B6）
 * 作者：王乐妍（2413423）
 * 主要修改者：王乐妍（2413423）
 */
use common\models\TeamPlayer;
use common\models\PlayerMatchStat;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\TeamPlayer */

$positionLabels = [
    TeamPlayer::POSITION_GK => '门将',
    TeamPlayer::POSITION_DF => '后卫',
    TeamPlayer::POSITION_MF => '中场',
    TeamPlayer::POSITION_FW => '前锋',
];

$this->title = '阵容记录 #' . $model->id;
$this->params['breadcrumbs'][] = ['label' => '本届阵容管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="team-player-admin-view">
    <p>
        <?= Html::a('修改', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('删除', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => ['confirm' => '确认删除该阵容记录？', 'method' => 'post'],
        ]) ?>
    </p>
    <!-- 依赖C交付的PlayerMatchStat模型；交付前不显示锁定提示，交付后自动生效 -->
    <?php if (class_exists(PlayerMatchStat::class) && PlayerMatchStat::find()->where(['team_player_id' => $model->id])->exists()): ?>
        <p class="text-danger">该阵容已存在比赛统计，球队、球员与赛事年份已被锁定，不可修改。</p>
    <?php endif; ?>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            ['attribute' => 'team_id', 'label' => '球队',
                'value' => $model->team === null ? $model->team_id : $model->team->name_zh],
            ['attribute' => 'player_id', 'label' => '球员',
                'value' => $model->player === null ? $model->player_id : $model->player->name_zh],
            'tournament_year',
            'shirt_number',
            ['attribute' => 'position_code',
                'value' => $positionLabels[$model->position_code] ?? $model->position_code],
        ],
    ]) ?>
</div>
