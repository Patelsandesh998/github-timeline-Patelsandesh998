
PHP_PATH=$(which php)
CRON_JOB="*/5 * * * * $PHP_PATH $(cd "$(dirname "$0")" && pwd)/cron.php >/dev/null 2>&1"

(crontab -l 2>/dev/null | grep -v 'cron.php'; echo "$CRON_JOB") | crontab -
echo "CRON job set to run cron.php every 5 minutes."
