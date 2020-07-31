@extends('layouts.app')

@section('content')
<div class="container">
  <h1>ADD</h1>
  <hr>
  <form action="/document-templates" enctype="multipart/form-data" method="post">
    {{ csrf_field() }}
    <div class="form-group">
      <label for="title">Название</label>
      <input class="form-control" value="{{ old('name') }}" name="name"> 
    </div>
    <div class="form-group">
      <label for="description">Описание</label>
      <textarea class="form-control" rows="3" name="description">{{ old('description') }}</textarea>
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
    <button type="submit" class="btn btn-primary">Добавить</button>
  </form>
</div>
@endsection