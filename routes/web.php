<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/', 'App\Http\Controllers\pembayaran@mastersiswa');
Route::get('/login', 'App\Http\Controllers\pembayaran@login');
Route::post('/login', 'App\Http\Controllers\pembayaran@login_request');
Route::get('/logout', 'App\Http\Controllers\pembayaran@logout');


Route::middleware(['admin'])->group(function () {
Route::get('/mastersiswa', 'App\Http\Controllers\pembayaran@mastersiswa');
Route::get('/mastersiswa/delete/{id}', 'App\Http\Controllers\pembayaran@mastersiswadelete');
Route::get('/mastersiswa/ubah/{id}', 'App\Http\Controllers\pembayaran@mastersiswaupdate');
Route::post('/mastersiswa/ubah/', 'App\Http\Controllers\pembayaran@mastersiswaupdateaksi');
Route::get('/mastersiswa/add/', 'App\Http\Controllers\pembayaran@mastersiswaadd');
Route::post('/mastersiswa/add/', 'App\Http\Controllers\pembayaran@mastersiswaaddaksi');

Route::get('/masterkelas', 'App\Http\Controllers\pembayaran@masterkelas');
Route::get('/masterkelas/delete/{id}', 'App\Http\Controllers\pembayaran@masterkelasdelete');
Route::get('/masterkelas/ubah/{id}', 'App\Http\Controllers\pembayaran@masterkelasupdate');
Route::post('/masterkelas/ubah/', 'App\Http\Controllers\pembayaran@masterkelasupdateaksi');
Route::get('/masterkelas/add/', 'App\Http\Controllers\pembayaran@masterkelasadd');
Route::post('/masterkelas/add/', 'App\Http\Controllers\pembayaran@masterkelasaddaksi');

Route::get('/masterspp', 'App\Http\Controllers\pembayaran@masterspp');
Route::get('/masterspp/delete/{id}', 'App\Http\Controllers\pembayaran@mastersppdelete');
Route::get('/masterspp/ubah/{id}', 'App\Http\Controllers\pembayaran@mastersppupdate');
Route::post('/masterspp/ubah/', 'App\Http\Controllers\pembayaran@mastersppupdateaksi');
Route::get('/masterspp/add/', 'App\Http\Controllers\pembayaran@mastersppadd');
Route::post('/masterspp/add/', 'App\Http\Controllers\pembayaran@mastersppaddaksi');

Route::get('/mastermakanan', 'App\Http\Controllers\pembayaran@mastermakan');
Route::get('/mastermakanan/delete/{id}', 'App\Http\Controllers\pembayaran@mastermakandelete');
Route::get('/mastermakanan/ubah/{id}', 'App\Http\Controllers\pembayaran@mastermakanupdate');
Route::post('/mastermakanan/ubah/', 'App\Http\Controllers\pembayaran@mastermakanupdateaksi');
Route::get('/mastermakanan/add/', 'App\Http\Controllers\pembayaran@mastermakanadd');
Route::post('/mastermakanan/add/', 'App\Http\Controllers\pembayaran@mastermakanaddaksi');

Route::get('/masterbiayakegiatan', 'App\Http\Controllers\pembayaran@masterkegiatan');
Route::get('/masterbiayakegiatan/delete/{id}', 'App\Http\Controllers\pembayaran@masterkegiatandelete');
Route::get('/masterbiayakegiatan/ubah/{id}', 'App\Http\Controllers\pembayaran@masterkegiatanupdate');
Route::post('/masterbiayakegiatan/ubah/', 'App\Http\Controllers\pembayaran@masterkegiatanupdateaksi');
Route::get('/masterbiayakegiatan/add/', 'App\Http\Controllers\pembayaran@masterkegiatanadd');
Route::post('/masterbiayakegiatan/add/', 'App\Http\Controllers\pembayaran@masterkegiatanaddaksi');

Route::get('/masterbiayabukupaket', 'App\Http\Controllers\pembayaran@masterbuku');
Route::get('/masterbiayabukupaket/delete/{id}', 'App\Http\Controllers\pembayaran@masterbukudelete');
Route::get('/masterbiayabukupaket/ubah/{id}', 'App\Http\Controllers\pembayaran@masterbukuupdate');
Route::post('/masterbiayabukupaket/ubah/', 'App\Http\Controllers\pembayaran@masterbukuupdateaksi');
Route::get('/masterbiayabukupaket/add/', 'App\Http\Controllers\pembayaran@masterbukuadd');
Route::post('/masterbiayabukupaket/add/', 'App\Http\Controllers\pembayaran@masterbukuaddaksi');

Route::get('/mastertahun', 'App\Http\Controllers\pembayaran@mastertahun');
Route::get('/mastertahun/delete/{id}', 'App\Http\Controllers\pembayaran@mastertahundelete');
Route::get('/mastertahun/ubah/{id}', 'App\Http\Controllers\pembayaran@mastertahunupdate');
Route::post('/mastertahun/ubah/', 'App\Http\Controllers\pembayaran@mastertahunupdateaksi');
Route::get('/mastertahun/add/', 'App\Http\Controllers\pembayaran@mastertahunadd');
Route::post('/mastertahun/add/', 'App\Http\Controllers\pembayaran@mastertahunaddaksi');

Route::get('/naikkelas', 'App\Http\Controllers\pembayaran@naikkelas');
Route::get('/naikkelas/{id}', 'App\Http\Controllers\pembayaran@naikkelasaksi');
Route::get('/tinggalkelas/{id}', 'App\Http\Controllers\pembayaran@tinggalkelasaksi');

Route::get('/perbulan', 'App\Http\Controllers\pembayaran@perbulan');
Route::get('/kirimangsuran/{id}', 'App\Http\Controllers\pembayaran@kirimangsuran');





Route::get('/transaksi', 'App\Http\Controllers\pembayaran@transaksiview');


Route::get('/gedung', 'App\Http\Controllers\pembayaran@gedung');
Route::get('/gedung/delete/{id}', 'App\Http\Controllers\pembayaran@gedungdelete');
Route::get('/gedung/ubah/{id}', 'App\Http\Controllers\pembayaran@gedungupdate');
Route::post('/gedung/ubah/', 'App\Http\Controllers\pembayaran@gedungupdateaksi');
Route::get('/gedung/add/', 'App\Http\Controllers\pembayaran@gedungadd');
Route::post('/gedung/add/', 'App\Http\Controllers\pembayaran@gedungaddaksi');

Route::get('/lainlain', 'App\Http\Controllers\pembayaran@lainlain');
Route::get('/lainlain/delete/{id}', 'App\Http\Controllers\pembayaran@lainlaindelete');
Route::get('/lainlain/ubah/{id}', 'App\Http\Controllers\pembayaran@lainlainupdate');
Route::post('/lainlain/ubah/', 'App\Http\Controllers\pembayaran@lainlainupdateaksi');
Route::get('/lainlain/add/', 'App\Http\Controllers\pembayaran@lainlainadd');
Route::post('/lainlain/add/', 'App\Http\Controllers\pembayaran@lainlainaddaksi');

Route::get('/pakaian', 'App\Http\Controllers\pembayaran@pakaian');
Route::get('/pakaian/delete/{id}', 'App\Http\Controllers\pembayaran@pakaiandelete');
Route::get('/pakaian/ubah/{id}', 'App\Http\Controllers\pembayaran@pakaianupdate');
Route::post('/pakaian/ubah/', 'App\Http\Controllers\pembayaran@pakaianupdateaksi');
Route::get('/pakaian/add/', 'App\Http\Controllers\pembayaran@pakaianadd');
Route::post('/pakaian/add/', 'App\Http\Controllers\pembayaran@pakaianaddaksi');

Route::get('/masterlesan', 'App\Http\Controllers\pembayaran@masterles');
Route::get('/masterlesan/delete/{id}', 'App\Http\Controllers\pembayaran@masterlesdelete');
Route::get('/masterlesan/ubah/{id}', 'App\Http\Controllers\pembayaran@masterlesupdate');
Route::post('/masterlesan/ubah/', 'App\Http\Controllers\pembayaran@masterlesupdateaksi');
Route::get('/masterlesan/add/', 'App\Http\Controllers\pembayaran@masterlesadd');
Route::post('/masterlesan/add/', 'App\Http\Controllers\pembayaran@masterlesaddaksi');
});


// Route::middleware(['siswa'])->group(function () {
//     Route::get('/transaksi/bayar/{id}', 'App\Http\Controllers\pembayaran@transaksibayar');
//     Route::post('/transaksi/bayar/', 'App\Http\Controllers\pembayaran@transaksibayaraksi');
// Route::get('/dpembayaran/{id}', 'App\Http\Controllers\pembayaran@lpembayaransiswa');
//     });
Route::middleware(['customer'])->group(function () {
Route::get('/ltagihan', 'App\Http\Controllers\pembayaran@ltagihan');
Route::get('/lpembayaran', 'App\Http\Controllers\pembayaran@lpembayaran');
Route::get('/dpembayaran', 'App\Http\Controllers\pembayaran@lpembayaran');
});




// Route::get('datakeuangan', function () {
//     return view('datakeuangan');
// });
// Route::get('transaksipembayaran', function () {
//     return view('mastersiswa');
// });
// Route::get('datapembayaran', function () {
//     return view('mastersiswa');
// });
// Route::get('laporanpembayaran', function () {
//     return view('mastersiswa');
// });
// Route::get('laporantagihan', function () {
//     return view('mastersiswa');
// });
