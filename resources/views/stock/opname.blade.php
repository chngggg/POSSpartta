@extends('layouts.master')

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm">

        <div class="card-header">
            <h4 class="mb-0">
                <i class="fas fa-barcode me-2"></i>Scan Barcode (Stock Opname)
            </h4>
        </div>

        <div class="card-body text-center">

            <div class="mb-4">
                <input type="text"
                    class="form-control form-control-lg text-center"
                    placeholder="Scan atau input barcode di sini..."
                    autofocus>
            </div>

            <div class="alert alert-info">
                Silakan scan barcode untuk melakukan stock opname 🚀
            </div>

            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary mt-3">
                Kembali ke Dashboard
            </a>

        </div>

    </div>
</div>
@endsection