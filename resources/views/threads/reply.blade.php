<reply :attributes="{{ $reply }}" inline-template v-cloak>
    <div id="reply-{{$reply->id}}" class="card card-default mb-2">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <div>
                    <a href="/profile/{{ $reply->owner->name }}">{{ $reply->owner->name }}</a>
                    <span>said {{ $reply->created_at->diffForHumans() }}...</span>
                </div>

                @if(Auth::check())
                <favourite :reply="{{ $reply }}"></favourite>
                @endif
            </div>
        </div>

        <div class="card-body">
            <div v-if="editing">
                <div class="form-group">
                    <textarea class="form-control" v-model="body"></textarea>
                </div>
                <button class="btn btn-sm btn-primary" @click="update">Update</button>
                <button class="btn btn-sm btn-link" @click="editing = false">Cancel</button>
            </div>

            <div v-else v-text="body"></div>
        </div>

        @can('destroy', $reply)
        <div class="card-footer text-muted">
            <button class="btn btn-sm btn-secondary" @click="editing = true">Edit</button>
            <button class="btn btn-sm btn-danger" @click="destroy">Delete</button>
        </div>
        @endcan
    </div>
</reply>