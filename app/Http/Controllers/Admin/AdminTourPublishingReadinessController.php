<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TourPackage;
use App\Services\TourPublishingReadinessService;

final class AdminTourPublishingReadinessController extends Controller
{
    public function show(
        TourPackage $tourPackage,
        TourPublishingReadinessService
            $readinessService
    ) {
        $report = $readinessService->inspect(
            $tourPackage
        );

        return view(
            'admin.tour-packages.readiness.show',
            [
                'tourPackage' => $tourPackage,
                'report' => $report,
            ]
        );
    }
}