<?php
/**
 * 项目：2026世界杯球队信息系统
 * 文件职责：后台球队表单视图（B6），新建与修改共用
 * 作者：王乐妍（2413423）
 * 创建日期：2026-11-22
 * 主要修改者：王乐妍（2413423）
 */
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Team */

$this->title = $model->isNewRecord ? '新建球队' : ('修改球队：' . $model->name_zh);
$this->params['breadcrumbs'][] = ['label' => '球队管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->isNewRecord ? '新建' : '修改';
?>
<div class="team-admin-form">
    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'name_zh')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'name_en')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'fifa_code')->textInput(['maxlength' => 3]) ?>
    <?= $form->field($model, 'country')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'continent')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'introduction')->textarea(['rows' => 5]) ?>
    <?= $form->field($model, 'logo_path')->textInput(['maxlength' => true])
        ->hint('队徽相对路径或URL，留空显示占位图；上传功能待A/B/C协调后接入') ?>
    <div class="form-group">
        <?= Html::submitButton('保存', ['class' => 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
