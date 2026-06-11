$ErrorActionPreference = 'Stop'

$chromePath = 'C:\Program Files\Google\Chrome\Application\chrome.exe'
$userDataDir = Join-Path $env:LOCALAPPDATA 'agent-browser\chrome-profile'
$port = 9222

if (-not (Test-Path $chromePath)) {
  throw "Chrome not found: $chromePath"
}

New-Item -ItemType Directory -Force -Path $userDataDir | Out-Null

$args = @(
  "--remote-debugging-port=$port"
  "--user-data-dir=$userDataDir"
)

Start-Process -FilePath $chromePath -ArgumentList $args | Out-Null
Write-Output "STARTED: $chromePath $($args -join ' ')"
