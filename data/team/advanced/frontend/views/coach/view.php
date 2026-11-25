<?php
/**
 * 项目：2026世界杯球队信息系统
 * 文件职责：教练详情视图（B4）：从球队详情进入；缺图占位
 * 作者：王乐妍（2413423）
 * 创建日期：2026-11-22
 * 主要修改者：王乐妍（2413423）
 */
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Coach */

$this->title = $model->name_zh;
$this->params['breadcrumbs'][] = ['label' => '球队列表', 'url' => ['/team/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="coach-view">
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
        </div>
    </div>

    <?php if ($model->introduction !== null && $model->introduction !== ''): ?>
        <p><?= Html::encode($model->introduction) ?></p>
    <?php endif; ?>

    <h2>2026届执教球队</h2>
    <?php if ($model->teamCoaches === []): ?>
        <p class="text-muted">暂未收录本届执教记录。</p>
    <?php else: ?>
        <ul>
            <?php foreach ($model->teamCoaches as $relation): ?>
                <li>
                    <?= Html::a(Html::encode($relation->team->name_zh),
                        ['/team/view', 'id' => $relation->team_id]) ?>
                    （<?= (int)$relation->tournament_year ?>届）
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>
