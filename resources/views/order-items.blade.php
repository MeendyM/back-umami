@extends('layouts.app')

@section('content')
<div class="py-6">
    <h1 class="text-2xl font-semibold mb-4">Order Items</h1>
    <livewire:order-items.table />
</div>
@endsection
