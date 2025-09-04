@extends('layouts.app')

@section('header')
<h2 class="text-3xl font-semibold text-gray-800">
    {{ __('Form Input Delivery') }}
</h2>
@endsection

@section('content')
<div class="page-content">

    <div class="container-fluid">
        <h1>Input Delivery Schedule <button style="margin-left:360px;" type="button" class="btn btn-outline-primary"
                onclick="window.location.href='{{ route('delivery.create') }}'">Scan Sebelumnya</button></h1>
        {{-- Toast Notification --}}
        @if(session('success'))
        <div class="position-fixed top-0 end-0 p-3" style="z-index: 1100; margin-top: 75px;">
            <div class="toast align-items-center text-bg-success border-0 show" role="alert" aria-live="assertive"
                aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        {{ session('success') }}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                        aria-label="Close"></button>
                </div>
            </div>
        </div>
        @endif
        @if(session('found'))
        <div class="position-fixed top-0 end-0 p-3" style="z-index: 1100; margin-top: 75px;">
            <div class="toast align-items-center text-bg-info border-0 show" role="alert" aria-live="assertive"
                aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        {{ session('found') }}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                        aria-label="Close"></button>
                </div>
            </div>
        </div>
        @endif

        {{-- Toast Not Found --}}
        @if(session('notfound'))
        <div class="position-fixed top-0 end-0 p-3" style="z-index: 1100; margin-top: 75px;">
            <div class="toast align-items-center text-bg-danger border-0 show" role="alert" aria-live="assertive"
                aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        {{ session('notfound') }}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                        aria-label="Close"></button>
                </div>
            </div>
        </div>
        @endif
        @if(session('exists'))
        <div class="position-fixed top-0 end-0 p-3" style="z-index: 1100; margin-top: 75px;">
            <div class="toast align-items-center text-bg-warning border-0 show" role="alert" aria-live="assertive"
                aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        {{ session('exists') }}
                    </div>
                    <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast"
                        aria-label="Close"></button>
                </div>
            </div>
        </div>
        @endif


        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <form action="{{ route('dsinput.index') }}" method="GET" id="search-form" autocomplete="off">
                            <div class="row align-items-end">
                                {{-- Choose Date --}}
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="tgl_bln_thn" class="form-label">Preparation Date</label>
                                        <input type="date" class="form-control" id="tgl_preparation"
                                            name="tgl_preparation" value="{{ date('Y-m-d') }}" readonly
                                            placeholder="Select date">
                                    </div>
                                </div>

                                {{-- Search DS --}}
                                <div class="col-md-6">
                                    <div class="mb-3 position-relative">
                                        <label for="ds_number" class="form-label">Search DS Number</label>
                                        <input type="text"
                                            class="form-control @error('ds_number') is-invalid @enderror"
                                            id="ds_number"
                                            name="ds_number"
                                            placeholder="Enter DS Number"
                                            value="{{ request('ds_number') }}"
                                            autocomplete="off">

                                        {{-- Custom Suggestion List --}}
                                        <ul id="ds_suggestions"
                                            class="list-group position-absolute w-100 shadow-sm rounded-2 mt-1"
                                            style="z-index: 2000; max-height: 200px; overflow-y: auto; display: none;">
                                        </ul>
                                    </div>
                                </div>


                                {{-- Search & Reset Buttons --}}
                                <div class="col-md-12 d-flex justify-content-end">
                                    <div class="mb-3">
                                        <button type="submit" class="btn btn-primary me-2">Search</button>
                                        <a href="{{ route('dsinput.index') }}" class="btn btn-secondary">Reset</a>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <hr>

                        {{-- New Delivery Input Form --}}
                        <form action="{{ route('dsinput.tambah') }}" method="POST" autocomplete="off">
                            @csrf
                            {{-- Hidden DS Number --}}
                            <input type="hidden" name="ds_number" value="{{ $ds_number }}">
                            {{-- Hidden Preparation Date --}}
                            <input type="hidden" name="tgl_preparation" value="{{ request('tgl_preparation') }}">

                            <div class="row">
                                {{-- Left --}}
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="tgl_delivery" class="form-label">Delivery Date</label>
                                        <input type="text" class="form-control" id="tgl_delivery" name="tgl_delivery"
                                            value="{{ $ds_data ? $ds_data->di_received_date_string : '' }}" readonly
                                            placeholder="Delivery Date">
                                    </div>

                                    <div class="mb-3">
                                        <label for="model" class="form-label">Model</label>
                                        <input type="text" class="form-control" id="model" name="model"
                                            value="{{ $ds_data ? $model_display : '' }}" readonly placeholder="Model">
                                    </div>

                                    <div class="mb-3">
                                        <label for="plant_destination" class="form-label">Plant Destination</label>
                                        <input type="text" class="form-control" id="plant_destination"
                                            name="plant_destination" value="{{ $ds_data ? $ds_data->gate : '' }}"
                                            readonly placeholder="Plant Destination">
                                    </div>
                                </div>

                                {{-- Right --}}
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="type_delivery" class="form-label">Type of Delivery</label>
                                        <input type="text" class="form-control" id="type_delivery" name="type_delivery"
                                            value="{{ $ds_data ? $type_delivery_display : '' }}" readonly
                                            placeholder="Type of Delivery">
                                    </div>

                                    <div class="mb-3" style="display:none;">
                                        <label for="type_quantity" class="form-label">Type Quantity</label>
                                        <input type="text" class="form-control" id="type_quantity" name="type_quantity"
                                            value="{{ $ds_data ? 'Full' : '' }}" placeholder="Type Quantity">
                                    </div>

                                    <div class="mb-3">
                                        <label for="quantity" class="form-label">Quantity</label>
                                        <input type="number" class="form-control" id="quantity" name="quantity"
                                            value="{{ $ds_data ? $ds_data->qty : '' }}" readonly placeholder="Quantity">
                                    </div>

                                    <div class="mb-3">
                                        <label for="pic" class="form-label">PIC</label>
                                        <select class="form-select" id="pic" name="pic" required>
                                            <option value="">Select PIC</option>
                                            @foreach($pics as $p)
                                            <option value="{{ $p }}">{{ $p }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            {{-- Submit Button --}}
                            <div class="mt-3 d-flex justify-content-end">
                                <button type="submit" class="btn btn-success">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Script AJAX untuk autocomplete --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Autocomplete DS Number
    let debounceTimer;
    $('#ds_number').on('input', function() {
        let query = $(this).val();
        clearTimeout(debounceTimer);

        if (query.length < 2) {
            $('#ds_suggestions').hide().empty();
            return;
        }

        debounceTimer = setTimeout(function() {
            $.ajax({
                url: "{{ route('dsinput.autocomplete') }}",
                type: 'GET',
                data: { query: query },
                success: function(data) {
                    let suggestions = '';
                    data.forEach(function(item) {
                        suggestions += `<li class="list-group-item list-group-item-action"
                                            style="cursor:pointer"
                                            onclick="selectSuggestion('${item.ds_number}')">
                                            ${item.ds_number}
                                        </li>`;
                    });

                    if (suggestions) {
                        $('#ds_suggestions').html(suggestions).show();
                    } else {
                        $('#ds_suggestions').hide().empty();
                    }
                }
            });
        }, 300);
    });

    // Pilih suggestion
    window.selectSuggestion = function(value) {
        $('#ds_number').val(value);
        $('#ds_suggestions').hide().empty();
    }

    // Klik luar area → tutup suggestion
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#ds_number, #ds_suggestions').length) {
            $('#ds_suggestions').hide();
        }
    });

    // Hilangkan toast otomatis setelah 5 detik
    setTimeout(function() {
        $('.toast').fadeOut('slow', function() {
            $(this).remove();
        });
    }, 5000);
});
</script>



@endsection