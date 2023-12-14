@extends('frontend.layouts.user_panel')

@section('panel_content')
    <div class="aiz-titlebar mb-4">
        <div class="h6 fw-700">
            <span>{{ translate('Conversations With ') }}</span>
            <a href="#" class="">{{$user[0]}}</a>
        </div>
    </div>
    <div class="card rounded-0 shadow-none border">
        <div class="card-header bg-light">
            <div>
                <!-- Conversation title -->
                <h5 class="card-title fs-14 fw-700 mb-1"></h5>
                <!-- Conversation Woth -->
                <p class="mb-0 fs-14 text-secondary fw-400">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" class="mr-2">
                        <g id="Group_24976" data-name="Group 24976" transform="translate(1053.151 256.688)">
                            <path id="Path_3012" data-name="Path 3012"
                                d="M134.849,88.312h-8a2,2,0,0,0-2,2v5a2,2,0,0,0,2,2v3l2.4-3h5.6a2,2,0,0,0,2-2v-5a2,2,0,0,0-2-2m1,7a1,1,0,0,1-1,1h-8a1,1,0,0,1-1-1v-5a1,1,0,0,1,1-1h8a1,1,0,0,1,1,1Z"
                                transform="translate(-1178 -341)" fill="#b5b5bf" />
                            <path id="Path_3013" data-name="Path 3013"
                                d="M134.849,81.312h8a1,1,0,0,1,1,1v5a1,1,0,0,1-1,1h-.5a.5.5,0,0,0,0,1h.5a2,2,0,0,0,2-2v-5a2,2,0,0,0-2-2h-8a2,2,0,0,0-2,2v.5a.5.5,0,0,0,1,0v-.5a1,1,0,0,1,1-1"
                                transform="translate(-1182 -337)" fill="#b5b5bf" />
                            <path id="Path_3014" data-name="Path 3014"
                                d="M131.349,93.312h5a.5.5,0,0,1,0,1h-5a.5.5,0,0,1,0-1" transform="translate(-1181 -343.5)"
                                fill="#b5b5bf" />
                            <path id="Path_3015" data-name="Path 3015"
                                d="M131.349,99.312h5a.5.5,0,1,1,0,1h-5a.5.5,0,1,1,0-1" transform="translate(-1181 -346.5)"
                                fill="#b5b5bf" />
                        </g>
                    </svg>
                    {{ translate('Between you and') }}
                    {{ $user[0] }}
                </p>
            </div>
        </div>
        <div class="card-body">
            <!---->
            @php
                $product = \App\Models\Product::where('id', $quotation->product_id)->first();
                $customer = \App\Models\User::where('id', $quotation->customer_id)->first();
            @endphp
            <div class="row">
                <div class="col-md-4">
                    <div>
                        <div class="row p-2">
                            <span class="text-dark fs-15 fw-500">Customer Name</span><br>
                            <span>{{ $customer->name }}</span>
                        </div>
                        <div class="row p-2">
                            <span class="text-dark fs-15 fw-500">Customer Address</span><br>
                            <span>{{ $customer->address }}</span>
                        </div>
                        <div class="row p-2">
                            <span class="text-dark fs-15 fw-500">Customer Email</span><br>
                            <span>{{ $customer->email }}</span>
                        </div>
                        <div class="row p-2">
                            <span class="text-dark fs-15 fw-500">Customer Phone</span><br>
                            <span>{{ $customer->phone }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div>
                        <div class="row p-2">
                            <span class="text-dark fs-15 fw-500">Product Name</span><br>
                            <span>{{ $quotation->product_name }}</span>
                        </div>
                        <div class="row p-2">
                            <span class="text-dark fs-15 fw-500">Discounted Price</span><br>
                            <span>{{ $quotation->discounted_price }}</span>
                        </div>
                        @foreach($quotation->attributes as $index => $attribute)
                            @if(!empty($attribute->attribute) && !empty($quotation->attributes_data[$index]->attribute_data))
                                <div class="row p-2">
                                    <span class="text-dark fs-15 fw-500">{{ $attribute->attribute }}</span><br>
                                    <span>{{ $quotation->attributes_data[$index]->attribute_data }}</span>
                                </div>
                            @endif
                        @endforeach
                        <div class="row p-2">
                            <span class="text-dark fs-15 fw-500">Qty</span><br>
                            <span>{{ $quotation->quantity }}</span>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <img src="{{ uploaded_asset($product->thumbnail_img) }}" width="200" height="150">
                </div>
</div>
            <!---->
            <!-- Conversations -->
                 <ul class="list-group list-group-flush">
               @foreach ($messages as $message)
                    <li class="list-group-item px-0">
                        <div class="media mb-2">
                            <div class="media-body">
                                <h6 class="mb-0 fw-600 mb-2">
                                    @php
                                        $user = App\Models\User::where('id', $message->user_id)->pluck('name');
                                        $users= App\Models\User::where('id', $message->user_id)->first();
                                    @endphp
                                     @if(auth()->check() && $users->id === auth()->user()->id)
                                            <span class="float-left">
                                                <br>
                            <img class="avatar avatar-sm mr-3float-left" src="{{uploaded_asset($users->avatar_original)}}"
                                onerror="this.onerror=null;this.src='https://timberbuddy.digitalbrain.co.in/public/assets/img/avatar-place.png';">
                                                {{ $user[0] }}
                                                <p class="opacity-50">
                                    {{ date('d.m.Y h:i:m', strtotime($message->created_at)) }}
                                </p>
                                 {{ $message->message }}
                                                </span>
                                            <hr>
                                            </h6>
                                        @else
                                            <span class="float-right">
                                                <br>
                                                <img class="avatar avatar-sm mr-3 float-right" src="{{uploaded_asset($users->avatar_original)}}"
                                onerror="this.onerror=null;this.src='https://timberbuddy.digitalbrain.co.in/public/assets/img/avatar-place.png';">
                                                {{ $user[0] }}
                                                <p class="opacity-50">
                                    {{ date('d.m.Y h:i:m', strtotime($message->created_at)) }}
                                </p>
                                 {{ $message->message }}
                                                </span>
                                            <hr>
                                            </h6>
                                        @endif
                                    
                                </h6>
                                <!--<p class="fs-12 text-secondary">-->
                                <!--    {{ date('d.m.Y h:i:m', strtotime($message->created_at)) }}-->
                                <!--</p>-->
                            </div>
                        </div>
                            <!--<p class="fs-14 fw-400">-->
                            <!--    {{ $message->message }}-->
                            <!--</p>-->
                    </li>
                @endforeach
            </ul>
            <!-- Send message -->
            <form class="pt-4" action="{{ route('quotation.message') }}" method="POST">
                @csrf
                <input type="hidden" name="quotation_id" value="{{ $quotation->id }}">
                <div class="form-group">
                    <textarea class="form-control rounded-0" rows="4" name="message" placeholder="{{ translate('Type your replys') }}" required></textarea>
                </div>
                <div class="form-group mb-0 text-right">
                    <button type="submit" class="btn btn-primary rounded-0 w-150px">{{ translate('Sends') }}</button>
                </div>
            </form>
        </div>
    </div>
@endsection
