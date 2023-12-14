@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="align-items-center">
        <h1 class="h3">{{translate('All Notifications')}}</h1>
    </div>
</div>


<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <form class="" id="sort_customers" action="" method="GET">
                <div class="card-header row gutters-5">
                    <div class="col">
                        <h5 class="mb-0 h6">{{translate('Notifications')}}</h5>
                    </div>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @forelse($notifications as $notification)
                            @if($notification->type == 'App\Notifications\OrderNotification')
                                <li class="list-group-item d-flex justify-content-between align-items- py-3">
                                    <div class="media text-inherit">
                                        <div class="media-body">
                                            <p class="mb-1 text-truncate-2">
                                                {{ translate('Order code: ') }}
                                                <a href="{{route('all_orders.show', encrypt($notification->data['order_id']))}}">
                                                    {{$notification->data['order_code']}}
                                                </a>
                                                {{translate(' has been '. ucfirst(str_replace('_', ' ', $notification->data['status'])))}}
                                            </p>
                                            <small class="text-muted">
                                                {{ date("F j Y, g:i a", strtotime($notification->created_at)) }}
                                            </small>
                                        </div>
                                    </div>
                                </li>
                            @endif

                            @if($notification->type == 'App\Notifications\AdminNotification')
                                <li class="list-group-item d-flex justify-content-between align-items- py-3">
                                    <div class="media text-inherit">
                                        <div class="media-body">
                                            {{-- <p class="mb-1">
                                                @if($notification->data['notification_key'] == "admin_reset_password" || $notification->data['notification_key'] == "new_staff_admin" || $notification->data['notification_key'] == "login_message")
                                                    @if($notification->data['content'] != null)
                                                        {!! $notification->data['content'] !!}
                                                    @else
                                                        {{$notification->data['body']}}
                                                    @endif
                                                @elseif($notification->data['notification_key'] == "customer_registration")
                                                    <a href="{{route('customers.index')}}">
                                                        @if($notification->data['content'] != null)
                                                            {!! $notification->data['content'] !!}
                                                        @else
                                                            {{$notification->data['body']}}
                                                        @endif
                                                    </a>
                                                @elseif($notification->data['notification_key'] == "seller_registration")
                                                    <a href="{{route('sellers.index')}}">
                                                        @if($notification->data['content'] != null)
                                                            {!! $notification->data['content'] !!}
                                                        @else
                                                            {{$notification->data['body']}}
                                                        @endif
                                                    </a>
                                                @elseif($notification->data['notification_key'] == "new_product")
                                                    <a href="{{route('product', $notification->data['slug'])}}" target="_blank">
                                                        @if($notification->data['content'] != null)
                                                            {!! $notification->data['content'] !!}
                                                        @else
                                                            {{$notification->data['body']}}
                                                        @endif
                                                    </a>
                                                @elseif($notification->data['notification_key'] == "support_ticket_by_customer" || $notification->data['notification_key'] == "support_ticket_by_seller")
                                                    <a href="{{route('support_ticket.admin_show', encrypt($notification->data['id']))}}">
                                                        @if($notification->data['content'] != null)
                                                            {!! $notification->data['content'] !!}
                                                        @else
                                                            {{$notification->data['body']}}
                                                        @endif
                                                    </a>
                                                @elseif($notification->data['notification_key'] == "admin_product_enquiry")
                                                    <a href="{{route('product_query.show', encrypt($notification->data['id']))}}">
                                                        @if($notification->data['content'] != null)
                                                            {!! $notification->data['content'] !!}
                                                        @else
                                                            {{$notification->data['body']}}
                                                        @endif
                                                    </a>
                                                @elseif($notification->data['notification_key'] == "admin_product_rating")
                                                    <a href="{{route('reviews.index')}}">
                                                        @if($notification->data['content'] != null)
                                                            {!! $notification->data['content'] !!}
                                                        @else
                                                            {{$notification->data['body']}}
                                                        @endif
                                                    </a>
                                                @elseif($notification->data['notification_key'] == "admin_product_quotation")
                                                    @if($notification->data['content'] != null)
                                                        {!! $notification->data['content'] !!}
                                                    @else
                                                        {{$notification->data['body']}}
                                                    @endif
                                                @elseif($notification->data['notification_key'] == "admin_subscription")
                                                    {!! $notification->data['content'] !!}
                                                @elseif($notification->data['notification_key'] == "admin_subscription_new")
                                                    {!! $notification->data['content'] !!}
                                                @elseif($notification->data['notification_key'] == "customer_conversation")
                                                    <a href="{{url('admin/conversations/'.encrypt($notification->data['id']).'/show')}}">
                                                        {!! $notification->data['content'] !!}
                                                    </a>
                                                @endif
                                            </p> --}}
                                            <p class="mb-1">
                                                @if(isset($notification->data['notification_key']))
                                                    @if($notification->data['notification_key'] == "admin_reset_password" || $notification->data['notification_key'] == "new_staff_admin" || $notification->data['notification_key'] == "login_message")
                                                        @if(isset($notification->data['content']) && $notification->data['content'] != null)
                                                            {!! $notification->data['content'] !!}
                                                        @else
                                                            {{$notification->data['body']}}
                                                        @endif
                                                    @elseif($notification->data['notification_key'] == "customer_registration")
                                                        <a href="{{route('customers.index')}}">
                                                            @if(isset($notification->data['content']) && $notification->data['content'] != null)
                                                                {!! $notification->data['content'] !!}
                                                            @else
                                                                {{$notification->data['body']}}
                                                            @endif
                                                        </a>
                                                    @elseif($notification->data['notification_key'] == "seller_registration")
                                                        <a href="{{route('sellers.index')}}">
                                                            @if(isset($notification->data['content']) && $notification->data['content'] != null)
                                                                {!! $notification->data['content'] !!}
                                                            @else
                                                                {{$notification->data['body']}}
                                                            @endif
                                                        </a>
                                                    <!-- Add more cases for other notification types -->
                                                    @else
                                                        <!-- Handle other notification types or log an error -->
                                                        Unknown notification type: {{$notification->data['notification_key']}}
                                                    @endif
                                                @else
                                                    <!-- Handle case where 'notification_key' is not set or log an error -->
                                                    Missing 'notification_key' in notification data
                                                @endif
                                            </p>

                                            <small class="text-muted">
                                                {{ date("F j Y, g:i a", strtotime($notification->created_at)) }}
                                            </small>
                                        </div>
                                    </div>
                                </li>
                            @endif

                            @if($notification->type == 'App\Notifications\ConversationNotification')
                                <li class="list-group-item d-flex justify-content-between align-items- py-3">
                                    <div class="media text-inherit">
                                        <div class="media-body">
                                            <p class="mb-1 text-truncate-2">
                                                <a href="{{route('conversations.admin_show', encrypt($notification->data['id']))}}">
                                                    {{$notification->data['body']}}
                                                </a>
                                            </p>
                                            <small class="text-muted">
                                                {{ date("F j Y, g:i a", strtotime($notification->created_at)) }}
                                            </small>
                                        </div>
                                    </div>
                                </li>
                            @endif

                        @empty
                            <li class="list-group-item">
                                <div class="py-4 text-center fs-16">{{ translate('No New Notifications') }}</div>
                            </li>
                        @endforelse
                    </ul>

                    {{ $notifications->links() }}
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

