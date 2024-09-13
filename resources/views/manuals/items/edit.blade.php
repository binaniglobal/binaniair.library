@extends('layouts.app')

@section('content')
    @push('styles')
        <link rel="stylesheet" href="{{ asset('storage/assets/vendor/libs/dropzone/dropzone.css') }}"/>
    @endpush
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="py-3 mb-4"><span class="text-muted fw-light">Manuals/</span> Edit Manual Items</h4>

        <div class="row">
            <!-- Form Separator -->
            <div class="col-xxl">
                <div class="card mb-4">
                    @if(session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif
                    <h5 class="card-header">Edit Folder {{ $Manual->name }} Manual</h5>
                    <form class="card-body overflow-hidden" method="POST"
                          action="{{ route('manual.items.update', ['id'=>$Id, 'ids'=>$Manual['manual_uid']]) }}">
                        @csrf
                        <input hidden type="hidden" name="type" value="Folder"/>
                        <input hidden type="hidden" name="id" value="{{ $Id }}"/>
                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label" for="manual_name">Manual Name</label>
                            <div class="col-sm-9">
                                <input type="text" id="manual_name" name="manual_name" class="form-control"
                                       value="{{ $Manual['name'] }}" placeholder="E.g. Folder Name"/>
                            </div>
                        </div>
                        <div class="pt-4">
                            <div class="row justify-content-end">
                                <div class="col-sm-9">
                                    <button type="submit" class="btn btn-primary me-sm-2 me-1">Update</button>
                                    <button type="reset" class="btn btn-outline-secondary">Reset</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
    @push('scripts')
        <script src="{{ asset('storage/assets/vendor/libs/dropzone/dropzone.js') }}"></script>
        <script src="{{ asset('storage/assets/js/forms-file-upload.js') }}"></script>
    @endpush
@endsection
