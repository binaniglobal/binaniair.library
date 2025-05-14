@extends('layouts.app')

@section('content')
    @push('styles')
        <link rel="stylesheet" href="{{ asset('storage/assets/vendor/libs/dropzone/dropzone.css') }}"/>
    @endpush
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="py-3 mb-4"><span class="text-muted fw-light">Manuals/</span> Add Manual Items</h4>

        <div class="row">
            <!-- Form Separator -->
            <div class="col-xxl">
                <div class="card mb-4">
                    @if(session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif
                    <h5 class="card-header">Add Folder {{ $Manual->name }} Manual</h5>
                    <form class="card-body overflow-hidden" method="POST"
                          action="{{ route('manual.items.add') }}">
                        @csrf
                        <input hidden type="hidden" name="type" value="Folder"/>
                        <input hidden type="hidden" name="id" value="{{ $Id }}"/>
                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label" for="manual_name">Manual Name</label>
                            <div class="col-sm-9">
                                <input type="text" id="manual_name" name="manual_name" class="form-control" placeholder="E.g. Folder Name"/>
                            </div>
                        </div>
                        <div class="pt-4">
                            <div class="row justify-content-end">
                                <div class="col-sm-9">
                                    <button type="submit" class="btn btn-primary me-sm-2 me-1">Submit</button>
                                    <button type="reset" class="btn btn-outline-secondary">Reset</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <hr class="my-4 mx-n4"/>

                <div class="col-12">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="card">
                        <h5 class="card-header">Upload Single or Multiple file for {{ $Manual->name }} Manual </h5>
                        <div class="card-body">
                            <form action="{{ route('manual.items.add') }}" class="dropzone needsclick"
                                  enctype="multipart/form-data" method="POST"
                                  id="dropzone-multi" novalidate>
                                @csrf
                                <input hidden type="hidden" name="id" value="{{ $Id }}"/>
                                <input hidden type="hidden" name="type" value="File"/>
                                <div class="dz-message needsclick">
{{--                                    Drop files here or --}}
                                    click to upload
                                </div>
                                <div class="fallback">
                                    <input name="files[]" type="file" />
                                    <button class="btn btn-outline-primary" type="submit">Upload</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    @push('scripts')

{{--        <script src="{{ asset('storage/assets/js/forms-file-upload.js') }}"></script>--}}

    @endpush
@endsection
