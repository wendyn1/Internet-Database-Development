<?php
/**
 * 项目：2026世界杯球队信息系统
 * 文件职责：后台教练列表视图（B6）
 * 作者：王乐妍（2413423）
 * 创建日期：2026-11-22
 * 主要修改者：王乐妍（2413423）
 */
use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '教练管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="coach-admin-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <p><?= Html::a('新建教练', ['create'], ['class' => 'btn btn-success']) ?></p>
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
            'nationality',
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]) ?>
</div>
