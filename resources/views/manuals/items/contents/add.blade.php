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
                        <h5 class="card-header">Upload Single or Multiple file for {{ $Manual->name }}</h5>
                        <div class="card-body">
                            <form action="{{ route('manual.items.content.add') }}" id="dropzone-multi" class="dropzone needsclick" method="POST"
                                  enctype="multipart/form-data">
                                @csrf
                                <input hidden type="hidden" name="id" value="{{ $Id }}"/>
                                <input hidden type="hidden" name="manual_uid" value="{{ $Manual->manual_uid }}"/>
                                <div class="dz-message needsclick">
{{--                                    Drop files here or--}}
                                    Click to upload
                                </div>
                                <div class="fallback">
                                    <input name="file[]" type="file" accept="application/pdf" />
                                    <button type="submit">Upload</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    @push('scripts')
        <script src="{{ asset('storage/assets/vendor/libs/dropzone/dropzone.js') }}"></script>
{{--        <script src="{{ asset('storage/assets/js/forms-file-upload.js') }}"></script>--}}

    @endpush
@endsection
