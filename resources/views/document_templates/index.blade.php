@extends('layouts.app')

@section('content')
<div class="container">

    <div class="row justify-content-center">
        <div class="col-md-8">

            @if (Session::has('message'))
                <div class="alert alert-info">{{ Session::get('message') }}</div>
            @endif

            <div class="card card-default">
                <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">

                    <b>Шаблоны документов</b>
                    <a class="action-link" href="{{ URL::to('document-templates/create') }}">
                        <button type="button" class="btn btn-primary">Добавить</button>
                    </a>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <thead>
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Название </th>
                                <th scope="col">Описание</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach ($documentTemplates as $documentTemplate)
                            <tr>
                                <td> {{ $documentTemplate->id }}</td>
                                <td>
                                    <a class="action-link" href="{{ URL::to('document-templates/' . $documentTemplate->id) }}">
                                        {{ $documentTemplate->name }}
                                    </a>
                                </td>
                                <td>{{ $documentTemplate->description }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    {{ $documentTemplates->links() }}
                </div>
            </div>

        </div>
    </div>
</div>
@endsection