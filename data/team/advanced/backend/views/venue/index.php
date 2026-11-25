<?php
/**
 * 项目：2026世界杯球队信息系统
 * 文件职责：后台场馆列表视图（C5）
 * 作者：邓佳怡（学号 2412612）
 * 创建日期：2026-11-25
 * 主要修改者：邓佳怡（学号 2412612）
 */
use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '场馆管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="venue-admin-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <p><?= Html::a('新建场馆', ['create'], ['class' => 'btn btn-success']) ?></p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'id',
            ['attribute' => 'name_zh',
                'format' => 'raw',
                'value' => function ($model) {
                    return Html::a(Html::encode($model->name_zh), ['view', 'id' => $model->id]);
                }],
            'name_en',
            'city',
            'country',
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]) ?>
</div>
