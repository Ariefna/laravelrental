@extends('template')
@section('content')
<div class="row">
    <div class="col-md-12">
        @if (\Session::has('success'))
        <div class="alert alert-success">
            <ul>
                <li>{!! \Session::get('success') !!}</li>
            </ul>
        </div>
        @endif
        @if (\Session::has('failed'))
        <div class="alert alert-danger">
            <ul>
                <li>{!! \Session::get('failed') !!}</li>
            </ul>
        </div>
        @endif
        <div class="main-card mb-3 card">
            <div class="card-body">
                <h5 class="card-title">Form Ubah</h5>
                <form method="POST" action="/mastertahun/ubah/">
                    {{ csrf_field() }}
                    <input type="hidden" value="{{$data[0]->id}}" name="id">
                    <div class="position-relative form-group"><label class="">Tahun</label><input require name="kode" placeholder="Masukkan Tahun" type="text" class="form-control" value="{{$data[0]->kode}}"></div>
                    <button class="mt-1 btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>
@stop