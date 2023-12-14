@extends('seller.layouts.app')

@section('panel_content')
    <div class="card">
        <form id="sort_orders" action="" method="GET">
            <div class="card-header row gutters-5">
                <div class="col text-center text-md-left">
                    <h5 class="mb-md-0 h6">{{ translate('Quotations') }}</h5>
                </div>
            </div>
        </form>
    </div>
    <div class="mt-4">
        <table class="table table-bordered">
            <thead>
                <th>Sr No.</th>
                <th>Customer Name</th>
                <th>Product Name</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Action</th>
            </thead>
            <tbody>
                @foreach ($quotations as $key => $quotation)
                    <tr>
                        @php
                            $customer = \App\Models\User::where('id', $quotation->customer_id)->first();
                            $product = \App\Models\Product::where('id', $quotation->product_id)->first();
                        @endphp
                        <td>{{ ++$key }}</td>
                        <td>{{ is_null($customer) ? 'User Deleted' : $customer->name }}</td>
                        <td>{{ is_null($product) ? 'Product Deleted' : $product->name }}</td>
                        <td>{{ $quotation->quantity }}</td>
                        <td>Rs. {{ $quotation->discounted_price }}</td>
                        <td>
                            @if (!is_null($customer))
                                @if (checkSellerPermission('quotation_details'))
                                    <a href="{{ route('seller.quotation.view', encrypt($quotation->id)) }}"
                                        class="btn btn-soft-primary btn-icon btn-circle btn-sm"
                                        title="{{ translate('View') }}">
                                        <i class="las la-eye"></i>
                                    </a>
                                @endif
                                @if (checkSellerPermission('quotation_delete'))
                                    <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete"
                                        onclick="deleteRecord(`{{ route('seller.quotation.destroy', $quotation->id) }}`)"
                                        title="{{ translate('Delete') }}">
                                        <i class="las la-trash"></i>
                                    </a>
                                @endif
                            @endif
                        </td>
                        <td>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="modal fade" id="confirm-delete">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title h6">{{ translate('Confirmation') }}</h5>
                    <button type="button" class="close" data-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>{{ translate('Do you really want to delete this subscription plan?') }}</p>
                </div>
                <div class="modal-footer">
                    <form action="" method="post" id="confirmationdelete">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-light"
                            data-dismiss="modal">{{ translate('Cancel') }}</button>
                        <button type="submit" name="btn" class="btn btn-primary">{{ translate('Proceed!') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        function deleteRecord(url) {
            $('#confirm-delete').modal('show', {
                backdrop: 'static'
            });
            document.getElementById('confirmationdelete').setAttribute('action', url);
        }
    </script>
@endsection
