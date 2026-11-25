<?php
/**
 * 项目：2026世界杯球队信息系统
 * 文件职责：后台荣誉定义列表视图（B6）
 * 作者：王乐妍（2413423）
 * 主要修改者：王乐妍（2413423）
 */
use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '荣誉管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="honor-admin-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <p><?= Html::a('新建荣誉', ['create'], ['class' => 'btn btn-success']) ?></p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'id',
            ['attribute' => 'name',
                'format' => 'raw',
                'value' => function ($model) {
                    return Html::a(Html::encode($model->name), ['view', 'id' => $model->id]);
                }],
            'competition',
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]) ?>
</div>
