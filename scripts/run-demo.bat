@echo off
setlocal
cd /d "%~dp0.."
title BookBinder - local demo

rem ---------------------------------------------------------------
rem  Starts BookBinder on http://localhost:8000 with a local SQLite
rem  database (var\demo.db) filled with the demo data in demo\.
rem
rem    scripts\run-demo.bat          start the site
rem    scripts\run-demo.bat reset    reload a fresh copy of the demo data
rem
rem  Requirements: PHP 8.1+ (on PATH or in C:\php) and the project's
rem  dependencies (vendor\, installed with "composer install").
rem ---------------------------------------------------------------

set "PHP_EXE=php"
where php >nul 2>nul
if errorlevel 1 (
    if exist "C:\php\php.exe" (
        set "PHP_EXE=C:\php\php.exe"
    ) else (
        echo PHP 8.1 or newer is required but was not found.
        echo Install PHP and add it to PATH, or place it in C:\php.
        pause
        exit /b 1
    )
)

rem Load the SQLite driver if php.ini does not enable it already
set "PHP_ARGS="
"%PHP_EXE%" -r "exit(extension_loaded('pdo_sqlite') ? 0 : 1);"
if errorlevel 1 (
    for /f "delims=" %%D in ('"%PHP_EXE%" -r "echo dirname(PHP_BINARY);"') do set "PHP_HOME=%%D"
    call set PHP_ARGS=-d extension_dir="%%PHP_HOME%%\ext" -d extension=pdo_sqlite
)
set PHP="%PHP_EXE%" %PHP_ARGS%

if not exist "vendor\autoload.php" (
    echo Dependencies are missing. Run "composer install" in the project folder first.
    pause
    exit /b 1
)

if /i "%~1"=="reset" (
    if exist "var\demo.db" del /q "var\demo.db"
)

if not exist "var\demo.db" (
    echo Creating the demo database...
    if not exist "var" mkdir "var"
    %PHP% bin\console cache:clear --no-warmup -q
    %PHP% bin\console doctrine:schema:create -q
    if errorlevel 1 goto :error
    %PHP% -r "$db = new PDO('sqlite:var/demo.db'); $db->exec(file_get_contents('demo/demo-data.sql')); echo 'Demo data loaded: ' . $db->query('SELECT COUNT(*) FROM book')->fetchColumn() . ' books' . PHP_EOL;"
    if errorlevel 1 goto :error
)

echo.
echo   BookBinder is running at   http://localhost:8000
echo   Log in with                test@test.com  /  test1234
echo.
echo   Keep this window open while you use the site.
echo   Close it (or press Ctrl+C) to stop the website.
echo.
start "" "http://localhost:8000/login"
%PHP% -S localhost:8000 -t public
exit /b 0

:error
echo.
echo Something went wrong while creating the demo database (see the messages above).
pause
exit /b 1
