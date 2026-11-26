<?php
/**
 * 项目：2026世界杯球队信息系统
 * 文件职责：后台留言列表视图，按状态筛选并执行审核操作
 * 作者：牛雨萱（学号 2411061）
 * 创建日期：2026-11-22
 * 主要修改者：牛雨萱（学号 2411061）
 */
use common\models\Comment;
use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $status string */

$this->title = '留言管理';
$this->params['breadcrumbs'][] = $this->title;

$statusClasses = [
    Comment::STATUS_PENDING => 'default',
    Comment::STATUS_APPROVED => 'success',
    Comment::STATUS_REJECTED => 'warning',
    Comment::STATUS_DELETED => 'danger',
];
?>
<div class="comment-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <p>访客留言提交后为待审核，审核通过后才在对应详情页公开展示。</p>

    <?= Html::beginForm(['/comment/index'], 'get', ['class' => 'form-inline', 'style' => 'margin-bottom:15px;']) ?>
        <?= Html::dropDownList('status', $status, ['' => '全部状态'] + Comment::statusLabels(),
            ['class' => 'form-control']) ?>
        <?= Html::submitButton('筛选', ['class' => 'btn btn-primary']) ?>
        <?= Html::a('重置', ['/comment/index'], ['class' => 'btn btn-default']) ?>
    <?= Html::endForm() ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => ['class' => 'table table-striped table-bordered'],
        'emptyText' => '暂无可显示的留言。',
        'columns' => [
            ['attribute' => 'id', 'label' => '编号'],
            ['label' => '留言对象', 'value' => function (Comment $model) {
                return $model->getTargetLabel();
            }],
            ['attribute' => 'nickname', 'label' => '昵称'],
            ['label' => '内容', 'value' => function (Comment $model) {
                $length = mb_strlen($model->content, 'UTF-8');
                return mb_substr($model->content, 0, 40, 'UTF-8') . ($length > 40 ? '…' : '');
            }],
            ['attribute' => 'created_at', 'label' => '提交时间'],
            ['label' => '状态', 'format' => 'raw', 'value' => function (Comment $model) use ($statusClasses) {
                $class = $statusClasses[$model->status] ?? 'default';
                return '<span class="label label-' . $class . '">'
                    . Html::encode($model->getStatusLabel()) . '</span>';
            }],
            ['label' => '操作', 'format' => 'raw', 'value' => function (Comment $model) {
                $html = Html::a('查看', ['view', 'id' => $model->id],
                    ['class' => 'btn btn-default btn-xs']);
                if ($model->status === Comment::STATUS_PENDING) {
                    $html .= ' ' . Html::beginForm(['approve', 'id' => $model->id], 'post',
                            ['style' => 'display:inline'])
                        . Html::submitButton('通过', ['class' => 'btn btn-success btn-xs',
                            'onclick' => "return confirm('确认通过该留言？')"])
                        . Html::endForm();
                    $html .= ' ' . Html::beginForm(['reject', 'id' => $model->id], 'post',
                            ['style' => 'display:inline'])
                        . Html::submitButton('不通过', ['class' => 'btn btn-warning btn-xs',
                            'onclick' => "return confirm('确认将该留言标记为未通过？')"])
                        . Html::endForm();
                }
                if ($model->status !== Comment::STATUS_DELETED) {
                    $html .= ' ' . Html::beginForm(['delete', 'id' => $model->id], 'post',
                            ['style' => 'display:inline'])
                        . Html::submitButton('删除', ['class' => 'btn btn-danger btn-xs',
                            'onclick' => "return confirm('删除后前台不可见，确认删除该留言？')"])
                        . Html::endForm();
                }
                return $html;
            }],
        ],
    ]) ?>
</div>
