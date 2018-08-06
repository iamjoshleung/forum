@forelse ($threads as $thread)
<article>
    <div class="d-flex align-items-center">
        <div class="d-flex flex-column">
            <h4>
                <a href="{{ $thread->path() }}">
                                        @if( auth()->check() && $thread->hasUpdatesFor(auth()->user()) ) 
                                            <strong>{{ $thread->title }}</strong>
                                        @else 
                                            {{ $thread->title }}
                                        @endif
                                    </a>
            </h4>
            <div>Posted by: <a href="{{ route('profile.show', $thread->creator) }}">{{ $thread->creator->name }}</a></div>
        </div>
        {{-- @if( $thread->replies_count ) --}}
        <div class="ml-auto">
            <a href="{{ $thread->path() }}">{{ $thread->replies_count }} {{ str_plural('reply', $thread->replies_count) }}</a>
        </div>
        {{-- @endif --}}
    </div>
    <div class="body mt-3 ml-3">{!! $thread->body !!}</div>
    <div>{{ $thread->visits }} visits</div>
</article>
<hr> @empty There are no relevant threads to show. @endforelse