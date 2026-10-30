<?php
/** 留言目标适配：路由及公开可见判定必须由对应模块确认，未配置则关闭入口。 */
namespace frontend\components;

use Yii;
use yii\web\NotFoundHttpException;
use yii\web\HttpException;

class CommentTarget
{
    public static function resolve($type, $id)
    {
        $fields = ['team' => 'team_id', 'player' => 'player_id', 'match' => 'match_id'];
        if (!is_string($type) || !isset($fields[$type])
            || (!is_string($id) && !is_int($id))
            || !preg_match('/^[1-9][0-9]*$/D', (string) $id)
            || filter_var($id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) === false) {
            throw new NotFoundHttpException('留言对象不存在。');
        }
        $config = Yii::$app->params['commentTargets'][$type] ?? [];
        $route = $config['detailRoute'] ?? null;
        $checker = $config['publicExists'] ?? null;
        if (!is_string($route) || !preg_match('~^/[a-z0-9_-]+/[a-z0-9_-]+$~D', $route)
            || !is_callable($checker)) {
            throw new HttpException(503, '留言暂未开放。');
        }
        if (!call_user_func($checker, (int) $id)) {
            throw new NotFoundHttpException('留言对象不存在或暂不公开。');
        }
        return ['field' => $fields[$type], 'id' => (int) $id,
            'url' => [$route, 'id' => (int) $id, '#' => 'comments']];
    }
}
