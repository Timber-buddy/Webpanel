@extends('seller.layouts.app')
@section('panel_content')
<div class="card">

        <div class="card-header row gutters-5">
            <div class="col text-center text-md-left">
                <h5 class="mb-md-0 h6">{{ translate('Quotations') }}</h5>
            </div>
        </div>
</div>
    @php
        $product = \App\Models\Product::where('id', $quotation->product_id)->first();
        $customer = \App\Models\User::where('id', $quotation->customer_id)->first();
    @endphp
<div class="card-body">
    <div class="row">
        <div class="col-md-4">
            <div>
                <div class="row p-2">
                    <span class="text-dark fs-15 fw-500">Customer Name</span>
                </div>
                <div class="row p-1">
                    <span class="fs-15">{{ $customer->name }}</span>
                </div>
            </div>
            @if(!empty($customer->address))
            <div>
                <div class="row p-2">
                    <span class="text-dark fs-15 fw-500">Customer Address</span><br>
                </div>
                <div class="row p-1">
                    <span class="fs-15"> {{ $customer->address }}</span>
                </div>
            </div>
            @endif
            <div>
                <div class="row p-2">
                    <span class="text-dark fs-15 fw-500">Customer Email</span><br>
                </div>
                 <div class="row p-1">
                    <span class="fs-15"> {{ $customer->email }}</span>
                </div>
            </div>
            <div>
                <div class="row p-2">
                    <span class="text-dark fs-15 fw-500">Customer Phone</span><br>
                </div>
                <div class="row p-1">
                    <span class="fs-15"> {{ $customer->phone }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div>
                <div class="row p-2">
                    <span class="text-dark fs-15 fw-500">Product Name </span><br>
                </div>
                <div class="row p-1">
                    <span class="fs-15"> {{ $quotation->product_name }} </span>
                </div>
            </div>
            <div>
                <div class="row p-2">
                    <span class="text-dark fs-15 fw-500">Discounted Price </span><br>
                </div>
                <div class="row p-1">
                    <span class="fs-15"> {{ $quotation->discounted_price }} </span>
                </div>
            </div>
            @foreach($quotation->attributes as $index => $attribute)
                @if(!empty($attribute->attribute) && !empty($quotation->attributes_data[$index]->attribute_data))
                    <div>
                        <div class="row p-2">
                            <span class="text-dark fs-15 fw-500">
                                {{ $attribute->attribute }}
                            </span>
                        </div>
                        <div class="row p-1">
                            <span class="fs-15">
                                {{ $quotation->attributes_data[$index]->attribute_data }}
                            </span>
                        </div>
                    </div>
                @endif
            @endforeach
            <div>
                <div class="row p-2">
                    <span class="text-dark fs-15 fw-500">Qty: {{ $quotation->quantity }}</span><br>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <img src="{{ uploaded_asset($product->thumbnail_img) }}" width="200" height="150">
        </div>
    </div>
</div>

    </div>
        @php
            $quotations = \App\Models\QuotationMessage::where('quotation_id', $quotation->id)->get();
        @endphp
        @if(!empty($quotations))
            @foreach($quotations as $key => $data)
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item px-0">
                            <div class="media mb-2">
                                <div class="media-body">
                                    <h6 class="mb-0 fw-600">
                                        @php
                                            $user = \App\Models\User::where('id', $data->user_id)->first();
                                        @endphp
                                        @if(auth()->check() && $user->id === auth()->user()->id)
                                            <span class="float-left">
                                                <br>
                                                {{ $user->name }}
                                                <p class="opacity-50">{{ $data->created_at->format('F j, Y g:i A') }}</p>
                                                {{ $data->message }}
                                            </span>
                                            <hr>
                                            </h6>
                                        @else
                                            <span class="float-right">
                                                <br>
                                                {{ $user->name }}
                                                <p class="opacity-50">{{ $data->created_at->format('F j, Y g:i A') }}</p>
                                                {{ $data->message }}
                                            </span>
                                            <hr>
                                            </h6>
                                        @endif
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            @endforeach
        @endif

        @if (checkSellerPermission('quotation_reply'))
        <form class="pt-4" action="{{ route('seller.quotation.mail') }}" method="POST">
            @csrf
            <input type="hidden" name="quotation_id" value="{{ $quotation->id }}">
            <input type="hidden" name="customer_name" value="{{ $customer->name }}">
            <input type="hidden" name="product_name" value="{{ $quotation->product_name }}">
            <div class="form-group">
                <textarea class="form-control" rows="4" name="message" placeholder="{{ translate('Type your reply') }}" required></textarea>
            </div>
            <div class="form-group mb-0 text-right">
                <button type="submit" class="btn btn-primary">{{ translate('Send') }}</button>
            </div>
        </form>
        @endif
    </div>
</div>
@endsection
