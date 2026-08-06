@extends('admin.layouts.app')

@section('content')
    @include(
        'admin.tour-packages.options.schedules._form',
        [
            'tourPackage' => $tourPackage,
            'tourOption' => $tourOption,
            'tourOptionSchedule' => null,
            'days' => $days,
        ]
    )
@endsection