<?php
/**
 * 项目：2026世界杯球队信息系统
 * 文件职责：后台比赛表单（C5）：场馆与球队下拉、比分状态、统计锁定提示
 * 作者：邓佳怡（学号 2412612）
 * 创建日期：2026-11-25
 * 主要修改者：邓佳怡（学号 2412612）
 */
use common\models\FootballMatch;
use common\models\Team;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model FootballMatch */
/* @var $venues array */
/* @var $hasStatistics bool */

$hasStatistics = $hasStatistics ?? false;
$teamOptions = [];
foreach (Team::find()->orderBy(['id' => SORT_ASC])->all() as $team) {
    $teamOptions[$team->id] = $team->name_zh . '（ID ' . $team->id . '）';
}
$lockedOptions = $hasStatistics ? ['disabled' => true] : [];
?>
<div class="football-match-admin-form">
    <?php if ($hasStatistics): ?>
        <div class="alert alert-warning">
            该比赛已存在球员统计：双方球队、名称、年份、状态与比分不可修改。如需调整，请先在“球员统计”中删除该场全部统计。
        </div>
    <?php endif; ?>

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'fifa_match_number')->textInput(['type' => 'number', 'min' => 1]) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'tournament_year')->textInput([
                'type' => 'number', 'value' => $model->tournament_year ?: 2026,
            ] + $lockedOptions) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'stage')->dropDownList(FootballMatch::stageLabels(), ['prompt' => '请选择阶段']) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'venue_id')->dropDownList($venues, ['prompt' => '请选择场馆']) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'match_date_beijing')->textInput(['type' => 'date']) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <h3>主队</h3>
            <?= $form->field($model, 'home_team_id')->dropDownList($teamOptions, [
                'prompt' => '库外/未关联球队',
            ] + $lockedOptions) ?>
            <?= $form->field($model, 'home_team_name')->textInput(['maxlength' => true] + $lockedOptions) ?>
            <p class="help-block">选择本库球队后仍以展示名称为准；库外对手留空ID、只填名称。</p>
        </div>
        <div class="col-md-6">
            <h3>客队</h3>
            <?= $form->field($model, 'away_team_id')->dropDownList($teamOptions, [
                'prompt' => '库外/未关联球队',
            ] + $lockedOptions) ?>
            <?= $form->field($model, 'away_team_name')->textInput(['maxlength' => true] + $lockedOptions) ?>
            <p class="help-block">至少一侧必须关联本库球队，且两侧不能相同。</p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3">
            <?= $form->field($model, 'status')->dropDownList(FootballMatch::statusLabels(), ['prompt' => '请选择状态'] + $lockedOptions) ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'home_score')->textInput(['type' => 'number', 'min' => 0] + $lockedOptions) ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'away_score')->textInput(['type' => 'number', 'min' => 0] + $lockedOptions) ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'after_extra_time')->dropDownList([0 => '否', 1 => '是']) ?>
        </div>
    </div>
    <p class="help-block">
        仅“已结束”状态可填写双方非负比分；其他状态比分必须留空。比分含加时，不含点球大战，点球决胜的平局仍计平局。
    </p>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '创建' : '保存', ['class' => 'btn btn-success']) ?>
        <?= Html::a('取消', ['index'], ['class' => 'btn btn-default']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
