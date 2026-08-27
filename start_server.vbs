Set WshShell = CreateObject("WScript.Shell")
WshShell.Run "cmd /c cd /d C:\laragon\www\sekawan-media\fleet-booking && php spark serve --port 8080", 0, False
