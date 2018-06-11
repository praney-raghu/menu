@extends('neev::admin.layout')

@section('content')
    {!! \Modules\DynamicMenu\Models\Menus::render() !!}
@endsection

@push('scripts')
    {!! \Modules\DynamicMenu\Models\Menus::scripts() !!}
@endpush
