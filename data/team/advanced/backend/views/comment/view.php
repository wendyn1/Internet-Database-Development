<?php
/**
 * 项目：2026世界杯球队信息系统
 * 文件职责：后台留言详情视图，展示对象、内容、时间与审核状态
 * 作者：牛雨萱（学号 2411061）
 * 创建日期：2026-11-22
 * 主要修改者：牛雨萱（学号 2411061）
 */
use common\models\Comment;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Comment */

$this->title = '留言详情';
$this->params['breadcrumbs'][] = ['label' => '留言管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$statusClasses = [
    Comment::STATUS_PENDING => 'default',
    Comment::STATUS_APPROVED => 'success',
    Comment::STATUS_REJECTED => 'warning',
    Comment::STATUS_DELETED => 'danger',
];
?>
<div class="comment-view">
    <h1><?= Html::encode($this->title) ?></h1>

    <table class="table table-bordered">
        <tr><th style="width:120px;">编号</th><td><?= (int) $model->id ?></td></tr>
        <tr><th>留言对象</th><td><?= Html::encode($model->getTargetLabel()) ?></td></tr>
        <tr><th>昵称</th><td><?= Html::encode($model->nickname) ?></td></tr>
        <tr><th>内容</th><td><?= nl2br(Html::encode($model->content)) ?></td></tr>
        <tr><th>提交时间</th><td><?= Html::encode($model->created_at) ?></td></tr>
        <tr>
            <th>状态</th>
            <td>
                <span class="label label-<?= $statusClasses[$model->status] ?? 'default' ?>">
                    <?= Html::encode($model->getStatusLabel()) ?>
                </span>
            </td>
        </tr>
        <tr><th>审核人编号</th><td><?= $model->reviewed_by === null ? '—' : (int) $model->reviewed_by ?></td></tr>
        <tr><th>审核时间</th><td><?= $model->reviewed_at === null ? '—' : Html::encode($model->reviewed_at) ?></td></tr>
    </table>

    <?php if ($model->status !== Comment::STATUS_DELETED): ?>
        <p>
            <?php if ($model->status === Comment::STATUS_PENDING): ?>
                <?= Html::beginForm(['approve', 'id' => $model->id], 'post', ['style' => 'display:inline']) ?>
                    <?= Html::submitButton('通过', ['class' => 'btn btn-success',
                        'onclick' => "return confirm('确认通过该留言？')"]) ?>
                <?= Html::endForm() ?>
                <?= Html::beginForm(['reject', 'id' => $model->id], 'post', ['style' => 'display:inline']) ?>
                    <?= Html::submitButton('不通过', ['class' => 'btn btn-warning',
                        'onclick' => "return confirm('确认将该留言标记为未通过？')"]) ?>
                <?= Html::endForm() ?>
            <?php endif; ?>
            <?= Html::beginForm(['delete', 'id' => $model->id], 'post', ['style' => 'display:inline']) ?>
                <?= Html::submitButton('删除', ['class' => 'btn btn-danger',
                    'onclick' => "return confirm('删除后前台不可见，确认删除该留言？')"]) ?>
            <?= Html::endForm() ?>
        </p>
    <?php endif; ?>

    <p><?= Html::a('返回列表', ['index'], ['class' => 'btn btn-default']) ?></p>
</div>
