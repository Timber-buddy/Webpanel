@extends('seller.layouts.app')

@section('panel_content')
    <form action="{{ route('seller.razorpay.callback') }}" method="POST">
        @csrf
        <a href="{{ url()->previous() }}" class="btn btn-primary">Go Back</a>
        <a href="{{ route('seller.process.payment.failed') }}" class="btn btn-primary">Back to Dashboard</a>
        <input type="hidden" name="record_id" value="{{ $subscription->id }}">
        <script src="https://checkout.razorpay.com/v1/checkout.js" {{-- data-key="{{ env('RAZOR_KEY') }}" --}} data-key="rzp_test_IaRrAVNXmEQ42q"
            data-amount="{{ $subscription->amount * 100 }}" data-buttontext="Pay" data-name="Timber Buddy" data-description=""
            data-image="{{ uploaded_asset(get_setting('site_icon')) }}" data-prefill.name="{{ $name }}"
            data-prefill.email="{{ $email }}" data-prefill.contact="{{ $phone }}" data-theme.color="#91794d">
        </script>
    </form>
@endsection

<style>
    .razorpay-payment-button {
        display: none !important;
    }
</style>

@section('script')
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"
        integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>
    <script>
        $('.razorpay-payment-button').click();
        $(document).ready(function() {
            $(".modal-close").click(function() {
                alert("asdasd");
                $("#positiveBtn").addClass('closePaymentBtn');
            });

            $(".closePaymentBtn").click(function() {
                alert("asdsd");
                window.location = "{{ route('seller.process.payment.failed') }}";
            });
        });
    </script>
@endsection
