<?php
/**
 * 项目：2026世界杯球队信息系统
 * 文件职责：后台执教关系列表视图（B6）
 * 作者：王乐妍（2413423）
 * 主要修改者：王乐妍（2413423）
 */
use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '执教关系管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="team-coach-admin-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <p><?= Html::a('新建执教关系', ['create'], ['class' => 'btn btn-success']) ?></p>
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
                'attribute' => 'coach_id',
                'label' => '教练',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->coach === null ? $model->coach_id
                        : Html::a(Html::encode($model->coach->name_zh), ['view', 'id' => $model->id]);
                },
            ],
            'tournament_year',
            'role',
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]) ?>
</div>
