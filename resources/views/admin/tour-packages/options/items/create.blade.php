@extends('admin.layouts.app')

@section('content')
    @include(
        'admin.tour-packages.options.items._form',
        [
            'tourPackage' => $tourPackage,
            'tourOption' => $tourOption,
            'tourOptionItem' => null,
            'itemTypes' => $itemTypes,
            'categories' => $categories,
        ]
    )
@endsection