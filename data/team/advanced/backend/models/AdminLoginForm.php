<?php
/** 管理员登录表单：仅指定的启用账号可建立会话。 */
namespace backend\models;

use Yii;
use backend\components\AdminAccess;
use common\models\User;
use yii\base\Model;

class AdminLoginForm extends Model
{
    public $username;
    public $password;
    private $identity;

    public function rules()
    {
        return [
            ['username', 'trim'],
            [['username', 'password'], 'required', 'message' => '{attribute}不能为空。'],
            [['username', 'password'], 'string'],
            ['password', 'validateCredentials'],
        ];
    }

    public function attributeLabels()
    {
        return ['username' => '用户名', 'password' => '密码'];
    }

    public function validateCredentials($attribute, $params)
    {
        if ($this->hasErrors()) {
            return;
        }
        $this->identity = User::findByUsername($this->username);
        if (!AdminAccess::isAdmin($this->identity)
            || !$this->identity->validatePassword($this->password)) {
            $this->addError($attribute, '用户名或密码错误。');
        }
    }

    public function login()
    {
        return $this->validate() && Yii::$app->user->login($this->identity, 0);
    }
}
