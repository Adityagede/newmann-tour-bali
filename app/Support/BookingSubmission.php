<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

final class BookingSubmission
{
    public function key(Request $request, string $flow): string
    {
        // Legacy open forms can still submit; new forms use a distinct UUID.
        $token = $request->input('submission_token') ?: $this->fingerprint($request->except(['_token', 'submission_token']));

        return hash('sha256', $flow."\0".$request->session()->getId()."\0".$token);
    }

    public function fingerprint(array $data): string
    {
        unset($data['submission_token']);
        $sort = function (array $value) use (&$sort): array {
            ksort($value);
            foreach ($value as $key => $item) {
                if (is_array($item)) {
                    $value[$key] = $sort($item);
                }
            }

            return $value;
        };

        return hash('sha256', json_encode($sort($data), JSON_THROW_ON_ERROR));
    }

    public function assertSamePayload(?string $storedHash, string $hash): void
    {
        if (! is_string($storedHash) || ! hash_equals($storedHash, $hash)) {
            throw ValidationException::withMessages(['booking' => __('security.changed_submission')]);
        }
    }

    public function rememberCustomTrip(Request $request, string $reference): void
    {
        $references = $request->session()->get('custom_trip_success_references', []);
        $references = is_array($references) ? $references : [];
        $references = array_filter($references, fn ($expires) => is_int($expires) && $expires > now()->timestamp);
        $references[$reference] = now()->addMinutes(max(1, (int) config('newman.booking_protection.success_access_minutes', 120)))->timestamp;
        $request->session()->put('custom_trip_success_references', array_slice($references, -100, null, true));
    }

    public function ownsCustomTrip(Request $request, string $reference): bool
    {
        $references = $request->session()->get('custom_trip_success_references', []);
        $expires = is_array($references) ? ($references[$reference] ?? null) : null;

        return is_int($expires) && $expires > now()->timestamp;
    }
}
