@echo off
start "Smart-Recruit AI" /min "%~dp0start-ai.cmd"
timeout /t 2 /nobreak >nul
start "Smart-Recruit Laravel" /min "%~dp0start-web.cmd"
