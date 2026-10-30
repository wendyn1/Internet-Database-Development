<?php
/** 首次部署管理员初始化命令；仅交互创建，拒绝覆盖已有账号。 */
namespace console\controllers;

use Yii;
use common\models\User;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\validators\EmailValidator;

class AdminController extends Controller
{
    /**
     * 交互创建后台配置指定的管理员：php yii admin/init。
     * 密码不接受命令行参数，不写入输出或文件。
     */
    public function actionInit()
    {
        if (!$this->interactive || PHP_OS_FAMILY !== 'Windows') {
            $this->stderr("请在 Windows 本机交互终端运行此命令。\n");
            return ExitCode::USAGE;
        }
        $params = array_merge(
            require Yii::getAlias('@backend/config/params.php'),
            require Yii::getAlias('@backend/config/params-local.php')
        );
        $username = $params['adminUsername'] ?? null;
        if (!is_string($username) || trim($username) === '') {
            $this->stderr("后台未配置有效的 adminUsername。\n");
            return ExitCode::CONFIG;
        }
        try {
            if (User::find()->where(['username' => $username])->exists()) {
                $this->stderr("账号已存在，拒绝覆盖；未修改原账号。\n");
                return ExitCode::DATAERR;
            }
            $this->stdout("将创建管理员：{$username}\n");
            $email = trim($this->prompt('请输入管理员邮箱'));
            if (!(new EmailValidator())->validate($email) || mb_strlen($email, 'UTF-8') > 255) {
                $this->stderr("邮箱格式不正确或超过255个字符。\n");
                return ExitCode::DATAERR;
            }
            if (User::find()->where(['email' => $email])->exists()) {
                $this->stderr("邮箱已被使用，未创建账号。\n");
                return ExitCode::DATAERR;
            }
            $password = $this->readHiddenPassword('请输入初始密码（8—64个字符）：');
            $confirm = $this->readHiddenPassword('请再次输入初始密码：');
            $length = mb_strlen($password, 'UTF-8');
            if ($length < 8 || $length > 64 || $password !== $confirm) {
                $this->stderr("密码须为8—64个字符，且两次输入一致；未创建账号。\n");
                return ExitCode::DATAERR;
            }
            if (!$this->confirm('确认创建该管理员？', false)) {
                $this->stdout("已取消，未创建账号。\n");
                return ExitCode::OK;
            }
            $user = new User();
            $user->username = $username;
            $user->email = $email;
            $user->status = User::STATUS_ACTIVE;
            $user->setPassword($password);
            $user->generateAuthKey();
            unset($password, $confirm);
            // 仅 INSERT；由唯一索引处理并发冲突，不使用更新或覆盖逻辑。
            if (!$user->save()) {
                $this->stderr("创建失败，请检查配置和字段约束。\n");
                return ExitCode::DATAERR;
            }
            $this->stdout("管理员创建成功。请登录后台并修改初始密码。\n");
            return ExitCode::OK;
        } catch (\Throwable $exception) {
            // 不输出数据库异常、SQL、连接配置或用户输入。
            $this->stderr("初始化未完成。请检查数据库服务、已有表、唯一约束及终端是否支持隐藏输入。不要重复运行迁移。\n");
            return ExitCode::UNSPECIFIED_ERROR;
        }
    }

    /** 使用 Windows 控制台隐藏读取；失败时终止，绝不降级为明文输入。 */
    private function readHiddenPassword($prompt)
    {
        $this->stdout($prompt);
        $script = <<<'PS'
$ErrorActionPreference = 'Stop'
$passwordText = [System.Text.StringBuilder]::new()
while ($true) {
    $key = [Console]::ReadKey($true)
    if ($key.Key -eq [ConsoleKey]::Enter) { break }
    if ($key.Key -eq [ConsoleKey]::Escape) { exit 1 }
    if ($key.Key -eq [ConsoleKey]::Backspace) {
        if ($passwordText.Length -gt 0) { $passwordText.Length-- }
    } elseif (-not [char]::IsControl($key.KeyChar)) {
        [void]$passwordText.Append($key.KeyChar)
    }
}
[Console]::Out.Write([Convert]::ToBase64String([Text.Encoding]::UTF8.GetBytes($passwordText.ToString())))
PS;
        $process = proc_open(
            ['powershell.exe', '-NoLogo', '-NoProfile', '-NonInteractive', '-Command', $script],
            [0 => STDIN, 1 => ['pipe', 'w'], 2 => ['pipe', 'w']],
            $pipes
        );
        if (!is_resource($process)) {
            throw new \RuntimeException('Hidden input unavailable');
        }
        $encoded = stream_get_contents($pipes[1]);
        fclose($pipes[1]);
        stream_get_contents($pipes[2]);
        fclose($pipes[2]);
        $exitCode = proc_close($process);
        $this->stdout("\n");
        $password = base64_decode($encoded, true);
        if ($exitCode !== 0 || $password === false) {
            throw new \RuntimeException('Hidden input failed');
        }
        return $password;
    }
}
