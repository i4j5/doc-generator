@extends('layouts.app')

@section('content')
<div class="container">
  <h1>Edit</h1>
  <hr>
  <form action="{{url('document-templates', [$documentTemplate->id])}}" enctype="multipart/form-data" method="post">
    <input type="hidden" name="_method" value="PUT">
    {{ csrf_field() }}
    <div class="form-group">
      <label for="title">Название</label>
      <input class="form-control" value="{{ old('name', $documentTemplate->name) }}" name="name"> 
    </div>
    <div class="form-group">
      <label for="description">Описание</label>
      <textarea class="form-control" rows="3" name="description">{{ old('description', $documentTemplate->description) }}</textarea>
    </div>

    <div class="form-group">
      <label for="exampleFormControlFile">Example file input</label>
      <input name="file" type="file" class="form-control-file" id="exampleFormControlFile">
    </div>

    @if ($errors->any())
      <div class="alert alert-danger">
      <ul>
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
      </div>
    @endif
    <button type="submit" class="btn btn-primary">Сохранить</button>
  </form>
</div>
@endsection