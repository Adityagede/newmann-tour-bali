<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Support\Str;
use Symfony\Component\Process\Process;
use Tests\TestCase;

final class BookingConcurrencyTest extends TestCase
{
    public function test_simultaneous_custom_requests_save_one_order_and_one_database_job(): void
    {
        $this->assertConcurrentRequests('');
    }

    public function test_tour_request_recovers_when_a_competing_submit_commits_after_initial_lookup(): void
    {
        $this->assertConcurrentRequests('-tour');
    }

    private function assertConcurrentRequests(string $suffix): void
    {
        $directory = sys_get_temp_dir().'/newman-booking-race-'.Str::uuid();
        mkdir($directory);
        touch($directory.'/race.sqlite');
        try {
            $this->worker($directory, 'init'.$suffix)->mustRun();
            $first = $this->worker($directory, 'first'.$suffix);
            $second = $this->worker($directory, 'second'.$suffix);
            $first->start();
            $second->start();
            $first->wait();
            $second->wait();
            $this->assertTrue($first->isSuccessful(), $first->getErrorOutput());
            $this->assertTrue($second->isSuccessful(), $second->getErrorOutput());
            $firstResponse = json_decode($first->getOutput(), true, flags: JSON_THROW_ON_ERROR);
            $secondResponse = json_decode($second->getOutput(), true, flags: JSON_THROW_ON_ERROR);
            $this->assertSame(302, $firstResponse['status']);
            $this->assertSame(302, $secondResponse['status']);
            $this->assertSame($firstResponse['location'], $secondResponse['location']);
            $inspector = $this->worker($directory, 'inspect'.$suffix);
            $inspector->mustRun();
            $this->assertSame(['orders' => 1, 'jobs' => 1], json_decode($inspector->getOutput(), true, flags: JSON_THROW_ON_ERROR));
        } finally {
            // Only the explicit files created by this isolated test are removed.
            foreach (['race.sqlite', 'race.sqlite-wal', 'race.sqlite-shm', 'race.sqlite-journal', 'first.ready', 'second.ready', 'first.finished', 'second.finished'] as $file) {
                if (is_file($directory.'/'.$file)) {
                    unlink($directory.'/'.$file);
                }
            }
            rmdir($directory);
        }
    }

    private function worker(string $directory, string $mode): Process
    {
        $arguments = [PHP_BINARY];
        if (PHP_OS_FAMILY === 'Windows') {
            $arguments = [...$arguments, '-d', 'extension='.dirname(PHP_BINARY).'/ext/php_pdo_sqlite.dll'];
        }

        return new Process([...$arguments, base_path('tests/Support/booking-race-worker.php'), $directory, $mode], base_path(), timeout: 30);
    }
}
