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
                <h5 class="card-title float-left">Master Buku Paket</h5>
                <a class="mb-2 mr-2 btn btn-primary float-right" href="<?= URL::to('/masterbiayabukupaket'); ?>/add/">Tambah</a>
                <div class="table-responsive">
                    <table class="mb-0 table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>tahun</th>
                                <th>Nama Buku</th>
                                <th>Harga</th>
                                <th>Kelas</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            if ($data) :
                                foreach ($data as $datas) :
                            ?>
                                    <tr>
                                        <td scope="row"><?= $no++; ?></td>
                                        <td><?= $datas->kode; ?></td>
                                        <td><?= $datas->nama_buku; ?></td>
                                        <td>@currency($datas->biaya_buku)</td>
                                        <td><?= $datas->nama_kelas; ?></td>
                                        <td>
                                            <a class="mb-2 mr-2 btn btn-warning" href="<?= URL::to('/masterbiayabukupaket'); ?>/ubah/<?= $datas->id_buku ?>">Edit</a>
                                            <a class="mb-2 mr-2 btn btn-danger" onclick="return confirm('Yakin ingin menghapus data?')" href="<?= URL::to('/masterbiayabukupaket'); ?>/delete/<?= $datas->id_buku ?>" href="#">Hapus</a>
                                        </td>
                                    </tr>
                                <?php endforeach;
                            else : ?>
                                <tr>
                                    <td colspan="4" class="text-center">Silahkan tambahkan Buku paket</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@stop