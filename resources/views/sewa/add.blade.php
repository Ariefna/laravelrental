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
                <h5 class="card-title">Form Tambah</h5>
                <form method="POST" action="/mastermakanan/add/">
                    {{ csrf_field() }}
                    <div class="position-relative form-group"><label class="">Nomor Plat</label><select required name="id_tahun" class="form-control">
                            <option disabled='disabled'>- Pilih -</option>
                            <?php
                            if ($plat) :
                                foreach ($plat as $plat) :
                            ?>
                                    <option value="<?= $plat->id; ?>"><?= $plat->plat; ?> <?= $plat->nama; ?></option>
                                <?php endforeach;
                            else : ?>
                                <option disabled='disabled'>- Tambah PLat Baru -</option>
                            <?php endif; ?>
                        </select></div>
                    <div class="position-relative form-group"><label class="">Tanggal</label><input required name="tanggal" type="date" class="form-control"></div>
                    <div class="position-relative form-group"><label class="">Keterangan</label><input name="keterangan" placeholder="Masukkan Keterangan" type="text" class="form-control"></div>
                    <button class="mt-1 btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>
@stop