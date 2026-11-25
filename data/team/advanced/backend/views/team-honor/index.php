<?php
/**
 * 项目：2026世界杯球队信息系统
 * 文件职责：后台球队荣誉记录列表视图（B6）
 * 作者：王乐妍（2413423）
 * 主要修改者：王乐妍（2413423）
 */
use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '球队荣誉记录管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="team-honor-admin-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <p><?= Html::a('新建荣誉记录', ['create'], ['class' => 'btn btn-success']) ?></p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'id',
            [
                'attribute' => 'team_id',
                'label' => '球队',
                'value' => function ($model) {
                    return $model->team === null ? $model->team_id : $model->team->name_zh;
                },
            ],
            [
                'attribute' => 'honor_id',
                'label' => '荣誉',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->honor === null ? $model->honor_id
                        : Html::a(Html::encode($model->honor->name), ['view', 'id' => $model->id]);
                },
            ],
            'year',
            'description',
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]) ?>
</div>
