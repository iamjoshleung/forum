<div class="card mb-5" v-cloak>
    <template v-if="! editing">
        <div class="card-header d-flex align-items-center">
            <img src="{{ $thread->creator->avatar_path }}" alt="{{ $thread->creator->name }}" width="20" height="20" class="mr-2">
            <a href="/profile/{{ $thread->creator->name }}">{{ $thread->creator->name }}</a>
            <span class="ml-4">posted: <span v-text="title"></span> </span>
        </div>
    
        <div class="card-body" v-html="body"></div>
    </template>
    <template v-else>
        <div class="card-header">
            <div class="form-group">
                <input type="text" class="form-control" v-model="form.title">
            </div>
        </div>
        <div class="card-body">
            <div class="form-group">
                <wysiwyg v-model="form.body"></wysiwyg>
            </div>
        </div>
    </template>


    <div class="card-footer text-muted d-flex justify-content-between align-items-start" v-if="this.authorize('owns', this.thread)">
        <button class="btn btn-primary btn-sm" @click="editing = true" v-show="! editing">Edit</button>
        <div v-show="editing">
            <button class="btn btn-secondary btn-sm"  @click="update">Update</button>
            <button class="btn btn-danger btn-sm" @click="resetForm">Cancel</button>
        </div>
        @can('update', $thread)
        <form action="{{ $thread->path() }}" method="POST">
            @method('DELETE') @csrf
            <button type="submit" class="btn btn-link">DELETE THREAD</button>
        </form>
        @endcan
    </div>
</div>