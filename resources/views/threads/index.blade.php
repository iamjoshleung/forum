@extends('layouts.app') 
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Threads</div>
                <div class="card-body">
    @include('threads._list') {{ $threads->render() }}
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card mb-5" style="width: 18rem;">
                <div class="card-header">
                    Search
                </div>
                <div class="card-body">
                    <form action="/threads/search" method="GET">
                        <div class="form-group">
                            <input type="text" name="q" class="form-control">
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Search</button>
                        </div>
                    </form>
                </div>
            </div>

            @if( count($trendings) )
            <div class="card" style="width: 18rem;">
                <div class="card-header">
                    Popular threads
                </div>
                <ul class="list-group list-group-flush">
                    @foreach ($trendings as $thread)
                    <li class="list-group-item">
                        <a href="{{ $thread->path }}">
                                {{ $thread->title }}
                            </a>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection