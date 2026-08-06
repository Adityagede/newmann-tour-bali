@extends('admin.layouts.app')

@section('content')
    @include(
        'admin.tour-packages._form',
        [
            'tourPackage' => $tourPackage,
        ]
    )
@endsection