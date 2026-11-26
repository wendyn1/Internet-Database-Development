<?php
/**
 * 项目：2026世界杯球队信息系统
 * 文件职责：后台球员统计表单（C5）
 * 作者：邓佳怡（学号 2412612）
 * 主要修改者：邓佳怡（学号 2412612）
 */
use common\models\PlayerMatchStat;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model PlayerMatchStat */
/* @var $matchOptions array */
/* @var $rosterOptions array */
?>
<div class="player-match-stat-admin-form">
    <div class="alert alert-info">
        仅“已结束”比赛可录入统计；阵容年份与球队必须匹配该场比赛。未出场时进球必须为0；助攻留空表示未核实，不等于0。
    </div>
    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'match_id')->dropDownList($matchOptions, ['prompt' => '请选择比赛']) ?>
    <?= $form->field($model, 'team_player_id')->dropDownList($rosterOptions, ['prompt' => '请选择本届阵容记录']) ?>
    <?= $form->field($model, 'appeared')->dropDownList(PlayerMatchStat::appearedLabels(), ['prompt' => '请选择']) ?>
    <?= $form->field($model, 'goals')->textInput(['type' => 'number', 'min' => 0]) ?>
    <?= $form->field($model, 'assists')->textInput(['type' => 'number', 'min' => 0])
        ->hint('留空表示未核实，不代表0次助攻。') ?>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '创建' : '保存', ['class' => 'btn btn-success']) ?>
        <?= Html::a('取消', ['index'], ['class' => 'btn btn-default']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
