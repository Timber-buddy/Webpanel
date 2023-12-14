@extends('seller.layouts.app')

@section('panel_content')
<style>
        input[type="checkbox"]:disabled + .slider {
        background-color: #ccc; /* Change the background color to a grayish tone */
        cursor: not-allowed; /* Change the cursor to indicate it's not clickable */
        pointer-events: none; /* Prevent click events on the label */
    }
</style>
    <div class="aiz-titlebar mt-2 mb-4">
      <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="h3">{{ translate('Products') }}</h1>
        </div>
      </div>
    </div>

    <div class="row gutters-10 justify-content-center mb-3">
        @if(checkSellerPermission('add_new_product'))
            @if(!is_null($subscription) || !empty($subscription))
                <div class="col-md-4" >
                    <div class="bg-grad-1 text-white rounded-lg overflow-hidden">
                      <span class="size-30px rounded-circle mx-auto bg-soft-primary d-flex align-items-center justify-content-center mt-3">
                          <i class="las la-upload la-2x text-white"></i>
                      </span>
                      <div class="px-3 pt-3 pb-3">
                          <div class="h4 fw-700 text-center">{{ max(0, $subscription->plan->product_limit - $product_count) }}</div>
                          <div class="opacity-50 text-center">{{  translate('Remaining Publish Products') }}</div>
                      </div>
                    </div>
                </div>
            @endif

            <div class="col-md-4" >
                <a href="{{ route('seller.products.create')}}">
                  <div class="p-3 rounded mb-3 c-pointer text-center bg-white shadow-sm hov-shadow-lg has-transition">
                        <span class="size-60px rounded-circle mx-auto bg-secondary d-flex align-items-center justify-content-center mb-3">
                            <i class="las la-plus la-3x text-white"></i>
                        </span>
                      <div class="fs-18 text-primary">{{ translate('Add New Product') }}</div>
                  </div>
                </a>
            </div>
        @endif

        <div class="col-md-4">
            @if(Auth::user()->user_type == "seller")
                <a href="{{ route('seller.profile.index') }}" class="text-center bg-white shadow-sm hov-shadow-lg text-center d-block p-3 rounded">
                    @if($subscription != null)
                        <img src="{{ asset('public/'.$subscription->plan->image) }}" height="44" class="mw-100 mx-auto">
                        <span class="d-block sub-title mb-2">{{ translate('Current Package')}}: {{ $subscription->plan->title }}</span>
                    @else
                        <i class="la la-frown-o mb-2 la-3x"></i>
                        <div class="d-block sub-title mb-2">{{ translate('No Package Found')}}</div>
                    @endif
                    <div class="btn btn-outline-primary py-1">{{ translate('Upgrade Package')}}</div>
                </a>
            @else
                @if($subscription != null)
                    <img src="{{ asset('public/'.$subscription->plan->image) }}" height="44" class="mw-100 mx-auto">
                    <span class="d-block sub-title mb-2">{{ translate('Current Package')}}: {{ $subscription->plan->title }}</span>
                @else
                    <i class="la la-frown-o mb-2 la-3x"></i>
                    <div class="d-block sub-title mb-2">{{ translate('No Package Found')}}</div>
                @endif
            @endif
        </div>
    </div>

    <div class="card">
        <form class="" id="sort_products" action="" method="GET">
            <div class="card-header row gutters-5">
                <div class="col">
                    <h5 class="mb-md-0 h6">{{ translate('All Products') }}</h5>
                </div>

                @if(checkSellerPermission('product_delete'))
                    <div class="dropdown mb-2 mb-md-0">
                        <button class="btn border dropdown-toggle" type="button" data-toggle="dropdown">
                            {{translate('Bulk Action')}}
                        </button>
                        <div class="dropdown-menu dropdown-menu-right">
                            @if(checkSellerPermission('product_delete'))
                                <a class="dropdown-item" href="#" onclick="bulk_delete()"> {{translate('Delete selection')}}</a>
                            @endif
                        </div>
                    </div>
                @endif
                <div class="col-md-4">
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control" id="search" name="search" @isset($search) value="{{ $search }}" @endisset placeholder="{{ translate('Search product') }}">
                    </div>
                </div>
            </div>
            <div class="card-body">
                <table class="table aiz-table mb-0">
                    <thead>
                        <tr>
                            <th>
                                <div class="form-group">
                                    <div class="aiz-checkbox-inline">
                                        <label class="aiz-checkbox">
                                            <input type="checkbox" class="check-all">
                                            <span class="aiz-square-check"></span>
                                        </label>
                                    </div>
                                </div>
                            </th>
                            <th width="30%">{{ translate('Name')}}</th>
                            <th data-breakpoints="md">{{ translate('Category')}}</th>
                            <th data-breakpoints="md">{{ translate('Current Qty')}}</th>
                            <th>{{ translate('Base Price')}}</th>
                            @if(get_setting('product_approve_by_admin') == 1)
                                <th data-breakpoints="md">{{ translate('Approval')}}</th>
                            @endif
                            <th data-breakpoints="md">{{ translate('Published')}}</th>
                            <th data-breakpoints="md">{{ translate('Featured')}}</th>
                            <th data-breakpoints="md">{{ translate('Featured By Admin')}}</th>
                            <th data-breakpoints="md" class="text-right">{{ translate('Options')}}</th>
                        </tr>
                    </thead>

                    <tbody>
                        {{-- @dump($products[0]) --}}
                        @foreach ($products as $key => $product)
                            <tr>
                                <td>
                                    <div class="form-group d-inline-block">
                                        <label class="aiz-checkbox">
                                            <input type="checkbox" class="check-one" name="id[]" value="{{$product->id}}">
                                            <span class="aiz-square-check"></span>
                                        </label>
                                    </div>
                                </td>
                                <td>
                                    <a href="{{ route('product', $product->slug) }}" target="_blank" class="text-reset">
                                        {{ $product->getTranslation('name') }}
                                    </a>
                                </td>
                                <td>
                                    @if ($product->category != null)
                                        {{ $product->category->getTranslation('name') }}
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $qty = 0;
                                        foreach ($product->stocks as $key => $stock) {
                                            $qty += $stock->qty;
                                        }
                                        echo $qty;
                                    @endphp
                                </td>
                                <td>{{ $product->unit_price }}</td>
                                @if(get_setting('product_approve_by_admin') == 1)
                                    <td>
                                        @if ($product->approved == 1)
                                            <span class="badge badge-inline badge-success">{{ translate('Approved')}}</span>
                                        @else
                                            <span class="badge badge-inline badge-info">{{ translate('Pending')}}</span>
                                        @endif
                                    </td>
                                @endif
                                <td>
                                    <label class="aiz-switch aiz-switch-success mb-0">
                                        <input onchange="update_published(this)" value="{{ $product->id }}" type="checkbox" <?php if($product->published == 1) echo "checked";?> >
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="aiz-switch aiz-switch-success mb-0">
                                        <input onchange="update_featured(this)" value="{{ $product->id }}" type="checkbox" <?php if($product->seller_featured == 1) echo "checked";?> >
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                                <td>
                                    @if($product->featured == 1)
                                        <span class="badge badge-inline badge-success">Yes</span>
                                    @else
                                        <span class="badge badge-inline badge-danger">No</span>
                                    @endif
                                </td>
                                <td class="text-right">
                                    @if(checkSellerPermission('product_edit'))
                                        <a class="btn btn-soft-info btn-icon btn-circle btn-sm" href="{{route('seller.products.edit', ['id'=>$product->id, 'lang'=>env('DEFAULT_LANGUAGE')])}}" title="{{ translate('Edit') }}">
                                            <i class="las la-edit"></i>
                                        </a>
                                    @endif
                                    @if(checkSellerPermission('product_duplicate'))
                                        <a href="{{route('seller.products.duplicate', $product->id)}}" class="btn btn-soft-success btn-icon btn-circle btn-sm"  title="{{ translate('Duplicate') }}">
                                            <i class="las la-copy"></i>
                                        </a>
                                    @endif
                                    @if(checkSellerPermission('product_delete'))
                                        <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" data-href="{{route('seller.products.destroy', $product->id)}}" title="{{ translate('Delete') }}">
                                            <i class="las la-trash"></i>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="aiz-pagination">
                    {{ $products->links() }}
                </div>
            </div>
        </form>
    </div>

