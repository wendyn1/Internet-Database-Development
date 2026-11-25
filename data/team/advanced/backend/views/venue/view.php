<?php
/**
 * 项目：2026世界杯球队信息系统
 * 文件职责：后台场馆详情视图（C5）
 * 作者：邓佳怡（学号 2412612）
 * 创建日期：2026-11-25
 * 主要修改者：邓佳怡（学号 2412612）
 */
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Venue */

$this->title = $model->name_zh;
$this->params['breadcrumbs'][] = ['label' => '场馆管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="venue-admin-view">
    <h1><?= Html::encode($this->title) ?></h1>
    <p>
        <?= Html::a('修改', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('删除', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => ['confirm' => '确定删除该场馆吗？', 'method' => 'post'],
        ]) ?>
    </p>
    <table class="table table-bordered">
        <tr><th style="width:180px">ID</th><td><?= (int) $model->id ?></td></tr>
        <tr><th>中文场馆名</th><td><?= Html::encode($model->name_zh) ?></td></tr>
        <tr><th>英文场馆名</th><td><?= Html::encode($model->name_en) ?></td></tr>
        <tr><th>城市</th><td><?= Html::encode($model->city) ?></td></tr>
        <tr><th>国家</th><td><?= Html::encode($model->country) ?></td></tr>
    </table>
</div>
