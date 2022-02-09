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
                <form method="POST" action="/masterbiayakegiatan/ubah/">
                    {{ csrf_field() }}
                    <input type="hidden" value="{{$data[0]->id}}" name="id">
                    <div class="position-relative form-group"><label class="">Harga</label><input required name="harga" placeholder="Masukkan Harga" type="number" class="form-control" value="{{$data[0]->biaya_kegiatan}}"></div>
                    <div class="position-relative form-group"><label class="">tahun</label><select required name="id_tahun" class="form-control">
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
                        <div class="position-relative form-group"><label class="">Status</label><select required name="status" class="form-control">
                            <option disabled='disabled'>- Pilih -</option>
                            @if ($data[0]->status == 1)
                            <option value="1" selected>Kelas 6</option>
                            <option value="2">Kelas 1 - 5</option>
                            @elseif ($data[0]->id_tahun == 2)
                            <option value="1">Kelas 6</option>
                            <option value="2" selected>Kelas 1 - 5</option>
                            @else
                            <option value="1">Kelas 6</option>
                            <option value="2">Kelas 1 - 5</option>
                            @endif
                        </select></div>
                    <button class="mt-1 btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>
@stop