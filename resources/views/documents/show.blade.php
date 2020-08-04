@extends('layouts.app')

@section('content')
<div class="container">
    {{ $document->name }}<br>
    {{ $document->description }}<br>
    {{ $document->path }}

    <br><br>

    <a href="{{ $document->id }}/download" target="_blank" class="btn btn-primary">Скачать файл</a>

    <br>
    <br>
    <form action="{{url('documents', [$document->id])}}" method="POST">
        <input type="hidden" name="_method" value="DELETE">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <input type="submit" class="btn btn-danger" value="Удалить"/>
    </form>
</div>
@endsection
