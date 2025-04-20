@extends('layouts.app')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="py-3 mb-4"><span class="text-muted fw-light">Issue/</span> Enter Issue</h4>

        <div class="row">
            <!-- Form Separator -->
            <div class="col-xxl">
                <div class="card mb-4">
                    <h5 class="card-header">Add Issue</h5>
                    <form class="card-body overflow-hidden" method="POST" action="{{ route('manual.create') }}">
                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label" for="manual_type">Borrower Name</label>
                            <div class="col-sm-9">
                                <select id="manual_type" class="select2 form-select" data-allow-clear="true">
                                    <optgroup label="Select Borrower">
                                        @foreach($Users as $user)
                                            <option>{{ $user->name.''.$user->surname }}</option>
                                        @endforeach

                                    </optgroup>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label" for="manual_type"> Hard Copy Manuals</label>
                            <div class="col-sm-9">
                                <select id="manual_type" class="select2 form-select" data-allow-clear="true">
                                    <optgroup label="Select Hard Copy Manuals">
                                        @foreach($Manuals as $manual)
                                            <option>{{ $manual->name }}</option>
                                        @endforeach
                                    </optgroup>
                                </select>

                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label" for="date_issued"> Date issued</label>
                            <div class="col-sm-9">
                                <div class="form-floating form-floating-outline">
                                    <input
                                        type="text"
                                        id="date_issued"
                                        class="form-control dob-picker"
                                        placeholder="YYYY-MM-DD"/>
                                    <label for="date_issued">Date issued</label>
                                </div>
                            </div>
                        </div>
{{--                        <div class="row mb-3">--}}
{{--                            <label class="col-sm-3 col-form-label" for="date_return"> Date Returned</label>--}}
{{--                            <div class="col-sm-9">--}}
{{--                                <div class="form-floating form-floating-outline">--}}
{{--                                    <input--}}
{{--                                        type="text"--}}
{{--                                        id="date_return"--}}
{{--                                        class="form-control dob-picker"--}}
{{--                                        placeholder="YYYY-MM-DD"/>--}}
{{--                                    <label for="date_return">Date Returned</label>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}

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
    @push('scripts')
        <!-- Page JS -->
        <script src="{{ asset('storage/assets/js/form-layouts.js') }}"></script>
    @endpush
@endsection
