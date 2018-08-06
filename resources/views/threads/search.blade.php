@extends('layouts.app') 
@section('content')
<div class="container">
    <ais-index class="row justify-content-center" app-id="{{ config('scout.algolia.id') }}" api-key="{{ config('scout.algolia.key') }}"
        index-name="threads" query="{{ request('q') }}">

        <div class="col-md-8">

            <ais-results>
                <template slot-scope="{ result }">
                                <p>
                                    <a :href="result.path">
                                        <ais-highlight :result="result" attribute-name="title"></ais-highlight>
                                    </a>
                                </p>
                            </template>
            </ais-results>

        </div>
        <div class="col-md-4">
            <div class="card mb-5" style="width: 18rem;">
                <div class="card-header">
                    Search
                </div>
                <div class="card-body">
                    <ais-search-box :autofocus="true">
                            <ais-input
                            placeholder="...Search threads"
                            :autofocus="true"
                            class="form-control"
                          >
                    </ais-search-box>
                </div>
            </div>
            <div class="card mb-5" style="width: 18rem;">
                    <div class="card-header">
                        Filtered by Channel
                    </div>
                    <div class="card-body">
                        <ais-refinement-list attribute-name="channel.name"></ais-refinement-list>
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
    </ais-index>
</div>
@endsection