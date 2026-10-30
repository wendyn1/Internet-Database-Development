<?php
/** 后台登录、退出和修改本人密码控制器。 */
namespace backend\controllers;

use Yii;
use backend\models\AdminLoginForm;
use backend\models\ChangePasswordForm;
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
        return $this->render('index');
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
