@extends('layouts.app')

@section('content')
    <router-view class="view"></router-view>
    <vue-progress-bar></vue-progress-bar>
@endsection

@push('scripts')
<script>
    var user = {!! $user !!};
</script>
@endpush