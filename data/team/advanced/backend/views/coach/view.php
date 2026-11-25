<?php
/**
 * 项目：2026世界杯球队信息系统
 * 文件职责：后台教练详情视图（B6）
 * 作者：王乐妍（2413423）
 * 主要修改者：王乐妍（2413423）
 */
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Coach */

$this->title = $model->name_zh;
$this->params['breadcrumbs'][] = ['label' => '教练管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="coach-admin-view">
    <p>
        <?= Html::a('修改', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('删除', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => ['confirm' => '确认删除该教练？', 'method' => 'post'],
        ]) ?>
    </p>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name_zh',
            'name_en',
            'nationality',
            ['attribute' => 'introduction', 'format' => 'ntext'],
            'photo_path',
        ],
    ]) ?>
</div>
