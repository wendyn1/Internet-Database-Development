<?php
/**
 * 项目：2026世界杯球队信息系统
 * 文件职责：后台执教关系详情视图（B6）
 * 作者：王乐妍（2413423）
 * 创建日期：2026-11-22
 * 主要修改者：王乐妍（2413423）
 */
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\TeamCoach */

$this->title = '执教关系 #' . $model->id;
$this->params['breadcrumbs'][] = ['label' => '执教关系管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="team-coach-admin-view">
    <p>
        <?= Html::a('修改', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('删除', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => ['confirm' => '确认删除该执教关系？', 'method' => 'post'],
        ]) ?>
    </p>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            ['attribute' => 'team_id', 'label' => '球队',
                'value' => $model->team === null ? $model->team_id : $model->team->name_zh],
            ['attribute' => 'coach_id', 'label' => '教练',
                'value' => $model->coach === null ? $model->coach_id : $model->coach->name_zh],
            'tournament_year',
            'role',
        ],
    ]) ?>
</div>
