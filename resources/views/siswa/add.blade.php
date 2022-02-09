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
                <form method="POST" action="/mastersiswa/add/" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="position-relative form-group"><label class="">Foto Siswa</label><br> <input type="file" id="file" name="file" multiple>
                    <div class="position-relative form-group"><label class="">NIS</label><input name="nis" placeholder="Masukkan NIS" type="number" class="form-control"></div>
                    <div class="position-relative form-group"><label class="">Nama</label><input require name="nama" placeholder="Masukkan Nama" type="text" class="form-control"></div>
                    <div class="position-relative form-group"><label class="">Kelas</label><select name="id_kelas" class="form-control">
                            <option disabled='disabled'>- Pilih -</option>
                            <?php
                            if ($kelas) :
                                foreach ($kelas as $kelas) :
                            ?>
                                    <option value="<?= $kelas->id; ?>"><?= $kelas->nama_kelas; ?></option>
                                <?php endforeach;
                            else : ?>
                                <option disabled='disabled'>- Tambah Kelas Baru -</option>
                            <?php endif; ?>
                        </select></div>
                    <div class="form-group">
                        <div class="form-check">
                            <input class="form-check-input" name="makancheck" type="checkbox" value="1" id="invalidCheck">
                            <label class="form-check-label" for="invalidCheck">
                                Biaya Makanan
                            </label>
                            <div class="invalid-feedback">
                                You must agree before submitting.
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-check">
                            <input class="form-check-input" name="makancicilcheck" type="checkbox" value="2" id="invalidCheck">
                            <label class="form-check-label" for="invalidCheck">
                                Cicil Biaya Makanan
                            </label>
                            <div class="invalid-feedback">
                                You must agree before submitting.
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-check">
                            <input class="form-check-input" name="lescheck" type="checkbox" value="1" id="invalidCheck">
                            <label class="form-check-label" for="invalidCheck">
                                Ikut Les
                            </label>
                            <div class="invalid-feedback">
                                You must agree before submitting.
                            </div>
                        </div>
                    </div>
                    <!-- <div class="form-group">
                        <div class="form-check">
                            <input class="form-check-input" name="lescicilcheck" type="checkbox" value="2" id="invalidCheck">
                            <label class="form-check-label" for="invalidCheck">
                                Cicil Biaya Les
                            </label>
                            <div class="invalid-feedback">
                                You must agree before submitting.
                            </div>
                        </div>
                    </div> -->
                    <button class="mt-1 btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>
@stop