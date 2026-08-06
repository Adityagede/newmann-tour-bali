@extends('admin.layouts.app')

@section('content')
    @include(
        'admin.tour-packages.options.discounts._form',
        [
            'tourPackage' => $tourPackage,
            'tourOption' => $tourOption,
            'tourOptionDiscount' => $tourOptionDiscount,
            'discountTypes' => $discountTypes,
            'participantTypes' => $participantTypes,
        ]
    )
@endsection