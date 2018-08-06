<?php

namespace App\Http\Controllers;

use App\User;
use Zttp\Zttp;
use App\Thread;
use App\Channel;
use App\Trending;
use App\Rules\SpamFree;
use App\Rules\Recaptcha;
use Illuminate\Http\Request;
use App\Filters\ThreadFilters;

class ThreadController extends Controller
{

    /**
     * Thread Constructor
     * 
     * @return 
     */
    public function __construct() {
        $this->middleware('auth')->except(['index', 'show']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Channel $channel, ThreadFilters $filters, Trending $trending)
    {
        $threads = $this->getThreads($channel, $filters);
        
        if (request()->wantsJson()) {
            return $threads;
        }


        return view('threads.index', [
            'threads' => $threads,
            'trendings' => $trending->get()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('threads.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Recaptcha $recaptcha)
    {
        $request->validate([
            'title' => ['required', new SpamFree],
            'body' => ['required', new SpamFree],
            'channel_id' => 'required|exists:channels,id',
            'g-recaptcha-response' => ['required', $recaptcha],
        ]);


        $thread = Thread::create([
            'user_id' => auth()->id(),
            'channel_id' => $request->input('channel_id'),
            'body' => $request->input('body'),
            'title' => $request->input('title'),
        ]);

        if( request()->wantsJson() ) {
            return response($thread, 201);
        }
 
        return redirect()
            ->route('threads.show', ['channel' => $thread->channel->slug, 'thread' => $thread->slug])
            ->with('flash', 'You have created a thread');
    }

    /**
     * Display the specified resource.
     *
     * @param int, $channel
     * @param  \App\Thread  $thread
     * @return \Illuminate\Http\Response
     */
    public function show($channel, Thread $thread, Trending $trending)
    {
        if( auth()->check() ) {
            auth()->user()->readThread($thread);
        }

        $trending->push($thread);

        $thread->increment('visits');

        return view('threads.show', compact('thread'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Thread  $thread
     * @return \Illuminate\Http\Response
     */
    public function edit(Thread $thread)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Thread  $thread
     * @return \Illuminate\Http\Response
     */
    public function update($channel, Thread $thread)
    {
        $this->authorize('update', $thread);
        
        $data = request()->validate([
            'title' => ['required', new SpamFree],
            'body' => ['required', new SpamFree],
        ]);

        $thread->update($data);

        return $thread;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Thread  $thread
     * @return \Illuminate\Http\Response
     */
    public function destroy($channel, Thread $thread)
    {
        $this->authorize('delete', $thread);
        // if( auth()->id() !== $thread->user_id ) {
        //     return abort(403);
        // }

        $thread->delete();

        if( request()->wantsJson() ) {
            return response([], 204);
        }

        return redirect('threads');

    }

    protected function getThreads(Channel $channel, ThreadFilters $filters) {
        $threads = Thread::latest()->filters($filters);
        
        if( $channel->exists ) {
        $threads = $threads->where('channel_id', $channel->id);
        }

        return $threads->paginate(25);
    }
}
