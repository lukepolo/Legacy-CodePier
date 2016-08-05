@extends('layouts.app')

@push('scripts')
    <script>
        var user = {!! $user !!};
    </script>
@endpush
@section('content')
    <router-view class="view"></router-view>
@endsection