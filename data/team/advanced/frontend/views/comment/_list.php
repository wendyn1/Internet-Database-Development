<?php
/** 单条公开留言视图：昵称、正文及时间均转义，正文按纯文本展示。 */
use yii\helpers\Html;
?>
<article class="comment-item">
    <p><strong><?= Html::encode($model->nickname) ?></strong>
        <time><?= Html::encode($model->created_at) ?></time></p>
    <p><?= nl2br(Html::encode($model->content)) ?></p>
</article>
