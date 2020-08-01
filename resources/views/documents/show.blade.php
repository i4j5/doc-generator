@extends('layouts.app')

@section('content')
<div class="container">
    {{ $document->name }}<br>
    {{ $document->description }}<br>
    {{ $document->path }}
</div>
@endsection
