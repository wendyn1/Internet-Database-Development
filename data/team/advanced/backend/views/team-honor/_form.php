<?php
/**
 * 项目：2026世界杯球队信息系统
 * 文件职责：后台球队荣誉记录表单视图（B6），新建与修改共用
 * 作者：王乐妍（2413423）
 * 创建日期：2026-11-22
 * 主要修改者：王乐妍（2413423）
 */
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\TeamHonor */
/* @var $lists array 球队与荣誉下拉列表 */

$this->title = $model->isNewRecord ? '新建荣誉记录' : ('修改荣誉记录 #' . $model->id);
$this->params['breadcrumbs'][] = ['label' => '球队荣誉记录管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->isNewRecord ? '新建' : '修改';
?>
<div class="team-honor-admin-form">
    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'team_id')->dropDownList($lists['teams'], ['prompt' => '请选择球队']) ?>
    <?= $form->field($model, 'honor_id')->dropDownList($lists['honors'], ['prompt' => '请选择荣誉']) ?>
    <?= $form->field($model, 'year')->textInput()->hint('获得年份；同球队同年不能重复登记同一荣誉') ?>
    <?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>
    <div class="form-group">
        <?= Html::submitButton('保存', ['class' => 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
