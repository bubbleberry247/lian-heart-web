param(
    [Parameter(Mandatory = $true)]
    [string]$FtpHost,

    [Parameter(Mandatory = $true)]
    [string]$User,

    [Parameter(Mandatory = $true)]
    [string]$Password,

    [Parameter(Mandatory = $true)]
    [string]$LocalThemePath,

    [Parameter(Mandatory = $true)]
    [string]$RemoteThemesPath,

    [string]$DeployFolderName = 'lian-heart-custom-theme-clean-family-next'
)

$ErrorActionPreference = 'Stop'

$lftpExe = if (Get-Command lftp -ErrorAction SilentlyContinue) {
    (Get-Command lftp -ErrorAction SilentlyContinue).Source
} elseif (Test-Path 'C:\cygwin64\usr\bin\lftp.exe') {
    'C:\cygwin64\usr\bin\lftp.exe'
} else {
    $null
}
if (-not $lftpExe) {
    throw 'lftp is not installed or not on PATH.'
}

$bash = 'C:\cygwin64\bin\bash.exe'
if (-not (Test-Path $bash)) {
    throw 'Cygwin bash was not found at C:\cygwin64\bin\bash.exe'
}

if (-not (Test-Path $LocalThemePath)) {
    throw "LocalThemePath not found: $LocalThemePath"
}

$resolvedLocal = (Resolve-Path $LocalThemePath).Path
$cygLocal = '/cygdrive/' + $resolvedLocal.Substring(0,1).ToLower() + $resolvedLocal.Substring(2).Replace('\\','/').Replace(' ','\\ ')
$remoteTarget = ($RemoteThemesPath.TrimEnd('/')) + '/' + $DeployFolderName

$styleCss = Join-Path $resolvedLocal 'style.css'
if (-not (Test-Path $styleCss)) {
    throw "style.css not found under $resolvedLocal"
}

$cygLftp = '/cygdrive/c/cygwin64/usr/bin/lftp.exe'
$cmd = @"
set +H
"$cygLftp" -u '$User','$Password' ftp://$FtpHost -e 'set ssl:verify-certificate no; set ftp:ssl-force true; set ftp:ssl-protect-data true; mkdir $remoteTarget; mirror -R --verbose --delete $cygLocal $remoteTarget; ls $remoteTarget; bye'
"@

& $bash -lc $cmd
if ($LASTEXITCODE -ne 0) {
    throw "SFTP/FTP deploy failed with exit code $LASTEXITCODE"
}

Write-Host "Deployed to $remoteTarget"
