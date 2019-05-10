@echo off

rem -------------------------------------------------------------
rem  Yii command line bootstrap script for Windows.
rem -------------------------------------------------------------

@setlocal

set YII_PATH=%~dp0

if "%PHP_COMMAND%" == "" set PHP_COMMAND=E:\wamp64\bin\php\php5.6.25\php.exe

"%PHP_COMMAND%" "%YII_PATH%yii" %*

@endlocal
