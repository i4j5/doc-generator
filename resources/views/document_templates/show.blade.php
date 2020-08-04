@extends('layouts.app')

@section('content')
<div class="container">
    {{ $documentTemplate->name }}<br>
    {{ $documentTemplate->description }}<br>

    <br><br>

    <a href="{{ $documentTemplate->id }}/edit" class="btn btn-primary">Редактировать</a>

    <br><br>

    <a href="{{ $documentTemplate->id }}/download" target="_blank" class="btn btn-primary">Скачать файл</a>

    <br>
    <br>
    <form action="{{url('document-templates', [$documentTemplate->id])}}" method="POST">
        <input type="hidden" name="_method" value="DELETE">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <input type="submit" class="btn btn-danger" value="Удалить"/>
    </form>
</div>
@endsection