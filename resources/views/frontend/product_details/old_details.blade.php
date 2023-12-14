<div class="text-left">
    <!-- Product Name -->
    <h1 class="mb-4 fs-16 fw-700 text-dark">
        {{ $detailedProduct->getTranslation('name') }}
    </h1>

    <div class="row align-items-center mb-3">
        <!-- Review -->
        @if ($detailedProduct->auction_product != 1)
            <div class="col-12">
                @php
                    $total = 0;
                    $total += $detailedProduct->reviews->count();
                @endphp
                <span class="rating rating-mr-1">
                    {{ renderStarRating($detailedProduct->rating) }}
                </span>
                <span class="ml-1 opacity-50 fs-14">({{ $total }}
                    {{ translate('reviews') }})</span>
            </div>
        @endif
        <!-- Estimate Shipping Time -->
        @if ($detailedProduct->est_shipping_days)
            <div class="col-auto fs-14 mt-1">
                <small class="mr-1 opacity-50 fs-14">{{ translate('Estimate Shipping Time') }}:</small>
                <span class="fw-500">{{ $detailedProduct->est_shipping_days }} {{ translate('Days') }}</span>
            </div>
        @endif
        <!-- In stock -->
        @if ($detailedProduct->digital == 1)
            <div class="col-12 mt-1">
                <span class="badge badge-md badge-inline badge-pill badge-success">{{ translate('In stock') }}</span>
            </div>
        @endif
    </div>
    <div class="row align-items-center">
        <!-- Ask about this product -->
        <div class="col-xl-3 col-lg-4 col-md-3 col-sm-4 mb-3">
            <a href="javascript:void();" onclick="goToView('product_query')" class="text-primary fs-14 fw-600 d-flex">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 32 32">
                    <g id="Group_25571" data-name="Group 25571" transform="translate(-975 -411)">
                        <g id="Path_32843" data-name="Path 32843" transform="translate(975 411)" fill="#fff">
                            <path
                                d="M 16 31 C 11.9933500289917 31 8.226519584655762 29.43972969055176 5.393400192260742 26.60659980773926 C 2.560270071029663 23.77347946166992 1 20.00665092468262 1 16 C 1 11.9933500289917 2.560270071029663 8.226519584655762 5.393400192260742 5.393400192260742 C 8.226519584655762 2.560270071029663 11.9933500289917 1 16 1 C 20.00665092468262 1 23.77347946166992 2.560270071029663 26.60659980773926 5.393400192260742 C 29.43972969055176 8.226519584655762 31 11.9933500289917 31 16 C 31 20.00665092468262 29.43972969055176 23.77347946166992 26.60659980773926 26.60659980773926 C 23.77347946166992 29.43972969055176 20.00665092468262 31 16 31 Z"
                                stroke="none" />
                            <path
                                d="M 16 2 C 12.26045989990234 2 8.744749069213867 3.456249237060547 6.100500106811523 6.100500106811523 C 3.456249237060547 8.744749069213867 2 12.26045989990234 2 16 C 2 19.73954010009766 3.456249237060547 23.2552490234375 6.100500106811523 25.89949989318848 C 8.744749069213867 28.54375076293945 12.26045989990234 30 16 30 C 19.73954010009766 30 23.2552490234375 28.54375076293945 25.89949989318848 25.89949989318848 C 28.54375076293945 23.2552490234375 30 19.73954010009766 30 16 C 30 12.26045989990234 28.54375076293945 8.744749069213867 25.89949989318848 6.100500106811523 C 23.2552490234375 3.456249237060547 19.73954010009766 2 16 2 M 16 0 C 24.8365592956543 0 32 7.163440704345703 32 16 C 32 24.8365592956543 24.8365592956543 32 16 32 C 7.163440704345703 32 0 24.8365592956543 0 16 C 0 7.163440704345703 7.163440704345703 0 16 0 Z"
                                stroke="none" fill="#f3af3d" />
                        </g>
                        <path id="Path_32842" data-name="Path 32842"
                            d="M28.738,30.935a1.185,1.185,0,0,1-1.185-1.185,3.964,3.964,0,0,1,.942-2.613c.089-.095.213-.207.361-.344.735-.658,2.252-2.032,2.252-3.555a2.228,2.228,0,0,0-2.37-2.37,2.228,2.228,0,0,0-2.37,2.37,1.185,1.185,0,1,1-2.37,0,4.592,4.592,0,0,1,4.74-4.74,4.592,4.592,0,0,1,4.74,4.74c0,2.577-2.044,4.432-3.028,5.333l-.284.255a1.89,1.89,0,0,0-.243.948A1.185,1.185,0,0,1,28.738,30.935Zm0,3.561a1.185,1.185,0,0,1-.835-2.026,1.226,1.226,0,0,1,1.671,0,1.061,1.061,0,0,1,.148.184,1.345,1.345,0,0,1,.113.2,1.41,1.41,0,0,1,.065.225,1.138,1.138,0,0,1,0,.462,1.338,1.338,0,0,1-.065.219,1.185,1.185,0,0,1-.113.207,1.06,1.06,0,0,1-.148.184A1.185,1.185,0,0,1,28.738,34.5Z"
                            transform="translate(962.004 400.504)" fill="#f3af3d" />
                    </g>
                </svg>
                <span class="ml-2 text-primary animate-underline-blue">{{ translate('Product Inquiry') }}</span>
            </a>
        </div>
        <div class="col mb-3">
            @if ($detailedProduct->auction_product != 1)
                <div class="d-flex">
                    <!-- Add to wishlist button -->
                    <a href="javascript:void(0)" onclick="addToWishList({{ $detailedProduct->id }})"
                        class="mr-3 fs-14 text-dark opacity-60 has-transitiuon hov-opacity-100">
                        <i class="la la-heart-o mr-1"></i>
                        {{ translate('Add to Wishlist') }}
                    </a>
                    <!-- Add to compare button -->
                    <a href="javascript:void(0)" onclick="addToCompare({{ $detailedProduct->id }})"
                        class="fs-14 text-dark opacity-60 has-transitiuon hov-opacity-100">
                        <i class="las la-sync mr-1"></i>
                        {{ translate('Add to Compare') }}
                    </a>
                </div>
            @endif
        </div>
    </div>


    <!-- Brand Logo & Name -->
    @if ($detailedProduct->brand != null)
        <div class="d-flex flex-wrap align-items-center mb-3">
            <span class="text-secondary fs-14 fw-400 mr-4 w-50px">{{ translate('Brand') }}</span><br>
            <a href="{{ route('products.brand', $detailedProduct->brand->slug) }}"
                class="text-reset hov-text-primary fs-14 fw-700">{{ $detailedProduct->brand->name }}</a>
        </div>
    @endif

    <!-- Seller Info -->
    <div class="d-flex flex-wrap align-items-center">
        <div class="d-flex align-items-center mr-4">
            <!-- Shop Name -->
            @if ($detailedProduct->added_by == 'seller' && get_setting('vendor_system_activation') == 1)
                <span class="text-secondary fs-14 fw-400 mr-4 w-50px">{{ translate('Sold by') }}</span>
                <a href="{{ route('shop.visit', $detailedProduct->user->shop->slug) }}"
                    class="text-reset hov-text-primary fs-14 fw-700">{{ $detailedProduct->user->shop->name }}</a>
            @else
                <p class="mb-0 fs-14 fw-700">{{ translate('Inhouse product') }}</p>
            @endif
        </div>
        <!-- Messase to seller -->
        @if (get_setting('conversation_system') == 1)
            <div class="">
                <button class="btn btn-sm btn-soft-warning btn-outline-warning hov-svg-white hov-text-white rounded-4"
                    onclick="show_chat_modal()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"
                        class="mr-2 has-transition">
                        <g id="Group_23918" data-name="Group 23918" transform="translate(1053.151 256.688)">
                            <path id="Path_3012" data-name="Path 3012"
                                d="M134.849,88.312h-8a2,2,0,0,0-2,2v5a2,2,0,0,0,2,2v3l2.4-3h5.6a2,2,0,0,0,2-2v-5a2,2,0,0,0-2-2m1,7a1,1,0,0,1-1,1h-8a1,1,0,0,1-1-1v-5a1,1,0,0,1,1-1h8a1,1,0,0,1,1,1Z"
                                transform="translate(-1178 -341)" fill="#f4b650" />
                            <path id="Path_3013" data-name="Path 3013"
                                d="M134.849,81.312h8a1,1,0,0,1,1,1v5a1,1,0,0,1-1,1h-.5a.5.5,0,0,0,0,1h.5a2,2,0,0,0,2-2v-5a2,2,0,0,0-2-2h-8a2,2,0,0,0-2,2v.5a.5.5,0,0,0,1,0v-.5a1,1,0,0,1,1-1"
                                transform="translate(-1182 -337)" fill="#f4b650" />
                            <path id="Path_3014" data-name="Path 3014"
                                d="M131.349,93.312h5a.5.5,0,0,1,0,1h-5a.5.5,0,0,1,0-1"
                                transform="translate(-1181 -343.5)" fill="#f4b650" />
                            <path id="Path_3015" data-name="Path 3015"
                                d="M131.349,99.312h5a.5.5,0,1,1,0,1h-5a.5.5,0,1,1,0-1"
                                transform="translate(-1181 -346.5)" fill="#f4b650" />
                        </g>
                    </svg>

                    {{ translate('Message Seller') }}
                </button>
            </div>
        @endif
    </div>

    <hr>

    <!-- For auction product -->
    @if ($detailedProduct->auction_product)
        <div class="row no-gutters mb-3">
            <div class="col-sm-2">
                <div class="text-secondary fs-14 fw-400 mt-1">{{ translate('Auction Will End') }}</div>
            </div>
            <div class="col-sm-10">
                @if ($detailedProduct->auction_end_date > strtotime('now'))
                    <div class="aiz-count-down align-items-center"
                        data-date="{{ date('Y/m/d H:i:s', $detailedProduct->auction_end_date) }}"></div>
                @else
                    <p>{{ translate('Ended') }}</p>
                @endif

            </div>
        </div>

        <div class="row no-gutters mb-3">
            <div class="col-sm-2">
                <div class="text-secondary fs-14 fw-400 mt-1">{{ translate('Starting Bid') }}</div>
            </div>
            <div class="col-sm-10">
                <span class="opacity-50 fs-20">
                    {{ single_price($detailedProduct->starting_bid) }}
                </span>
                @if ($detailedProduct->unit != null)
                    <span class="opacity-70">/{{ $detailedProduct->getTranslation('unit') }}</span>
                @endif
            </div>
        </div>

        @if (Auth::check() &&
                Auth::user()->product_bids->where('product_id', $detailedProduct->id)->first() != null)
            <div class="row no-gutters mb-3">
                <div class="col-sm-2">
                    <div class="text-secondary fs-14 fw-400 mt-1">{{ translate('My Bidded Amount') }}</div>
                </div>
                <div class="col-sm-10">
                    <span class="opacity-50 fs-20">
                        {{ single_price(Auth::user()->product_bids->where('product_id', $detailedProduct->id)->first()->amount) }}
                    </span>
                </div>
            </div>
            <hr>
        @endif

        @php $highest_bid = $detailedProduct->bids->max('amount'); @endphp
        <div class="row no-gutters my-2 mb-3">
            <div class="col-sm-2">
                <div class="text-secondary fs-14 fw-400 mt-1">{{ translate('Highest Bid') }}</div>
            </div>
            <div class="col-sm-10">
                <strong class="h3 fw-600 text-primary">
                    @if ($highest_bid != null)
                        {{ single_price($highest_bid) }}
                    @endif
                </strong>
            </div>
        </div>
    @else
        <!-- Without auction product -->
        @if ($detailedProduct->wholesale_product == 1)
            <!-- Wholesale -->
            <table class="table mb-3">
                <thead>
                    <tr>
                        <th class="border-top-0">{{ translate('Min Qty') }}</th>
                        <th class="border-top-0">{{ translate('Max Qty') }}</th>
                        <th class="border-top-0">{{ translate('Unit Price') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($detailedProduct->stocks->first()->wholesalePrices as $wholesalePrice)
                        <tr>
                            <td>{{ $wholesalePrice->min_qty }}</td>
                            <td>{{ $wholesalePrice->max_qty }}</td>
                            <td>{{ single_price($wholesalePrice->price) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <!-- Without Wholesale -->
            @if (home_price($detailedProduct) != home_discounted_price($detailedProduct))
                <div class="row no-gutters mb-3">
                    <div class="col-sm-2">
                        <div class="text-secondary fs-14 fw-400">{{ translate('Price') }}</div>
                    </div>
                    <div class="col-sm-10">
                        <div class="d-flex align-items-center">
                            <!-- Discount Price -->
                            <strong class="fs-16 fw-700 text-primary">
                                {{ home_discounted_price($detailedProduct) }}
                            </strong>
                            <!-- Home Price -->
                            <del class="fs-14 opacity-60 ml-2">
                                {{ home_price($detailedProduct) }}
                            </del>
                            <!-- Unit -->
                            @if ($detailedProduct->unit != null)
                                <span class="opacity-70 ml-1">/{{ $detailedProduct->getTranslation('unit') }}</span>
                            @endif
                            <!-- Discount percentage -->
                            @if (discount_in_percentage($detailedProduct) > 0)
                                <span class="bg-primary ml-2 fs-11 fw-700 text-white w-35px text-center p-1"
                                    style="padding-top:2px;padding-bottom:2px;">-{{ discount_in_percentage($detailedProduct) }}%</span>
                            @endif
                            <!-- Club Point -->
                            @if (addon_is_activated('club_point') && $detailedProduct->earn_point > 0)
                                <div class="ml-2 bg-warning d-flex justify-content-center align-items-center px-3 py-1"
                                    style="width: fit-content;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12"
                                        viewBox="0 0 12 12">
                                        <g id="Group_23922" data-name="Group 23922" transform="translate(-973 -633)">
                                            <circle id="Ellipse_39" data-name="Ellipse 39" cx="6"
                                                cy="6" r="6" transform="translate(973 633)"
                                                fill="#fff" />
                                            <g id="Group_23920" data-name="Group 23920"
                                                transform="translate(973 633)">
                                                <path id="Path_28698" data-name="Path 28698"
                                                    d="M7.667,3H4.333L3,5,6,9,9,5Z" transform="translate(0 0)"
                                                    fill="#f3af3d" />
                                                <path id="Path_28699" data-name="Path 28699"
                                                    d="M5.33,3h-1L3,5,6,9,4.331,5Z" transform="translate(0 0)"
                                                    fill="#f3af3d" opacity="0.5" />
                                                <path id="Path_28700" data-name="Path 28700"
                                                    d="M12.666,3h1L15,5,12,9l1.664-4Z" transform="translate(-5.995 0)"
                                                    fill="#f3af3d" />
                                            </g>
                                        </g>
                                    </svg>
                                    <small class="fs-11 fw-500 text-white ml-2">{{ translate('Club Point') }}:
                                        {{ $detailedProduct->earn_point }}</small>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @else
                <div class="row no-gutters mb-3">
                    <div class="col-sm-2">
                        <div class="text-secondary fs-14 fw-400">{{ translate('Price') }}</div>
                    </div>
                    <div class="col-sm-10">
                        <div class="d-flex align-items-center">
                            <!-- Discount Price -->
                            <strong class="fs-16 fw-700 text-primary">
                                {{ home_discounted_price($detailedProduct) }}
                            </strong>
                            <!-- Unit -->
                            @if ($detailedProduct->unit != null)
                                <span class="opacity-70">/{{ $detailedProduct->getTranslation('unit') }}</span>
                            @endif
                            <!-- Club Point -->
                            @if (addon_is_activated('club_point') && $detailedProduct->earn_point > 0)
                                <div class="ml-2 bg-warning d-flex justify-content-center align-items-center px-3 py-1"
                                    style="width: fit-content;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12"
                                        viewBox="0 0 12 12">
                                        <g id="Group_23922" data-name="Group 23922" transform="translate(-973 -633)">
                                            <circle id="Ellipse_39" data-name="Ellipse 39" cx="6"
                                                cy="6" r="6" transform="translate(973 633)"
                                                fill="#fff" />
                                            <g id="Group_23920" data-name="Group 23920"
                                                transform="translate(973 633)">
                                                <path id="Path_28698" data-name="Path 28698"
                                                    d="M7.667,3H4.333L3,5,6,9,9,5Z" transform="translate(0 0)"
                                                    fill="#f3af3d" />
                                                <path id="Path_28699" data-name="Path 28699"
                                                    d="M5.33,3h-1L3,5,6,9,4.331,5Z" transform="translate(0 0)"
                                                    fill="#f3af3d" opacity="0.5" />
                                                <path id="Path_28700" data-name="Path 28700"
                                                    d="M12.666,3h1L15,5,12,9l1.664-4Z" transform="translate(-5.995 0)"
                                                    fill="#f3af3d" />
                                            </g>
                                        </g>
                                    </svg>
                                    <small class="fs-11 fw-500 text-white ml-2">{{ translate('Club Point') }}:
                                        {{ $detailedProduct->earn_point }}</small>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        @endif
    @endif

    @if ($detailedProduct->auction_product != 1)
        <form id="option-choice-form">
            @csrf
            <input type="hidden" name="id" value="{{ $detailedProduct->id }}">

            @if ($detailedProduct->digital == 0)
                <!-- Choice Options -->
                <!--@if ($detailedProduct->choice_options != null)-->
                <!--    @foreach (json_decode($detailedProduct->choice_options) as $key => $choice)-->
                <!--        <div class="row no-gutters mb-3">-->
                <!--            <div class="col-sm-2">-->
                <!--                <div class="text-secondary fs-14 fw-400 mt-2 ">-->
                <!--                    {{ \App\Models\Attribute::find($choice->attribute_id)->getTranslation('name') }}-->
                <!--                </div>-->
                <!--            </div>-->
                <!--            <div class="col-sm-10">-->
                <!--                <div class="aiz-radio-inline">-->
                <!--                    @foreach ($choice->values as $key => $value)-->
                <!--                        <label class="aiz-megabox pl-0 mr-2 mb-0">-->
                <!--                            <input type="radio" name="attribute_id_{{ $choice->attribute_id }}"-->
                <!--                                value="{{ $value }}"-->
                <!--                                @if ($key == 0) checked @endif>-->
                <!--                            <span-->
                <!--                                class="aiz-megabox-elem rounded-0 d-flex align-items-center justify-content-center py-1 px-3" onclick="add_lence(this,'{{ $choice->attribute_id }}');">-->
                <!--                                {{ $value }}-->
                <!--                            </span>-->
                <!--                        </label>-->
                <!--                    @endforeach-->
                <!--                </div>-->
                <!--            </div>-->
                <!--        </div>-->
                <!--    @endforeach-->
                <!--@endif-->
                
                @if ($detailedProduct->choice_options != null)
                    @foreach (json_decode($detailedProduct->choice_options) as $key => $choice)
                        <div class="row no-gutters mb-3">
                            <div class="col-sm-2">
                                <div class="text-secondary fs-14 fw-400 mt-2 ">
                                    {{ \App\Models\Attribute::find($choice->attribute_id)->getTranslation('name') }}
                                </div>
                            </div>
                            <div class="col-sm-10">
                                <div class="aiz-radio-inline">
                                    @foreach ($choice->values as $key => $value)
                                        <label class="aiz-megabox pl-0 mr-2 mb-0">
                                            <input type="radio" name="attribute_id_{{ $choice->attribute_id }}"
                                                value="{{ $value }}"
                                                @if ($key == 0) @endif>
                                            <span class="aiz-megabox-elem rounded-0 d-flex align-items-center justify-content-center py-1 px-3" 
                                                onclick="add_lence(this, '{{ $choice->attribute_id }}', '{{ $value }}', {{ json_encode($choice->values) }});">
                                                {{ $value }}
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
                <!-- Color Options -->
                @if (count(json_decode($detailedProduct->colors)) > 0)
                    <div class="row no-gutters mb-3">
                        <div class="col-sm-2">
                            <div class="text-secondary fs-14 fw-400 mt-2">{{ translate('Color') }}</div>
                        </div>
                        <div class="col-sm-10">
                            <div class="aiz-radio-inline">
                                @foreach (json_decode($detailedProduct->colors) as $key => $color)
                                    <label class="aiz-megabox pl-0 mr-2 mb-0" data-toggle="tooltip"
                                        data-title="{{ \App\Models\Color::where('code', $color)->first()->name }}">
                                        <input type="radio" name="color"
                                            value="{{ \App\Models\Color::where('code', $color)->first()->name }}"
                                            @if ($key == 0) checked @endif>
                                        <span
                                            class="aiz-megabox-elem rounded-0 d-flex align-items-center justify-content-center p-1">
                                            <span class="size-25px d-inline-block rounded"
                                                style="background: {{ $color }};"></span>
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Quantity + Add to cart -->
                <div class="row no-gutters mb-3">
                    <div class="col-sm-2">
                        <div class="text-secondary fs-14 fw-400 mt-2">{{ translate('Quantity') }}</div>
                    </div>
                    <div class="col-sm-10">
                        <div class="product-quantity d-flex align-items-center">
                            <div class="row no-gutters align-items-center aiz-plus-minus mr-3" style="width: 130px;">
                                <button class="btn col-auto btn-icon btn-sm btn-light rounded-0" type="button"
                                    data-type="minus" data-field="quantity" disabled="">
                                    <i class="las la-minus"></i>
                                </button>
                                <input type="number" name="quantity"
                                    class="col border-0 text-center flex-grow-1 fs-16 input-number" placeholder="1"
                                    value="{{ $detailedProduct->min_qty }}" min="{{ $detailedProduct->min_qty }}"
                                    max="10" lang="en">
                                <button class="btn col-auto btn-icon btn-sm btn-light rounded-0" type="button"
                                    data-type="plus" data-field="quantity">
                                    <i class="las la-plus"></i>
                                </button>
                            </div>
                            @php
                                $qty = 0;
                                foreach ($detailedProduct->stocks as $key => $stock) {
                                    $qty += $stock->qty;
                                }
                            @endphp
                            <div class="avialable-amount opacity-60">
                                @if ($detailedProduct->stock_visibility_state == 'quantity')
                                    (<span id="available-quantity">{{ $qty }}</span>
                                    {{ translate('available') }})
                                @elseif($detailedProduct->stock_visibility_state == 'text' && $qty >= 1)
                                    (<span id="available-quantity">{{ translate('In Stock') }}</span>)
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

            @endif

            <!-- Total Price -->
            <div class="row no-gutters pb-3 d-none" id="chosen_price_div">
                <div class="col-sm-2">
                    <div class="text-secondary fs-14 fw-400 mt-1">{{ translate('Total Price') }}</div>
                </div>
                <div class="col-sm-10">
                    <div class="product-price">
                        <strong id="chosen_price" class="fs-20 fw-700 text-primary">

                        </strong>
                    </div>
                </div>
            </div>

        </form>
    @endif

    @if ($detailedProduct->auction_product)
        @php
            $highest_bid = $detailedProduct->bids->max('amount');
            $min_bid_amount = $highest_bid != null ? $highest_bid + 1 : $detailedProduct->starting_bid;
        @endphp
        @if ($detailedProduct->auction_end_date >= strtotime('now'))
            <div class="mt-4">
                @if (Auth::check() && $detailedProduct->user_id == Auth::user()->id)
                    <span
                        class="badge badge-inline badge-danger">{{ translate('Seller Can Not Place Bid to His Own Product') }}</span>
                @else
                    <button type="button" class="btn btn-primary buy-now  fw-600 min-w-150px rounded-0"
                        onclick="bid_modal()">
                        <i class="las la-gavel"></i>
                        @if (Auth::check() &&
                                Auth::user()->product_bids->where('product_id', $detailedProduct->id)->first() != null)
                            {{ translate('Change Bid') }}
                        @else
                            {{ translate('Place Bid') }}
                        @endif
                    </button>
                @endif
            </div>
        @endif
    @else
    
<!-- Get Quotation Feature -->
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
    
    <!-- Button trigger modal -->
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
        Get Quotation
    </button>
    
    <!-- Modal -->
    <div class="modal fade show" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" style="z-index: 1040; display: block; padding-left: 0px;" aria-modal="true" role="dialog">
    <!--<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">-->
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background: #91794d;color: white;">
                    <h5 class="modal-title" id="exampleModalLabel">Get Quotation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-header">
                    <!-- Product Name -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <h1 class="mb-4 fs-16 fw-700 text-dark">
                                {{ $detailedProduct->getTranslation('name') }}
                            </h1>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-12">
                            <!-- Review -->
                            @if ($detailedProduct->auction_product != 1)
                                @php
                                    $total = 0;
                                    $total += $detailedProduct->reviews->count();
                                @endphp
                                <!--<span class="rating rating-mr-1">-->
                                <!--    {{ renderStarRating($detailedProduct->rating) }}-->
                                <!--</span>-->
                                <!--<span class="ml-1 opacity-50 fs-14">({{ $total }} {{ translate('reviews') }})</span>-->
                            @endif
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-12">
                            <h1 class="mb-4 fs-16 fw-700 text-dark">
                                <!-- Estimate Shipping Time -->
                                @if ($detailedProduct->est_shipping_days)
                                    <div class="fs-14">
                                        <small class="mr-1 opacity-50 fs-14">{{ translate('Estimate Shipping Time') }}:</small>
                                        <span class="fw-500">{{ $detailedProduct->est_shipping_days }} {{ translate('Days') }}</span>
                                    </div>
                                @endif
                            </h1>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <!-- In stock -->
                        <div class="col-12">
                            @if ($detailedProduct->digital == 1)
                                <span class="badge badge-md badge-inline badge-pill badge-success">{{ translate('In stock') }}</span>
                            @endif
                        </div>
                    </div>

                    <br> 
                   @if ($detailedProduct->auction_product)
                        <div class="row no-gutters mb-3">
                            <div class="col-sm-2">
                                <div class="text-secondary fs-14 fw-400 mt-1">{{ translate('Auction Will End') }}</div>
                            </div>
                            <div class="col-sm-10">
                                @if ($detailedProduct->auction_end_date > strtotime('now'))
                                    <div class="aiz-count-down align-items-center"
                                        data-date="{{ date('Y/m/d H:i:s', $detailedProduct->auction_end_date) }}"></div>
                                @else
                                    <p>{{ translate('Ended') }}</p>
                                @endif
                
                            </div>
                        </div>
                
                        <div class="row no-gutters mb-3">
                            <div class="col-sm-2">
                                <div class="text-secondary fs-14 fw-400 mt-1">{{ translate('Starting Bid') }}</div>
                            </div>
                            <div class="col-sm-10">
                                <span class="opacity-50 fs-20">
                                    {{ single_price($detailedProduct->starting_bid) }}
                                </span>
                                @if ($detailedProduct->unit != null)
                                    <span class="opacity-70">/{{ $detailedProduct->getTranslation('unit') }}</span>
                                @endif
                            </div>
                        </div>
                
                        @if (Auth::check() &&
                                Auth::user()->product_bids->where('product_id', $detailedProduct->id)->first() != null)
                            <div class="row no-gutters mb-3">
                                <div class="col-sm-2">
                                    <div class="text-secondary fs-14 fw-400 mt-1">{{ translate('My Bidded Amount') }}</div>
                                </div>
                                <div class="col-sm-10">
                                    <span class="opacity-50 fs-20">
                                        {{ single_price(Auth::user()->product_bids->where('product_id', $detailedProduct->id)->first()->amount) }}
                                    </span>
                                </div>
                            </div>
                            <hr>
                        @endif
                
                        @php $highest_bid = $detailedProduct->bids->max('amount'); @endphp
                        <div class="row no-gutters my-2 mb-3">
                            <div class="col-sm-2">
                                <div class="text-secondary fs-14 fw-400 mt-1">{{ translate('Highest Bid') }}</div>
                            </div>
                            <div class="col-sm-10">
                                <strong class="h3 fw-600 text-primary">
                                    @if ($highest_bid != null)
                                        {{ single_price($highest_bid) }}
                                    @endif
                                </strong>
                            </div>
                        </div>
                    @else
                        <!-- Without auction product -->
                        @if ($detailedProduct->wholesale_product == 1)
                            <!-- Wholesale -->
                            <table class="table mb-3">
                                <thead>
                                    <tr>
                                        <th class="border-top-0">{{ translate('Min Qty') }}</th>
                                        <th class="border-top-0">{{ translate('Max Qty') }}</th>
                                        <th class="border-top-0">{{ translate('Unit Price') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($detailedProduct->stocks->first()->wholesalePrices as $wholesalePrice)
                                        <tr>
                                            <td>{{ $wholesalePrice->min_qty }}</td>
                                            <td>{{ $wholesalePrice->max_qty }}</td>
                                            <td>{{ single_price($wholesalePrice->price) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <!-- Without Wholesale -->
                            @if (home_price($detailedProduct) != home_discounted_price($detailedProduct))
                                <div class="row no-gutters mb-3">
                                    <div class="col-sm-2">
                                        <div class="text-secondary fs-14 fw-400">{{ translate('Price') }}</div>
                                    </div>
                                    <div class="col-sm-10">
                                        <div class="d-flex align-items-center">
                                            <!-- Discount Price -->
                                            <strong class="fs-16 fw-700 text-primary">
                                                {{ home_discounted_price($detailedProduct) }}
                                            </strong>
                                            <!-- Home Price -->
                                            <del class="fs-14 opacity-60 ml-2">
                                                {{ home_price($detailedProduct) }}
                                            </del>
                                            <!-- Unit -->
                                            @if ($detailedProduct->unit != null)
                                                <span class="opacity-70 ml-1">/{{ $detailedProduct->getTranslation('unit') }}</span>
                                            @endif
                                            <!-- Discount percentage -->
                                            @if (discount_in_percentage($detailedProduct) > 0)
                                                <span class="bg-primary ml-2 fs-11 fw-700 text-white w-35px text-center p-1"
                                                    style="padding-top:2px;padding-bottom:2px;">-{{ discount_in_percentage($detailedProduct) }}%</span>
                                            @endif
                                            <!-- Club Point -->
                                            @if (addon_is_activated('club_point') && $detailedProduct->earn_point > 0)
                                                <div class="ml-2 bg-warning d-flex justify-content-center align-items-center px-3 py-1"
                                                    style="width: fit-content;">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12"
                                                        viewBox="0 0 12 12">
                                                        <g id="Group_23922" data-name="Group 23922" transform="translate(-973 -633)">
                                                            <circle id="Ellipse_39" data-name="Ellipse 39" cx="6"
                                                                cy="6" r="6" transform="translate(973 633)"
                                                                fill="#fff" />
                                                            <g id="Group_23920" data-name="Group 23920"
                                                                transform="translate(973 633)">
                                                                <path id="Path_28698" data-name="Path 28698"
                                                                    d="M7.667,3H4.333L3,5,6,9,9,5Z" transform="translate(0 0)"
                                                                    fill="#f3af3d" />
                                                                <path id="Path_28699" data-name="Path 28699"
                                                                    d="M5.33,3h-1L3,5,6,9,4.331,5Z" transform="translate(0 0)"
                                                                    fill="#f3af3d" opacity="0.5" />
                                                                <path id="Path_28700" data-name="Path 28700"
                                                                    d="M12.666,3h1L15,5,12,9l1.664-4Z" transform="translate(-5.995 0)"
                                                                    fill="#f3af3d" />
                                                            </g>
                                                        </g>
                                                    </svg>
                                                    <small class="fs-11 fw-500 text-white ml-2">{{ translate('Club Point') }}:
                                                        {{ $detailedProduct->earn_point }}</small>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="row no-gutters mb-3">
                                    <div class="col-sm-2">
                                        <div class="text-secondary fs-14 fw-400">{{ translate('Price') }}</div>
                                    </div>
                                    <div class="col-sm-10">
                                        <div class="d-flex align-items-center">
                                            <!-- Discount Price -->
                                            <strong class="fs-16 fw-700 text-primary">
                                                {{ home_discounted_price($detailedProduct) }}
                                            </strong>
                                            <!-- Unit -->
                                            @if ($detailedProduct->unit != null)
                                                <span class="opacity-70">/{{ $detailedProduct->getTranslation('unit') }}</span>
                                            @endif
                                            <!-- Club Point -->
                                            @if (addon_is_activated('club_point') && $detailedProduct->earn_point > 0)
                                                <div class="ml-2 bg-warning d-flex justify-content-center align-items-center px-3 py-1"
                                                    style="width: fit-content;">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12"
                                                        viewBox="0 0 12 12">
                                                        <g id="Group_23922" data-name="Group 23922" transform="translate(-973 -633)">
                                                            <circle id="Ellipse_39" data-name="Ellipse 39" cx="6"
                                                                cy="6" r="6" transform="translate(973 633)"
                                                                fill="#fff" />
                                                            <g id="Group_23920" data-name="Group 23920"
                                                                transform="translate(973 633)">
                                                                <path id="Path_28698" data-name="Path 28698"
                                                                    d="M7.667,3H4.333L3,5,6,9,9,5Z" transform="translate(0 0)"
                                                                    fill="#f3af3d" />
                                                                <path id="Path_28699" data-name="Path 28699"
                                                                    d="M5.33,3h-1L3,5,6,9,4.331,5Z" transform="translate(0 0)"
                                                                    fill="#f3af3d" opacity="0.5" />
                                                                <path id="Path_28700" data-name="Path 28700"
                                                                    d="M12.666,3h1L15,5,12,9l1.664-4Z" transform="translate(-5.995 0)"
                                                                    fill="#f3af3d" />
                                                            </g>
                                                        </g>
                                                    </svg>
                                                    <small class="fs-11 fw-500 text-white ml-2">{{ translate('Club Point') }}:
                                                        {{ $detailedProduct->earn_point }}</small>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endif
                    @endif
                    </div>
                    <div class="modal-body">
                         <div id="checkedValuesContainer">
                             <div class="modal-body">
    
                        </div>
                            </div>
                         <div id="selected-choices-placeholder"></div>
                            <div class="col-sm-12" style="background: #f5e0b89c;margin-bottom: 10px;">
                                <div style="padding: 5px;display: flex;">
                                    <span style="flex: auto;">Girth (In Centimeter)</span>
                                    <span>30</span>
                                </div>
                            </div>
                            <div class="col-sm-12" style="background: #f5e0b89c;margin-bottom: 10px;">
                                <div style="padding: 5px;display: flex;">
                                    <span style="flex: auto;">Kiln Dry processing</span>
                                    <span>30</span>
                                </div>
                            </div>
                            <div class="col-sm-12" style="background: #f5e0b89c;margin-bottom: 10px;">
                                <div style="padding: 5px;display: flex;">
                                    <span style="flex: auto;">Girth (In Diameter)</span>
                                    <span>30</span>
                                </div>
                            </div>
                            <div class="col-sm-12" style="background: #f5e0b89c;margin-bottom: 10px;">
                                <div style="padding: 5px;display: flex;">
                                    <span style="flex: auto;">Sawmilling facitlity for processing</span>
                                    <span>30</span>
                                </div>
                            </div>
                            <div class="col-sm-12" style="background: #f5e0b89c;margin-bottom: 10px;">
                                <div style="padding: 5px;display: flex;">
                                    <span style="flex: auto;">Country Of Origin</span>
                                    <span>30</span>
                                </div>
                            </div>
                            <div class="col-sm-12" style="background: #f5e0b89c;margin-bottom: 10px;">
                                <div style="padding: 5px;display: flex;">
                                    <span style="flex: auto;">Inspection of Material</span>
                                    <span>30</span>
                                </div>
                            </div>
                            <div class="col-sm-12" style="background: #f5e0b89c;margin-bottom: 10px;">
                                <div style="padding: 5px;display: flex;">
                                    <span style="flex: auto;">Quantity</span>
                                    <span>30</span>
                                </div>
                            </div>
                            
                            <div class="col-sm-12">
                                <div style="padding: 5px;display: flex;">
                                    <span class="text-dark fs-6 fw-bold" style="flex: auto;">{{ translate('Quantity') }}</span>
                                    <div class="product-quantity d-flex align-items-center">
                                        <div class="row no-gutters align-items-center aiz-plus-minus" style="width: 130px;">
                                            <button class="btn col-auto btn-icon btn-sm btn-light rounded-0" type="button"
                                                data-type="minus" data-field="quantity" disabled="">
                                                <i class="las la-minus"></i>
                                            </button>
                                            <input type="number" name="quantity"
                                                class="col border-0 text-center flex-grow-1 fs-16 input-number" placeholder="1"
                                                value="{{ $detailedProduct->min_qty }}" min="{{ $detailedProduct->min_qty }}"
                                                max="10" lang="en">
                                            <button class="btn col-auto btn-icon btn-sm btn-light rounded-0" type="button"
                                                data-type="plus" data-field="quantity">
                                                <i class="las la-plus"></i>
                                            </button>
                                        </div>
                                        @php
                                            $qty = 0;
                                            foreach ($detailedProduct->stocks as $key => $stock) {
                                                $qty += $stock->qty;
                                            }
                                        @endphp
                                        
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                    <!--Girth (In Centimeter) <br>-->
                                    <!--Kiln Dry processing <br>-->
                                    <!--Girth (In Diameter)<br>-->
                                    <!--Sawmilling facitlity for processing<br>-->
                                    <!--Country Of Origin<br>-->
                                    <!--Inspection of Material<br>-->
                                    <!--Quantity<br>-->
                            <!--@if ($detailedProduct->choice_options != null)-->
                            <!--    @foreach (json_decode($detailedProduct->choice_options) as $key => $choice)-->
                                                <div id="checkedValuesContainer">
                                                    <!-- Checked values will be displayed here -->
                                                </div>
                                            
                                                <div id="logMessagesContainer">
                                                    <!-- Log messages will be displayed here -->
                                                </div>
                                            
                                                <div class="col-sm-12">
                                                    <!-- Your attribute choice options and radio buttons here -->
                                                </div>
                                    <div class="row no-gutters mb-3">
                                        <div class="col-sm-2">
                                            <div class="text-secondary fs-14 fw-400 mt-2 ">
                                                <!--{{ \App\Models\Attribute::find($choice->attribute_id)->getTranslation('name') }}-->
                                            </div>
                                        </div>
                                        <div class="col-sm-10">
                                            <div class="aiz-radio-inline">
                                                
                                            </div>
                                        </div>
                                    </div>
                            <!--    @endforeach-->
                            <!--@endif-->
                        </div>
                    </div>
                <div class="row no-gutters mb-3">
                    <div class="col-sm-2">
                        <div class="text-secondary fs-14 fw-400 mt-2">{{ translate('Quantity') }}</div>
                    </div>
                    <div class="col-sm-10">
                        <div class="product-quantity d-flex align-items-center">
                            <div class="row no-gutters align-items-center aiz-plus-minus mr-3" style="width: 130px;">
                                <button class="btn col-auto btn-icon btn-sm btn-light rounded-0" type="button"
                                    data-type="minus" data-field="quantity" disabled="">
                                    <i class="las la-minus"></i>
                                </button>
                                <input type="number" name="quantity"
                                    class="col border-0 text-center flex-grow-1 fs-16 input-number" placeholder="1"
                                    value="{{ $detailedProduct->min_qty }}" min="{{ $detailedProduct->min_qty }}"
                                    max="10" lang="en">
                                <button class="btn col-auto btn-icon btn-sm btn-light rounded-0" type="button"
                                    data-type="plus" data-field="quantity">
                                    <i class="las la-plus"></i>
                                </button>
                            </div>
                            @php
                                $qty = 0;
                                foreach ($detailedProduct->stocks as $key => $stock) {
                                    $qty += $stock->qty;
                                }
                            @endphp
                            <!--<div class="avialable-amount opacity-60">-->
                            <!--    @if ($detailedProduct->stock_visibility_state == 'quantity')-->
                            <!--        (<span id="available-quantity">{{ $qty }}</span>-->
                            <!--        {{ translate('available') }})-->
                            <!--    @elseif($detailedProduct->stock_visibility_state == 'text' && $qty >= 1)-->
                            <!--        (<span id="available-quantity">{{ translate('In Stock') }}</span>)-->
                            <!--    @endif-->
                            <!--</div>-->
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    
                </div>
            </div>
        </div>
    </div>

<!-- Quotation-->
    
    
        <!-- Add to cart & Buy now Buttons -->
        <div class="mt-3">
            @if ($detailedProduct->digital == 0)
                @if ($detailedProduct->external_link != null)
                    <a type="button" class="btn btn-primary buy-now fw-600 add-to-cart px-4 rounded-0"
                        href="{{ $detailedProduct->external_link }}">
                        <i class="la la-share"></i> {{ translate($detailedProduct->external_link_btn) }}
                    </a>
                @else
                    <!--<button type="button"-->
                    <!--    class="btn btn-warning mr-2 add-to-cart fw-600 min-w-150px rounded-0 text-white"-->
                    <!--    @if (Auth::check()) onclick="addToCart()" @else onclick="showLoginModal()" @endif>-->
                    <!--    <i class="las la-shopping-bag"></i>-->
                    <!--    <span class="d-none d-md-inline-block"> {{ translate('Add to cart') }}</span>-->
                    <!--</button>-->
                    <!--<button type="button" class="btn btn-primary buy-now fw-600 add-to-cart min-w-150px rounded-0"-->
                    <!--    @if (Auth::check()) onclick="buyNow()" @else onclick="showLoginModal()" @endif>-->
                    <!--    <i class="la la-shopping-cart"></i> {{ translate('Buy Now') }}-->
                    <!--</button>-->
                @endif
                <button type="button" class="btn btn-secondary out-of-stock fw-600 d-none" disabled>
                    <i class="la la-cart-arrow-down"></i> {{ translate('Out of Stock') }}
                </button>
            @elseif ($detailedProduct->digital == 1)
                <button type="button"
                    class="btn btn-warning mr-2 add-to-cart fw-600 min-w-150px rounded-0 text-white"
                    @if (Auth::check()) onclick="addToCart()" @else onclick="showLoginModal()" @endif>
                    <i class="las la-shopping-bag"></i>
                    <span class="d-none d-md-inline-block"> {{ translate('Add to cart') }}</span>
                </button>
                <button type="button" class="btn btn-primary buy-now fw-600 add-to-cart min-w-150px rounded-0"
                    @if (Auth::check()) onclick="buyNow()" @else onclick="showLoginModal()" @endif>
                    <i class="la la-shopping-cart"></i> {{ translate('Buy Now') }}
                </button>
            @endif
        </div>

        <!-- Promote Link -->
        <div class="d-table width-100 mt-3">
            <div class="d-table-cell">
                @if (Auth::check() &&
                        addon_is_activated('affiliate_system') &&
                        (\App\Models\AffiliateOption::where('type', 'product_sharing')->first()->status ||
                            \App\Models\AffiliateOption::where('type', 'category_wise_affiliate')->first()->status) &&
                        Auth::user()->affiliate_user != null &&
                        Auth::user()->affiliate_user->status)
                    @php
                        if (Auth::check()) {
                            if (Auth::user()->referral_code == null) {
                                Auth::user()->referral_code = substr(Auth::user()->id . Str::random(10), 0, 10);
                                Auth::user()->save();
                            }
                            $referral_code = Auth::user()->referral_code;
                            $referral_code_url = URL::to('/product') . '/' . $detailedProduct->slug . "?product_referral_code=$referral_code";
                        }
                    @endphp
                    <div>
                        <button type="button" id="ref-cpurl-btn" class="btn btn-secondary w-200px rounded-0"
                            data-attrcpy="{{ translate('Copied') }}" onclick="CopyToClipboard(this)"
                            data-url="{{ $referral_code_url }}">{{ translate('Copy the Promote Link') }}</button>
                    </div>
                @endif
            </div>
        </div>

        <!-- Refund -->
        @php
            $refund_sticker = get_setting('refund_sticker');
        @endphp
        @if (addon_is_activated('refund_request'))
            <div class="row no-gutters mt-3">
                <div class="col-sm-2">
                    <div class="text-secondary fs-14 fw-400 mt-2">{{ translate('Refund') }}</div>
                </div>
                <div class="col-sm-10">
                    @if ($detailedProduct->refundable == 1)
                        <a href="{{ route('returnpolicy') }}" target="_blank">
                            @if ($refund_sticker != null)
                                <img src="{{ uploaded_asset($refund_sticker) }}" height="36">
                            @else
                                <img src="{{ static_asset('assets/img/refund-sticker.jpg') }}" height="36">
                            @endif
                        </a>
                        <a href="{{ route('returnpolicy') }}" class="text-blue hov-text-primary fs-14 ml-3"
                            target="_blank">{{ translate('View Policy') }}</a>
                    @else
                        <div class="text-dark fs-14 fw-400 mt-2">{{ translate('Not Applicable') }}</div>
                    @endif
                </div>
            </div>
        @endif

        <!-- Seller Guarantees -->
        @if ($detailedProduct->digital == 1)
            @if ($detailedProduct->added_by == 'seller')
                <div class="row no-gutters mt-3">
                    <div class="col-2">
                        <div class="text-secondary fs-14 fw-400">{{ translate('Seller Guarantees') }}</div>
                    </div>
                    <div class="col-10">
                        @if ($detailedProduct->user->shop->verification_status == 1)
                            <span class="text-success fs-14 fw-700">{{ translate('Verified seller') }}</span>
                        @else
                            <span class="text-danger fs-14 fw-700">{{ translate('Non verified seller') }}</span>
                        @endif
                    </div>
                </div>
            @endif
        @endif
    @endif

    <!-- Share -->
    <div class="row no-gutters mt-4">
        <div class="col-sm-2">
            <div class="text-secondary fs-14 fw-400 mt-2">{{ translate('Share') }}</div>
        </div>
        <div class="col-sm-10">
            <div class="aiz-share"></div>
        </div>
    </div>
</div>

<script>
function add_lence(element, id, value) {
    $('#attribute_id').val(id);
    $('#selected_value').val(value);

    var selectedValue = $('#selected_value').val();
    console.log(selectedValue);

    var radioInput = $(element).find('input[type="radio"]');
    if (radioInput.prop('checked')) {
        addCheckedValue(value);
        updateLogMessagesSection(generateLogMessage(value));
    } else {
        removeCheckedValue(value);
        updateLogMessagesSection('' + value);
    }
}

function updateLogMessagesSection(logMessage) {
    var logMessagesSection = $('#logMessagesContainer');
    logMessagesSection.append('<p>' + logMessage + '</p>');
}
</script>
<script>
var checkedValues = [];
var checkedCount = 1;

function addCheckedValue(value) {
    if (!checkedValues.includes(value)) {
        checkedValues.push(value);
    }
}

function removeCheckedValue(value) {
    var index = checkedValues.indexOf(value);
    if (index !== -1) {
        checkedValues.splice(index, 1);
    }
}

function generateLogMessage(value) {
    return 'checked_value' + checkedCount++ + ': ' + value;
}
</script>


