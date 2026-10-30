<?php
/** 提交失败页：复用留言区，保留安全输入并提供固定详情返回链接。 */
use frontend\widgets\CommentWidget;
use yii\helpers\Html;
$this->title = '留言未提交';
?>
<h1><?= Html::encode($this->title) ?></h1>
<p><?= Html::a('返回详情', $targetUrl) ?></p>
<?= CommentWidget::widget(['targetType' => $targetType,
    'targetId' => $targetId, 'model' => $model]) ?>
