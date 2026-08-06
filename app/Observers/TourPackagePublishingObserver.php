<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\TourPackage;
use App\Services\TourPublishingReadinessService;

final class TourPackagePublishingObserver
{
    public function __construct(
        private readonly TourPublishingReadinessService
            $readinessService
    ) {
    }

    public function saving(
        TourPackage $tourPackage
    ): void {
        /*
         * Product berstatus Draft boleh disimpan
         * walaupun belum lengkap.
         */
        if (
            (string) $tourPackage->status
            !== 'active'
        ) {
            return;
        }

        /*
         * Pemeriksaan menggunakan atribut terbaru
         * yang sedang disimpan, tetapi relation
         * options dan roadmap dibaca ulang.
         */
        $candidate = clone $tourPackage;

        $candidate->unsetRelations();

        $this->readinessService
            ->assertTourCanPublish(
                $candidate
            );
    }
}