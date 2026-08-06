<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\TourOption;
use App\Services\TourPublishingReadinessService;

final class TourOptionPublishingObserver
{
    public function __construct(
        private readonly TourPublishingReadinessService
            $readinessService
    ) {
    }

    public function saving(
        TourOption $tourOption
    ): void {
        /*
         * Draft dan inactive boleh disimpan tanpa
         * melewati publishing readiness.
         */
        if (
            (string) $tourOption->status
            !== 'active'
        ) {
            return;
        }

        /*
         * Gunakan clone agar pemeriksaan tidak
         * mengubah relation pada model asli.
         *
         * Relation dilepas supaya data prices,
         * items, dan schedules dibaca ulang
         * dari database.
         */
        $candidate = clone $tourOption;

        $candidate->unsetRelations();

        $this->readinessService
            ->assertOptionCanActivate(
                $candidate
            );
    }
}