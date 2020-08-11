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

                    <b>Документы</b>
                    <a class="action-link" href="{{ URL::to('documents/create') }}">
                        <button type="button" class="btn btn-primary">Добавить</button>
                    </a>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <thead>
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Название </th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach ($documents as $document)
                            <tr>
                                <td> {{ $document->id }}</td>
                                <td>
                                    <a class="action-link" href="{{ URL::to('documents/' . $document->id) }}">
                                        {{ $document->name }}
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    {{ $documents->links() }}
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
