@extends('layouts.app')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="py-3 mb-4"><span class="text-muted fw-light">Manuals/</span> Edit Manuals</h4>

        <div class="row">
            <!-- Form Separator -->
            <div class="col-xxl">
                <div class="card mb-4">
                    @if(session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif
                    <h5 class="card-header">Add Manuals</h5>
                    <form class="card-body overflow-hidden" method="POST" action="{{ route('manual.create') }}">
                        @csrf
                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label" for="manual_name">Manual Name</label>
                            <div class="col-sm-9">
                                <input type="text" id="manual_name" name="manual_name" class="form-control" placeholder="E.g. Folder Name"/>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label" for="manual_type">Manual Type</label>
                            <div class="col-sm-9">
                                <select id="manual_type" class="select2 form-select" data-allow-clear="true" disabled>
                                    <optgroup label="Select Manual type">
                                        <option selected value="0">SoftCopy</option>
                                        <option value="1">HardCopy</option>
                                    </optgroup>
                                </select>
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
            </div>

        </div>
    </div>
@endsection
