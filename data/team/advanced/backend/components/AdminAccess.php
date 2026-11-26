<?php
/**
 * 项目：2026世界杯球队信息系统
 * 文件职责：后台统一管理员身份判定，仅配置的启用账号视为管理员
 * 作者：牛雨萱（学号 2411061）
 * 创建日期：2026-11-22
 * 主要修改者：牛雨萱（学号 2411061）
 */
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
