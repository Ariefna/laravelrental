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
                <h5 class="card-title">Form Pembayaran {{$data[0]->nama_siswa}}</h5>
                <form method="POST" action="/transaksi/bayar">
                    {{ csrf_field() }}
                    <input type="hidden" name="id" value="{{$data[0]->id}}">
                    <div class="position-relative form-group"><label class="">Total Tagihan</label><input name="tagihan" placeholder="" type="number" class="form-control" disabled value="{{$data[0]->tagihan}}"></div>
                    <div class="position-relative form-group"><label class="">Total Pembayaran</label><input name="bayar" placeholder="Masukkan Pembayaran" type="number" max="{{$data[0]->tagihan}}" min="1" class="form-control" require></div>
                    <div class="position-relative form-group"><label class="">Keterangan</label><input name="keterangan" placeholder="Masukkan Keterangan" type="text" class="form-control" require></div>
                    <button class="mt-1 btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>
@stop