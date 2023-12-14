<div class="modal-body">
  <div class="text-center">
      <span class="avatar avatar-xxl mb-3">
          <img src="{{ uploaded_asset($shop->user->avatar_original) }}" onerror="this.onerror=null;this.src='{{ static_asset('assets/img/avatar-place.png') }}';">
      </span>
      <h1 class="h5 mb-1">{{ $shop->user->name }}</h1>
      <p class="text-sm text-muted">{{ $shop->name }}</p>

      <div class="pad-ver btn-groups">
          <a href="{{ $shop->facebook }}" class="btn btn-icon demo-pli-facebook icon-lg add-tooltip" data-original-title="Facebook" data-container="body"></a>
          <a href="{{ $shop->twitter }}" class="btn btn-icon demo-pli-twitter icon-lg add-tooltip" data-original-title="Twitter" data-container="body"></a>
          <a href="{{ $shop->google }}" class="btn btn-icon demo-pli-google-plus icon-lg add-tooltip" data-original-title="Google+" data-container="body"></a>
      </div>
  </div>
  <hr>

  <!-- Profile Details -->
  <h6 class="mb-4">{{translate('About')}} {{ $shop->user->name }}</h6>
  <p><i class="demo-pli-map-marker-2 icon-lg icon-fw mr-1"></i>{{ $shop->address }}</p>
  <p>
    <i class="demo-pli-map-marker-2 icon-lg icon-fw mr-1"></i>{{translate('GSTIN')}} : {{ $shop->gst_number }}
    &emsp;
    @if($shop->gst_number_status != "Verified")
      @if(is_null($shop->gst_number_status))
        <span id="gst_number_verify_status"></span>
      @else
        <span id="gst_number_verify_status">
          <svg xmlns="http://www.w3.org/2000/svg" width="17.5" height="17.5" viewBox="0 0 17.5 17.5">
            <g id="Group_25616" data-name="Group 25616" transform="translate(-537.249 -1042.75)">
                <path id="Union_5" data-name="Union 5" d="M0,8.75A8.75,8.75,0,1,1,8.75,17.5,8.75,8.75,0,0,1,0,8.75Zm.876,0A7.875,7.875,0,1,0,8.75.875,7.883,7.883,0,0,0,.876,8.75Zm.875,0a7,7,0,1,1,7,7A7.008,7.008,0,0,1,1.751,8.751Zm3.73-.907a.789.789,0,0,0,0,1.115l2.23,2.23a.788.788,0,0,0,1.115,0l3.717-3.717a.789.789,0,0,0,0-1.115.788.788,0,0,0-1.115,0l-3.16,3.16L6.6,7.844a.788.788,0,0,0-1.115,0Z" transform="translate(537.249 1042.75)" fill="red"></path>
            </g>
          </svg>
          {{translate($shop->gst_number_status)}}
        </span>
      @endif
      <div class="text-center" id="auto-load" style="display: none;">
        <svg version="1.1" id="L9" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" height="60" viewBox="0 0 100 100" enable-background="new 0 0 0 0" xml:space="preserve">
          <path fill="#000" d="M73,50c0-12.7-10.3-23-23-23S27,37.3,27,50 M30.9,50c0-10.5,8.5-19.1,19.1-19.1S69.1,39.5,69.1,50">
            <animateTransform attributeName="transform" attributeType="XML" type="rotate" dur="1s" from="0 50 50" to="360 50 50" repeatCount="indefinite" />
          </path>
        </svg>
      </div>
      <button id="gst-verify-btn" class="btn btn-primary" style="padding: 0.208rem 0.5rem;" onclick="verifyGstNumber('{{ $shop->gst_number }}', {{ $shop->id }})">Verify</button>
    @else
    <span id="gst_number_verify_status">
      <svg xmlns="http://www.w3.org/2000/svg" width="17.5" height="17.5" viewBox="0 0 17.5 17.5">
        <g id="Group_25616" data-name="Group 25616" transform="translate(-537.249 -1042.75)">
            <path id="Union_5" data-name="Union 5" d="M0,8.75A8.75,8.75,0,1,1,8.75,17.5,8.75,8.75,0,0,1,0,8.75Zm.876,0A7.875,7.875,0,1,0,8.75.875,7.883,7.883,0,0,0,.876,8.75Zm.875,0a7,7,0,1,1,7,7A7.008,7.008,0,0,1,1.751,8.751Zm3.73-.907a.789.789,0,0,0,0,1.115l2.23,2.23a.788.788,0,0,0,1.115,0l3.717-3.717a.789.789,0,0,0,0-1.115.788.788,0,0,0-1.115,0l-3.16,3.16L6.6,7.844a.788.788,0,0,0-1.115,0Z" transform="translate(537.249 1042.75)" fill="#3490f3"></path>
        </g>
      </svg>
      {{translate($shop->gst_number_status)}}
    </span>
    @endif
  </p>

