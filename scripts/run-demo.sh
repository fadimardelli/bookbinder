#!/usr/bin/env bash
# Starts BookBinder on http://localhost:8000 with a local SQLite database
# (var/demo.db) filled with the demo data in demo/.
#
#   ./scripts/run-demo.sh          start the site
#   ./scripts/run-demo.sh reset    reload a fresh copy of the demo data
set -euo pipefail
cd "$(dirname "$0")/.."

PHP="${PHP:-php}"
command -v "$PHP" >/dev/null 2>&1 || { echo "PHP 8.1 or newer is required."; exit 1; }
"$PHP" -r 'exit(extension_loaded("pdo_sqlite") ? 0 : 1);' || { echo "Enable the pdo_sqlite PHP extension."; exit 1; }

if [ ! -f vendor/autoload.php ]; then
  command -v composer >/dev/null 2>&1 || { echo "Run 'composer install' first."; exit 1; }
  composer install --no-interaction
fi

if [ "${1:-}" = "reset" ]; then rm -f var/demo.db; fi

if [ ! -f var/demo.db ]; then
  echo "Creating the demo database..."
  mkdir -p var
  "$PHP" bin/console cache:clear --no-warmup -q
  "$PHP" bin/console doctrine:schema:create -q
  "$PHP" -r '$db = new PDO("sqlite:var/demo.db"); $db->exec(file_get_contents("demo/demo-data.sql")); echo "Demo data loaded: " . $db->query("SELECT COUNT(*) FROM book")->fetchColumn() . " books" . PHP_EOL;'
fi

echo
echo "  BookBinder is running at   http://localhost:8000"
echo "  Log in with                test@test.com  /  test1234"
echo "  Press Ctrl+C to stop."
echo
"$PHP" -S localhost:8000 -t public
