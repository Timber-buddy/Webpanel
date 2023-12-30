<div class="aiz-topbar px-15px px-lg-25px d-flex align-items-stretch justify-content-between">
    <div class="d-flex">
        <div class="aiz-topbar-nav-toggler d-flex align-items-center justify-content-start mr-2 mr-md-3 ml-0" data-toggle="aiz-mobile-nav">
            <button class="aiz-mobile-toggler">
                <span></span>
            </button>
        </div>
    </div>
    <div class="d-flex justify-content-between align-items-stretch flex-grow-xl-1">
        <div class="d-flex justify-content-around align-items-center align-items-stretch">
            <div class="d-flex justify-content-around align-items-center align-items-stretch">
                <div class="aiz-topbar-item">
                    <div class="d-flex align-items-center">
                        <a class="btn btn-light" href="{{ route('home')}}" target="_blank" title="{{ translate('Browse Website') }}">
                            <i class="las la-globe 3x"></i>
                            Browse Website
                        </a>
                    </div>
                </div>
            </div>
            @if (addon_is_activated('pos_system') && auth()->user()->can('pos_manager'))
                <div class="d-flex justify-content-around align-items-center align-items-stretch ml-3">
                    <div class="aiz-topbar-item">
                        <div class="d-flex align-items-center">
                            <a class="btn btn-icon btn-circle btn-light" href="{{ route('poin-of-sales.index') }}" target="_blank" title="{{ translate('POS') }}">
                                <i class="las la-print"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endif
            <div class="d-flex justify-content-around align-items-center align-items-stretch ml-3">
                <div class="aiz-topbar-item">
                    <div class="d-flex align-items-center">
                        <a class="btn btn-soft-danger btn-sm d-flex align-items-center" href="{{ route('cache.clear')}}">
                            <i class="las la-hdd fs-20"></i>
                            <span class="fw-500 ml-1 mr-0 d-none d-md-block">{{ translate('Clear Cache') }}</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-around align-items-center align-items-stretch">

            <div class="aiz-topbar-item ml-2">
                <div class="align-items-stretch d-flex dropdown" data-toggle="tooltip" data-title="{{ translate('Notification') }}" data-placement="top">
                    <a class="dropdown-toggle no-arrow" data-toggle="dropdown" href="javascript:void(0);" role="button" aria-haspopup="false" aria-expanded="false">
                        <span class="btn btn-icon p-0 d-flex justify-content-center align-items-center">
                            <span class="d-flex align-items-center position-relative">
                                <i class="las la-bell fs-24"></i>
                                @if(Auth::user()->unreadNotifications->count() > 0)
                                    <span class="badge badge-sm badge-dot badge-circle badge-primary position-absolute absolute-top-right"></span>
                                @endif
                            </span>
                        </span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right dropdown-menu-animated dropdown-menu-lg py-0">
                        <div class="p-3 bg-light border-bottom">
                            <h6 class="mb-0">{{ translate('Notifications') }}</h6>
                        </div>
                        <div class="px-3 c-scrollbar-light overflow-auto " style="max-height:300px;">
                            <ul class="list-group list-group-flush">
                                @forelse(Auth::user()->unreadNotifications->take(20) as $notification)
                                    <li class="list-group-item d-flex justify-content-between align-items- py-3">
                                        <div class="media text-inherit">
                                            <div class="media-body">
                                                @if($notification->type == 'App\Notifications\OrderNotification')
                                                    <p class="mb-1 text-truncate-2">
                                                        {{translate('Order code: ')}} {{$notification->data['order_code']}} {{ translate('has been '. ucfirst(str_replace('_', ' ', $notification->data['status'])))}}
                                                    </p>
                                                    <small class="text-muted">
                                                        {{ date("F j Y, g:i a", strtotime($notification->created_at)) }}
                                                    </small>
                                                @endif

                                                @if($notification->type == 'App\Notifications\AdminNotification')
                                                    <p class="mb-1 text-truncate-2">
                                                        @if($notification->data['notification_key'] == "admin_reset_password" || $notification->data['notification_key'] == "new_staff_admin" || $notification->data['notification_key'] == "login_message" || $notification->data['notification_key'] == "admin_subscription" || $notification->data['notification_key'] == "admin_subscription_new")
                                                            {{$notification->data['body']}}
                                                        @elseif($notification->data['notification_key'] == "customer_conversation")
                                                            <a href="{{url('admin/conversations/'.encrypt($notification->data['id']).'/show')}}">
                                                                {{$notification->data['body']}}
                                                            </a>
                                                        @elseif($notification->data['notification_key'] == "customer_registration")
                                                            <a href="{{route('customers.index')}}">
                                                                {{$notification->data['body']}}
                                                            </a>
                                                        @elseif($notification->data['notification_key'] == "seller_registration")
                                                            <a href="{{route('sellers.index')}}">
                                                                {{$notification->data['body']}}
                                                            </a>
                                                        @elseif($notification->data['notification_key'] == "new_product")
                                                            <a href="{{route('products.seller.edit', $notification->data['id'])}}" target="_blank">
                                                                {{$notification->data['body']}}
                                                            </a>
                                                        @elseif($notification->data['notification_key'] == "request_featured_product")
                                                            <a href="{{route('product', $notification->data['slug'])}}" target="_blank">Requested for Featured Product listing !
                                                                {{$notification->data['body']}}
                                                            </a>
                                                        @elseif($notification->data['notification_key'] == "support_ticket_by_customer" || $notification->data['notification_key'] == "support_ticket_by_seller")
                                                            <a href="{{route('support_ticket.admin_show', encrypt($notification->data['id']))}}">
                                                                {{$notification->data['body']}}
                                                            </a>
                                                        @elseif($notification->data['notification_key'] == "admin_product_enquiry")
                                                            <a href="{{route('product_query.show', encrypt($notification->data['id']))}}">
                                                                {{$notification->data['body']}}
                                                            </a>
                                                        @elseif($notification->data['notification_key'] == "admin_product_rating")
                                                            <a href="{{route('reviews.index')}}">
                                                                {{$notification->data['body']}}
                                                            </a>
                                                        @elseif($notification->data['notification_key'] == "admin_product_quotation")
                                                            {{$notification->data['body']}}
                                                        @endif
                                                    </p>
                                                    <small class="text-muted">
                                                        {{ date("F j Y, g:i a", strtotime($notification->created_at)) }}
                                                    </small>
                                                @endif

                                                @if($notification->type == 'App\Notifications\ConversationNotification')
                                                    <p class="mb-1 text-truncate-2">
                                                        <a href="{{route('conversations.admin_show', encrypt($notification->data['id']))}}">
                                                            {{$notification->data['body']}}
                                                        </a>
                                                    </p>
                                                    <small class="text-muted">
                                                        {{ date("F j Y, g:i a", strtotime($notification->created_at)) }}
                                                    </small>
                                                @endif
                                            </div>
                                        </div>
                                    </li>
                                @empty
                                    <li class="list-group-item">
                                        <div class="py-4 text-center fs-16">
                                            {{ translate('No New Notifications') }}
                                        </div>
                                    </li>
                                @endforelse
                            </ul>
                        </div>
                        <div class="text-center border-top">
                            <a href="{{ route('admin.all-notification') }}" class="text-reset d-block py-2">
                                {{translate('View All/Previous notifications in details')}}
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- language --}}
            @php
                if(Session::has('locale')){
                    $locale = Session::get('locale', Config::get('app.locale'));
                }
                else{
                    $locale = env('DEFAULT_LANGUAGE');
                }
            @endphp
            <div class="aiz-topbar-item ml-2">
                <div class="align-items-stretch d-flex dropdown " id="lang-change">
                    <a class="dropdown-toggle no-arrow" data-toggle="dropdown" href="javascript:void(0);" role="button" aria-haspopup="false" aria-expanded="false">
                        <span class="btn btn-icon">
                            <img src="{{ static_asset('assets/img/flags/'.$locale.'.png') }}" height="11">
                        </span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-right dropdown-menu-animated dropdown-menu-xs">

                        @foreach (\App\Models\Language::where('status', 1)->get() as $key => $language)
                            <li>
                                <a href="javascript:void(0)" data-flag="{{ $language->code }}" class="dropdown-item @if($locale == $language->code) active @endif">
                                    <img src="{{ static_asset('assets/img/flags/'.$language->code.'.png') }}" class="mr-2">
                                    <span class="language">{{ $language->name }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <div class="aiz-topbar-item ml-2">
                <div class="align-items-stretch d-flex dropdown">
                    <a class="dropdown-toggle no-arrow text-dark" data-toggle="dropdown" href="javascript:void(0);" role="button" aria-haspopup="false" aria-expanded="false">
                        <span class="d-flex align-items-center">
                            <span class="avatar avatar-sm mr-md-2">
                                <img
                                    src="{{ uploaded_asset(Auth::user()->avatar_original) }}"
                                    onerror="this.onerror=null;this.src='{{ static_asset('assets/img/avatar-place.png') }}';"
                                >
                            </span>
                            <span class="d-none d-md-block">
                                <span class="d-block fw-500">{{Auth::user()->name}}</span>
                                <span class="d-block small opacity-60">{{Auth::user()->user_type}}</span>
                            </span>
                        </span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right dropdown-menu-animated dropdown-menu-md">
                        <a href="{{ route('profile.index') }}" class="dropdown-item">
                            <i class="las la-user-circle"></i>
                            <span>{{translate('Profile')}}</span>
                        </a>

                        <a href="{{ route('logout')}}" class="dropdown-item">
                            <i class="las la-sign-out-alt"></i>
                            <span>{{translate('Logout')}}</span>
                        </a>
                    </div>
                </div>
            </div><!-- .aiz-topbar-item -->
        </div>
    </div>
</div><!-- .aiz-topbar -->
