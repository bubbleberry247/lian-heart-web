param(
    [string]$ThemeName = 'lian-heart-custom-theme-clean-family'
)

$ErrorActionPreference = 'Stop'

$builder = Join-Path $PSScriptRoot '..\deploy\build-theme-artifact.ps1'
if (-not (Test-Path $builder)) {
    throw "Build helper not found: $builder"
}

Write-Warning "package-theme.ps1 is a compatibility wrapper. Use deploy\\build-theme-artifact.ps1 directly for normal releases."

& $builder -ThemeName $ThemeName -ThemesRoot $PSScriptRoot
