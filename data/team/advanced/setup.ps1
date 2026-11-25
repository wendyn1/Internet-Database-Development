<#
2026世界杯球队信息系统（Yii2 advanced）本地初始化脚本
适用：Windows + PowerShell 5.1 及以上；XAMPP 或独立 PHP 8 / MySQL(MariaDB)。

脚本完成：
1. 检查 PHP 与 mysql 客户端路径；
2. 本地配置缺失时执行 php init（Development）；
3. 创建目标数据库（若不存在）；
4. 执行 Yii 初始迁移创建 user 表；
5. 导入 data/install.sql 的 11 张业务表与样本数据。

不包含：管理员账号创建（请运行 php yii admin/init）、Web 服务器配置。
注意：若数据库中已存在 team 表，脚本会停止，避免重复导入或覆盖数据。
#>
[CmdletBinding()]
param(
    [string]$PhpPath = 'D:\xampp\php\php.exe',
    [string]$MysqlPath = 'D:\xampp\mysql\bin\mysql.exe',
    [string]$DbHost = '127.0.0.1',
    [int]$DbPort = 3306,
    [string]$DbName = 'worldcup',
    [string]$DbUser = 'root',
    [string]$DbPassword = ''
)

$ErrorActionPreference = 'Stop'
$root = Split-Path -Parent $MyInvocation.MyCommand.Path
Set-Location -LiteralPath $root

if (-not (Test-Path -LiteralPath $PhpPath)) {
    throw "未找到 PHP 可执行文件：$PhpPath"
}
if (-not (Test-Path -LiteralPath $MysqlPath)) {
    throw "未找到 mysql 客户端：$MysqlPath"
}
if ((& $PhpPath -r "echo extension_loaded('intl') ? '1' : '0';") -ne '1') {
    Write-Warning '当前 PHP 未启用 intl，Web 页面会返回 500。启动内置服务器时请加 -d extension=intl，或在 php.ini 启用 intl。'
}
$installSql = Join-Path (Split-Path -Parent (Split-Path -Parent $root)) 'install.sql'
if (-not (Test-Path -LiteralPath $installSql)) {
    throw "未找到安装脚本：$installSql"
}

if (-not (Test-Path -LiteralPath (Join-Path $root 'common\config\main-local.php'))) {
    Write-Host '未发现本地配置，执行 php init --env=Development ...'
    & $PhpPath init --env=Development --overwrite=All
    if ($LASTEXITCODE -ne 0) {
        throw 'php init 执行失败。'
    }
}

# 将数据库参数写入本地配置（main-local.php 已被 .gitignore 忽略，不纳入提交）
$configPath = Join-Path $root 'common\config\main-local.php'
$configText = Get-Content -Raw -Encoding UTF8 $configPath
$configText = [regex]::Replace($configText, "'dsn'\s*=>\s*'[^']*'",
    "'dsn' => 'mysql:host=$DbHost;port=$DbPort;dbname=$DbName'")
$configText = [regex]::Replace($configText, "'username'\s*=>\s*'[^']*'",
    "'username' => '$DbUser'")
$configText = [regex]::Replace($configText, "'password'\s*=>\s*'[^']*'",
    "'password' => '$DbPassword'")
$utf8NoBom = New-Object System.Text.UTF8Encoding($false)
[IO.File]::WriteAllText($configPath, $configText, $utf8NoBom)

if ($DbPassword -ne '') {
    $env:MYSQL_PWD = $DbPassword
}
try {
    $schema = & $MysqlPath -h $DbHost -P $DbPort -u $DbUser -N -e "SELECT SCHEMA_NAME FROM information_schema.SCHEMATA WHERE SCHEMA_NAME='$DbName';"
    if (-not $schema) {
        Write-Host "创建数据库 $DbName ..."
        & $MysqlPath -h $DbHost -P $DbPort -u $DbUser -e "CREATE DATABASE ``$DbName`` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
    }

    $teamTable = & $MysqlPath -h $DbHost -P $DbPort -u $DbUser -N -e "SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA='$DbName' AND TABLE_NAME='team';"
    if ($teamTable.Trim() -ne '0') {
        throw "数据库 $DbName 已存在 team 表，为避免覆盖样本数据，脚本停止。如需重装，请先人工备份并清空该库。"
    }

    Write-Host '执行 Yii 初始迁移（user 表）...'
    & $PhpPath yii migrate --interactive=0
    if ($LASTEXITCODE -ne 0) {
        throw 'Yii 迁移失败，请检查 common/config/main-local.php 的数据库连接。'
    }

    Write-Host '导入 11 张业务表与样本数据 ...'
    $sourcePath = $installSql.Replace('\', '/')
    & $MysqlPath -h $DbHost -P $DbPort -u $DbUser --default-character-set=utf8mb4 $DbName -e "source $sourcePath"
    if ($LASTEXITCODE -ne 0) {
        throw '导入 data/install.sql 失败。'
    }
} finally {
    Remove-Item Env:\MYSQL_PWD -ErrorAction SilentlyContinue
}

Write-Host ''
New-Item -ItemType Directory -Force -Path (Join-Path $root 'frontend\runtime\sessions'), (Join-Path $root 'backend\runtime\sessions') | Out-Null
Write-Host '初始化完成。请继续：'
Write-Host '1. 检查 common/config/main-local.php 的 dsn 是否与本次参数一致；'
Write-Host "2. 执行：& '$PhpPath' yii admin/init  （交互创建管理员）；"
Write-Host "3. 前台：& '$PhpPath' -d extension=intl -d session.save_path=frontend\runtime\sessions -d sys_temp_dir=frontend\runtime -S 127.0.0.1:8080 -t frontend\web"
Write-Host "4. 后台：& '$PhpPath' -d extension=intl -d session.save_path=backend\runtime\sessions -d sys_temp_dir=backend\runtime -S 127.0.0.1:8081 -t backend\web"
