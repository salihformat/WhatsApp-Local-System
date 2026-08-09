$ErrorActionPreference = "Stop"

$projectDir = "C:\xampp\htdocs\whatsapp-local-system"
$logFile = "$projectDir\storage\logs\auto-update.log"
$dateStr = Get-Date -Format "yyyy-MM-dd HH:mm:ss"

# Check for Git
$gitPath = "C:\Program Files\Git\cmd\git.exe"
if (!(Test-Path $gitPath)) {
    Add-Content -Path $logFile -Value "[$dateStr] ⚠️ Git is not installed at $gitPath. Auto-update failed."
    exit 1
}

try {
    cd $projectDir

    # Fetch to see if there are updates
    & $gitPath fetch origin main 2>&1 | Out-Null
    
    $local = & $gitPath rev-parse HEAD
    $remote = & $gitPath rev-parse origin/main
    
    if ($local -eq $remote) {
        # No updates
        exit 0
    }

    Add-Content -Path $logFile -Value "[$dateStr] 🔄 Updates found! Pulling from GitHub..."

    # Pull changes
    $pullResult = & $gitPath pull origin main 2>&1
    Add-Content -Path $logFile -Value "[$dateStr] $pullResult"

    # Run Composer (if exists)
    $composerBat = "C:\ProgramData\ComposerSetup\bin\composer.bat"
    if (Test-Path $composerBat) {
        & $composerBat install --no-interaction --prefer-dist --optimize-autoloader 2>&1 | Out-Null
    }

    # Run Migrations (using absolute PHP path from XAMPP to avoid PATH issues)
    $phpExe = "C:\xampp\php\php.exe"
    if (Test-Path $phpExe) {
        & $phpExe artisan migrate --force 2>&1 | Out-Null
        & $phpExe artisan optimize:clear 2>&1 | Out-Null
    }

    Add-Content -Path $logFile -Value "[$dateStr] ✅ Auto-update completed successfully!"
    
} catch {
    $err = $_.Exception.Message
    Add-Content -Path $logFile -Value "[$dateStr] ❌ Error during auto-update: $err"
    # Send notification via artisan command
    $phpExe = "C:\xampp\php\php.exe"
    if (Test-Path $phpExe) {
        & $phpExe artisan app:notify-owner "⚠️ فشل التحديث التلقائي للنظام من GitHub.`nالسبب: $err" 2>&1 | Out-Null
    }
}
