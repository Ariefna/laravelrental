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
                <h5 class="card-title">Form Pembayaran</h5>
                <form method="POST" action="/transaksi/bayar" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <input type="hidden" name="id_siswa" value="{{$data[0]->id_siswa}}">
                    <input type="hidden" name="id_kelas" value="{{$id_kelas}}">
                    <div class="position-relative form-group"><label class="">Total Tagihan</label><input name="tagihan" placeholder="" type="number" class="form-control" value="{{$data[0]->tagihan}}" readonly></div>
                    <div class="position-relative form-group"><label class="">Total Pembayaran</label><input name="bayar" placeholder="Masukkan Pembayaran" type="number" class="form-control" value="{{$data[0]->tagihan}}" readonly></div>
                    <div class="position-relative form-group"><label class="">Keterangan</label><input name="keterangan" placeholder="Masukkan Keterangan" type="text" class="form-control"></div>
                    <div class="position-relative form-group"><label class="">Bukti Pembayaran</label><br> <input type="file" id="file" name="file" multiple>
                </div>
                    <button class="mt-1 btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>
@stop