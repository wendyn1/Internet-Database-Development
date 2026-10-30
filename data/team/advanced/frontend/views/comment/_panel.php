<?php
/** 留言区：已通过列表和统一表单，每页10条。 */
use yii\widgets\ListView;
?>
<section id="comments">
    <h2>留言</h2>
    <?= ListView::widget([
        'dataProvider' => $provider,
        'itemView' => '@frontend/views/comment/_list',
        'emptyText' => '暂无公开留言。',
        'summary' => '',
    ]) ?>
    <?= $this->render('_form', ['model' => $model,
        'targetType' => $targetType, 'targetId' => $targetId]) ?>
</section>
