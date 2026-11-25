<?php
/**
 * 项目：2026世界杯球队信息系统
 * 文件职责：后台执教关系表单视图（B6），新建与修改共用
 * 作者：王乐妍（2413423）
 * 主要修改者：王乐妍（2413423）
 */
use common\models\TeamCoach;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\TeamCoach */
/* @var $lists array 球队与教练下拉列表 */

$this->title = $model->isNewRecord ? '新建执教关系' : ('修改执教关系 #' . $model->id);
$this->params['breadcrumbs'][] = ['label' => '执教关系管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->isNewRecord ? '新建' : '修改';
?>
<div class="team-coach-admin-form">
    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'team_id')->dropDownList($lists['teams'], ['prompt' => '请选择球队'])
        ->hint('同球队同届仅能登记一名主教练') ?>
    <?= $form->field($model, 'coach_id')->dropDownList($lists['coaches'], ['prompt' => '请选择教练']) ?>
    <?= $form->field($model, 'tournament_year')->textInput()->hint('仅支持2026届') ?>
    <?= $form->field($model, 'role')->dropDownList([TeamCoach::ROLE_HEAD_COACH => '主教练']) ?>
    <div class="form-group">
        <?= Html::submitButton('保存', ['class' => 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
