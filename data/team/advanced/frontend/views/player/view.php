<?php
/**
 * 项目：2026世界杯球队信息系统
 * 文件职责：球员详情视图（B4）：出生日期未知不显示年龄，缺图占位，留言组件已挂载
 * 作者：王乐妍（2413423）
 * 创建日期：2026-11-22
 * 主要修改者：王乐妍（2413423）
 */
use common\models\TeamPlayer;
use frontend\widgets\CommentWidget;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Player */

$positionLabels = [
    TeamPlayer::POSITION_GK => '门将',
    TeamPlayer::POSITION_DF => '后卫',
    TeamPlayer::POSITION_MF => '中场',
    TeamPlayer::POSITION_FW => '前锋',
];

$this->title = $model->name_zh;
$this->params['breadcrumbs'][] = ['label' => '球员列表', 'url' => ['/player/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="player-view">
    <div class="media">
        <div class="media-left">
            <?php if ($model->photo_path !== null && $model->photo_path !== ''): ?>
                <?= Html::img(Html::encode($model->photo_path),
                    ['alt' => Html::encode($model->name_zh), 'class' => 'media-object', 'style' => 'width:96px']) ?>
            <?php else: ?>
                <div class="media-object text-center"
                     style="width:96px;height:96px;line-height:96px;background:#eee;color:#999">
                    暂无照片
                </div>
            <?php endif; ?>
        </div>
        <div class="media-body">
            <h1><?= Html::encode($model->name_zh) ?></h1>
            <p><?= Html::encode($model->name_en) ?>｜国籍：<?= Html::encode($model->nationality) ?></p>
            <?php if ($model->birth_date !== null && $model->birth_date !== ''): ?>
                <p>
                    出生日期：<?= Html::encode($model->birth_date) ?>
                    <?php $birth = \DateTime::createFromFormat('Y-m-d', $model->birth_date);
                    if ($birth !== false): ?>
                    ｜年龄：<?= (int)(new \DateTime('today'))->diff($birth)->y ?>
                    <?php endif; ?>
                </p>
            <?php endif; ?>
        </div>
    </div>

    <h2>2026届归属</h2>
    <?php if ($model->teamPlayers === []): ?>
        <p class="text-muted">该球员未收录2026届阵容。</p>
    <?php else: ?>
        <table class="table table-striped">
            <tr><th>球队</th><th>位置</th><th>号码</th></tr>
            <?php foreach ($model->teamPlayers as $entry): ?>
                <tr>
                    <td><?= Html::a(Html::encode($entry->team->name_zh),
                            ['/team/view', 'id' => $entry->team_id]) ?></td>
                    <td><?= Html::encode($positionLabels[$entry->position_code] ?? $entry->position_code) ?></td>
                    <td><?= (int)$entry->shirt_number ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

    <?= CommentWidget::widget([
        'targetType' => 'player',
        'targetId' => $model->id,
    ]) ?>
</div>
