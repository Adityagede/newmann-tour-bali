@extends('admin.layouts.app')

@section('content')
    @include(
        'admin.tour-packages.options.blackouts._form',
        [
            'tourPackage' => $tourPackage,
            'tourOption' => $tourOption,
            'tourOptionBlackoutDate' => null,
        ]
    )
@endsection