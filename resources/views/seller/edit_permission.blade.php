@extends('seller.layouts.app')

@section('panel_content')

<div class="col-lg-12 mx-auto">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6">{{translate('Role Information')}}</h5>
        </div>
        <form action="{{ route('seller.roles.update', $role->id) }}" method="POST">
            @csrf
            <input name="_method" type="hidden" value="PATCH">
            <input type="hidden" name="lang" value="{{ $lang }}">
            <div class="card-body">
                <div class="form-group row">
                    <label class="col-md-3 col-from-label" for="name">{{translate('Name')}}</label>
                    <div class="col-md-9">
                        @php $roleForTranslation = \App\Models\Role::where('id',$role->id)->first(); @endphp
                        <input type="text" placeholder="{{translate('Name')}}" id="name" name="name" class="form-control" value="{{ $roleForTranslation->getTranslation('name', $lang) }}" required>
                    </div>
                </div>
                <div class="card-header">
                    <h5 class="mb-0 h6">{{ translate('Permissions') }}</h5>
                </div>
                <br>
                @foreach ($permission_groups as $key => $permission_group)
                @if ($key !== 'orders')
                    <ul class="list-group mb-4">
                        <li class="list-group-item bg-light" aria-current="true">{{ translate(Str::headline($permission_group[0]['section'])) }}</li>
                        <li class="list-group-item">
                            <div class="row">
                                @foreach ($permission_group as $key => $permission)
                                @if ($loop->index != 6 && $loop->index != 7 && $loop->index != 8 && $loop->index != 9 && $loop->index != 10 && $loop->index != 14)
                                    <div class="col-lg-2 col-md-3 col-sm-4 col-xs-6">
                                        <div class="p-2 border mt-1 mb-2">
                                            <label class="control-label d-flex">{{ translate(Str::headline($permission->name)) }}</label>
                                            <label class="aiz-switch aiz-switch-success">
                                                <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="{{ $permission->id }}" {{(in_array($permission->id, $allowed_permissions))?"checked":""}}>
                                                <span class="slider round"></span>
                                            </label>
                                        </div>
                                    </div>
                                    @endif
                                @endforeach
                            </div>
                        </li>
                    </ul>
                    @endif
                @endforeach
                </li>
                <div class="form-group mb-3 mt-3 text-right">
                    <button type="submit" class="btn btn-primary">{{translate('Update')}}</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

@section('modal')
    @include('modals.delete_modal')
@endsection