<?php
/** 修改当前管理员密码表单，验证失败不更新数据库。 */
namespace backend\models;

use Yii;
use backend\components\AdminAccess;
use yii\base\Model;

class ChangePasswordForm extends Model
{
    public $oldPassword;
    public $newPassword;
    public $confirmPassword;

    public function rules()
    {
        return [
            [['oldPassword', 'newPassword', 'confirmPassword'], 'required', 'message' => '{attribute}不能为空。'],
            [['oldPassword', 'newPassword', 'confirmPassword'], 'string'],
            ['newPassword', 'string', 'min' => 8, 'max' => 64,
                'tooShort' => '新密码至少需要8个字符。', 'tooLong' => '新密码最多64个字符。'],
            ['confirmPassword', 'compare', 'compareAttribute' => 'newPassword', 'message' => '两次新密码输入不一致。'],
            ['oldPassword', 'validateOldPassword'],
            ['newPassword', 'validateDifferentPassword'],
        ];
    }

    public function attributeLabels()
    {
        return ['oldPassword' => '旧密码', 'newPassword' => '新密码', 'confirmPassword' => '确认新密码'];
    }

    public function validateOldPassword($attribute, $params)
    {
        if ($this->hasErrors($attribute)) {
            return;
        }
        $identity = Yii::$app->user->identity;
        if (!AdminAccess::isAdmin($identity) || !$identity->validatePassword($this->oldPassword)) {
            $this->addError($attribute, '旧密码错误。');
        }
    }

    public function validateDifferentPassword($attribute, $params)
    {
        if (!$this->hasErrors($attribute) && is_string($this->oldPassword)
            && $this->newPassword === $this->oldPassword) {
            $this->addError($attribute, '新密码不能与旧密码相同。');
        }
    }

    public function changePassword()
    {
        if (!$this->validate()) {
            return false;
        }
        $identity = Yii::$app->user->identity;
        if (!AdminAccess::isAdmin($identity)) {
            return false;
        }
        $identity->setPassword($this->newPassword);
        if (!$identity->save(false, ['password_hash', 'updated_at'])) {
            $this->addError('newPassword', '密码保存失败，请重试。');
            return false;
        }
        return true;
    }
}
