@extends('layouts.app') {{-- Assuming you have a layout file --}}

@push('styles')
    {{-- Add Dropzone CSS --}}
    <link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />
    <style>
        .dropzone {
            border: 2px dashed #007bff;
            border-radius: 5px;
            background: white;
            min-height: 150px;
            padding: 20px;
        }
    </style>
@endpush

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Add Files to '{{ $Manual->name }}'</div>
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        {{-- The form is only for file uploads --}}
                        <form action="{{ route('manual.items.content.store') }}" method="POST" enctype="multipart/form-data" id="addContentForm" class="dropzone">
                            @csrf
                            {{-- The controller needs both the parent manual ID and the item ID --}}
                            <input type="hidden" name="id" value="{{ $Id }}">
                            <input type="hidden" name="manual_uid" value="{{ $Manual->manual_uid }}">
                            <div class="dz-message" data-dz-message><span>Drop PDF files here or click to upload</span></div>
                        </form>

                        <div class="d-flex justify-content-end mt-3">
                            <a href="{{ route('manual.items.content.index', $Id) }}" class="btn btn-secondary me-2">Cancel</a>
                            <button type="button" class="btn btn-primary" id="submit-button">Upload Files</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- Add Dropzone JS --}}
    <script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
    <script>
        // Prevent Dropzone from auto-discovering this element
        Dropzone.autoDiscover = false;

        // Get the form element
        const form = document.getElementById('addContentForm');

        const myDropzone = new Dropzone(form, {
            // The URL is taken from the form's action attribute
            autoProcessQueue: false, // We will trigger uploads manually
            uploadMultiple: true, // Group all files in one request
            parallelUploads: 5, // Max number of files to upload in parallel
            maxFiles: 5, // Matches controller validation
            paramName: "file", // Matches `$request->validate(['file' => ...])` in the controller
            acceptedFiles: 'application/pdf',
            addRemoveLinks: true,
            // We link the submit button to the Dropzone instance
            clickable: ".dz-message",
        });

        myDropzone.on("sendingmultiple", function(file, xhr, formData) {
            // Append the hidden form data to the request
            formData.append("_token", form.querySelector('input[name="_token"]').value);
            formData.append("id", form.querySelector('input[name="id"]').value);
            formData.append("manual_uid", form.querySelector('input[name="manual_uid"]').value);
        });

        myDropzone.on("successmultiple", function(files, response) {
            // Redirect on success
            window.location.href = "{{ route('manual.items.content.index', $Id) }}";
        });

        myDropzone.on("errormultiple", function(files, response) {
            alert('An error occurred during the upload. Please check the files and try again.');
            console.error('Dropzone error:', response);
        });

        // Trigger the upload when the custom button is clicked
        document.getElementById('submit-button').addEventListener('click', function() {
            if (myDropzone.getQueuedFiles().length > 0) {
                myDropzone.processQueue();
            } else {
                alert('Please select files to upload.');
            }
        });
    </script>
@endpush
