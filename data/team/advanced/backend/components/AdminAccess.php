<?php
/** 后台统一管理员身份判定。 */
namespace backend\components;

use Yii;
use common\models\User;

class AdminAccess
{
    public static function isAdmin($identity)
    {
        $username = Yii::$app->params['adminUsername'] ?? null;
        return $identity instanceof User
            && is_string($username) && $username !== ''
            && $identity->username === $username
            && (int) $identity->status === User::STATUS_ACTIVE;
    }
}
