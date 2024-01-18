@extends('backend.layouts.app')

@section('content')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        /* .collapsible {
                background-color: #8a8b92;
                color: white;
                cursor: pointer;
                padding: 18px;
                width: 100%;
                border: none;
                text-align: left;
                outline: none;
                font-size: 15px;
            }

            .active,
            .collapsible:hover {
                background-color: #838e85;
            }

            .content {
                padding: 0 18px;
                max-height: 0;
                overflow: hidden;
                transition: max-height 0.2s ease-out;
                background-color: #f1f1f1;
            } */

        .container {
            width: 80%;
            max-width: 600px;
            margin: 50px auto;
        }

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
            content: "\f150";
            font-family: "fontawesome";
            font-size: 18px;
            float: right;
        }

        button.accordion.is-open:after {
            content: "\f151";
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

    <div class="aiz-titlebar text-left mt-2 mb-3">
        <div class="align-items-center">
            <h1 class="h3">{{ translate('All FAQ') }}</h1>
        </div>
    </div>
    <div class="card">
        <form class="" id="sort_customers" action="" method="GET">
            <div class="card-header row gutters-5">
                <div class="col">
                    <h5 class="mb-0 h6">{{ translate('All FAQ') }}</h5>
                </div>
                <div class="col-md-2 text-right">
                    @can('subscription_plan_create')
                        <div class="form-group mb-0">
                            <a href="javascript:void(0);" data-toggle="modal" data-target="#create_new"
                                class="btn btn-primary">New Add</a>
                        </div>
                    @endcan
                </div>
            </div>
            <div class="card-body">
                @foreach ($faqs as $key => $data)
                    <div class="faq-entry">
                        <button type="button" class="accordion">{{ $data->question }}</button>
                        <div class="accordion-content py-1 px-15px">
                            <p class="mt-3">{{ $data->answer }}</p>

                            <div class="mb-3 text-right">
                                <!-- Status Button -->
                                <label class="aiz-switch aiz-switch-success">
                                    <input onchange="update_status(this)" value="{{ $data->id }}" type="checkbox"
                                        <?php if ($data->status == 1) {
                                            echo 'checked';
                                        } ?>>
                                    <span class="slider round" style="top: 7px;"></span>
                                </label>
                                <!-- Edit Button -->
                                <a href="javascript:void(0);" class="btn btn-soft-success btn-icon btn-circle btn-sm"
                                    onclick="setRecord(`{{ route('faqs.show', $data->id) }}`)"
                                    title="{{ translate('Edit') }}">
                                    <i class="las la-edit"></i>
                                </a>
                                <!-- Delete Button -->
                                <a href="javascript:void(0);" class="btn btn-soft-danger btn-icon btn-circle btn-sm"
                                    onclick="deleteRecord(`{{ route('faqs.destroy', $data->id) }}`)"
                                    title="{{ translate('Delete') }}">
                                    <i class="las la-trash"></i>
                                </a>
                                </span>
                            </div>
                        </div>
                @endforeach
            </div>
        </form>
    </div>
    <div class="modal fade" id="create_new">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title h6">{{ translate('Add FAQ') }}</h5>
                    <button type="button" class="close" data-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('faqs.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label style="font-size: 15px;">{{ translate('Question') }} <span class="text-danger">*</span></label>
                                <textarea class="form-control" maxlength="200" rows="3" name="question" style="resize: none;"
                                    placeholder="{{ translate('Enter Your Question Here...') }}" required></textarea>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label style="font-size: 15px;">{{ translate('Answer') }} <span class="text-danger">*</span></label>
                                <textarea class="form-control" maxlength="500" rows="6" name="answer" style="resize: none;"
                                    placeholder="{{ translate('Enter Your Answer Here...') }}" required></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light"
                            data-dismiss="modal">{{ translate('Cancel') }}</button>
                        <button type="submit" name="sub" value="create"
                            class="btn btn-success">{{ translate('Create') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="edit-record">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title h6">{{ translate('Update FAQ') }}</h5>
                    <button type="button" class="close" data-dismiss="modal"></button>
                </div>
                <form method="POST" action="" enctype="multipart/form-data" id="edit-form">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label style="font-size: 15px;">{{ translate('Question') }} <span class="text-danger">*</span></label>
                                <textarea class="form-control" maxlength="200" rows="3" name="question" id="question" style="resize: none;"
                                    placeholder="{{ translate('Enter Your Question Here...') }}" required></textarea>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label style="font-size: 15px;">{{ translate('Answer') }} <span class="text-danger">*</span></label>
                                <textarea class="form-control" maxlength="500" rows="6" name="answer" id="answer" style="resize: none;"
                                    placeholder="{{ translate('Enter Your Answer Here...') }}" required></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light"
                            data-dismiss="modal">{{ translate('Cancel') }}</button>
                        <button type="submit" name="sub" value="create"
                            class="btn btn-success">{{ translate('Update') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="confirm-delete">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title h6">{{ translate('Confirmation') }}</h5>
                    <button type="button" class="close" data-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>{{ translate('Do you really want to delete this Faq?') }}</p>
                </div>
                <div class="modal-footer">
                    <form action="" method="post" id="confirmationdelete">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-light"
                            data-dismiss="modal">{{ translate('Cancel') }}</button>
                        <button type="submit" name="btn"
                            class="btn btn-primary">{{ translate('Proceed!') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
        function sort_customers(el) {
            $('#sort_customers').submit();
        }

        function deleteRecord(url) {
            $('#confirm-delete').modal('show', {
                backdrop: 'static'
            });
            document.getElementById('confirmationdelete').setAttribute('action', url);
        }

        // collapsible
        // var coll = document.getElementsByClassName("collapsible");
        // var i;

        // for (i = 0; i < coll.length; i++) {
        //     coll[i].addEventListener("click", function() {
        //         this.classList.toggle("active");
        //         var content = this.nextElementSibling;
        //         if (content.style.maxHeight) {
        //             content.style.maxHeight = null;
        //         } else {
        //             content.style.maxHeight = content.scrollHeight + "px";
        //         }
        //     });
        // }

        function setRecord(url) {
            $.get(url, function(result) {
                document.getElementById('edit-form').setAttribute('action', result.url);
                $("#question").val(result.faq.question);
                $("#answer").val(result.faq.answer);
                $('#edit-record').modal('show', {
                    backdrop: 'static'
                });
            });
        }

        function update_status(el) {
            if (el.checked) {
                var status = 1;
            } else {
                var status = 0;
            }
            $.post('{{ route('faqs.status') }}', {
                _token: '{{ csrf_token() }}',
                id: el.value,
                status: status
            }, function(data) {
                if (data == 1) {
                    AIZ.plugins.notify('success', '{{ translate('Status updated successfully') }}');
                } else {
                    AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
            });
        }

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
