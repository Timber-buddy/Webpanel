@extends('seller.layouts.app')

@section('panel_content')
<div class="aiz-titlebar mt-2 mb-4">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="h3">{{ translate('Add Your Product') }}</h1>
        </div>
    </div>
</div>

<!-- Error Meassages -->
@if(session('errors'))
    <div class="alert alert-danger">
        <ul>
            @foreach(session('errors') as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif


<form class="" onsubmit="return validateForm()" action="{{ route('seller.products.store') }}" method="POST" enctype="multipart/form-data" id="choice_form">
    <div class="row gutters-5">
        <div class="col-lg-8">
            @csrf
            <input type="hidden" name="added_by" value="seller">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 h6">{{ translate('Product Information') }}</h5>
                </div>
                <div class="card-body">

                    <!--Product Name-->
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">
                            <b>{{ translate('Product Name') }}</b>
                            <span class="text-danger">*</span>
                        </label>
                        <div class="col-md-8">
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" placeholder="{{ translate('Product Names') }}" value="{{ old('name') }}" onchange="update_sku()">
                            <span id="nameError" class="text-danger"></span>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!--Category-->
                    <div class="form-group row" id="category">
                        <label class="col-md-3 col-from-label"><b>{{ translate('Category') }}</b>
                            <span class="text-danger">*</span></label>
                        <div class="col-md-8">
                            <select class="form-control aiz-selectpicker" name="category_id" id="category_id" data-live-search="true">
                                <option value="">{{ translate('Select Category') }}</option>
                                @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->getTranslation('name') }}</option>
                                @foreach ($category->childrenCategories as $childCategory)
                                @include('categories.child_category', [
                                'child_category' => $childCategory,
                                ])
                                @endforeach
                                @endforeach
                            </select>
                            <span id="categoryError" class="text-danger"></span>
                        </div>
                    </div>

                    <!--Unit-->
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label"><b>{{ translate('Unit') }}</b>
                            <span class="text-danger">*</span></label>
                        <div class="col-md-8">
                            <input type="text" class="form-control" id="unit" name="unit" placeholder="{{ translate('Unit (e.g. KG, Pc etc)') }}" value="{{ old('unit') }}">
                            <span id="unitError" class="text-danger"></span>
                        </div>
                    </div>

                    <!--Minimum Purchase Qty-->
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label"><b>{{ translate('Minimum Purchase Qty') }}</b>
                            <span class="text-danger">*</span></label>
                        <div class="col-md-8">
                            <input type="number" lang="en" class="form-control" name="min_qty" value="1" min="1" required>
                        </div>
                    </div>

                    <!--Tags-->
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">{{ translate('Tags') }} <span class="text-danger">*</span></label>
                        <div class="col-md-8">
                            <input type="text" class="form-control aiz-tag-input" id="tags" name="tags" placeholder="{{ translate('Type and hit enter to add a tag') }}" value="{{ old('tags') }}">
                            <span id="tagsError" class="text-danger"></span>
                        </div>
                    </div>

                    <!--Brand-->
                    <div class="form-group row" id="brand">
                        <label class="col-md-3 col-from-label">{{ translate('Brand') }}</label>
                        <div class="col-md-8">
                            <select class="form-control aiz-selectpicker" name="brand_id" id="brand_id" data-live-search="true">
                                <option value="">{{ translate('Select Brand') }}</option>
                                @foreach (\App\Models\Brand::all() as $brand)
                                <option value="{{ $brand->id }}">{{ $brand->getTranslation('name') }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!--Weight-->
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">{{ translate('Weight') }}
                            <small>({{ translate('In Kg') }})</small></label>
                        <div class="col-md-8">
                            <input type="number" class="form-control" name="weight" step="0.01" value="0.00" placeholder="0.00">
                        </div>
                    </div>

                    <!--
                        @if (addon_is_activated('pos_system'))
                            <div class="form-group row">
                                <label class="col-md-3 col-from-label">{{ translate('Barcode') }}</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="barcode"
                                        placeholder="{{ translate('Barcode') }}">
                                </div>
                            </div>
                        @endif
                        @if (addon_is_activated('refund_request'))
                            <div class="form-group row">
                                <label class="col-md-3 col-from-label">{{ translate('Refundable') }}</label>
                                <div class="col-md-8">
                                    <label class="aiz-switch aiz-switch-success mb-0">
                                        <input type="checkbox" name="refundable" checked value="1">
                                        <span></span>
                                    </label>
                                </div>
                            </div>
                        @endif
                    -->
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 h6">{{ translate('Product Images') }}</h5>
                </div>
                <div class="card-body">

                    <!--Gallery Images-->
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label" for="signinSrEmail"><b>{{ translate('Gallery Images') }}</b>
                            <span class="text-danger">*</span></label>
                        <div class="col-md-8">
                            <div class="input-group" data-toggle="aizuploader" data-type="image" data-multiple="true">
                                <div class="input-group-prepend">
                                    <div class="input-group-text bg-soft-secondary font-weight-medium">
                                        {{ translate('Browse') }}
                                    </div>
                                </div>
                                <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                <input type="hidden" name="photos" id="photo" class="selected-files" required>
                            </div>
                            <div class="file-preview box sm">
                            </div>
                            <span id="photoError" class="text-danger"></span>
                        </div>
                    </div>

                    <!--Thumbnail Image-->
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label" for="signinSrEmail"><b>{{ translate('Thumbnail Image') }}</b>
                            <span class="text-danger">*</span>
                            <small>(290x300)</small></label>
                        <div class="col-md-8">
                            <div class="input-group" data-toggle="aizuploader" data-type="image">
                                <div class="input-group-prepend">
                                    <div class="input-group-text bg-soft-secondary font-weight-medium">
                                        {{ translate('Browse') }}
                                    </div>
                                </div>
                                <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                <input type="hidden" name="thumbnail_img" id="thumbnail" class="selected-files" required>
                            </div>
                            <div class="file-preview box sm">
                            </div>
                            <span id="thumbnailError" class="text-danger"></span>
                        </div>
                    </div>

                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 h6">{{ translate('Product Variation') }}</h5>
                </div>
                <div class="card-body">

                    <!--Colors-->
                    <div class="form-group row">
                        <div class="col-md-3">
                            <input type="text" class="form-control" value="{{ translate('Colors') }}" disabled>
                        </div>
                        <div class="col-md-8">
                            <select class="form-control aiz-selectpicker" data-live-search="true" name="colors[]" data-selected-text-format="count" id="colors" multiple disabled>
                                @foreach (\App\Models\Color::orderBy('name', 'asc')->get() as $key => $color)
                                <option value="{{ $color->code }}" data-content="<span><span class='size-15px d-inline-block mr-2 rounded border' style='background:{{ $color->code }}'></span><span>{{ $color->name }}</span></span>">
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-1">
                            <label class="aiz-switch aiz-switch-success mb-0">
                                <input value="1" type="checkbox" name="colors_active">
                                <span></span>
                            </label>
                        </div>
                    </div>

                    <div class="form-group row">

                        <!--Attributes-->
                        <div class="col-md-3">
                            <input type="text" class="form-control" value="{{ translate('Attributes') }}" disabled>
                        </div>
                        <div class="col-md-8">
                            <select name="choice_attributes[]" id="choice_attributes" class="form-control aiz-selectpicker" data-live-search="true" data-selected-text-format="count" multiple data-placeholder="{{ translate('Choose Attributes') }}">
                                @foreach (\App\Models\Attribute::all() as $key => $attribute)
                                <option value="{{ $attribute->id }}">{{ $attribute->getTranslation('name') }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div>
                        <p>{{ translate('Choose the attributes of this product and then input values of each attribute') }}
                        </p>
                        <br>
                    </div>

                    <div class="customer_choice_options" id="customer_choice_options">

                    </div>
                </div>
            </div>

                        <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 h6">{{ translate('Product price + stock') }}</h5>
                </div>
                <div class="card-body">

                    <!-- Unit price -->
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label"><b>{{ translate('Unit price') }}</b>
                            <span class="text-danger">*</span></label>
                        <div class="col-md-6">
                            <input type="number" lang="en" min="0" value="" step="0.01" placeholder="{{ translate('Unit price') }}" name="unit_price" id="unit_price"class="form-control">
                            <span id="unit_priceError" class="text-danger"></span>
                        </div>
                    </div>

                    <!-- Quantity -->
                    <div id="show-hide-div">
                        <div class="form-group row">
                            <label class="col-md-3 col-from-label"><b>{{ translate('Quantity') }}</b>
                                <span class="text-danger">*</span></label>
                            <div class="col-md-6">
                                <input type="number" lang="en" min="0" value="1" step="1" placeholder="{{ translate('Quantity') }}" name="current_stock" class="form-control" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">
                                {{ translate('SKU') }}
                            </label>
                            <div class="col-md-6">
                                <input type="text" placeholder="{{ translate('SKU') }}" name="sku" class="form-control">
                            </div>
                        </div>
                    </div>

                    <!-- Discount Date Range  -->
                    <div class="form-group row">
                        <label class="col-md-3 control-label" for="start_date">{{ translate('Discount Date Range') }}</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control aiz-date-range" name="date_range" placeholder="{{ translate('Select Date') }}" data-time-picker="true" data-format="DD-MM-Y HH:mm:ss" data-separator=" to " autocomplete="off">
                        </div>
                    </div>

                    <!-- Discount -->
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">{{ translate('Discount') }} <span class="text-danger">*</span></label></label>
                        <div class="col-md-6">
                            <input type="number" lang="en" min="0" value="0" step="0.01" placeholder="{{ translate('Discount') }}" name="discount" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <select class="form-control aiz-selectpicker" name="discount_type">
                                <option value="amount">{{ translate('Flat') }}</option>
                                <option value="percent">{{ translate('Percent') }}</option>
                            </select>
                        </div>
                    </div>

                    <!-- External link -->
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">
                            {{ translate('External link') }}
                        </label>
                        <div class="col-md-9">
                            <input type="text" placeholder="{{ translate('External link') }}" name="external_link" class="form-control">
                            <small class="text-muted">{{ translate('Leave it blank if you do not use external site link') }}</small>
                        </div>
                    </div>

                    <!-- External link button text -->
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">
                            {{ translate('External link button text') }}
                        </label>
                        <div class="col-md-9">
                            <input type="text" placeholder="{{ translate('External link button text') }}" name="external_link_btn" class="form-control">
                            <small class="text-muted">{{ translate('Leave it blank if you do not use external site link') }}</small>
                        </div>
                    </div>

                    <br>

                    <div class="sku_combination" id="sku_combination">

                    </div>
                </div>
            </div>
            <div class="card">
                <!-- Description -->
                <div class="card-header">
                    <h5 class="mb-0 h6">{{ translate('Product Description') }}</h5>
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label"><b>{{ translate('Description') }}</b><span class="text-danger">*</span></label>
                        <div class="col-md-8">
                            <textarea class="aiz-text-editor" name="description" id="description">{{ old('description') }}</textarea>
                            <span id="descriptionError" class="text-danger"></span>
                        </div>
                         @error('description')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 h6">{{ translate('Product Videos') }}</h5>
                </div>
                <div class="card-body">

                    <!-- Video Provider-->
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">{{ translate('Video Provider') }}</label>
                        <div class="col-md-8">
                            <select class="form-control aiz-selectpicker" name="video_provider" id="video_provider">
                                <option value="">{{ translate('Nothing selected') }}</option>
                                <option value="youtube">{{ translate('Youtube') }}</option>
                                <option value="dailymotion">{{ translate('Dailymotion') }}</option>
                                <option value="vimeo">{{ translate('Vimeo') }}</option>
                            </select>
                        </div>
                    </div>

                    <!--Video Link  -->
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">{{ translate('Video Link') }}</label>
                        <div class="col-md-8">
                            <input type="text" class="form-control" name="video_link" id="video_link" placeholder="{{ translate('Video Link') }}">
                            <span id="video_linkError" class="text-danger"></span>
                        </div>
                    </div>

                </div>
            </div>



            <!-- PDF Specification -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 h6">{{ translate('PDF Specification') }}</h5>
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label" for="signinSrEmail">{{ translate('PDF Specification') }}</label>
                        <div class="col-md-8">
                            <div class="input-group" data-toggle="aizuploader" data-type="document">
                                <div class="input-group-prepend">
                                    <div class="input-group-text bg-soft-secondary font-weight-medium">
                                        {{ translate('Browse') }}
                                    </div>
                                </div>
                                <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                <input type="hidden" name="pdf" class="selected-files">
                            </div>
                            <div class="file-preview box sm">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SEO Meta Tags -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 h6">{{ translate('SEO Meta Tags') }}</h5>
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">{{ translate('Meta Title') }}</label>
                        <div class="col-md-8">
                            <input type="text" class="form-control" name="meta_title" placeholder="{{ translate('Meta Title') }}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">{{ translate('Description') }}</label>
                        <div class="col-md-8">
                            <textarea name="meta_description" rows="8" class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label" for="signinSrEmail">{{ translate('Meta Image') }}</label>
                        <div class="col-md-8">
                            <div class="input-group" data-toggle="aizuploader" data-type="image">
                                <div class="input-group-prepend">
                                    <div class="input-group-text bg-soft-secondary font-weight-medium">
                                        {{ translate('Browse') }}
                                    </div>
                                </div>
                                <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                <input type="hidden" name="meta_img" class="selected-files">
                            </div>
                            <div class="file-preview box sm">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">

            <!-- Shipping Configuration -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 h6">
                        {{ translate('Shipping Configuration') }}
                    </h5>
                </div>

                <div class="card-body">
                    @if (get_setting('shipping_type') == 'product_wise_shipping')
                    <div class="form-group row">
                        <label class="col-md-6 col-from-label">{{ translate('Free Shipping') }}</label>
                        <div class="col-md-6">
                            <label class="aiz-switch aiz-switch-success mb-0">
                                <input type="radio" name="shipping_type" value="free" checked>
                                <span></span>
                            </label>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-6 col-from-label">{{ translate('Flat Rate') }}</label>
                        <div class="col-md-6">
                            <label class="aiz-switch aiz-switch-success mb-0">
                                <input type="radio" name="shipping_type" value="flat_rate">
                                <span></span>
                            </label>
                        </div>
                    </div>

                    <div class="flat_rate_shipping_div" style="display: none">
                        <div class="form-group row">
                            <label class="col-md-6 col-from-label">{{ translate('Shipping cost') }}</label>
                            <div class="col-md-6">
                                <input type="number" lang="en" min="0" value="0" step="0.01" placeholder="{{ translate('Shipping cost') }}" name="flat_shipping_cost" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    @else
                    <p>
                        {{ translate('Shipping configuration is maintained by Admin.') }}
                    </p>
                    @endif

                </div>
            </div>

            <!-- Low Stock Quantity Warning -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 h6">{{ translate('Low Stock Quantity Warning') }}</h5>
                </div>
                <div class="card-body">
                    <div class="form-group mb-3">
                        <label for="name">
                            {{ translate('Quantity') }}
                        </label>
                        <input type="number" name="low_stock_quantity" value="1" min="0" step="1" class="form-control">
                    </div>
                </div>
            </div>

            <!-- Stock Visibility State -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 h6">
                        {{ translate('Stock Visibility State') }}
                    </h5>
                </div>

                <div class="card-body">

                    <div class="form-group row">
                        <label class="col-md-6 col-from-label">{{ translate('Show Stock Quantity') }}</label>
                        <div class="col-md-6">
                            <label class="aiz-switch aiz-switch-success mb-0">
                                <input type="radio" name="stock_visibility_state" value="quantity" checked>
                                <span></span>
                            </label>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-6 col-from-label">{{ translate('Show Stock With Text Only') }}</label>
                        <div class="col-md-6">
                            <label class="aiz-switch aiz-switch-success mb-0">
                                <input type="radio" name="stock_visibility_state" value="text">
                                <span></span>
                            </label>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-6 col-from-label">{{ translate('Hide Stock') }}</label>
                        <div class="col-md-6">
                            <label class="aiz-switch aiz-switch-success mb-0">
                                <input type="radio" name="stock_visibility_state" value="hide">
                                <span></span>
                            </label>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Cash On Delivery -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 h6">{{ translate('Cash On Delivery') }}</h5>
                </div>
                <div class="card-body">
                    @if (get_setting('cash_payment') == '1')
                    <div class="form-group row">
                        <label class="col-md-6 col-from-label">{{ translate('Status') }}</label>
                        <div class="col-md-6">
                            <label class="aiz-switch aiz-switch-success mb-0">
                                <input type="checkbox" name="cash_on_delivery" value="1" checked="">
                                <span></span>
                            </label>
                        </div>
                    </div>
                    @else
                    <p>
                        {{ translate('Cash On Delivery activation is maintained by Admin.') }}
                    </p>
                    @endif
                </div>
            </div>

            <!-- Estimate Shipping Time -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 h6">{{ translate('Estimate Shipping Time') }}</h5>
                </div>
                <div class="card-body">
                    <div class="form-group mb-3">
                        <label for="name">
                            {{ translate('Shipping Days') }}
                        </label>
                        <div class="input-group">
                            <input type="number" class="form-control" name="est_shipping_days" min="1" step="1" placeholder="{{ translate('Shipping Days') }}">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="inputGroupPrepend">{{ translate('Days') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!--
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 h6"><b>{{ translate('VAT & Tax') }}</b>
                                  <span class="text-danger">*</span></h5>
                    </div>
                    <div class="card-body">
                        @foreach (\App\Models\Tax::where('tax_status', 1)->get() as $tax)
                            <label for="name">
                                {{ $tax->name }}
                                <input type="hidden" value="{{ $tax->id }}" name="tax_id[]">
                            </label>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <input type="number" lang="en" min="0" value="0" step="0.01"
                                        placeholder="{{ translate('Tax') }}" name="tax[]" class="form-control"
                                        required>
                                </div>
                                <div class="form-group col-md-6">
                                    <select class="form-control aiz-selectpicker" name="tax_type[]">
                                        <option value="amount">{{ translate('Flat') }}</option>
                                        <option value="percent">{{ translate('Percent') }}</option>
                                    </select>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                -->

        </div>

        <div class="col-12">
            <div class="mar-all text-right mb-2">
                <button type="submit" name="button" value="publish" class="btn btn-primary">{{ translate('Upload Product') }}</button>
            </div>
        </div>
    </div>

</form>
@endsection

@section('script')

@section('script')
    <script type="text/javascript">
            function validateForm() {
            // Reset error messages
            document.getElementById('nameError').textContent = '';
            document.getElementById('photoError').textContent = '';
            document.getElementById('thumbnailError').textContent = '';
            document.getElementById('tagsError').textContent = '';
            document.getElementById('unit_priceError').textContent = '';
            // document.getElementById('choseError').textContent = '';
            document.getElementById('descriptionError').textContent = '';
            document.getElementById('unitError').textContent = '';
            document.getElementById('video_linkError').textContent = '';
            document.getElementById('categoryError').textContent = '';

            // Get form values
            var name = document.getElementById('name').value;
            var photo = document.getElementById('photo').value;
            var thumbnail = document.getElementById('thumbnail').value;
            var tags = document.getElementById('tags').value;
            var unit_price = document.getElementById('unit_price').value;
            var video_provider = document.getElementById('video_provider').value;
            // var choice_attributes = document.getElementById('choice_attributes').value;
            var description = document.getElementById('description').value;
            var category_id = document.getElementById('category_id').value;
            var unit = document.getElementById('unit').value;


            // Validate name
            if (name === '') {
                document.getElementById('nameError').textContent = 'Name is required';
                AIZ.plugins.notify('danger', '{{ translate('Name is required') }}');
                return false;
            }
            if (category_id === '') {
                document.getElementById('categoryError').textContent = 'Category is required';
                AIZ.plugins.notify('danger', '{{ translate('Category is required') }}');
                return false;
            }
            if (unit === '') {
                document.getElementById('unitError').textContent = 'Unit is required';
                AIZ.plugins.notify('danger', '{{ translate('Unit is required') }}');
                return false;
            }
            if (tags === '') {
                document.getElementById('tagsError').textContent = 'Tags is required';
                AIZ.plugins.notify('danger', '{{ translate('Tags is required') }}');
                return false;
            }

            // // Validate Photo
            if (photo === '') {
                document.getElementById('photoError').textContent = 'Gallery Images is required';
                AIZ.plugins.notify('danger', '{{ translate('Gallery Images is required') }}');
                return false;
            }
            // // Validate thumbnail
            if (thumbnail === '') {
                document.getElementById('thumbnailError').textContent = 'Thumbnail Image is required';
                AIZ.plugins.notify('danger', '{{ translate('Thumbnail Image is required') }}');
                return false;
            }

            if (video_provider != '') {
                var video_link = document.getElementById('video_link').value;
                if (video_link === '') {
                    document.getElementById('video_linkError').textContent = 'Video Link is required';
                    AIZ.plugins.notify('danger', '{{ translate('Video Link is required') }}');
                    return false;
                }
            }

            // // if (choice_attributes != '') {
            // //     var choice = document.getElementById('choice_id').value;
            // //     if (choice === '') {
            // //         document.getElementById('choseError').textContent = 'Please Select Options Is required';
            // //         AIZ.plugins.notify('danger', '{{ translate('Please Select Options Is required') }}');
            // //         return false;
            // //     }
            // // }


            if (unit_price === '') {
                document.getElementById('unit_priceError').textContent = 'Unit Price is required';
                AIZ.plugins.notify('danger', '{{ translate('Unit Price is required') }}');
                return false;
            }
            if (description === '') {
                document.getElementById('descriptionError').textContent = 'Description is required';
                AIZ.plugins.notify('danger', '{{ translate('Description is required') }}');
                return false;
            }

            // Form is valid
            // alert('Form submitted successfully!');
            return true;
        }


        $("[name=shipping_type]").on("change", function() {
            $(".product_wise_shipping_div").hide();
            $(".flat_rate_shipping_div").hide();
            if ($(this).val() == 'product_wise') {
                $(".product_wise_shipping_div").show();
            }
            if ($(this).val() == 'flat_rate') {
                $(".flat_rate_shipping_div").show();
            }

        });

        function add_more_customer_choice_option(i, name) {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: '{{ route('seller.products.add-more-choice-option') }}',
                data: {
                    attribute_id: i
                },
                success: function(data) {
                    var obj = JSON.parse(data);
                    $('#customer_choice_options').append('\
                        <div class="form-group row">\
                            <div class="col-md-3">\
                                <input type="hidden" name="choice_no[]" value="' + i + '">\
                                <input type="text" class="form-control" name="choice[]" value="' + name +
                        '" placeholder="{{ translate('Choice Title') }}" readonly>\
                            </div>\
                            <div class="col-md-8">\
                                <select class="form-control aiz-selectpicker attribute_choice" data-live-search="true" name="choice_options_' + i + '[]" multiple required>\
                                    ' + obj + '\
                                </select>\
                            </div>\
                        </div>');
                    AIZ.plugins.bootstrapSelect('refresh');
                }
            });


        }

        $('input[name="colors_active"]').on('change', function() {
            if (!$('input[name="colors_active"]').is(':checked')) {
                $('#colors').prop('disabled', true);
                AIZ.plugins.bootstrapSelect('refresh');
            } else {
                $('#colors').prop('disabled', false);
                AIZ.plugins.bootstrapSelect('refresh');
            }
            update_sku();
        });

        $(document).on("change", ".attribute_choice", function() {
            update_sku();
        });

        $('#colors').on('change', function() {
            update_sku();
        });

        $('input[name="unit_price"]').on('keyup', function() {
            update_sku();
        });

        // $('input[name="name"]').on('keyup', function() {
        //     update_sku();
        // });

        function delete_row(em) {
            $(em).closest('.form-group row').remove();
            update_sku();
        }

        function delete_variant(em) {
            $(em).closest('.variant').remove();
        }

        function update_sku() {
            $.ajax({
                type: "POST",
                url: '{{ route('seller.products.sku_combination') }}',
                data: $('#choice_form').serialize(),
                success: function(data) {
                    $('#sku_combination').html(data);
                    AIZ.plugins.fooTable();
                    if (data.length > 1) {
                        $('#show-hide-div').hide();
                    } else {
                        $('#show-hide-div').show();
                    }
                }
            });
        }

        // $('#choice_attributes').on('change', function() {
        //     $('#customer_choice_options').html(null);
        //     $.each($("#choice_attributes option:selected"), function() {
        //         add_more_customer_choice_option($(this).val(), $(this).text());
        //     });
        //     update_sku();
        // });
        var timeout;

        $('#choice_attributes').on('change', function() {
            clearTimeout(timeout);

            timeout = setTimeout(function() {
                updateAfterDelay();
            }, 2000);
        });

        function updateAfterDelay() {
            $('#customer_choice_options').html(null);

            $.each($("#choice_attributes option:selected"), function() {
                add_more_customer_choice_option($(this).val(), $(this).text());
            });

            update_sku();
        }





$(document).on('change', '[name=country_id]', function() {
    var country_id = $(this).val();
    get_states(country_id);
});

$(document).on('change', '[name=state_id]', function() {
    var state_id = $(this).val();
    get_city(state_id);
});

function get_states(country_id) {
    $('[name="state"]').html("");
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: "{{ route('get-state') }}",
        type: 'POST',
        data: {
            country_id: country_id
        },
        success: function(response) {
            var obj = JSON.parse(response);
            if (obj != '') {
                $('[name="state_id"]').html(obj);
                AIZ.plugins.bootstrapSelect('refresh');
            }
        }
    });
}

function get_city(state_id) {
    $('[name="city"]').html("");
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: "{{ route('get-city') }}",
        type: 'POST',
        data: {
            state_id: state_id
        },
        success: function(response) {
            var obj = JSON.parse(response);
            if (obj != '') {
                $('[name="city_id[]"]').html(obj);
                AIZ.plugins.bootstrapSelect('refresh');
            }
        }
    });
}

    </script>
@endsection

@endsection