@endsection

@section('modal')
    @include('modals.delete_modal')
@endsection

@section('script')
    <script type="text/javascript">

        $(document).on("change", ".check-all", function() {
            if(this.checked) {
                // Iterate each checkbox
                $('.check-one:checkbox').each(function() {
                    this.checked = true;
                });
            } else {
                $('.check-one:checkbox').each(function() {
                    this.checked = false;
                });
            }

        });

        function update_featured(el){
            if(el.checked){
                var status = 1;
            }
            else{
                var status = 0;
            }
            $.post('{{ route('seller.products.featured') }}', {_token:'{{ csrf_token() }}', id:el.value, status:status}, function(data){
                if(data == 1){
                    AIZ.plugins.notify('success', '{{ translate('Featured products updated successfully') }}');
                }
                else{
                    AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                    location.reload();
                }
            });
        }

        function update_published(el){
            if(el.checked){
                var status = 1;
            }
            else{
                var status = 0;
            }
            $.post('{{ route('seller.products.published') }}', {_token:'{{ csrf_token() }}', id:el.value, status:status}, function(data){
                if(data == 1){
                    AIZ.plugins.notify('success', '{{ translate('Published products updated successfully') }}');
                }
                else if(data == 2){
                    AIZ.plugins.notify('danger', '{{ translate('Please upgrade your package.') }}');
                    // location.reload();
                    setTimeout(function(){ window.location.reload(); }, 3000);
                }
                else{
                    AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                    // location.reload();
                    setTimeout(function(){ window.location.reload(); }, 3000);
                }
            });
        }

        function bulk_delete() {
            var data = new FormData($('#sort_products')[0]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{route('seller.products.bulk-delete')}}",
                type: 'POST',
                data: data,
                cache: false,
                contentType: false,
                processData: false,
                success: function (response) {
                    if(response == 1) {
                        location.reload();
                    }
                }
            });
        }

    </script>
@endsection
