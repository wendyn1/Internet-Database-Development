<?php
/**
 * 项目：2026世界杯球队信息系统
 * 文件职责：球队列表视图（B4）
 * 作者：王乐妍（2413423）
 * 创建日期：2026-11-22
 * 主要修改者：王乐妍（2413423）
 */
use frontend\models\TeamSearch;
use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel TeamSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '球队列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="team-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <p class="text-muted">本站为2026世界杯精选球队样本，不代表完整参赛名单。</p>

    <div class="panel panel-default">
        <div class="panel-body">
            <?= Html::beginForm(['/team/index'], 'get', ['class' => 'form-inline']) ?>
            <div class="form-group">
                <?= Html::activeLabel($searchModel, 'name') ?>
                <?= Html::activeTextInput($searchModel, 'name', ['class' => 'form-control']) ?>
            </div>
            <?= Html::submitButton('筛选', ['class' => 'btn btn-primary']) ?>
            <?= Html::a('重置', ['/team/index'], ['class' => 'btn btn-default']) ?>
            <?= Html::endForm() ?>
        </div>
    </div>

    <?php if ($searchModel->hasErrors()): ?>
        <div class="alert alert-danger">
            <?= Html::encode(implode('；', $searchModel->getFirstErrors())) ?>
        </div>
    <?php endif; ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'id',
            [
                'attribute' => 'name_zh',
                'format' => 'raw',
                'value' => function ($model) {
                    return Html::a(Html::encode($model->name_zh), ['/team/view', 'id' => $model->id]);
                },
            ],
            'name_en',
            'fifa_code',
            'country',
            'continent',
        ],
    ]) ?>
</div>
