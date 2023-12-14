@extends('frontend.layouts.user_panel')

@section('panel_content')
    <div class="aiz-titlebar mb-4">
      <div class="row align-items-center">
          <div class="col-md-6">
              <h5 class="fs-20 fw-700 text-dark">{{ translate('Quotation')}}</h5>
              <p class="fs-14 fw-400 text-secondary">{{ translate('Select a Quotation conversation to view all Quotation')}}</p>
          </div>
      </div>
    </div>

    @if (count($quotations) > 0)
    <div class="p-0">
        <ul class="list-group list-group-flush p-0">
            @foreach ($quotations as $key => $quotation)
                <li class="list-group-item p-4 has-transition hov-bg-light border mb-3">
                    <div class="row gutters-10">
                        <!-- Receiver/Shop Image -->
                        <div class="col-auto">
                            <div class="media">
                                @php
                                    $product = \App\Models\Product::where('id', $quotation->product_id)->first();
                                @endphp
                                <span class="avatar avatar-sm flex-shrink-0 border">
                                    <img src="{{uploaded_asset($product->thumbnail_img)}}" onerror="this.onerror=null;this.src='https://timberbuddy.digitalbrain.co.in/public/assets/img/avatar-place.png';">
                                </span>
                            </div>
                        </div>
                        <!-- Receiver/Shop Name & Time -->
                        <div class="col-auto col-lg-3">
                            @php
                                $user = App\Models\User::where('id', $quotation->customer_id)->pluck('name');
                            @endphp
                            <a href="{{ route('quotation.show', $quotation->id) }}" class="text-reset hov-text-primary fw-700 fs-14">{{ $quotation->product_name }}</a>
                            <br>
                            <small class="text-secondary fs-12">
                               {{ \Carbon\Carbon::parse($quotation->updated_at)->format('d.m.Y h:i') }}
                            </small>
                        </div>
                        
                    </div>
                </li>
            @endforeach
        </ul>
    </div>
    <div class="mt-4">
        {{ $quotations->links() }}
    </div>
    @else
        <div class="row">
            <div class="col">
                <div class="text-center bg-white p-4 border">
                    <img class="mw-100 h-200px" src="{{ static_asset('assets/img/nothing.svg') }}" alt="Image">
                    <h5 class="mb-0 h5 mt-3">{{ translate("There isn't anything added yet")}}</h5>
                </div>
            </div>
        </div>
    @endif
@endsection