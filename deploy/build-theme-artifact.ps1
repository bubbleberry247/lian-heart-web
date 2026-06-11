param(
    [string]$ThemeName = 'lian-heart-custom-theme-clean-family',
    [string]$ThemesRoot = (Join-Path $PSScriptRoot '..\wordpress-theme'),
    [string]$OutDir = (Join-Path $PSScriptRoot 'artifacts')
)

$ErrorActionPreference = 'Stop'
$themesRootResolved = (Resolve-Path $ThemesRoot).Path
$themeRoot = Join-Path $themesRootResolved $ThemeName
if (-not (Test-Path $themeRoot)) {
    throw "Theme folder not found: $themeRoot"
}
if (-not (Test-Path (Join-Path $themeRoot 'style.css'))) {
    throw "style.css not found: $themeRoot"
}

New-Item -ItemType Directory -Force -Path $OutDir | Out-Null
$zipPath = Join-Path $OutDir ($ThemeName + '.zip')
if (Test-Path $zipPath) {
    Remove-Item $zipPath -Force
}

$stageRoot = Join-Path ([System.IO.Path]::GetTempPath()) ('lh-theme-stage-' + [Guid]::NewGuid().ToString('N'))
$stageTheme = Join-Path $stageRoot $ThemeName
New-Item -ItemType Directory -Force -Path $stageTheme | Out-Null
Copy-Item -Path $themeRoot -Destination $stageRoot -Recurse -Force

Push-Location $stageRoot
try {
    & tar.exe -a -cf $zipPath $ThemeName
    if ($LASTEXITCODE -ne 0) {
        throw "tar.exe failed with exit code $LASTEXITCODE"
    }
}
finally {
    Pop-Location
    Remove-Item $stageRoot -Recurse -Force -ErrorAction SilentlyContinue
}

Add-Type -AssemblyName System.IO.Compression.FileSystem
$archive = [System.IO.Compression.ZipFile]::OpenRead($zipPath)
try {
    $expected = $ThemeName + '/style.css'
    $entries = $archive.Entries | ForEach-Object { $_.FullName.Replace('\', '/') }
    if (-not ($entries | Where-Object { $_ -eq $expected })) {
        throw "Artifact invalid. Expected root entry missing: $expected"
    }
}
finally {
    $archive.Dispose()
}

Write-Host "Created artifact: $zipPath"
