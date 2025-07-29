@extends('layouts.app') {{-- Assuming you have a layout file --}}

@push('styles')
    {{-- Add Dropzone CSS --}}
    <link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />
    <style>
        /* Custom styles for better UX */
        .dropzone {
            border: 2px dashed #007bff;
            border-radius: 5px;
            background: white;
            min-height: 150px;
            padding: 20px;
        }
        .hidden {
            display: none;
        }
    </style>
@endpush

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Add New Item to '{{ $Manual->name }}'</div>
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

                        {{-- The form will handle both folder creation and file uploads --}}
                        <form action="{{ route('manual.items.store') }}" method="POST" enctype="multipart/form-data" id="addItemForm">
                            @csrf
                            <input type="hidden" name="id" value="{{ $Id }}">

                            <div class="form-group mb-3">
                                <label>What do you want to create?</label>
                                <div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="type" id="typeFolder" value="Folder" checked>
                                        <label class="form-check-label" for="typeFolder">Folder</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="type" id="typeFile" value="File">
                                        <label class="form-check-label" for="typeFile">File(s)</label>
                                    </div>
                                </div>
                            </div>

                            {{-- Input for Folder Name --}}
                            <div id="folderInput" class="form-group mb-3">
                                <label for="manual_name">Folder Name</label>
                                <input type="text" name="manual_name" class="form-control" placeholder="e.g., Chapter 1">
                            </div>

                            {{-- Dropzone container for File Uploads --}}
                            <div id="fileInput" class="form-group mb-3 hidden">
                                <label>Upload PDF Files</label>
                                {{-- This div is the target for Dropzone --}}
                                <div id="file-upload-dropzone" class="dropzone"></div>
                            </div>

                            <div class="d-flex justify-content-end">
                                <a href="{{ route('manual.items.index', $Id) }}" class="btn btn-secondary me-2">Cancel</a>
                                <button type="submit" class="btn btn-primary" id="submit-button">Create Item</button>
                            </div>
                        </form>
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
        // --- UI Logic to toggle between Folder and File inputs ---
        const typeFolderRadio = document.getElementById('typeFolder');
        const typeFileRadio = document.getElementById('typeFile');
        const folderInput = document.getElementById('folderInput');
        const fileInput = document.getElementById('fileInput');
        const form = document.getElementById('addItemForm');
        const submitButton = document.getElementById('submit-button');

        function toggleInputs() {
            if (typeFileRadio.checked) {
                folderInput.classList.add('hidden');
                fileInput.classList.remove('hidden');
            } else {
                folderInput.classList.remove('hidden');
                fileInput.classList.add('hidden');
            }
        }

        typeFolderRadio.addEventListener('change', toggleInputs);
        typeFileRadio.addEventListener('change', toggleInputs);

        // --- Dropzone Configuration ---
        // Prevent Dropzone from auto-discovering this element
        Dropzone.autoDiscover = false;

        const myDropzone = new Dropzone("#file-upload-dropzone", {
            url: form.action, // Set the url from the form action
            paramName: "files", // Matches `files.*` validation in the controller
            autoProcessQueue: false, // We will trigger uploads manually
            uploadMultiple: true, // Group all files in one request
            parallelUploads: 10, // Max number of files to upload in parallel
            maxFiles: 10, // Matches controller validation
            maxFilesize: {{ env('FILE_SIZE') }},
            acceptedFiles: 'application/pdf',
            addRemoveLinks: true, // Show remove links
            // The text displayed before any files are dropped.
            dictDefaultMessage: "Drop PDF files here or click to upload",
        });

        myDropzone.on("sendingmultiple", function(file, xhr, formData) {
            // Append the form data to the request
            // This ensures _token, id, and type are sent with the files
            formData.append("_token", form.querySelector('input[name="_token"]').value);
            formData.append("id", form.querySelector('input[name="id"]').value);
            formData.append("type", 'File'); // We know it's a file upload
        });

        myDropzone.on("successmultiple", function(files, response) {
            // This is the key! Redirect on success.
            window.location.href = "{{ route('manual.items.index', $Id) }}";
        });

        myDropzone.on("errormultiple", function(files, response) {
            // Handle errors here, e.g., show an alert
            alert('An error occurred during the upload. Please check the files and try again.');
            console.error('Dropzone error:', response);
        });

        // --- Form Submission Logic ---
        form.addEventListener('submit', function(e) {
            // If the user is uploading files, prevent the default form submission
            if (typeFileRadio.checked) {
                e.preventDefault();
                e.stopPropagation();

                // If there are files in the queue, process them
                if (myDropzone.getQueuedFiles().length > 0) {
                    myDropzone.processQueue();
                } else {
                    // If no files are selected, you can show an alert or do nothing
                    alert('Please select files to upload.');
                }
            }
            // If 'Folder' is selected, the form will submit normally.
        });
    </script>
@endpush
