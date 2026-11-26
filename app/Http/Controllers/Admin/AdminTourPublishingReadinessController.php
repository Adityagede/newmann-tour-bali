<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TourOption;
use App\Models\TourPackage;
use App\Services\TourPublishingReadinessService;
use App\Support\TourPackageEditor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

final class AdminTourPublishingReadinessController extends Controller
{
    public function resume(
        TourPackage $tourPackage,
        TourPublishingReadinessService $readinessService
    ): RedirectResponse {
        if ($tourPackage->status !== 'draft') {
            return redirect()->route('admin.tour-packages.edit', $tourPackage);
        }

        $report = $readinessService->inspect($tourPackage);
        $checks = collect($report['checks'])->where('severity', 'blocking')->where('passed', false);

        $packageChecks = $checks->whereNotIn('key', ['default_option', 'active_option', 'active_options_ready']);
        $informationCheck = $packageChecks->whereNotIn('key', ['gallery_images', 'shared_roadmap', 'tour_options'])->first();
        $packageBlocker = $informationCheck ?? $packageChecks->first();

        if ($packageBlocker) {
            return redirect()->to(TourPackageEditor::packageCheckUrl($tourPackage, $packageBlocker['key']));
        }

        $options = collect($report['option_reports']);
        $option = $options->firstWhere('is_default', true) ?? $options->first();
        $optionBlocker = collect($option['checks'] ?? [])->where('severity', 'blocking')->firstWhere('passed', false);

        if ($optionBlocker) {
            return redirect()->to(TourPackageEditor::optionCheckUrl($tourPackage, $option['option_id'], $optionBlocker['key']));
        }

        return redirect()->route('admin.tour-packages.readiness.show', $tourPackage);
    }

    public function show(
        TourPackage $tourPackage,
        TourPublishingReadinessService $readinessService
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

    public function updateStatus(Request $request, TourPackage $tourPackage): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['draft', 'active', 'inactive'])],
        ]);

        try {
            $tourPackage->update(['status' => $validated['status']]);
        } catch (ValidationException $exception) {
            return redirect()->route('admin.tour-packages.readiness.show', $tourPackage)
                ->withErrors($exception->errors());
        }

        $message = match ($tourPackage->status) {
            'active' => 'Tour package published.',
            'inactive' => 'Tour package unpublished.',
            default => 'Tour package moved to draft.',
        };

        return redirect()->route('admin.tour-packages.readiness.show', $tourPackage)
            ->with('success', $message);
    }

    public function activateOption(TourPackage $tourPackage, TourOption $tourOption): RedirectResponse
    {
        abort_unless((int) $tourOption->tour_package_id === (int) $tourPackage->id, 404);

        $redirect = redirect()->route('admin.tour-packages.readiness.show', $tourPackage);

        try {
            $tourOption->update(['status' => 'active']);
        } catch (ValidationException $exception) {
            return $redirect->withErrors($exception->errors());
        }

        return $redirect->with('success', 'Tour option activated.');
    }
}
