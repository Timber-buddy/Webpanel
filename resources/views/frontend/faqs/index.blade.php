@extends('frontend.layouts.app')


@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<style>
    button.accordion {
       width: 100%;
       background-color: whitesmoke;
       border: none;
       outline: none;
       text-align: left;
       padding: 15px 20px;
       font-size: 18px;
       color: #333;
       cursor: pointer;
       transition: background-color 0.2s linear;
   }

   button.accordion:after {
       font-family: FontAwesome;
       content: "\f107";
       font-family: "fontawesome";
       font-size: 18px;
       float: right;
   }

   button.accordion.is-open:after {
       content: "\f106";
   }

   button.accordion:hover,
   button.accordion.is-open {
       background-color: #ddd;
   }

   .accordion-content {
       background-color: white;
       border-left: 1px solid whitesmoke;
       border-right: 1px solid whitesmoke;
       padding: 0 20px;
       max-height: 0;
       overflow: hidden;
       transition: max-height 0.2s ease-in-out;
   }
</style>
    <section class="mb-4">
        <div class="container">
            <div class="aiz-titlebar text-left mt-3 mb-3">
                <div class="align-items-center">
                    <h1 class="fw-600 h4">{{ translate('All FAQ') }}</h1>
                </div>
            </div>
            <div class="card-body">
                @foreach ($faqs as $key => $data)
                    <div class="faq-entry">
                        <button type="button" class="accordion">{{ $data->question }}</button>
                        <div class="accordion-content py-1 px-15px">
                            <p class="mt-3">{{ $data->answer }}</p>
                        </div>
                @endforeach
            </div>
        </div>
    </section>
    <script type="text/javascript">
        const accordionBtns = document.querySelectorAll(".accordion");
        accordionBtns.forEach((accordion) => {
            accordion.onclick = function() {
                this.classList.toggle("is-open");
                let content = this.nextElementSibling;
                console.log(content);
                if (content.style.maxHeight) {
                    //this is if the accordion is open
                    content.style.maxHeight = null;
                } else {
                    //if the accordion is currently closed
                    content.style.maxHeight = content.scrollHeight + "px";
                    console.log(content.style.maxHeight);
                }
            };
        });
    </script>
@endsection
