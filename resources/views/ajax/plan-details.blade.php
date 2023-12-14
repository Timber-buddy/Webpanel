<div class="row">
    <div class="col-md-2">
        <img src="{{asset('public/'.$subscription->image)}}" alt="" class="img-fluid" style="max-height: 100px;">
    </div>
    <div class="col-md-10">
        <h6>{{$subscription->title}}</h6>
        <p>{{$subscription->description}}</p>
        <div class="row mb-1">
            <div class="col-md-6">
                Product Limit : {{$subscription->product_limit}}
                <input type="hidden" id="selected_plan_product_limit" value="{{$subscription->product_limit}}">
            </div>
            <div class="col-md-6">
                Amount : ₹{{number_format($subscription->price, 2)}}
                <input type="hidden" id="selected_plan_amount" value="{{$subscription->price}}">
            </div>
        </div>
    </div>
</div>