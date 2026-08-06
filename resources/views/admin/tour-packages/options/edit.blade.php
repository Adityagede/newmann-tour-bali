@extends('admin.layouts.app')

@section('content')
    @include(
        'admin.tour-packages.options._form',
        [
            'tourPackage' => $tourPackage,
            'tourOption' => $tourOption,
            'pickupTypes' => $pickupTypes,
        ]
    )
@endsection