@extends('seller.layouts.app')

@section('panel_content')
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6">{{ translate('Product Queries') }}</h5>
        </div>
        <div class="card-body">
            <table class="table aiz-table mb-0" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th data-breakpoints="lg">#</th>
                        <th>{{ translate('User Name') }}</th>
                        <th>{{ translate('Product Name') }}</th>
                        <th data-breakpoints="lg">{{ translate('Question') }}</th>
                        <th data-breakpoints="lg">{{ translate('Reply') }}</th>
                        <th>{{ translate('status') }}</th>
                        <th class="text-right">{{ translate('Options') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($queries as $key => $query) 
                        @if (!is_null(optional($query->user)->name) && !is_null(optional($query->product)->name))
                        <tr>
                            <td>{{ translate($key + 1) }}</td>
                            <td>{{ optional($query->user)->name }}</td>
                            <td>{{ optional($query->product)->name }}</td>
                            <td>{{ translate(Str::limit($query->question, 20)) }}</td>
                            <td>{{ translate(Str::limit($query->reply, 20)) }}</td>
                            <td>
                                <span
                                    class="badge badge-inline {{ $query->reply == null ? 'badge-warning' : 'badge-success' }}">
                                    {{ $query->reply == null ? translate('Not Replied') : translate('Replied') }}
                                </span>
                            </td>
                            <td class="text-right">
                                @if(checkSellerPermission('view_query'))
                                    <a class="btn btn-soft-primary btn-icon btn-circle btn-sm"
                                        href="{{ route('seller.product_query.show', encrypt($query->id)) }}"
                                        title="{{ translate('View') }}">
                                        <i class="las la-eye"></i>
                                    </a>
                                @endif
                                @if(checkSellerPermission('edit_query'))
                                    @if(!is_null($query->reply) || $query->reply != "")
                                    <a class="btn btn-soft-success btn-icon btn-circle btn-sm"
                                        href="{{ route('seller.product_query.edit', encrypt($query->id)) }}"
                                        title="{{ translate('Edit') }}">
                                        <i class="las la-edit"></i>
                                    </a>
                                    @endif
                                @endif
                                @if(checkSellerPermission('delete_query'))
                                    <a class="btn btn-soft-danger btn-icon btn-circle btn-sm"
                                        href="{{ route('seller.product_query.destroy', encrypt($query->id)) }}"
                                        title="{{ translate('Delete') }}">
                                        <i class="las la-trash"></i>
                                    </a>
                                @endif
                            </td>
                        </tr>
                        @endif
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
