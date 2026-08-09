---
name: Arabic Batch Files Support
description: "Rules for handling .bat files that contain Arabic text to prevent cmd.exe parsing errors on Windows."
---

# Arabic Batch Files Rules for Windows CMD

When creating, editing, or managing `.bat` files that contain Arabic text in a Windows environment, you **MUST** follow these strict rules to prevent `cmd.exe` from experiencing syntax errors and offset parsing bugs:

1. **File Encoding**: The file MUST be saved in `Windows-1256` (ANSI Arabic) encoding. Do NOT save it as UTF-8 (neither with nor without BOM).
2. **Line Endings**: The file MUST strictly use Windows `CRLF` (`\r\n`) line endings. Unix `LF` (`\n`) will cause parsing to fail randomly.
3. **Code Page Command**: At the top of the file, you MUST use `chcp 1256 >nul` to ensure the console renders the ANSI Arabic characters properly. Do NOT use `chcp 65001`.
4. **No Blocks with Arabic**: Avoid using Arabic text inside block statements like `if ( ... )` or `for ( ... )`. If Arabic text must be printed conditionally, use `goto` labels to jump outside the block and execute the `echo` commands. `cmd.exe` calculates byte offsets for block closures, and multi-byte or codepage issues can cause it to miscalculate and crash.

## Example of Safe Arabic Batch Script:
```bat
@echo off
chcp 1256 >nul

echo مرحباً بك في النظام

if "%1"=="install" goto do_install
goto end

:do_install
echo جاري التثبيت...
goto end

:end
pause
```

## How to Save Properly
Since standard `write_to_file` tools often output UTF-8 with LF line endings, you should write the initial script using `write_to_file` (with English or minimal Arabic), then explicitly run a PowerShell command to correct the encoding and line endings:
```powershell
$file = 'script.bat'
$text = [System.IO.File]::ReadAllText($file, [System.Text.Encoding]::UTF8)
$text = $text -replace '(?<!\r)\n', "`r`n"
[System.IO.File]::WriteAllText($file, $text, [System.Text.Encoding]::GetEncoding(1256))
```
