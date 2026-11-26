<?php
/**
 * 项目：2026世界杯球队信息系统
 * 文件职责：后台登录、退出和修改本人密码控制器
 * 作者：牛雨萱（学号 2411061）
 * 创建日期：2026-11-22
 * 主要修改者：牛雨萱（学号 2411061）
 */
namespace backend\controllers;

use Yii;
use backend\models\AdminLoginForm;
use backend\models\ChangePasswordForm;
use common\models\Coach;
use common\models\Comment;
use common\models\FootballMatch;
use common\models\Honor;
use common\models\Player;
use common\models\Team;
use yii\filters\VerbFilter;

class SiteController extends AdminController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['adminAccess']['except'] = ['login', 'error'];
        $behaviors['verbs'] = [
            'class' => VerbFilter::class,
            'actions' => ['logout' => ['post'], 'login' => ['get', 'post'],
                'change-password' => ['get', 'post']],
        ];
        return $behaviors;
    }

    public function actions()
    {
        return ['error' => ['class' => 'yii\web\ErrorAction']];
    }

    public function actionIndex()
    {
        $counts = [
            'team' => Team::find()->count(),
            'player' => Player::find()->count(),
            'coach' => Coach::find()->count(),
            'match' => FootballMatch::find()->count(),
            'honor' => Honor::find()->count(),
            'pendingComment' => Comment::find()
                ->where(['status' => Comment::STATUS_PENDING])->count(),
        ];
        $recentComments = Comment::find()
            ->where(['status' => Comment::STATUS_PENDING])
            ->orderBy(['created_at' => SORT_DESC, 'id' => SORT_DESC])
            ->limit(5)
            ->all();

        return $this->render('index', [
            'counts' => $counts,
            'recentComments' => $recentComments,
        ]);
    }

    public function actionLogin()
    {
        if (\backend\components\AdminAccess::isAdmin(Yii::$app->user->identity)) {
            return $this->goHome();
        }
        $model = new AdminLoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goHome();
        }
        $model->password = '';
        return $this->render('login', ['model' => $model]);
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->redirect(['site/login']);
    }

    public function actionChangePassword()
    {
        $model = new ChangePasswordForm();
        if ($model->load(Yii::$app->request->post()) && $model->changePassword()) {
            Yii::$app->user->logout();
            Yii::$app->session->setFlash('success', '密码修改成功，请使用新密码重新登录。');
            return $this->redirect(['site/login']);
        }
        $model->oldPassword = '';
        $model->newPassword = '';
        $model->confirmPassword = '';
        return $this->render('change-password', ['model' => $model]);
    }
}
