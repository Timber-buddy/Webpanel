@extends('seller.layouts.app')

@section('panel_content')

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
                                        {{ translate('Order: ') }}
                                        <a href="{{ route('seller.orders.show', encrypt($notification->data['order_id'])) }}">
                                            {{ $notification->data['order_code'] }}
                                        </a>
                                        {{ translate(' has been '. ucfirst(str_replace('_', ' ', $notification->data['status']))) }}
                                    </p>
                                    <small class="text-muted">
                                        {{ date("F j Y, g:i a", strtotime($notification->created_at)) }}
                                    </small>
                                </div>
                            </div>
                        </li>
                    @endif

                    @if($notification->type == 'App\Notifications\SellerNotification')
                        <li class="list-group-item d-flex justify-content-between align-items- py-3">
                            <div class="media text-inherit">
                                <div class="media-body">
                                    <p class="mb-1">
                                        @if($notification->data['notification_key'] == "shop_approval" || $notification->data['notification_key'] == "reset_password" || $notification->data['notification_key'] == "product_deleted" || $notification->data['notification_key'] == "seller_ban" || $notification->data['notification_key'] == "shop_disapproval" || $notification->data['notification_key'] == "new_follower" || $notification->data['notification_key'] == "new_staff_seller" || $notification->data['notification_key'] == "login_message" || $notification->data['notification_key'] == "seller_update" || $notification->data['notification_key'] == "seller_product_update")
                                            @if(isset($notification->data['content']))
                                                {!! $notification->data['content'] !!}
                                            @else
                                                {{$notification->data['body']}}
                                            @endif

                                        @elseif($notification->data['notification_key'] == "seller_flash_deal" || $notification->data['notification_key'] == "product_approved" || $notification->data['notification_key'] == "product_unpublished" || $notification->data['notification_key'] == "product_published")
                                            <a href="{{ route('seller.products.edit', $notification->data['id']) }}">
                                                @if(isset($notification->data['content']))
                                                    {!! $notification->data['content'] !!}
                                                @else
                                                    {{$notification->data['body']}}
                                                @endif
                                            </a>
                                        @elseif($notification->data['notification_key'] == "seller_ticket_reply")
                                            <a href="{{ route('seller.support_ticket.show', encrypt($notification->data['id'])) }}">
                                                @if(isset($notification->data['content']))
                                                    {!! $notification->data['content'] !!}
                                                @else
                                                    {{$notification->data['body']}}
                                                @endif
                                            </a>
                                        @elseif($notification->data['notification_key'] == "seller_product_enquiry")
                                            <a href="{{ route('seller.product_query.show', encrypt($notification->data['id'])) }}">
                                                @if(isset($notification->data['content']))
                                                    {!! $notification->data['content'] !!}
                                                @else
                                                    {{$notification->data['body']}}
                                                @endif
                                            </a>
                                        @elseif($notification->data['notification_key'] == "seller_product_rating")
                                            <a href="{{ route('seller.reviews') }}">
                                                @if(isset($notification->data['content']))
                                                    {!! $notification->data['content'] !!}
                                                @else
                                                    {{$notification->data['body']}}
                                                @endif
                                            </a>
                                        @elseif($notification->data['notification_key'] == "seller_product_quotation")
                                            <a href="{{ route('seller.quotation.view', encrypt($notification->data['id'])) }}">
                                                @if(isset($notification->data['content']))
                                                    {!! $notification->data['content'] !!}
                                                @else
                                                    {{$notification->data['body']}}
                                                @endif
                                            </a>
                                        @elseif($notification->data['notification_key'] == "seler_subscription")
                                            {!! $notification->data['content'] !!}
                                        @elseif($notification->data['notification_key'] == "seler_subscription_expiry")
                                            {!! $notification->data['content'] !!}
                                        @elseif($notification->data['notification_key'] == "seler_subscription_new")
                                            {!! $notification->data['content'] !!}
                                        @elseif($notification->data['notification_key'] == "customer_conversation")
                                            <a href="{{URL('seller/conversations/show/'.encrypt($notification->data['id']))}}">
                                                {!! $notification->data['content'] !!}
                                            </a>
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
                                        <a href="{{ route('conversations.show', encrypt($notification->data['id'])) }}">
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

                    @if($notification->type == 'App\Notifications\SellerUpdate')
                        <li class="list-group-item d-flex justify-content-between align-items- py-3">
                            <div class="media text-inherit">
                                <div class="media-body">
                                    <p class="mb-1 text-truncate-2">
                                        {{$notification->data['body']}}
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

@endsection

@section('modal')
    <div class="modal fade" id="order_details" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <div id="order-details-modal-body">

                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">

        function show_order_details(order_id)
        {
            $('#order-details-modal-body').html(null);

            if(!$('#modal-size').hasClass('modal-lg')){
                $('#modal-size').addClass('modal-lg');
            }

            $.post('{{ route('orders.details') }}', { _token : AIZ.data.csrf, order_id : order_id}, function(data){
                $('#order-details-modal-body').html(data);
                $('#order_details').modal();
                $('.c-preloader').hide();
            });
        }
        function sort_orders(el){
            $('#sort_orders').submit();
        }
    </script>
@endsection