@if(!is_null($subscription))
  <div class="row mb-3">
    <div class="col-2">
        <img src="{{asset('public/'.$subscription->plan->image)}}" alt="" class="img-fluid" style="max-height: 100px;">
    </div>
    <div class="col-10">
        <h6>{{$subscription->plan->title}}</h6>
        <p>{{$subscription->plan->description}}</p>
        <div class="row mb-2">
            <div class="col-6">
                Product Limit :<br> {{$subscription->plan->product_limit}}
            </div>
            <div class="col-6">
                Amount :<br> ₹{{number_format($subscription->amount, 2)}}
                @if($subscription->status == 'P')
                    <span style="width: fit-content !important;" class="badge badge-warning">Pending</span>
                @elseif($subscription->status == 'C')
                    <span style="width: fit-content !important;" class="badge badge-primary">Captured</span>
                @elseif($subscription->status == 'S')
                    <span style="width: fit-content !important;" class="badge badge-success">Success</span>
                @else
                    <span style="width: fit-content !important;" class="badge badge-danger">Failed</span>
                @endif
            </div>
        </div>
        <div class="row">
            <div class="col-6">
                Purchase On :<br> {{date('d F, Y', strtotime($subscription->purchase_at))}}
            </div>
            <div class="col-6">
                Valid Upto :<br> {{date('d F, Y', strtotime($subscription->valid_upto))}}
            </div>
        </div>
    </div>
</div>
@endif

  <p><a href="{{ route('shop.visit', $shop->slug) }}" class="btn-link"><i class="demo-pli-internet icon-lg icon-fw mr-1"></i>{{ $shop->name }}</a></p>
  <p><i class="demo-pli-old-telephone icon-lg icon-fw mr-1"></i>{{ $shop->user->phone }}</p>

    <!--
  <h6 class="mb-4">{{translate('Payout Info')}}</h6>
  <p>{{translate('Bank Name')}} : {{ $shop->bank_name }}</p>
  <p>{{translate('Bank Acc Name')}} : {{ $shop->bank_acc_name }}</p>
  <p>{{translate('Bank Acc Number')}} : {{ $shop->bank_acc_no }}</p>
  <p>{{translate('Bank Routing Number')}} : {{ $shop->bank_routing_no }}</p>
    -->
  <br>

  <div class="table-responsive">
      <table class="table table-striped mar-no">
          <tbody>
          <tr>
              <td>{{ translate('Total Products') }}</td>
              <td>{{ App\Models\Product::where('user_id', $shop->user->id)->get()->count() }}</td>
          </tr>
          <!--
          <tr>
              <td>{{ translate('Total Orders') }}</td>
              <td>{{ App\Models\OrderDetail::where('seller_id', $shop->user->id)->get()->count() }}</td>
          </tr>
          <tr>
              <td>{{ translate('Total Sold Amount') }}</td>
              @php
                  $orderDetails = \App\Models\OrderDetail::where('seller_id', $shop->user->id)->get();
                  $total = 0;
                  foreach ($orderDetails as $key => $orderDetail) {
                      if($orderDetail->order != null && $orderDetail->order->payment_status == 'paid'){
                          $total += $orderDetail->price;
                      }
                  }
              @endphp
              <td>{{ single_price($total) }}</td>
          </tr>
          <tr>
              <td>{{ translate('Wallet Balance') }}</td>
              <td>{{ single_price($shop->user->balance) }}</td>
          </tr>
          -->
          </tbody>
      </table>
  </div>
</div>
