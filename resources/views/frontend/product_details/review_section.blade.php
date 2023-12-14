<div class="bg-white border mb-4">
    <div class="p-3 p-sm-4">
        <h3 class="fs-16 fw-700 mb-0">
            <span class="mr-4">{{ translate('Reviews & Ratings') }}</span>
        </h3>
    </div>
    <!-- Ratting -->
    <div class="px-3 px-sm-4 mb-4">
        <div class="border border-warning bg-soft-warning p-3 p-sm-4">
            <div class="row align-items-center">
                <div class="col-md-8 mb-3">
                    <div class="d-flex align-items-center justify-content-between justify-content-md-start">
                        <div class="w-100 w-sm-auto">
                            <span class="fs-36 mr-3">{{ $detailedProduct->rating }}</span>
                            <span class="fs-14 mr-3">{{ translate('out of 5.0') }}</span>
                        </div>
                        <div class="mt-sm-3 w-100 w-sm-auto d-flex flex-wrap justify-content-end justify-content-md-start">
                            @php
                                $total = 0;
                                $total += $detailedProduct->reviews->count();
                            @endphp
                            <span class="rating rating-mr-1">
                                {{ renderStarRating($detailedProduct->rating) }}
                            </span>
                            <span class="ml-1 fs-14">({{ $total }}
                                {{ translate('reviews') }})</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-right">
                    <a  href="javascript:void(0);" onclick="product_review('{{ $detailedProduct->id }}')" 
                        class="btn btn-warning fw-400 rounded-0 text-white">
                        <span class="d-md-inline-block"> {{ translate('Rate this Product') }}</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <!-- Reviews -->
    @include('frontend.product_details.reviews')
</div>

@section('modal')
    <!-- Product Review Modal -->
    <div class="modal fade" id="product-review-modal">
        <div class="modal-dialog">
            <div class="modal-content" id="product-review-modal-content">

            </div>
        </div>
    </div>

    <!-- Payment Modal -->
    <div class="modal fade" id="payment_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div id="payment_modal_body">

                </div>
            </div>
        </div>
    </div>
@endsection

<script type="text/javascript">
function product_review(product_id) {
    @if (Auth::check() && isCustomer())
        $.post('{{ route('product_review_modal') }}', {
            _token: '{{ @csrf_token() }}',
            product_id: product_id
        }, function(data) {
            $('#product-review-modal-content').html(data);
            $('#product-review-modal').modal('show', {
                backdrop: 'static'
            });
            // AIZ.extra.inputRating();
        });
    @else
        $('#login_modal').modal('show');
    @endif
}
</script>

