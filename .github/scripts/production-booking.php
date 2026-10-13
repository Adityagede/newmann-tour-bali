<?php

declare(strict_types=1);

// Deployment-only helper. It lives outside public_html and never prints credentials.
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

const BOOKING_MIGRATION = '2026_10_13_100000_add_booking_submission_protection';
const BOOKING_COLUMNS = ['submission_key', 'submission_hash', 'admin_notification_pending', 'admin_notification_queued_at', 'admin_notified_at'];

function requireDeploymentCondition(bool $condition, string $message): void
{
    if (! $condition) {
        throw new RuntimeException('DEPLOY_CHECK: '.$message);
    }
}

try {
    $basePath = $argv[1] ?? '';
    $command = $argv[2] ?? '';
    requireDeploymentCondition(is_file($basePath.'/artisan'), 'Application directory is missing.');
    chdir($basePath);
    require $basePath.'/vendor/autoload.php';
    $app = require $basePath.'/bootstrap/app.php';
    $app->make(Kernel::class)->bootstrap();

    if ($command === 'preflight') {
        requireDeploymentCondition(version_compare(PHP_VERSION, '8.3', '>='), 'PHP 8.3 or newer is required.');
        requireDeploymentCondition(DB::connection()->getDriverName() === 'mysql', 'This backup procedure requires MySQL.');
        requireDeploymentCondition(function_exists('pcntl_alarm'), 'Bounded queue processing must be available.');
        requireDeploymentCondition(! $app->isDownForMaintenance(), 'Production is already in maintenance mode; leave its existing state untouched.');
        foreach (['bookings', 'tour_booking_requests', 'jobs', 'failed_jobs', 'migrations'] as $table) {
            requireDeploymentCondition(Schema::hasTable($table), 'Required table is missing: '.$table);
        }
        $cacheDriver = config('cache.stores.'.config('cache.default').'.driver');
        requireDeploymentCondition(in_array($cacheDriver, ['file', 'database', 'redis', 'memcached'], true), 'A persistent cache with atomic locks is required.');
        $lock = Cache::lock('newman-deploy-preflight-'.bin2hex(random_bytes(8)), 10);
        requireDeploymentCondition($lock->get(), 'Unable to acquire an application cache lock.');
        $lock->release();
        $recipient = config('newman.booking_notification_email');
        requireDeploymentCondition(is_string($recipient) && filter_var(trim($recipient), FILTER_VALIDATE_EMAIL) !== false, 'Admin notification recipient must be configured.');
        requireDeploymentCondition(config('mail.mailers.'.config('mail.default').'.transport') === 'smtp', 'The production SMTP transport must be configured.');
        $applied = DB::table('migrations')->where('migration', BOOKING_MIGRATION)->exists();
        foreach (['bookings', 'tour_booking_requests'] as $table) {
            foreach (BOOKING_COLUMNS as $column) {
                requireDeploymentCondition(Schema::hasColumn($table, $column) === $applied, 'Booking schema and migration record disagree; inspect before deployment.');
            }
        }
        echo 'BOOKING_PREFLIGHT_OK'.PHP_EOL;
    } elseif ($command === 'prepare-backup') {
        $backupPath = $argv[3] ?? '';
        requireDeploymentCondition(is_dir($backupPath), 'Backup directory is unavailable.');
        requireDeploymentCondition($app->isDownForMaintenance(), 'Database backup requires the deployment maintenance window.');
        $db = DB::connection()->getConfig();
        requireDeploymentCondition(($db['driver'] ?? '') === 'mysql', 'MySQL connection is required.');
        $clientFile = $backupPath.'/mysql-client.cnf';
        $client = "[client]\n";
        foreach (['host' => 'host', 'port' => 'port', 'user' => 'username', 'password' => 'password', 'socket' => 'unix_socket'] as $option => $key) {
            if (isset($db[$key]) && $db[$key] !== '') {
                requireDeploymentCondition(is_scalar($db[$key]), 'Unsupported MySQL backup configuration.');
                $client .= $option.'="'.addcslashes((string) $db[$key], "\\\"\n\r").'"'."\n";
            }
        }
        umask(0077);
        requireDeploymentCondition(file_put_contents($clientFile, $client) !== false, 'Unable to prepare protected MySQL client configuration.');
        $databaseName = (string) $db['database'];
        requireDeploymentCondition($databaseName !== '' && ! str_starts_with($databaseName, '-') && ! preg_match('/[\x00\r\n]/', $databaseName), 'Unsupported MySQL database name.');
        requireDeploymentCondition(file_put_contents($backupPath.'/database-name.txt', $databaseName) !== false, 'Unable to save database name.');
        $counts = [];
        foreach (['bookings', 'tour_booking_requests'] as $table) {
            $counts[$table] = DB::table($table)->count();
        }
        requireDeploymentCondition(file_put_contents($backupPath.'/booking-counts.json', json_encode($counts, JSON_THROW_ON_ERROR)) !== false, 'Unable to save booking counts.');
        echo 'DATABASE_BACKUP_PREPARED'.PHP_EOL;
    } elseif ($command === 'normalize-url') {
        $url = config('app.url');
        if ($url === 'http:https://newmantourbali.com') {
            $envPath = $basePath.'/.env';
            $contents = file_get_contents($envPath);
            requireDeploymentCondition(is_string($contents), 'Unable to read production environment.');
            $updated = preg_replace('/^APP_URL=.*$/m', 'APP_URL=https://newmantourbali.com', $contents, -1, $replacements);
            requireDeploymentCondition($replacements === 1 && is_string($updated), 'Expected one APP_URL entry; inspect environment manually.');
            requireDeploymentCondition(file_put_contents($envPath, $updated, LOCK_EX) !== false, 'Unable to correct APP_URL.');
            echo 'APP_URL_CORRECTED'.PHP_EOL;
        } else {
            requireDeploymentCondition(is_string($url) && filter_var($url, FILTER_VALIDATE_URL) !== false, 'APP_URL must be a valid production URL.');
            echo 'APP_URL_OK'.PHP_EOL;
        }
    } elseif ($command === 'validate') {
        $backupPath = $argv[3] ?? '';
        requireDeploymentCondition(DB::table('migrations')->where('migration', BOOKING_MIGRATION)->exists(), 'Booking protection migration has not completed.');
        $counts = json_decode(file_get_contents($backupPath.'/booking-counts.json'), true, 512, JSON_THROW_ON_ERROR);
        foreach (['bookings', 'tour_booking_requests'] as $table) {
            foreach (BOOKING_COLUMNS as $column) {
                requireDeploymentCondition(Schema::hasColumn($table, $column), 'Booking protection column is missing.');
            }
            requireDeploymentCondition(DB::table($table)->count() === $counts[$table], 'Booking row count changed during deployment.');
        }
        requireDeploymentCondition(config('queue.connections.booking-notifications.driver') === 'database', 'Notification database queue is missing.');
        requireDeploymentCondition(config('app.url') === 'https://newmantourbali.com', 'Production APP_URL is unexpected.');
        echo 'BOOKING_SCHEMA_AND_EXISTING_ORDERS_OK'.PHP_EOL;
    } else {
        throw new RuntimeException('DEPLOY_CHECK: Unknown deployment command.');
    }
} catch (Throwable $exception) {
    // Framework/connection exceptions can contain private configuration; do not put them in Actions logs.
    $message = str_starts_with($exception->getMessage(), 'DEPLOY_CHECK: ')
        ? $exception->getMessage()
        : 'DEPLOY_CHECK: Application/database operation failed; inspect the private server logs.';
    fwrite(STDERR, $message.PHP_EOL);
    exit(1);
}
