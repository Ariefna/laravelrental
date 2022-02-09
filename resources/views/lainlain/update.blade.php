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
                <form method="POST" action="/lainlain/ubah/">
                    {{ csrf_field() }}
                    <input type="hidden" name="id" value="{{$data[0]->id}}">
                    <div class="position-relative form-group"><label class="">Harga</label><input name="harga" placeholder="Masukkan Harga" type="number" class="form-control" value="{{$data[0]->biaya_formulir}}"></div>
                    <div class="position-relative form-group"><label class="">tahun</label><select name="id_tahun" class="form-control">
                            <option disabled='disabled'>- Pilih -</option>
                            <?php
                            if ($tahun) :
                                foreach ($tahun as $tahun) :
                                    if ($tahun->id == $data[0]->id_tahun) {
                            ?>
                                        <option value="<?= $tahun->id; ?>" selected><?= $tahun->kode; ?></option>
                                    <?php
                                    } else {
                                    ?>
                                        <option value="<?= $tahun->id; ?>"><?= $tahun->kode; ?></option>
                                    <?php
                                    }
                                    ?>
                                <?php endforeach;
                            else : ?>
                                <option disabled='disabled'>- Tambah tahun Baru -</option>
                            <?php endif; ?>
                        </select></div>
                    <button class="mt-1 btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>
@stop