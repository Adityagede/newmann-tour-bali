<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomTripRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

final class AdminCustomTripRequestController extends Controller
{
    private const STATUSES = [
        'pending',
        'contacted',
        'confirmed',
        'cancelled',
        'completed',
    ];

    public function index(Request $request): View
    {
        $validated = $request->validate([
            'status' => [
                'nullable',
                'string',
                Rule::in(self::STATUSES),
            ],
            'q' => ['nullable', 'string', 'max:120'],
        ]);

        $status = $validated['status'] ?? null;
        $search = trim((string) ($validated['q'] ?? ''));

        $customTripRequests = CustomTripRequest::query()
            ->customOnly()
            ->when(
                $status,
                fn (Builder $query) => $query->where(
                    'status',
                    $status
                )
            )
            ->when(
                $search !== '',
                function (Builder $query) use ($search): void {
                    $query->where(
                        function (Builder $inner) use ($search): void {
                            $inner
                                ->where(
                                    'booking_code',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'name',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'whatsapp',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'email',
                                    'like',
                                    "%{$search}%"
                                );
                        }
                    );
                }
            )
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $counts = ['all' => CustomTripRequest::query()->customOnly()->count()];

        foreach (self::STATUSES as $item) {
            $counts[$item] = CustomTripRequest::query()
                ->customOnly()
                ->where('status', $item)
                ->count();
        }

        return view('admin.custom-trip-requests.index', [
            'customTripRequests' => $customTripRequests,
            'counts' => $counts,
            'status' => $status,
            'search' => $search,
            'availableStatuses' => self::STATUSES,
        ]);
    }

    public function show(
        CustomTripRequest $customTripRequest
    ): View {
        abort_unless(
            $customTripRequest->tour_package_id === null,
            404
        );

        return view('admin.custom-trip-requests.show', [
            'customTripRequest' => $customTripRequest,
            'availableStatuses' => self::STATUSES,
        ]);
    }

    public function updateStatus(
        Request $request,
        CustomTripRequest $customTripRequest
    ): RedirectResponse {
        abort_unless(
            $customTripRequest->tour_package_id === null,
            404
        );

        $validated = $request->validate([
            'status' => [
                'required',
                'string',
                Rule::in(self::STATUSES),
            ],
        ]);

        $customTripRequest->update([
            'status' => $validated['status'],
        ]);

        return back()->with(
            'success',
            'Custom Trip Request status updated.'
        );
    }

    public function destroy(
        CustomTripRequest $customTripRequest
    ): RedirectResponse {
        abort_unless(
            $customTripRequest->tour_package_id === null,
            404
        );

        $reference = $customTripRequest->booking_code;
        $customTripRequest->delete();

        return redirect()
            ->route('admin.custom-trip-requests.index')
            ->with(
                'success',
                "Custom Trip Request {$reference} has been deleted."
            );
    }
}
