@extends('admin.layouts.app')

@section('content')
    @include(
        'admin.tour-packages.options._form',
        [
            'tourPackage' => $tourPackage,
            'tourOption' => null,
            'pickupTypes' => $pickupTypes,
        ]
    )
@endsection