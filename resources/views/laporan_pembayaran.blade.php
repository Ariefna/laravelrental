@extends('template')
@section('content')
<script>
  $(document).ready(function() {
    $('.table').DataTable({ select: true, dom: 'Blfrtip', lengthMenu: [ [10, 25, 50, -1], ['10 Data', '25 Data', '50 Data', 'Semua'] ], dom: 'Bfrtip', buttons: [ { extend: 'pdf', text: ' Export a PDF' }, { extend: 'csv', text: ' Export a CSV' }, { extend: 'excel', text: ' Export a EXCEL' }, 'pageLength' ], }); table.buttons().container() .appendTo('#datatable_wrapper .col-md-6:eq(0)');

} );
</script>
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
                <h5 class="card-title float-left">Report Pembayaran</h5>
                <form action="" method="get" id="form-id">
                <select name="id_kelas" class="form-control" onchange="document.getElementById('form-id').submit();">
            @if(app('request')->input('id_kelas') !== null)
                @if(app('request')->input('id_kelas') == 1)
                <option value="1" selected>Kelas 1</option>
  <option value="2">Kelas 2</option>
  <option value="3">Kelas 3</option>
  <option value="4">Kelas 4</option>
  <option value="5">Kelas 5</option>
  <option value="6">Kelas 6</option>
                @endif
                @if(app('request')->input('id_kelas') == 2)
                <option value="2" selected>Kelas 2</option>
                <option value="1">Kelas 1</option>
  <option value="3">Kelas 3</option>
  <option value="4">Kelas 4</option>
  <option value="5">Kelas 5</option>
  <option value="6">Kelas 6</option>
                @endif
                @if(app('request')->input('id_kelas') == 3)
                <option value="3" selected>Kelas 3</option>
                <option value="1">Kelas 1</option>
  <option value="2">Kelas 2</option>
  <option value="4">Kelas 4</option>
  <option value="5">Kelas 5</option>
  <option value="6">Kelas 6</option>
                @endif
                @if(app('request')->input('id_kelas') == 4)
                <option value="4" selected>Kelas 4</option>
                <option value="1">Kelas 1</option>
  <option value="2">Kelas 2</option>
  <option value="3">Kelas 3</option>
  <option value="5">Kelas 5</option>
  <option value="6">Kelas 6</option>
                @endif
                @if(app('request')->input('id_kelas') == 5)
                <option value="5" selected>Kelas 5</option>
                <option value="1">Kelas 1</option>
  <option value="2">Kelas 2</option>
  <option value="3">Kelas 3</option>
  <option value="4">Kelas 4</option>
  <option value="6">Kelas 6</option>
                @endif
                @if(app('request')->input('id_kelas') == 6)
                <option value="6" selected>Kelas 6</option>
                <option value="1">Kelas 1</option>
  <option value="2">Kelas 2</option>
  <option value="3">Kelas 3</option>
  <option value="4">Kelas 4</option>
  <option value="5">Kelas 5</option>
                @endif
  @else 
  <option value=""></option>
  <option value="1">Kelas 1</option>
  <option value="2">Kelas 2</option>
  <option value="3">Kelas 3</option>
  <option value="4">Kelas 4</option>
  <option value="5">Kelas 5</option>
  <option value="6">Kelas 6</option>
  @endif
</select>
                </form>
               
                <div class="table-responsive">
                    <table class="mb-0 table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama Siswa</th>
                                <th>Kelas</th>
                                <th>Keterangan</th>
                                <th>Pembayaran</th>
                                <th>Tagihan</th>
                                <th>Tanggal</th>
                                <th>bukti</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            $totalk = 0;
                            $totald = 0;
                            if ($data) :
                                foreach ($data as $datas) :
                                    $totald=$totald+$datas->debet;
                                    $totalk=$totalk+$datas->kredit;
                            ?>
                                    <tr>
                                        <td scope="row"><?= $no++; ?></td>
                                        <td><?= $datas->nama_siswa; ?></td>
                                        <td><?= $datas->nama_kelas; ?></td>
                                        <td><?= $datas->keterangan; ?></td>
                                        <td>@currency($datas->debet)</td>
                                        <td>@currency($datas->kredit)</td>
                                        <td><?= $datas->tanggal; ?></td>
                                        <td> <img src="<?= URL::to('/'); ?>/data_file/<?= $datas->bukti; ?>" alt=" " class="" width="500px">  </td>
                                    </tr>
                                <?php endforeach;
                            else : ?>
                                <tr>
                                    <td colspan="14" class="text-center">Silahkan tambahkan data Transaksi / Transaksi baru</td>
                                </tr>
                            <?php endif; ?>
                            <tr>
                                        <td scope="row"><?= $no++; ?></td>
                                        <td></td>
                                        <td colspan="0" class="text-right">Total</td>
                                        <!-- <td></td>
                                        <td></td> -->
                                        <td></td> 
                                        <td>@currency($totald)</td>
                                        <td>@currency($totalk)</td>
                                        <td>@currency($totalk-$totald)</td> 
                                        <td></td>
                                    </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@stop