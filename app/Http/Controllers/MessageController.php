<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use Auth;
use App\Models\Conversation;
use App\Models\User;
use App\Notifications\ConversationNotification;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $message = new Message;
        $message->conversation_id = $request->conversation_id;
        $message->user_id = Auth::user()->id;
        $message->message = $request->message;
        $message->save();
        $conversation = $message->conversation;
        
        if ($conversation->sender_id == Auth::user()->id) {
            $conversation->receiver_viewed ="1";
        }
        elseif($conversation->receiver_id == Auth::user()->id) {
            $conversation->sender_viewed ="1";
        }
        $conversation->save();

        $conversation = Conversation::findOrFail($request->conversation_id);
        $user = User::findOrFail($conversation->sender_id);
        $notificationData = [
            'name' => $user->name,
            'body' => Config('notification.conversation_reply'),
            'thanks' => 'Thank you',
            'id' => $conversation->id,
            'notification_key' => 'conversation_reply',
            'content' => "💬 New Message Received!<br>You have a response from ".Auth::user()->name." regarding your conversation on ".$conversation->title.".<br>Tap to read the message."
        ];

        try {
            \Notification::send($user, new ConversationNotification($notificationData));
        } catch (Exception $e) {
            // dd($e);
            echo "<script>console.log('".$e."')</script>";
            // return back();
        }
        
        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
