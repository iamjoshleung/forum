@extends('layouts.app') 
@section('content')
<div class="container">
    <avatar-form :user="{{ $profileUser }}"></avatar-form>

    @forelse ($activities as $date => $records)
    <h3>{{ $date }}</h3>
        @foreach($records as $record)
           @if(view()->exists("profiles.activities.{$record->type}"))
            @include("profiles.activities.{$record->type}", ['activity' => $record]) 
           @endif
        @endforeach 
    @empty
    <p>No activity recorded yet.</p>
    @endforelse

    {{-- {{ $threads->links() }} --}}
</div>
@endsection