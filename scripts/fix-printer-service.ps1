# Ensure Admin
if (-not ([Security.Principal.WindowsPrincipal][Security.Principal.WindowsIdentity]::GetCurrent()).IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)) {
    Write-Warning "Please run this script as Administrator."
    exit
}

$serviceName = "WhatsAppLocalApache"

# Check if service exists
$service = Get-Service -Name $serviceName -ErrorAction SilentlyContinue
if (-not $service) {
    Write-Error "Service $serviceName not found."
    exit
}

Write-Host "=======================================================" -ForegroundColor Cyan
Write-Host " Apache Service Printer Fix Tool (Log On Account)" -ForegroundColor Cyan
Write-Host "=======================================================" -ForegroundColor Cyan
Write-Host "You will be prompted to enter the Windows account username and password." -ForegroundColor Yellow
Write-Host "Please enter the credentials of the account where the printer is installed." -ForegroundColor Yellow
Write-Host "Example: .\Administrator  (or your computer's username)" -ForegroundColor Yellow
Write-Host ""

$cred = Get-Credential -Message "Enter the Windows Username and Password (e.g. .\Administrator)"

if ($cred) {
    $username = $cred.UserName
    $password = $cred.GetNetworkCredential().Password

    Write-Host "Applying credentials ($username) to system services and tasks..." -ForegroundColor Cyan

    # 1. Update Apache Service
    $service = Get-Service -Name $serviceName -ErrorAction SilentlyContinue
    if ($service) {
        Write-Host "Changing service logon for $serviceName ..."
        $wmiService = Get-WmiObject -Class Win32_Service -Filter "Name='$serviceName'"
        if ($wmiService) {
            $result = $wmiService.Change($null,$null,$null,$null,$null,$null,$username,$password)
            if ($result.ReturnValue -eq 0 -or $result.ReturnValue -eq 22) {
                Write-Host "Service account changed successfully! Restarting service..." -ForegroundColor Green
                Restart-Service -Name $serviceName -Force
            } else {
                Write-Error "Error changing service account. Error code: $($result.ReturnValue)"
            }
        }
    } else {
        Write-Host "Service $serviceName not found. Skipping." -ForegroundColor DarkGray
    }

    # 2. Update Scheduled Tasks
    $tasksToUpdate = @("WhatsAppLocalSystem-QueueWorker", "WhatsAppLocalSystem-Scheduler")
    foreach ($taskName in $tasksToUpdate) {
        $task = Get-ScheduledTask -TaskName $taskName -ErrorAction SilentlyContinue
        if ($task) {
            Write-Host "Changing logon account for Scheduled Task: $taskName ..."
            
            # Use schtasks.exe to change the running user and password
            $schtasksArgs = "/change /tn `"$taskName`" /ru `"$username`" /rp `"$password`""
            $process = Start-Process -FilePath "schtasks.exe" -ArgumentList $schtasksArgs -NoNewWindow -Wait -PassThru
            
            if ($process.ExitCode -eq 0) {
                Write-Host "Scheduled Task $taskName updated successfully!" -ForegroundColor Green
                # Restart the task so it runs under the new user
                Stop-ScheduledTask -TaskName $taskName -ErrorAction SilentlyContinue
                Start-Sleep -Seconds 1
                Start-ScheduledTask -TaskName $taskName -ErrorAction SilentlyContinue
            } else {
                Write-Error "Failed to update Scheduled Task $taskName. Please check the credentials."
            }
        } else {
            Write-Host "Scheduled Task $taskName not found. Skipping." -ForegroundColor DarkGray
        }
    }
    
    Write-Host "Done! The background services should now be able to see the printer." -ForegroundColor Cyan

} else {
    Write-Warning "Operation cancelled."
}

Write-Host "Press any key to exit..."
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")
