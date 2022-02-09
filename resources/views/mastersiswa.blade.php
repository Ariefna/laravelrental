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
                <h5 class="card-title float-left">Master Siswa</h5>
                <a class="mb-2 mr-2 btn btn-primary float-right" href="<?= URL::to('/mastersiswa'); ?>/add/">Tambah</a>
                <div class="table-responsive">
                    <table class="mb-0 table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Foto Siswa</th>
                                <th>NIS</th>
                                <th>Nama</th>
                                <th>Kelas</th>
                                <th>Status Makan</th>
                                <th>Status Les</th>
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
                                        <td> <img src="<?= URL::to('/'); ?>/data_file/<?= $datas->foto_siswa; ?>" alt=" " class="" width="100px">  </td>
                                        <td><?= $datas->nis; ?></td>
                                        <td><?= $datas->nama_siswa; ?></td>
                                        <td><?= $datas->nama_kelas; ?></td>
                                        <td><?= $datas->status_makan; ?></td>
                                        <td><?= $datas->status_les; ?></td>
                                        <td>
                                            <a class="mb-2 mr-2 btn btn-warning" href="<?= URL::to('/mastersiswa'); ?>/ubah/<?= $datas->siswa_id ?>">Edit</a>
                                            <a class="mb-2 mr-2 btn btn-danger" onclick="return confirm('Yakin ingin menghapus data?')" href="<?= URL::to('/mastersiswa'); ?>/delete/<?= $datas->siswa_id ?>" href="#">Hapus</a> 
                                        </td>
                                    </tr>
                                <?php endforeach;
                            else : ?>
                                <tr>
                                    <td colspan="5" class="text-center">Silahkan tambahkan data siswa / siswi baru</td>
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