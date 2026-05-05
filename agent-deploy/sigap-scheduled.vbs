' SIGAP - Silent Launcher (Scheduled Mode)
' Berfungsi untuk menjalankan agent PowerShell di latar belakang tanpa jendela.

Dim objShell
Set objShell = CreateObject("Wscript.Shell")
objShell.Run "powershell.exe -ExecutionPolicy Bypass -WindowStyle Hidden -File """ & Replace(WScript.ScriptFullName, WScript.ScriptName, "") & "sigap-agent.ps1"" -Mode scheduled", 0, False
Set objShell = Nothing
