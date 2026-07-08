@echo off
rem Builds Tailwind CSS without Node.js via the standalone CLI in ..\tools
"%~dp0..\tools\tailwindcss.exe" -i "%~dp0resources\css\app.css" -o "%~dp0public\css\app.css" --minify %*
