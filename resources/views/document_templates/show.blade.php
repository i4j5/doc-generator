@extends('layouts.app')

@section('content')
<div class="container">
    {{ $documentTemplate->name }}<br>
    {{ $documentTemplate->description }}<br>
    {{ $documentTemplate->path }}
</div>
@endsection