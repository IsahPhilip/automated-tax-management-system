param(
    [string]$Target = '/var/www/automated-tax-management-system',
    [string]$Host = '',
    [string]$User = 'root'
)

$ErrorActionPreference = 'Stop'
$AppRoot = Split-Path -Parent $PSScriptRoot
Set-Location $AppRoot

if (-not $Host) {
    throw 'DEPLOY_HOST is not set. Provide -Host or set the environment variable before deployment.'
}

composer install --no-interaction --prefer-dist --no-progress --no-scripts --no-dev
php -d memory_limit=-1 vendor\bin\phpunit --configuration phpunit.xml --colors=never

$exclude = @('.git', '.github', '.env', '.env.example', 'vendor', 'storage/logs', 'storage/uploads')
$source = "$AppRoot\*"
$destination = "$User@$Host:$Target"

if (-not (Get-Command rsync -ErrorAction SilentlyContinue)) {
    throw 'rsync is required for deployment. Install it in the PATH and retry.'
}

& rsync -az --delete --exclude '.git' --exclude '.github' --exclude '.env' --exclude '.env.example' --exclude 'vendor/' --exclude 'storage/logs/' --exclude 'storage/uploads/' "$AppRoot/" "$destination/"

Write-Host "Deployment completed to $destination"
