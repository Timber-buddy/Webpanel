<?php

namespace App\Http\Controllers\Seller;

use Illuminate\Http\Request;
use App\Models\Conversation;
use App\Models\BusinessSetting;
use App\Models\Message;
use App\Models\ProductQuery;
use App\Models\User;
use Auth;
use App\Notifications\ConversationNotification;

class ConversationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (BusinessSetting::where('type', 'conversation_system')->first()->value == 1) {
            $conversations = Conversation::where('sender_id', getSellerId())->orWhere('receiver_id', getSellerId())->orderBy('created_at', 'desc')->paginate(5);
            return view('seller.conversations.index', compact('conversations'));
        } else {
            flash(translate('Conversation is disabled at this moment'))->warning();
            return back();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $conversation = Conversation::findOrFail(decrypt($id));
        if ($conversation->sender_id == getSellerId()) {
            $conversation->sender_viewed = 1;
        } elseif ($conversation->receiver_id == getSellerId()) {
            $conversation->receiver_viewed = 1;
        }
        $conversation->save();
        return view('seller.conversations.show', compact('conversation'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function refresh(Request $request)
    {
        $conversation = Conversation::findOrFail(decrypt($request->id));
        if ($conversation->sender_id == getSellerId()) {
            $conversation->sender_viewed = 1;
            $conversation->save();
        } else {
            $conversation->receiver_viewed = 1;
            $conversation->save();
        }
        return view('frontend.partials.messages', compact('conversation'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function message_store(Request $request)
    {
        $message = new Message;
        $message->conversation_id = $request->conversation_id;
        $message->user_id = Auth::user()->id;
        $message->message = $request->message;
        $message->save();
        $conversation = $message->conversation;
        if ($conversation->sender_id == getSellerId()) {
            $conversation->receiver_viewed = "1";
        } elseif ($conversation->receiver_id == getSellerId()) {
            $conversation->sender_viewed = "1";
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
    
}
