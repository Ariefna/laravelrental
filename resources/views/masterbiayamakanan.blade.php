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
                <h5 class="card-title float-left">Master Makanan</h5>
                <a class="mb-2 mr-2 btn btn-primary float-right" href="<?= URL::to('/mastermakanan'); ?>/add/">Tambah</a>
                <div class="table-responsive">
                    <table class="mb-0 table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>tahun</th>
                                <th>Harga</th>
                                <th>status</th>
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
                                        <td>@currency($datas->biaya_makan)</td>
                                        <?php if($datas->status == 2){?>
                                            <td>Kelas 1 - 5</td>
                                        <?php }else{?>
                                        <td>Kelas 6</td>
                                            <?php }?>
                                        <td>
                                            <a class="mb-2 mr-2 btn btn-warning" href="<?= URL::to('/mastermakanan'); ?>/ubah/<?= $datas->makan_id ?>">Edit</a>
                                            <a class="mb-2 mr-2 btn btn-danger" onclick="return confirm('Yakin ingin menghapus data?')" href="<?= URL::to('/mastermakanan'); ?>/delete/<?= $datas->makan_id ?>" href="#">Hapus</a>
                                        </td>
                                    </tr>
                                <?php endforeach;
                            else : ?>
                                <tr>
                                    <td colspan="4" class="text-center">Silahkan tambahkan data biaya makanan baru</td>
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