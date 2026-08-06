@extends('admin.layouts.app')

@section('content')
    @include(
        'admin.tour-packages.roadmap._form',
        [
            'tourPackage' => $tourPackage,
            'tourStop' => null,
            'stopTypes' => $stopTypes,
        ]
    )
@endsection