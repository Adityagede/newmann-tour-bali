@extends('admin.layouts.app')

@section('content')
    @include(
        'admin.tour-packages.options.items._form',
        [
            'tourPackage' => $tourPackage,
            'tourOption' => $tourOption,
            'tourOptionItem' => $tourOptionItem,
            'itemTypes' => $itemTypes,
            'categories' => $categories,
        ]
    )
@endsection