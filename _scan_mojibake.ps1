$targets = @('app\views', 'public\assets')
$files = Get-ChildItem -Path $targets -Recurse -Include *.php, *.js -File -ErrorAction SilentlyContinue
$hits = @()
$cps = @(0x00E2, 0x00C2, 0x201A,  ​0x00A6,  ​0x20AC,  ​0x2013,​ 0x2014,​ 0x2018,​ 0x2019,​ 0x201C,​ 0x201D,​ 0x2026)
foreach ($f in $files) {
  $t = [System.IO.File]::ReadAllText($f.FullName, [System.Text.Encoding]::UTF8)
  $hit = $null
  foreach ($cp in $cps) {
    if ($t.Contains([string][char]$cp)) { $hit = ('U+' + $cp.ToString('X4')); break }
  }
  if ($hit) { $hits += ($f.FullName + ' => ' + $hit) }
}
if ($hits.Count -gt 0) {
  Write-Host 'Files with mojibake markers:'
  $hits
} else {
  Write-Host ('Clean: no suspicious code points in ' + $files.Count + ' php/js files under app/views + public/assets')
}