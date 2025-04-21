@php use App\Models\ManualItemContent;use App\Models\Manuals;use Illuminate\Support\Facades\Auth; @endphp
@extends('layouts.app')

@section('content')
    @push('styles')
        <!-- Vendors CSS -->
        <link rel="stylesheet" href="{{ url('storage/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}"/>
        <link rel="stylesheet" href="{{ url('storage/assets/vendor/libs/typeahead-js/typeahead.css') }}"/>
        <link rel="stylesheet" href="{{ url('storage/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}"/>
        <link rel="stylesheet"
              href="{{ url('storage/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}"/>
        <link rel="stylesheet" href="{{ url('storage/assets/vendor/libs/flatpickr/flatpickr.css') }}"/>
    @endpush
    <div class="container-xxl flex-grow-1 container-p-y">
        @php
            $user = Auth::user();
            $size = new \App\Http\Controllers\ManualsItemController();
        @endphp
        <h4 class="py-3 mb-4"><span class="text-muted fw-light">Home / Manuals / <a href="javascript:void();" onclick="history.back()">{{ (getParentManual($Manual->manual_uid))->name }}</a></span>
            / {{ $Manual->name }}</h4>

        <!-- Responsive Datatable -->
        <div class="card">
            @if(session('success'))
                <div class="alert alert-success" role="alert">
                    {{ session('success') }}
                </div>
            @endif
            <h5 class="card-header">{{ ucfirst($Manual->name) ? ucfirst($Manual->name): ''}}</h5>
            <div class="card-datatable table-responsive text-nowrap">
                <table class="dt-responsive table table-hover" id="dt-responsive">
                    <thead>
                    <tr>
                        <th>{{ $Manual->name }}</th>
                        <th>File Type</th>
                        <th>Size</th>
                        @can('destroy-manual')
                            <th>Action</th>
                        @endcan
                    </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                    @can('view-manual')
                        @foreach($Items as $items)
                            @php
                                $count = ManualItemContent::where('manual_items_uid', $items->miid)->count();
                                $parentManual = Manuals::where('mid', $items->manual_uid)->first();
    //                            dd('access-manual-'.$parentManual->name.'.'. $Manual->name);
    //                            dd('access-manual-'. $Manual->name);
                            @endphp
                            @can('access-manual-'.$parentManual->name.'.'. $Manual->name.'.'.$items->name)
                                <tr>
                                    <td>
                                        <a href="{{ route('download.contents', $items->link) }}">{{ $items->name }}</a>
                                    </td>
                                    <td>{{ $items->file_type=='application/pdf'?'PDF':$items->file_type }}</td>
                                    <td>{{ $size->formatBytes($items->file_size) }}</td>
                                    @can('destroy-manual')
                                        <td>
                                            <div class="dropdown">
                                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                                        data-bs-toggle="dropdown">
                                                    <i class="mdi mdi-dots-vertical"></i>
                                                </button>
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item"
                                                       href="{{ route('manual.items.content.destroy', ['id'=>$items->manual_uid, 'ids'=>$items->micd]) }}"
                                                    ><i class="mdi mdi-trash-can-outline me-1"></i> Delete</a
                                                    >
                                                </div>
                                            </div>
                                        </td>
                                    @endcan
                                </tr>
                            @endcan
                        @endforeach
                    @endcan
                    </tbody>
                </table>
            </div>
        </div>
        <!--/ Responsive Datatable -->
    </div>
    @push('scripts')
        <script>
            $("#dt-responsive").DataTable({
                "responsive": true, "lengthChange": false, "autoWidth": false,
                buttons: [
                    'pageLength',
                        @can('create-manual')
                    {
                        html: '<a class="btn btn-primary" href="{{ route('manual.items.content.show', $Id) }}"><span class="fa fa-plus-circle" aria-hidden="true"></span> ' +
                            ' <i class="menu-icon tf-icons mdi mdi-file-document"></i>' +
                            ' </a>',
                        attr: {
                            class: 'btn btn-primary'
                        },
                    }
                    @endcan
                ],
                "language": {
                    "emptyTable": "<span class='text-success'>Empty table</span>"
                }
            }).buttons().container().appendTo('#dt-responsive_wrapper .col-md-6:eq(0)');
        </script>
    @endpush
@endsection
