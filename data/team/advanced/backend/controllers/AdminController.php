<?php
/** 后台业务控制器基类：所有动作默认要求指定管理员身份。 */
namespace backend\controllers;

use Yii;
use backend\components\AdminAccess;
use yii\filters\AccessControl;
use yii\web\Controller;

class AdminController extends Controller
{
    public function behaviors()
    {
        return [
            'adminAccess' => [
                'class' => AccessControl::class,
                'rules' => [[
                    'allow' => true,
                    'roles' => ['@'],
                    'matchCallback' => function ($rule, $action) {
                        return AdminAccess::isAdmin(Yii::$app->user->identity);
                    },
                ]],
            ],
        ];
    }
}
