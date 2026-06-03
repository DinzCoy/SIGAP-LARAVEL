
Dim objShell
Set objShell = CreateObject("Wscript.Shell")
objShell.Run "powershell.exe -ExecutionPolicy Bypass -WindowStyle Hidden -File """ & Replace(WScript.ScriptFullName, WScript.ScriptName, "") & "sigap-agent.ps1"" -Mode scheduled", 0, False
Set objShell = Nothing
