<?php
/**
 * 项目：2026世界杯球队信息系统
 * 文件职责：后台本届阵容列表视图（B6）
 * 作者：王乐妍（2413423）
 * 创建日期：2026-11-22
 * 主要修改者：王乐妍（2413423）
 */
use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '本届阵容管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="team-player-admin-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <p><?= Html::a('新建阵容记录', ['create'], ['class' => 'btn btn-success']) ?></p>
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
                'attribute' => 'player_id',
                'label' => '球员',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->player === null ? $model->player_id
                        : Html::a(Html::encode($model->player->name_zh), ['view', 'id' => $model->id]);
                },
            ],
            'tournament_year',
            'shirt_number',
            'position_code',
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]) ?>
</div>
