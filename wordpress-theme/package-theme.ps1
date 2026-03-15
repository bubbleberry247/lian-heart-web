param(
    [string]$ThemeName = 'lian-heart-custom-theme'
)

$ErrorActionPreference = 'Stop'

$themeRoot = Join-Path $PSScriptRoot $ThemeName
$zipPath = Join-Path $PSScriptRoot ($ThemeName + '.zip')

if (-not (Test-Path $themeRoot)) {
    throw "Theme directory not found: $themeRoot"
}

if (Test-Path $zipPath) {
    Remove-Item $zipPath -Force
}

Compress-Archive -Path (Join-Path $themeRoot '*') -DestinationPath $zipPath -Force
Write-Host "Created: $zipPath"
