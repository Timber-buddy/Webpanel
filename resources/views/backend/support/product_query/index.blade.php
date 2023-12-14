@extends('backend.layouts.app')

@section('content')
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6">{{ translate('Product Queries') }}</h5>
        </div>
        <div class="card-body">
            <table class="table aiz-table mb-0 " cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ translate('User Name') }}</th>
                        <th >{{ translate('Product Name') }}</th>
                        <th data-breakpoints="lg">{{ translate('Question') }}</th>
                        <th data-breakpoints="lg">{{ translate('Reply') }}</th>
                        <th>{{ translate('status') }}</th>
                        <th width="150px">{{ translate('Options') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($queries as $key => $query)
                        <tr>
                            <td>{{ translate($key + 1) }}</td>
                            <td>{{ $query->user->name ?? translate('Customer Not Found') }}</td>
                            <td>{{ $query->product != null ? $query->product->getTranslation('name') : translate('Product Not Found') }}</td>
                            <td>{{ Str::limit($query->question, 100) }}</td>
                            <td>{{ Str::limit($query->reply, 100) }}</td>
                            <td>
                                <span
                                    class="badge badge-inline {{ $query->reply == null ? 'badge-warning' : 'badge-success'  }}">
                                    {{ $query->reply == null ? translate('Not Replied') : translate('Replied')}}                                   
                                </span>
                            </td>
                            <td class="text-right">
                                <a class="btn btn-soft-primary btn-icon btn-circle btn-sm"
                                    href="{{ route('product_query.show', encrypt($query->id)) }}"
                                    title="{{ translate('View') }}">
                                    <i class="las la-eye"></i>
                                </a>
                                @if(!is_null($query->reply) || $query->reply != "")
                                <a class="btn btn-soft-success btn-icon btn-circle btn-sm"
                                    href="{{ route('product_query.edit', encrypt($query->id)) }}"
                                    title="{{ translate('Edit') }}">
                                    <i class="las la-edit"></i>
                                </a>
                                @endif
                                <a class="btn btn-soft-danger btn-icon btn-circle btn-sm"
                                    href="{{ route('product_query.destroy', encrypt($query->id)) }}"
                                    title="{{ translate('Delete') }}">
                                    <i class="las la-trash"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="aiz-pagination">
                {{ $queries->appends(request()->input())->links() }}
            </div>
        </div>
    </div>
@endsection

@section('modal')
    @include('modals.delete_modal')
@endsection
