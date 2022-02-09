<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use File;
use Session;
use DateTime;
use App\Jobs\SendSisiRocks;
class pembayaran extends Controller
{

    public function logout()
    {
        Session::forget('role');
        return redirect('/login');
    }
    public function login()
    {
        SendSisiRocks::dispatch()->delay(now()->addMinutes(5));

        return view('login');
    }
    public function login_request(Request $request)
    {
        $pass = $request->input('password');
        $username = $request->input('username');
        // $user = DB::table('master_siswa')->where('nama_siswa', $username)->where('nis', $pass)->get();
        //count ini digunakan untuk memperiksa apakah login dengan nis dan password ini ada di database atau tidak?
        //jika ada maka bernilai 1 bisa masuk, jika tidak ber nilai 0 maka tidak bisa masuk
        if ((($pass != "admin") && ($username != "admin")) && (($pass != "penyewa") && ($username != "penyewa")))
        {
            return redirect('/login')->with('failed', 'Maaf, username atau password salah');
        }
        else
        {
            // $request->session()->put('role', $user[0]->role);
            // $request->session()->put('id_user', $user[0]->id_user);
            if ($pass == "admin" && $username == "admin")
            {
                $request->session()
                    ->put('role', 'admin');
                $request->session()
                    ->put('nama', 'admin');
                return redirect('/');
            }
            else if ($pass == "penyewa" && $username == "penyewa")
            {
                $request->session()
                    ->put('role', 'penyewa');
                $request->session()
                    ->put('nama', 'penyewa');
                return redirect('/ltagihan');
            }
            // else if($user->count())
            // {
            //     $request->session()
            //         ->put('id_siswa', $user[0]->id);
            //     $request->session()
            //         ->put('role', 'siswa');
            //     $request->session()
            //         ->put('nama', $user[0]->nama_siswa);
            //     return redirect('/transaksi/bayar/' . $user[0]->id);
            // }
            else {
            return redirect('/login')->with('failed', 'Maaf, username atau password salah');
            }
        }
    }
    //master siswa
    
    public function sewa()
    { 
        $data = DB::table('mobil')->get();
        return view('sewa.add', ['plat' => $data]);
    }
        public function mastersiswa()
        {
        if (Session::get('role') == null)
        {
            return redirect('/login');
        }
        $data = DB::table('master_siswa')->join('master_kelas', 'master_kelas.id', '=', 'master_siswa.id_kelas')
            ->whereBetween('master_siswa.id_kelas', [1, 6])
            ->select('*', 'master_siswa.nama_siswa as nama_siswa', 'master_kelas.nama_kelas AS nama_kelas', 'master_siswa.id AS siswa_id')
            ->get();
        return view('mastersiswa', ['data' => $data]);
    }
    public function mastersiswadelete($id)
    {
        DB::table('master_siswa')->where('id', '=', $id)->delete();
        return redirect()
            ->back()
            ->with('success', 'Data Anda Berhasil Dihapus');
    }
    public function mastersiswaupdate($id)
    {
        $data = DB::table('master_siswa')->where('id', $id)->get();
        $kelas = DB::table('master_kelas')->get();
        return view('siswa.update', ['data' => $data, 'kelas' => $kelas]);
    }
    public function mastersiswaupdateaksi(Request $r)
    {
        $id = $r->input('id');
        $file = $r->file('file');
        $tujuan_upload = 'data_file';
        $file->move($tujuan_upload, $file->getClientOriginalName());
        $nis = $r->input('nis');
        $nama = $r->input('nama');
        $id_kelas = $r->input('id_kelas');
        $makancheck = $r->input('makancheck');
        $lescheck = $r->input('lescheck');

        if (1 == $r->input('makancheck'))
        {
            $makancheck = $r->input('makancheck');
        }
        
        if (2 == $r->input('makancicilcheck'))
        {
            $makancheck = $r->input('makancicilcheck');
        }
        $lescheck = 0;
        $lescheck = $r->input('lescheck');
        // $lescheck = $r->input('lescicilcheck');
        if ($lescheck == null)
        {
            $lescheck = 0;
        }
        if ($makancheck == null)
        {
            $makancheck = 0;
        }
        if ($id_kelas == 1) //ini dimulai dari kelas 1
        {
            $id = DB::table('master_siswa')->insertGetId(['nis' => $nis, 'nama_siswa' => $nama, 'id_kelas' => $id_kelas, 'status_les' => $lescheck, 'status_makan' => $makancheck, 'foto_siswa' => $file->getClientOriginalName()]);
        }
        else
        {
            $time = new \DateTime('NOW'); // ambil tanggal yang sekarang
            // $newTimestamp = strtotime('+1 years', new \DateTime('NOW'));
            $newTimestamp = $time->modify('+1 year') // modify buat tambah tahun
                ->format('Y-m-d H:i:s');
                // echo $newTimestamp;
            $id = DB::table('master_siswa')->insertGetId(['nis' => $nis, 'nama_siswa' => $nama, 'id_kelas' => $id_kelas, 'status_les' => $lescheck, 'status_makan' => $makancheck, 'tanggal' => $newTimestamp, 'foto_siswa' => $file->getClientOriginalName()]);
            $newTimestamp = $time->modify('-1 year')
            ->format('Y-m-d H:i:s');
        }
        if ($lescheck != 0 && $id_kelas != 6) //ini digunakan untuk mengirim tagihan les, jika 0 maka tidak ikut les, dan kelas 6 tidak mengikuti les.
        {
            DB::statement("INSERT INTO transaksi (id_siswa, id_kelas, debet, kredit, keterangan) (SELECT " . $id . " as id_siswa, ".$id_kelas." as id_kelas, '0' as debet, biaya_les as kredit, CONCAT('Biaya Les ',year(CURRENT_TIMESTAMP)) as keterangan FROM `les` a join master_tahun b on a.id_tahun = b.id where b.kode = year(CURRENT_TIMESTAMP) order by a.biaya_les desc limit 1)");
        }

        if ($id_kelas == 1) 
        {
            if ($makancheck == 2)
            {
                $harga_makanan = DB::table('makan')->join('master_tahun', 'master_tahun.id', '=', 'makan.id_tahun')
                    ->select('biaya_makan')
                    ->where('status', '2')
                    ->where('master_tahun.kode', DB::raw('year(CURRENT_TIMESTAMP)'))
                    ->get();
                $biayaperbulan = (int)$harga_makanan[0]->biaya_makan / 12;
                if ($biayaperbulan != 0)
                {
                  
                        DB::statement("INSERT INTO transaksi (id_siswa, id_kelas, debet, kredit, keterangan) (SELECT '" . $id . "' as id_siswa, ".$id_kelas." as id_kelas, '0' as debet, (" . $biayaperbulan . ") as kredit, CONCAT('Biaya makanan tahun ',year(CURRENT_TIMESTAMP)) as keterangan FROM makan a join master_tahun b on a.id_tahun = b.id where b.kode = (year(CURRENT_TIMESTAMP)+1) and status = 2)");
                    
                }
            }
            else if ($makancheck == 1)
            {
                DB::statement("INSERT INTO transaksi (id_siswa, id_kelas, debet, kredit, keterangan) (SELECT '" . $id . "' as id_siswa, ".$id_kelas." as id_kelas, '0' as debet, biaya_makan as kredit, CONCAT('Biaya makanan ',year(CURRENT_TIMESTAMP)) as keterangan FROM makan a join master_tahun b on a.id_tahun = b.id where b.kode = year(CURRENT_TIMESTAMP) and status = 2)");
            }
            // DB::statement("INSERT INTO transaksi (id_siswa, debet, kredit, keterangan) (SELECT '" . $id . "' as id_siswa, '0' as debet, biaya_pakaian as kredit, CONCAT('Biaya Pakaian ',year(CURRENT_TIMESTAMP)) as keterangan FROM `pakaian` a join master_tahun b on a.id_tahun = b.id order by b.id desc limit 1)");

            DB::statement("INSERT INTO transaksi (id_siswa, id_kelas, debet, kredit, keterangan) (SELECT '" . $id . "' as id_siswa, ".$id_kelas." as id_kelas, '0' as debet, biaya_spp as kredit, CONCAT('Biaya SPP ',year(CURRENT_TIMESTAMP)) as keterangan FROM `spp` a join master_tahun b on a.id_tahun = b.id where b.kode = year(CURRENT_TIMESTAMP))");

            // DB::statement("INSERT INTO transaksi (id_siswa, debet, kredit, keterangan) (SELECT '" . $id . "' as id_siswa, '0' as debet, a.biaya_kegiatan as kredit, CONCAT('Biaya Kegiatan ',year(CURRENT_TIMESTAMP)) as keterangan FROM `kegiatan` a join master_tahun b on a.id_tahun = b.id where b.kode = year(CURRENT_TIMESTAMP) and status = 2)");
            // DB::statement("INSERT INTO transaksi (id_siswa, debet, kredit, keterangan) (SELECT '" . $id . "' as id_siswa, '0' as debet, biaya_buku as kredit, CONCAT('Biaya Buku ',a.nama_buku,' ',year(CURRENT_TIMESTAMP)) as keterangan FROM `buku` a join master_tahun b on a.id_tahun = b.id where b.kode = year(CURRENT_TIMESTAMP) and a.id_kelas = (select id_kelas from master_siswa where id = " . $id . "))");
        }
        else
        {
            if ($id_kelas < 6)
            {
                // DB::statement("INSERT INTO transaksi (id_siswa, debet, kredit, keterangan) (SELECT '" . $id . "' as id_siswa, '0' as debet, a.biaya_kegiatan as kredit, CONCAT('Biaya Kegiatan ',year(CURRENT_TIMESTAMP)) as keterangan FROM `kegiatan` a join master_tahun b on a.id_tahun = b.id where b.kode = (year(CURRENT_TIMESTAMP)+1) and status = 2)");
                // DB::statement("INSERT INTO transaksi (id_siswa, debet, kredit, keterangan) (SELECT '" . $id . "' as id_siswa, '0' as debet, biaya_pakaian as kredit, CONCAT('Biaya Pakaian ',year(CURRENT_TIMESTAMP)) as keterangan FROM `pakaian` a join master_tahun b on a.id_tahun = b.id order by b.id desc limit 1 OFFSET 1)");

                DB::statement("INSERT INTO transaksi (id_siswa, id_kelas, debet, kredit, keterangan) (SELECT '" . $id . "' as id_siswa, ".$id_kelas." as id_kelas, '0' as debet, biaya_spp as kredit, CONCAT('Biaya SPP ',year(CURRENT_TIMESTAMP)) as keterangan FROM `spp` a join master_tahun b on a.id_tahun = b.id where b.kode = year(CURRENT_TIMESTAMP)+1)");

                // DB::statement("INSERT INTO transaksi (id_siswa, debet, kredit, keterangan) (SELECT '" . $id . "' as id_siswa, '0' as debet, biaya_buku as kredit, CONCAT('Biaya Buku ',a.nama_buku,' ',year(CURRENT_TIMESTAMP)) as keterangan FROM `buku` a join master_tahun b on a.id_tahun = b.id where b.kode = (year(CURRENT_TIMESTAMP)+1) and a.id_kelas = (select id_kelas from master_siswa where id = " . $id . "))");
            }
            else
            {
                // DB::statement("INSERT INTO transaksi (id_siswa, debet, kredit, keterangan) (SELECT '" . $id . "' as id_siswa, '0' as debet, a.biaya_kegiatan as kredit, CONCAT('Biaya Kegiatan ',year(CURRENT_TIMESTAMP)) as keterangan FROM `kegiatan` a join master_tahun b on a.id_tahun = b.id where b.kode = (year(CURRENT_TIMESTAMP)+1) and status = 1)");
                // DB::statement("INSERT INTO transaksi (id_siswa, debet, kredit, keterangan) (SELECT '" . $id . "' as id_siswa, '0' as debet, biaya_pakaian as kredit, CONCAT('Biaya Pakaian ',year(CURRENT_TIMESTAMP)) as keterangan FROM `pakaian` a join master_tahun b on a.id_tahun = b.id order by b.id desc limit 1 OFFSET 1)");

                DB::statement("INSERT INTO transaksi (id_siswa, id_kelas, debet, kredit, keterangan) (SELECT '" . $id . "' as id_siswa, ".$id_kelas." as id_kelas, '0' as debet, biaya_spp as kredit, CONCAT('Biaya SPP ',year(CURRENT_TIMESTAMP)) as keterangan FROM `spp` a join master_tahun b on a.id_tahun = b.id where b.kode = year(CURRENT_TIMESTAMP)+1)");
                
                // DB::statement("INSERT INTO transaksi (id_siswa, debet, kredit, keterangan) (SELECT '" . $id . "' as id_siswa, '0' as debet, biaya_buku as kredit, CONCAT('Biaya Buku ',a.nama_buku,' ',year(CURRENT_TIMESTAMP)) as keterangan FROM `buku` a join master_tahun b on a.id_tahun = b.id where b.kode = (year(CURRENT_TIMESTAMP)+1) and a.id_kelas = (select id_kelas from master_siswa where id = " . $id . "))");
            }
            if ($id_kelas != 6 && $makancheck == 2) //
            {
                $harga_makanan = DB::table('makan')->join('master_tahun', 'master_tahun.id', '=', 'makan.id_tahun')
                    ->select('biaya_makan')
                    ->where('status', '2')
                    ->where('master_tahun.kode', DB::raw('year(CURRENT_TIMESTAMP)'))
                    ->get();
                $biayaperbulan = (int)$harga_makanan[0]->biaya_makan / 12;
                if ($biayaperbulan != 0)
                {
                  
                        DB::statement("INSERT INTO transaksi (id_siswa, id_kelas, debet, kredit, keterangan) (SELECT '" . $id . "' as id_siswa, ".$id_kelas." as id_kelas, '0' as debet, (" . $biayaperbulan . ") as kredit, CONCAT('Biaya makanan tahun ',year(CURRENT_TIMESTAMP)) as keterangan FROM makan a join master_tahun b on a.id_tahun = b.id where b.kode = (year(CURRENT_TIMESTAMP)+1) and status = 2)");
                    
                }
            }
            else if ($makancheck == 1)
            {
                DB::statement("INSERT INTO transaksi (id_siswa, id_kelas, debet, kredit, keterangan) (SELECT '" . $id . "' as id_siswa, ".$id_kelas." as id_kelas, '0' as debet, biaya_makan as kredit, CONCAT('Biaya makanan ',year(CURRENT_TIMESTAMP)) as keterangan FROM makan a join master_tahun b on a.id_tahun = b.id where b.kode = year(CURRENT_TIMESTAMP) and status = 2)");
            }
            else if ($id_kelas == 6)
            {
                DB::statement("INSERT INTO transaksi (id_siswa, id_kelas, debet, kredit, keterangan) (SELECT '" . $id . "' as id_siswa, ".$id_kelas." as id_kelas, '0' as debet, biaya_makan as kredit, CONCAT('Biaya makanan ',year(CURRENT_TIMESTAMP)) as keterangan FROM makan a join master_tahun b on a.id_tahun = b.id where b.kode = year(CURRENT_TIMESTAMP) and status = 1)");
            }
        }
        DB::table('master_siswa')
            ->where('id', $id)->update(['nis' => $nis, 'nama_siswa' => $nama, 'id_kelas' => $id_kelas, 'status_makan' => $makancheck, 'status_les' => $lescheck, 'foto_siswa' => $file->getClientOriginalName()]);
        return redirect()->back()
            ->with('success', 'Data Anda Berhasil Diubah');
    }
    public function mastersiswaadd()
    {
        $kelas = DB::table('master_kelas')->get();
        return view('siswa.add', ['kelas' => $kelas]);
    }
    public function mastersiswaaddaksi(Request $r)
    {
        $file = $r->file('file');
        $tujuan_upload = 'data_file';
        $file->move($tujuan_upload, $file->getClientOriginalName());
        $nis = $r->input('nis');
        $nama = $r->input('nama');
        $id_kelas = $r->input('id_kelas');
        $makancheck = 0; //pertama di deklarasikan dulu default nya
       
        if (1 == $r->input('makancheck'))
        {
            $makancheck = $r->input('makancheck');
        }
        
        if (2 == $r->input('makancicilcheck'))
        {
            $makancheck = $r->input('makancicilcheck');
        }
        $lescheck = 0;
        $lescheck = $r->input('lescheck');
        // $lescheck = $r->input('lescicilcheck');
        if ($lescheck == null)
        {
            $lescheck = 0;
        }
        if ($makancheck == null)
        {
            $makancheck = 0;
        }
        if ($id_kelas == 1) //ini dimulai dari kelas 1
        {
            $biaya_spp = DB::table('spp')->join('master_tahun', 'master_tahun.id', '=', 'spp.id_tahun')
            ->select('spp.id')
            ->where('master_tahun.kode', DB::raw('year(CURRENT_TIMESTAMP)'))
            ->get();
            $id = DB::table('master_siswa')->insertGetId(['nis' => $nis, 'nama_siswa' => $nama, 'id_kelas' => $id_kelas, 'status_les' => $lescheck, 'status_makan' => $makancheck, 'id_spp' => $biaya_spp[0]->id, 'foto_siswa' => $file->getClientOriginalName()]);
        }
        else
        {
            $time = new \DateTime('NOW'); // ambil tanggal yang sekarang
            // $newTimestamp = strtotime('+1 years', new \DateTimaster_siswame('NOW'));
            $newTimestamp = $time->modify('+1 year') // modify buat tambah tahun
                ->format('Y-m-d H:i:s');
                // echo $newTimestamp;
                $biaya_spp = DB::table('spp')->join('master_tahun', 'master_tahun.id', '=', 'spp.id_tahun')
            ->select('spp.id')
            ->where('master_tahun.kode', DB::raw('year(CURRENT_TIMESTAMP+INTERVAL 1 YEAR)'))
            ->get();

            $id = DB::table('master_siswa')->insertGetId(['nis' => $nis, 'nama_siswa' => $nama, 'id_kelas' => $id_kelas, 'status_les' => $lescheck, 'status_makan' => $makancheck, 'tanggal' => $newTimestamp, 'id_spp' => $biaya_spp[0]->id, 'foto_siswa' => $file->getClientOriginalName()] );
            $newTimestamp = $time->modify('-1 year')
            ->format('Y-m-d H:i:s');
        }
        if ($lescheck != 0 && $id_kelas != 6) //ini digunakan untuk mengirim tagihan les, jika 0 maka tidak ikut les, dan kelas 6 tidak mengikuti les.
        {
            DB::statement("INSERT INTO transaksi (id_siswa, id_kelas, debet, kredit, keterangan) (SELECT " . $id . " as id_siswa, ".$id_kelas." as id_kelas, '0' as debet, biaya_les as kredit, CONCAT('Biaya Les ',year(CURRENT_TIMESTAMP)) as keterangan FROM `les` a join master_tahun b on a.id_tahun = b.id where b.kode = year(CURRENT_TIMESTAMP) order by a.biaya_les desc limit 1)");
        }

        if ($id_kelas == 1) 
        {
            if ($makancheck == 2)
            {
                $harga_makanan = DB::table('makan')->join('master_tahun', 'master_tahun.id', '=', 'makan.id_tahun')
                    ->select('biaya_makan')
                    ->where('status', '2')
                    ->where('master_tahun.kode', DB::raw('year(CURRENT_TIMESTAMP)'))
                    ->get();
                $biayaperbulan = (int)$harga_makanan[0]->biaya_makan / 12;
                if ($biayaperbulan != 0)
                {
                  
                        DB::statement("INSERT INTO transaksi (id_siswa, id_kelas, debet, kredit, keterangan) (SELECT '" . $id . "' as id_siswa, ".$id_kelas." as id_kelas, '0' as debet, (" . $biayaperbulan . ") as kredit, CONCAT('Biaya makanan tahun ',year(CURRENT_TIMESTAMP)) as keterangan FROM makan a join master_tahun b on a.id_tahun = b.id where b.kode = (year(CURRENT_TIMESTAMP)+1) and status = 2)");
                    
                }
            }
            else if ($makancheck == 1)
            {
                DB::statement("INSERT INTO transaksi (id_siswa, id_kelas, debet, kredit, keterangan) (SELECT '" . $id . "' as id_siswa, ".$id_kelas." as id_kelas, '0' as debet, biaya_makan as kredit, CONCAT('Biaya makanan ',year(CURRENT_TIMESTAMP)) as keterangan FROM makan a join master_tahun b on a.id_tahun = b.id where b.kode = year(CURRENT_TIMESTAMP) and status = 2)");
            }
            // DB::statement("INSERT INTO transaksi (id_siswa, debet, kredit, keterangan) (SELECT '" . $id . "' as id_siswa, '0' as debet, biaya_pakaian as kredit, CONCAT('Biaya Pakaian ',year(CURRENT_TIMESTAMP)) as keterangan FROM `pakaian` a join master_tahun b on a.id_tahun = b.id order by b.id desc limit 1)");

            DB::statement("INSERT INTO transaksi (id_siswa, id_kelas, debet, kredit, keterangan) (SELECT '" . $id . "' as id_siswa, ".$id_kelas." as id_kelas, '0' as debet, biaya_spp as kredit, CONCAT('Biaya SPP ',year(CURRENT_TIMESTAMP)) as keterangan FROM `spp` a join master_tahun b on a.id_tahun = b.id where b.kode = year(CURRENT_TIMESTAMP))");

            // DB::statement("INSERT INTO transaksi (id_siswa, debet, kredit, keterangan) (SELECT '" . $id . "' as id_siswa, '0' as debet, a.biaya_kegiatan as kredit, CONCAT('Biaya Kegiatan ',year(CURRENT_TIMESTAMP)) as keterangan FROM `kegiatan` a join master_tahun b on a.id_tahun = b.id where b.kode = year(CURRENT_TIMESTAMP) and status = 2)");
            // DB::statement("INSERT INTO transaksi (id_siswa, debet, kredit, keterangan) (SELECT '" . $id . "' as id_siswa, '0' as debet, biaya_buku as kredit, CONCAT('Biaya Buku ',a.nama_buku,' ',year(CURRENT_TIMESTAMP)) as keterangan FROM `buku` a join master_tahun b on a.id_tahun = b.id where b.kode = year(CURRENT_TIMESTAMP) and a.id_kelas = (select id_kelas from master_siswa where id = " . $id . "))");
        }
        else
        {
            if ($id_kelas < 6)
            {
                // DB::statement("INSERT INTO transaksi (id_siswa, debet, kredit, keterangan) (SELECT '" . $id . "' as id_siswa, '0' as debet, a.biaya_kegiatan as kredit, CONCAT('Biaya Kegiatan ',year(CURRENT_TIMESTAMP)) as keterangan FROM `kegiatan` a join master_tahun b on a.id_tahun = b.id where b.kode = (year(CURRENT_TIMESTAMP)+1) and status = 2)");
                // DB::statement("INSERT INTO transaksi (id_siswa, debet, kredit, keterangan) (SELECT '" . $id . "' as id_siswa, '0' as debet, biaya_pakaian as kredit, CONCAT('Biaya Pakaian ',year(CURRENT_TIMESTAMP)) as keterangan FROM `pakaian` a join master_tahun b on a.id_tahun = b.id order by b.id desc limit 1 OFFSET 1)");

                DB::statement("INSERT INTO transaksi (id_siswa, id_kelas, debet, kredit, keterangan) (SELECT '" . $id . "' as id_siswa, ".$id_kelas." as id_kelas, '0' as debet, biaya_spp as kredit, CONCAT('Biaya SPP ',year(CURRENT_TIMESTAMP)) as keterangan FROM `spp` a join master_tahun b on a.id_tahun = b.id where b.kode = year(CURRENT_TIMESTAMP)+1)");

                // DB::statement("INSERT INTO transaksi (id_siswa, debet, kredit, keterangan) (SELECT '" . $id . "' as id_siswa, '0' as debet, biaya_buku as kredit, CONCAT('Biaya Buku ',a.nama_buku,' ',year(CURRENT_TIMESTAMP)) as keterangan FROM `buku` a join master_tahun b on a.id_tahun = b.id where b.kode = (year(CURRENT_TIMESTAMP)+1) and a.id_kelas = (select id_kelas from master_siswa where id = " . $id . "))");
            }
            else
            {
                // DB::statement("INSERT INTO transaksi (id_siswa, debet, kredit, keterangan) (SELECT '" . $id . "' as id_siswa, '0' as debet, a.biaya_kegiatan as kredit, CONCAT('Biaya Kegiatan ',year(CURRENT_TIMESTAMP)) as keterangan FROM `kegiatan` a join master_tahun b on a.id_tahun = b.id where b.kode = (year(CURRENT_TIMESTAMP)+1) and status = 1)");
                // DB::statement("INSERT INTO transaksi (id_siswa, debet, kredit, keterangan) (SELECT '" . $id . "' as id_siswa, '0' as debet, biaya_pakaian as kredit, CONCAT('Biaya Pakaian ',year(CURRENT_TIMESTAMP)) as keterangan FROM `pakaian` a join master_tahun b on a.id_tahun = b.id order by b.id desc limit 1 OFFSET 1)");

                DB::statement("INSERT INTO transaksi (id_siswa, id_kelas, debet, kredit, keterangan) (SELECT '" . $id . "' as id_siswa, ".$id_kelas." as id_kelas, '0' as debet, biaya_spp as kredit, CONCAT('Biaya SPP ',year(CURRENT_TIMESTAMP)) as keterangan FROM `spp` a join master_tahun b on a.id_tahun = b.id where b.kode = year(CURRENT_TIMESTAMP)+1)");
                
                // DB::statement("INSERT INTO transaksi (id_siswa, debet, kredit, keterangan) (SELECT '" . $id . "' as id_siswa, '0' as debet, biaya_buku as kredit, CONCAT('Biaya Buku ',a.nama_buku,' ',year(CURRENT_TIMESTAMP)) as keterangan FROM `buku` a join master_tahun b on a.id_tahun = b.id where b.kode = (year(CURRENT_TIMESTAMP)+1) and a.id_kelas = (select id_kelas from master_siswa where id = " . $id . "))");
            }
            if ($id_kelas != 6 && $makancheck == 2) //
            {
                $harga_makanan = DB::table('makan')->join('master_tahun', 'master_tahun.id', '=', 'makan.id_tahun')
                    ->select('biaya_makan')
                    ->where('status', '2')
                    ->where('master_tahun.kode', DB::raw('year(CURRENT_TIMESTAMP)'))
                    ->get();
                $biayaperbulan = (int)$harga_makanan[0]->biaya_makan / 12;
                if ($biayaperbulan != 0)
                {
                  
                        DB::statement("INSERT INTO transaksi (id_siswa, id_kelas, debet, kredit, keterangan) (SELECT '" . $id . "' as id_siswa, ".$id_kelas." as id_kelas, '0' as debet, (" . $biayaperbulan . ") as kredit, CONCAT('Biaya makanan tahun ',year(CURRENT_TIMESTAMP)) as keterangan FROM makan a join master_tahun b on a.id_tahun = b.id where b.kode = (year(CURRENT_TIMESTAMP)+1) and status = 2)");
                    
                }
            }
            else if ($makancheck == 1)
            {
                DB::statement("INSERT INTO transaksi (id_siswa, id_kelas, debet, kredit, keterangan) (SELECT '" . $id . "' as id_siswa, ".$id_kelas." as id_kelas, '0' as debet, biaya_makan as kredit, CONCAT('Biaya makanan ',year(CURRENT_TIMESTAMP)) as keterangan FROM makan a join master_tahun b on a.id_tahun = b.id where b.kode = year(CURRENT_TIMESTAMP) and status = 2)");
            }
            else if ($id_kelas == 6)
            {
                DB::statement("INSERT INTO transaksi (id_siswa, id_kelas, debet, kredit, keterangan) (SELECT '" . $id . "' as id_siswa, ".$id_kelas." as id_kelas, '0' as debet, biaya_makan as kredit, CONCAT('Biaya makanan ',year(CURRENT_TIMESTAMP)) as keterangan FROM makan a join master_tahun b on a.id_tahun = b.id where b.kode = year(CURRENT_TIMESTAMP) and status = 1)");
            }
        }
        return redirect()->back()
            ->with('success', 'Data Anda Berhasil Dimasukkan ');
    }
    //master kelas
    public function masterkelas()
    {
        $data = DB::table('master_kelas')->get();
        return view('masterkelas', ['data' => $data]);
    }
    public function masterkelasdelete($id)
    {
        DB::table('master_kelas')->where('id', '=', $id)->delete();
        return redirect()
            ->back()
            ->with('success', 'Data Anda Berhasil Dihapus');
    }

    public function masterkelasupdate($id)
    {
        $data = DB::table('master_kelas')->where('id', $id)->get();
        return view('kelas.update', ['data' => $data]);
    }
    public function masterkelasupdateaksi(Request $r)
    {
        $id = $r->input('id');
        $nama = $r->input('nama');
        DB::table('master_kelas')
            ->where('id', $id)->update(['nama_kelas' => $nama]);
        return redirect()->back()
            ->with('success', 'Data Anda Berhasil Diubah');
    }

    public function masterkelasadd()
    {
        return view('kelas.add');
    }
    public function masterkelasaddaksi(Request $r)
    {
        $nama = $r->input('nama');
        DB::table('master_kelas')
            ->insert(['nama_kelas' => $nama]);
        return redirect()->back()
            ->with('success', 'Data Anda Berhasil Dimasukkan');
    }

    //master spp
    public function masterspp()
    {
        $data = DB::table('spp')->join('master_tahun', 'master_tahun.id', '=', 'spp.id_tahun')
            ->select('*', 'spp.id as spp_id')
            ->get();
        return view('masterspp', ['data' => $data]);
    }
    public function mastersppdelete($id)
    {
        DB::table('spp')->where('id', '=', $id)->delete();
        return redirect()
            ->back()
            ->with('success', 'Data Anda Berhasil Dihapus');
    }
    public function mastersppupdate($id)
    {
        $data = DB::table('spp')->where('id', $id)->get();
        $tahun = DB::table('master_tahun')->get();
        return view('spp.update', ['data' => $data, 'tahun' => $tahun]);
    }
    public function mastersppupdateaksi(Request $r)
    {
        $id = $r->input('id');
        $id_tahun = $r->input('id_tahun');
        $harga = $r->input('harga');
        DB::table('spp')
            ->where('id', $id)->update(['id_tahun' => $id_tahun, 'biaya_spp' => $harga]);
        return redirect()->back()
            ->with('success', 'Data Anda Berhasil Diubah');
    }
    public function mastersppadd()
    {
        $tahun = DB::table('master_tahun')->get();
        return view('spp.add', ['tahun' => $tahun]);
    }
    public function mastersppaddaksi(Request $r)
    {
        $harga = $r->input('harga');
        $id_tahun = $r->input('id_tahun');
        DB::table('spp')
            ->insert(['biaya_spp' => $harga, 'id_tahun' => $id_tahun]);
        return redirect()->back()
            ->with('success', 'Data Anda Berhasil Dimasukkan');
    }

    //master makan
    public function mastermakan()
    {
        $data = DB::table('makan')->join('master_tahun', 'master_tahun.id', '=', 'makan.id_tahun')
            ->select('*', 'makan.id as makan_id')
            ->get();
        return view('masterbiayamakanan', ['data' => $data]);
    }
    public function mastermakandelete($id)
    {
        DB::table('makan')->where('id', '=', $id)->delete();
        return redirect()
            ->back()
            ->with('success', 'Data Anda Berhasil Dihapus');
    }
    public function mastermakanupdate($id)
    {
        $data = DB::table('makan')->where('id', $id)->get();
        $tahun = DB::table('master_tahun')->get();
        return view('makan.update', ['data' => $data, 'tahun' => $tahun]);
    }
    public function mastermakanupdateaksi(Request $r)
    {
        $id = $r->input('id');
        $harga = $r->input('harga');
        $id_tahun = $r->input('id_tahun');
        $status = $r->input('status');
        DB::table('makan')
            ->where('id', $id)->update(['biaya_makan' => $harga, 'id_tahun' => $id_tahun, 'status' => $status]);
        return redirect()->back()
            ->with('success', 'Data Anda Berhasil Diubah');
    }
    public function mastermakanadd()
    {
        $tahun = DB::table('master_tahun')->get();
        return view('makan.add', ['tahun' => $tahun]);
    }
    public function mastermakanaddaksi(Request $r)
    {
        $harga = $r->input('harga');
        $id_tahun = $r->input('id_tahun');
        $status = $r->input('status');
        DB::table('makan')
            ->insert(['biaya_makan' => $harga, 'id_tahun' => $id_tahun, 'status' => $status]);
        return redirect()->back()
            ->with('success', 'Data Anda Berhasil Dimasukkan');
    }

    //master kegiatan
    public function masterkegiatan()
    {
        $data = DB::table('kegiatan')->join('master_tahun', 'master_tahun.id', '=', 'kegiatan.id_tahun')
            ->select('*', 'kegiatan.id as id_kegiatan')
            ->get();
        return view('masterbiayakegiatan', ['data' => $data]);
    }
    public function masterkegiatandelete($id)
    {
        DB::table('kegiatan')->where('id', '=', $id)->delete();
        return redirect()
            ->back()
            ->with('success', 'Data Anda Berhasil Dihapus');
    }

    public function masterkegiatanupdate($id)
    {
        $data = DB::table('kegiatan')->where('id', $id)->get();
        $tahun = DB::table('master_tahun')->get();
        return view('kegiatan.update', ['data' => $data, 'tahun' => $tahun]);
    }
    public function masterkegiatanupdateaksi(Request $r)
    {
        $id = $r->input('id');
        $harga = $r->input('harga');
        $id_tahun = $r->input('id_tahun');
        DB::table('kegiatan')
            ->where('id', $id)->update(['biaya_kegiatan' => $harga, 'id_tahun' => $id_tahun]);
        return redirect()->back()
            ->with('success', 'Data Anda Berhasil Diubah');
    }
    public function masterkegiatanadd()
    {
        $tahun = DB::table('master_tahun')->get();
        return view('kegiatan.add', ['tahun' => $tahun]);
    }
    public function masterkegiatanaddaksi(Request $r)
    {
        $harga = $r->input('harga');
        $id_tahun = $r->input('id_tahun');
        $status = $r->input('status');
        DB::table('kegiatan')
            ->insert(['biaya_kegiatan' => $harga, 'id_tahun' => $id_tahun, 'status' => $status]);
        return redirect()->back()
            ->with('success', 'Data Anda Berhasil Dimasukkan');
    }

    //master buku
    public function masterbuku()
    {
        $data = DB::table('buku')->join('master_tahun', 'master_tahun.id', '=', 'buku.id_tahun')
            ->join('master_kelas', 'master_kelas.id', '=', 'buku.id_kelas')
            ->select('*', 'buku.id as id_buku')
            ->get();
        return view('masterbiayabukupaket', ['data' => $data]);
    }
    public function masterbukudelete($id)
    {
        DB::table('buku')->where('id', '=', $id)->delete();
        return redirect()
            ->back()
            ->with('success', 'Data Anda Berhasil Dihapus');
    }
    public function masterbukuupdate($id)
    {
        $data = DB::table('buku')->where('id', $id)->get();
        $tahun = DB::table('master_tahun')->get();
        $kelas = DB::table('master_kelas')->get();
        return view('buku.update', ['data' => $data, 'tahun' => $tahun, 'kelas' => $kelas]);
    }
    public function masterbukuupdateaksi(Request $r)
    {
        // DB::enableQueryLog();
        $id = $r->input('id');
        $id_tahun = $r->input('id_tahun');
        $buku = $r->input('buku');
        $harga = $r->input('harga');
        $kelas = $r->input('id_kelas');
        DB::table('buku')
            ->where('id', $id)->update(['id_tahun' => $id_tahun, 'nama_buku' => $buku, 'biaya_buku' => $harga, 'id_kelas' => $kelas]);
        // dd(DB::getQueryLog());
        return redirect()->back()
            ->with('success', 'Data Anda Berhasil Diubah');
    }
    public function masterbukuadd()
    {
        $tahun = DB::table('master_tahun')->get();
        $kelas = DB::table('master_kelas')->get();
        return view('buku.add', ['tahun' => $tahun, 'kelas' => $kelas]);
    }
    public function masterbukuaddaksi(Request $r)
    {
        $id_tahun = $r->input('id_tahun');
        $buku = $r->input('buku');
        $harga = $r->input('harga');
        $kelas = $r->input('id_kelas');
        DB::table('buku')
            ->insert(['id_tahun' => $id_tahun, 'nama_buku' => $buku, 'biaya_buku' => $harga, 'id_kelas' => $kelas]);
        return redirect()->back()
            ->with('success', 'Data Anda Berhasil Dimasukkan');
    }
    //master tahun
    public function mastertahun()
    {
        $data = DB::table('master_tahun')->get();
        return view('mastertahun', ['data' => $data]);
    }
    public function mastertahundelete($id)
    {
        DB::table('master_tahun')->where('id', '=', $id)->delete();
        return redirect()
            ->back()
            ->with('success', 'Data Anda Berhasil Dihapus');
    }
    public function mastertahunupdate($id)
    {
        $data = DB::table('master_tahun')->where('id', $id)->get();
        return view('tahun.update', ['data' => $data]);
    }
    public function mastertahunupdateaksi(Request $r)
    {
        $id = $r->input('id');
        $kode = $r->input('kode');
        DB::table('master_tahun')
            ->where('id', $id)->update(['kode' => $kode]);
        return redirect()->back()
            ->with('success', 'Data Anda Berhasil Diubah');
    }
    public function mastertahunadd()
    {
        return view('tahun.add');
    }
    public function mastertahunaddaksi(Request $r)
    {
        $kode = $r->input('kode');
        DB::table('master_tahun')
            ->insert(['kode' => $kode]);
        return redirect()->back()
            ->with('success', 'Data Anda Berhasil Dimasukkan');
    }
    //relasi naik kelas
    public function naikkelas()
    {
        $data = DB::table('master_siswa')->join('master_kelas', 'master_kelas.id', '=', 'master_siswa.id_kelas')
            ->whereBetween('master_siswa.id_kelas', [0, 6])
            ->select('*', 'master_siswa.nama_siswa as nama_siswa', 'master_kelas.nama_kelas AS nama_kelas', 'master_siswa.id AS siswa_id')
            ->get();
        return view('naikkelas', ['data' => $data]);
    }
    public function naikkelasaksi($id)
    {
        DB::table('master_siswa')->where('id', $id)->update(['id_kelas' => DB::raw('id_kelas + 1'), 'tanggal' => DB::raw('tanggal')]);
        $data = DB::table('master_siswa')->where('id', $id)->get();
        $id_kelas = $data[0]->id_kelas;
        if($data[0]->id_kelas == 6){
        DB::statement("INSERT INTO transaksi (id_siswa, id_kelas, debet, kredit, keterangan) (SELECT '" . $id . "' as id_siswa, ".$id_kelas." as id_kelas, '0' as debet, biaya_spp as kredit, CONCAT('Biaya SPP ',year(CURRENT_TIMESTAMP)) as keterangan FROM `spp` a join master_tahun b on a.id_tahun = b.id where b.kode = (select year(tanggal) from master_siswa where id = " . $data[0]->id . "))");
        return redirect()->back()
        ->with('success', 'siswa Anda Berhasil Naik Kelas');
        }
        if ($data[0]->status_makan == 2 && $data[0]->id_kelas != 6)
        {
            $harga_makanan = DB::table('makan')->join('master_tahun', 'master_tahun.id', '=', 'makan.id_tahun')
                ->select('biaya_makan')
                ->where('status', '2')
                ->where('master_tahun.kode', DB::raw('year(CURRENT_TIMESTAMP)'))
                ->get();
            $biayaperbulan = (int)$harga_makanan[0]->biaya_makan / 12;
            if ($biayaperbulan != 0)
            {
                DB::statement("INSERT INTO transaksi (id_siswa, id_kelas, debet, kredit, keterangan) (SELECT '" . $id . "' as id_siswa, ".$id_kelas." as id_kelas, '0' as debet, (" . $biayaperbulan . ") as kredit, CONCAT('Biaya makanan tahun',year(CURRENT_TIMESTAMP)) as keterangan FROM makan a join master_tahun b on a.id_tahun = b.id where b.kode = (select year(tanggal) from master_siswa where id = " . $id . ") and status = 2)");
            }
        }
        else if ($data[0]->status_makan == 1 && $data[0]->id_kelas != 6)
        {
            DB::statement("INSERT INTO transaksi (id_siswa, id_kelas, debet, kredit, keterangan) (SELECT '" . $id . "' as id_siswa, ".$id_kelas." as id_kelas, '0' as debet, biaya_makan as kredit, CONCAT('Biaya makanan ',year(CURRENT_TIMESTAMP)) as keterangan FROM makan a join master_tahun b on a.id_tahun = b.id where b.kode = (select year(tanggal) from master_siswa where id = " . $id . ") and status = 2)");
        }
        if ($data[0]->status_les != 0 && $data[0]->id_kelas != 6)
        {
            DB::statement("INSERT INTO transaksi (id_siswa, id_kelas, debet, kredit, keterangan) (SELECT " . $id . " as id_siswa, ".$id_kelas." as id_kelas, '0' as debet, biaya_les as kredit, CONCAT('Biaya Les ',year(CURRENT_TIMESTAMP)) as keterangan FROM `les` a join master_tahun b on a.id_tahun = b.id where b.kode = year(CURRENT_TIMESTAMP) order by a.biaya_les desc limit 1)");
        }
        // uang pakaian, uang spp, uang kegiatan, uang buku paket, uang gedung, uang makan
        // DB::statement("INSERT INTO transaksi (id_siswa, debet, kredit, keterangan) (SELECT '" . $id . "' as id_siswa, '0' as debet, biaya_pakaian as kredit, CONCAT('Biaya Pakaian ',year(CURRENT_TIMESTAMP)) as keterangan FROM `pakaian` a join master_tahun b on a.id_tahun = b.id order by b.id desc limit 1)");
        DB::statement("INSERT INTO transaksi (id_siswa, id_kelas, debet, kredit, keterangan) (SELECT " . $id . " as id_siswa, ".$id_kelas." as id_kelas, '0' as debet, biaya_spp as kredit, CONCAT('Biaya SPP ',year(CURRENT_TIMESTAMP)) as keterangan FROM `spp` a join master_tahun b on a.id_tahun = b.id where b.kode = (select year(tanggal) from master_siswa where id = " . $data[0]->id . "))");
        // echo "SELECT " . $id . " as id_siswa, '0' as debet, biaya_spp as kredit, CONCAT('Biaya SPP ',year(CURRENT_TIMESTAMP)) as keterangan FROM `spp` a join master_tahun b on a.id_tahun = b.id where b.kode = (select year(tanggal) from master_siswa where id = " . $data[0]->id . ")";
        // DB::statement("INSERT INTO transaksi (id_siswa, debet, kredit, keterangan) (SELECT '" . $id . "' as id_siswa, '0' as debet, a.biaya_kegiatan as kredit, CONCAT('Biaya Kegiatan ',year(CURRENT_TIMESTAMP)) as keterangan FROM `kegiatan` a join master_tahun b on a.id_tahun = b.id where b.kode = (select year(tanggal) from master_siswa where id = " . $id . ") and status = 1)");
        // DB::statement("INSERT INTO transaksi (id_siswa, debet, kredit, keterangan) (SELECT '" . $id . "' as id_siswa, '0' as debet, biaya_buku as kredit, CONCAT('Biaya Buku ',a.nama_buku,' ',year(CURRENT_TIMESTAMP)) as keterangan FROM `buku` a join master_tahun b on a.id_tahun = b.id where b.kode = (select year(tanggal) from master_siswa where id = " . $id . ") and a.id_kelas = (select id_kelas from master_siswa where id = " . $id . "))");

        return redirect()->back()
            ->with('success', 'siswa Anda Berhasil Naik Kelas');
    }
    //relasi button tidak naik kelas
    public function tinggalkelasaksi($id)
    {
        DB::table('master_siswa')->where('id', $id)->update(['id_kelas' => DB::raw('id_kelas'), 'tanggal' => DB::raw('tanggal') ]);
        $data = DB::table('master_siswa')->where('id', $id)->get();
        $id_kelas = $data[0]->id_kelas;
        if($data[0]->id_kelas == 6){
            DB::statement("INSERT INTO transaksi (id_siswa, id_kelas, debet, kredit, keterangan) (SELECT '" . $id . "' as id_siswa, ".$id_kelas." as id_kelas, '0' as debet, biaya_spp as kredit, CONCAT('Biaya SPP ',year(CURRENT_TIMESTAMP)) as keterangan FROM `spp` a join master_tahun b on a.id_tahun = b.id where b.kode = (select year(tanggal) from master_siswa where id = " . $id . "))");
            return redirect()->back()
            ->with('success', 'siswa Anda Berhasil Tinggal Kelas');
            }
        if ($data[0]->status_makan == 2 && $data[0]->id_kelas != 6)
        {
            $harga_makanan = DB::table('makan')->join('master_tahun', 'master_tahun.id', '=', 'makan.id_tahun')
                ->select('biaya_makan')
                ->where('status', '2')
                ->where('master_tahun.kode', DB::raw('year(CURRENT_TIMESTAMP)'))
                ->get();
            $biayaperbulan = (int)$harga_makanan[0]->biaya_makan / 12;
            if ($biayaperbulan != 0)
            {
                DB::statement("INSERT INTO transaksi (id_siswa, id_kelas, debet, kredit, keterangan) (SELECT '" . $id . "' as id_siswa, ".$id_kelas." as id_kelas, '0' as debet, (" . $biayaperbulan . ") as kredit, CONCAT('Biaya makanan tahun',year(CURRENT_TIMESTAMP)) as keterangan FROM makan a join master_tahun b on a.id_tahun = b.id where b.kode = (select year(tanggal) from master_siswa where id = " . $id . ") and status = 2)");
            }

        }
        else if ($data[0]->status_makan == 1 && $data[0]->id_kelas != 6)
        {
            DB::statement("INSERT INTO transaksi (id_siswa, id_kelas, debet, kredit, keterangan) (SELECT '" . $id . "' as id_siswa, ".$id_kelas." as id_kelas, '0' as debet, biaya_makan as kredit, CONCAT('Biaya makanan ',year(CURRENT_TIMESTAMP)) as keterangan FROM makan a join master_tahun b on a.id_tahun = b.id where b.kode = (select year(tanggal) from master_siswa where id = " . $id . ") and status = 2)");
        }
        if ($data[0]->status_les != 0 && $data[0]->id_kelas != 6)
        {
            DB::statement("INSERT INTO transaksi (id_siswa, id_kelas, debet, kredit, keterangan) (SELECT " . $id . " as id_siswa, ".$id_kelas." as id_kelas, '0' as debet, biaya_les as kredit, CONCAT('Biaya Les ',year(CURRENT_TIMESTAMP)) as keterangan FROM `les` a join master_tahun b on a.id_tahun = b.id where b.kode = year(CURRENT_TIMESTAMP) order by a.biaya_les desc limit 1)");
        }
        // DB::statement("INSERT INTO transaksi (id_siswa, debet, kredit, keterangan) (SELECT '" . $id . "' as id_siswa, '0' as debet, biaya_pakaian as kredit, CONCAT('Biaya Pakaian ',year(CURRENT_TIMESTAMP)) as keterangan FROM `pakaian` a join master_tahun b on a.id_tahun = b.id order by b.id desc limit 1)");
        DB::statement("INSERT INTO transaksi (id_siswa, id_kelas, debet, kredit, keterangan) (SELECT '" . $id . "' as id_siswa, ".$id_kelas." as id_kelas, '0' as debet, biaya_spp as kredit, CONCAT('Biaya SPP ',year(CURRENT_TIMESTAMP)) as keterangan FROM `spp` a join master_tahun b on a.id_tahun = b.id where b.kode = (select year(tanggal) from master_siswa where id = " . $id . "))");
        // DB::statement("INSERT INTO transaksi (id_siswa, debet, kredit, keterangan) (SELECT '" . $id . "' as id_siswa, '0' as debet, a.biaya_kegiatan as kredit, CONCAT('Biaya Kegiatan ',year(CURRENT_TIMESTAMP)) as keterangan FROM `kegiatan` a join master_tahun b on a.id_tahun = b.id where b.kode = (select year(tanggal) from master_siswa where id = " . $id . ") and status = 1)");
        // DB::statement("INSERT INTO transaksi (id_siswa, debet, kredit, keterangan) (SELECT '" . $id . "' as id_siswa, '0' as debet, biaya_buku as kredit, CONCAT('Biaya Buku ',a.nama_buku,' ',year(CURRENT_TIMESTAMP)) as keterangan FROM `buku` a join master_tahun b on a.id_tahun = b.id where b.kode = (select year(tanggal) from master_siswa where id = " . $id . ") and a.id_kelas = (select id_kelas from master_siswa where id = " . $id . "))");
        return redirect()->back()
            ->with('success', 'siswa Anda Berhasil Tinggal Kelas');
    }

    public function perbulan()
    {
        $data = DB::table('master_siswa')->join('master_kelas', 'master_kelas.id', '=', 'master_siswa.id_kelas')
            ->whereBetween('master_siswa.id_kelas', [0, 6])
            ->select('*', 'master_siswa.nama_siswa as nama_siswa', 'master_kelas.nama_kelas AS nama_kelas', 'master_siswa.id AS siswa_id')
            ->get();
        return view('perbulan', ['data' => $data]);
    }
    public function kirimangsuran($id)
    {
        $data = DB::table('master_siswa')->where('id', $id)->get();
        $id_kelas = $data[0]->id_kelas;
        if ($data[0]->status_makan == 2 && $data[0]->id_kelas != 6)
        {
            $harga_makanan = DB::table('makan')->join('master_tahun', 'master_tahun.id', '=', 'makan.id_tahun')
                ->select('biaya_makan')
                ->where('status', '2')
                ->where('master_tahun.kode', DB::raw('year(CURRENT_TIMESTAMP)'))
                ->get();
            $biayaperbulan = (int)$harga_makanan[0]->biaya_makan / 12;
            if ($biayaperbulan != 0)
            {
                DB::statement("INSERT INTO transaksi (id_siswa, id_kelas, debet, kredit, keterangan) (SELECT '" . $id . "' as id_siswa, ".$id_kelas." as id_kelas, '0' as debet, (" . $biayaperbulan . ") as kredit, CONCAT('Biaya makanan tahun',year(CURRENT_TIMESTAMP)) as keterangan FROM makan a join master_tahun b on a.id_tahun = b.id where b.kode = (select year(tanggal) from master_siswa where id = " . $id . ") and status = 2)");
            }
        }
        else if ($data[0]->status_makan == 1 && $data[0]->id_kelas != 6)
        {
            // DB::statement("INSERT INTO transaksi (id_siswa, debet, kredit, keterangan) (SELECT '" . $id . "' as id_siswa, '0' as debet, biaya_makan as kredit, CONCAT('Biaya makanan ',year(CURRENT_TIMESTAMP)) as keterangan FROM makan a join master_tahun b on a.id_tahun = b.id where b.kode = (select year(tanggal) from master_siswa where id = " . $id . ") and status = 2)");
        }
        if ($data[0]->status_les != 0 && $data[0]->id_kelas != 6)
        {
            DB::statement("INSERT INTO transaksi (id_siswa, id_kelas, debet, kredit, keterangan) (SELECT " . $id . " as id_siswa, ".$id_kelas." as id_kelas, '0' as debet, biaya_les as kredit, CONCAT('Biaya Les ',year(CURRENT_TIMESTAMP)) as keterangan FROM `les` a join master_tahun b on a.id_tahun = b.id where b.kode = year(CURRENT_TIMESTAMP) order by a.biaya_les desc limit 1)");
        }
        DB::statement("INSERT INTO transaksi (id_siswa, id_kelas, debet, kredit, keterangan) (SELECT " . $id . " as id_siswa, ".$id_kelas." as id_kelas, '0' as debet, biaya_spp as kredit, CONCAT('Biaya SPP ',year(CURRENT_TIMESTAMP)) as keterangan FROM `spp` a join master_tahun b on a.id_tahun = b.id where b.kode = (select year(tanggal) from master_siswa where id = " . $id . "))");

        // DB::statement("INSERT INTO transaksi (id_siswa, debet, kredit, keterangan) (SELECT '" . $id . "' as id_siswa, '0' as debet, biaya_makan as kredit, CONCAT('Biaya makanan tahun',year(CURRENT_TIMESTAMP)) as keterangan FROM makan a join master_tahun b on a.id_tahun = b.id where b.kode = (year(CURRENT_TIMESTAMP)+1) and status = 2)");
        return redirect()->back()
            ->with('success', 'Anda Berhasil Kirim Angsuran');
    }
    public function transaksiview() // digunakan untuk tampilan pembayaran
    {
        $data = DB::table('master_siswa')->join('master_kelas', 'master_kelas.id', '=', 'master_siswa.id_kelas')
            ->select('*', 'master_siswa.nama_siswa as nama_siswa', 'master_kelas.nama_kelas AS nama_kelas', 'master_siswa.id AS siswa_id')
            ->get();
        return view('transaksi', ['data' => $data]);
    }
    public function transaksibayar($id) //akumulasi tagihan yang harus di bayar
    {
        $data = DB::table('master_siswa')->where('id', $id)->get();
        $id_kelas = $data[0]->id_kelas;
        $data = DB::table('transaksi')->where('id_siswa', $id)->select(DB::raw('COALESCE(sum(kredit-debet),0) as tagihan') , 'id_siswa')
            ->groupBy('id_siswa')
            ->get();
        return view('transaksi.bayar', ['data' => $data, 'id_kelas' => $id_kelas]);
    }
    public function transaksibayaraksi(Request $r) // diguunakan untuk submit pembayaran
    {
        $id = $r->input('id_siswa');
        $id_kelas = $r->input('id_kelas');
        $keterangan = $r->input('keterangan');
        $bayar = $r->input('bayar');
        $file = $r->file('file');
        $tujuan_upload = 'data_file';
        $file->move($tujuan_upload, $file->getClientOriginalName());
        DB::table('transaksi')
            ->insert(['id_siswa' => $id, 'id_kelas' => $id_kelas, 'keterangan' => $keterangan, 'debet' => $bayar, 'kredit' => 0, 'bukti' => $file->getClientOriginalName() ]);
        return redirect()
            ->back()
            ->with('success', 'Transaksi Berhasil Di bayar');
    }
    //lain lain
    public function lainlain()
    {
        $data = DB::table('formulir')->join('master_tahun', 'master_tahun.id', '=', 'formulir.id_tahun')
            ->get();
        return view('lainlain', ['data' => $data]);
    }
    public function lainlaindelete($id)
    {
        DB::table('formulir')->where('id', '=', $id)->delete();
        return redirect()
            ->back()
            ->with('success', 'Data Anda Berhasil Dihapus');
    }

    public function lainlainupdate($id)
    {
        $tahun = DB::table('master_tahun')->get();
        $data = DB::table('formulir')->where('id', $id)->get();
        return view('lainlain.update', ['data' => $data, 'tahun' => $tahun]);
    }
    public function lainlainupdateaksi(Request $r)
    {
        $id = $r->input('id');
        $id_tahun = $r->input('id_tahun');
        $harga = $r->input('harga');
        $keterangan = $r->input('keterangan');
        DB::table('formulir')
            ->where('id', $id)->update(['id_tahun' => $id_tahun, 'biaya_formulir' => $harga]);
        return redirect()->back()
            ->with('success', 'Data Anda Berhasil Diubah');
    }
    public function lainlainadd()
    {
        $tahun = DB::table('master_tahun')->get();
        return view('lainlain.add', ['tahun' => $tahun]);
    }
    public function lainlainaddaksi(Request $r)
    {
        $id_tahun = $r->input('id_tahun');
        $harga = $r->input('harga');
        DB::table('formulir')
            ->insert(['id_tahun' => $id_tahun, 'biaya_formulir' => $harga]);
        return redirect()->back()
            ->with('success', 'Data Anda Berhasil Dimasukkan');
    }
    //lain lain
    //gadung
    public function gedung()
    {
        $data = DB::table('gedung')->join('master_tahun', 'master_tahun.id', '=', 'gedung.id_tahun')
            ->select('*', 'gedung.id as gedung_id')
            ->get();
        return view('gedung', ['data' => $data]);
    }
    public function gedungdelete($id)
    {
        DB::table('gedung')->where('id', '=', $id)->delete();
        return redirect()
            ->back()
            ->with('success', 'Data Anda Berhasil Dihapus');
    }

    public function gedungupdate($id)
    {
        $tahun = DB::table('master_tahun')->get();
        $data = DB::table('gedung')->where('id', $id)->get();
        return view('gedung.update', ['data' => $data, 'tahun' => $tahun]);
    }
    public function gedungupdateaksi(Request $r)
    {
        $id = $r->input('id');
        $id_tahun = $r->input('id_tahun');
        $uang_gedung = $r->input('uang_gedung');
        DB::table('gedung')
            ->where('id', $id)->update(['id_tahun' => $id_tahun, 'biaya_gedung' => $uang_gedung]);
        return redirect()->back()
            ->with('success', 'Data Anda Berhasil Diubah');
    }

    public function gedungadd()
    {
        $tahun = DB::table('master_tahun')->get();
        return view('gedung.add', ['tahun' => $tahun]);
    }
    public function gedungaddaksi(Request $r)
    {
        $id_tahun = $r->input('id_tahun');
        $uang_gedung = $r->input('uang_gedung');
        DB::table('gedung')
            ->insert(['id_tahun' => $id_tahun, 'biaya_gedung' => $uang_gedung]);
        return redirect()->back()
            ->with('success', 'Data Anda Berhasil Dimasukkan');
    }

    //gedung
    //pakaian
    public function pakaian()
    {
        $data = DB::table('pakaian')->join('master_tahun', 'master_tahun.id', '=', 'pakaian.id_tahun')
            ->get();
        return view('pakaian', ['data' => $data]);
    }
    public function pakaiandelete($id)
    {
        DB::table('pakaian')->where('id', '=', $id)->delete();
        return redirect()
            ->back()
            ->with('success', 'Data Anda Berhasil Dihapus');
    }

    public function pakaianupdate($id)
    {
        $tahun = DB::table('master_tahun')->get();
        $data = DB::table('pakaian')->where('id', $id)->get();
        return view('pakaian.update', ['data' => $data, 'tahun' => $tahun]);
    }
    public function pakaianupdateaksi(Request $r)
    {
        $id = $r->input('id');
        $harga_pakaian = $r->input('harga');
        $id_tahun = $r->input('id_tahun');
        DB::table('pakaian')
            ->where('id', $id)->update(['biaya_pakaian' => $harga_pakaian, 'id_tahun' => $id_tahun]);
        return redirect()->back()
            ->with('success', 'Data Anda Berhasil Diubah');
    }

    public function pakaianadd()
    {
        $tahun = DB::table('master_tahun')->get();
        return view('pakaian.add', ['tahun' => $tahun]);
    }
    public function pakaianaddaksi(Request $r)
    {
        $id_tahun = $r->input('id_tahun');
        $harga_pakaian = $r->input('harga');
        DB::table('pakaian')
            ->insert(['id_tahun' => $id_tahun, 'biaya_pakaian' => $harga_pakaian]);
        return redirect()->back()
            ->with('success', 'Data Anda Berhasil Dimasukkan');
    }
    //pakaian
    //les
    public function masterles()
    {
        $data = DB::table('les')->join('master_tahun', 'master_tahun.id', '=', 'les.id_tahun')
            ->select('*', 'les.id as les_id')
            ->get();
        return view('masterbiayalesan', ['data' => $data]);
    }
    public function masterlesdelete($id)
    {
        DB::table('les')->where('id', '=', $id)->delete();
        return redirect()
            ->back()
            ->with('success', 'Data Anda Berhasil Dihapus');
    }
    public function masterlesupdate($id)
    {
        $data = DB::table('les')->where('id', $id)->get();
        $tahun = DB::table('master_tahun')->get();

        return view('les.update', ['data' => $data, 'tahun' => $tahun]);
    }
    public function masterlesupdateaksi(Request $r)
    {
        $id = $r->input('id');
        $harga = $r->input('harga');
        $id_tahun = $r->input('id_tahun');
        DB::table('les')
            ->where('id', $id)->update(['biaya_les' => $harga, 'id_tahun' => $id_tahun]);
        return redirect()->back()
            ->with('success', 'Data Anda Berhasil Diubah');
    }
    public function masterlesadd()
    {
        $tahun = DB::table('master_tahun')->get();
        return view('les.add', ['tahun' => $tahun]);
    }
    public function masterlesaddaksi(Request $r)
    {
        $harga = $r->input('harga');
        $id_tahun = $r->input('id_tahun');
        DB::table('les')
            ->insert(['biaya_les' => $harga, 'id_tahun' => $id_tahun]);
        return redirect()->back()
            ->with('success', 'Data Anda Berhasil Dimasukkan');
    }
    //les
    public function ltagihan(Request $r)
    {
        $id_kelas = $r->input('id_kelas');
if ($id_kelas == null) {
    $data = DB::table('transaksi')->join('master_siswa', 'transaksi.id_siswa', '=', 'master_siswa.id')->join('master_kelas', 'master_kelas.id', '=', 'transaksi.id_kelas')
    ->get();
}else {
            $data = DB::table('transaksi')->join('master_siswa', 'transaksi.id_siswa', '=', 'master_siswa.id')->join('master_kelas', 'master_kelas.id', '=', 'transaksi.id_kelas')
            ->where('transaksi.id_kelas', $id_kelas)->get();
}
        
        return view('Laporan_tagihan', ['data' => $data]);
    }
    public function lpembayaran(Request $r)
    {
        $id_kelas = $r->input('id_kelas');
        if ($id_kelas != null) {
            $data = DB::table('transaksi')->join('master_siswa', 'transaksi.id_siswa', '=', 'master_siswa.id')->join('master_kelas', 'master_kelas.id', '=', 'transaksi.id_kelas')
            ->where('transaksi.id_kelas', $id_kelas)->get();
        }else {
        $data = DB::table('transaksi')->join('master_siswa', 'transaksi.id_siswa', '=', 'master_siswa.id')->join('master_kelas', 'master_kelas.id', '=', 'transaksi.id_kelas')
            ->get();
        }
        return view('laporan_pembayaran', ['data' => $data]);
    }
    public function lpembayaransiswa($id, Request $r)
    {
        $id_kelas = $r->input('id_kelas');
        if ($id_kelas != null) {
            $data = DB::table('transaksi')->join('master_siswa', 'transaksi.id_siswa', '=', 'master_siswa.id')->join('master_kelas', 'master_kelas.id', '=', 'transaksi.id_kelas')
            ->where('id_siswa', $id)->where('transaksi.id_kelas', $id_kelas)->get();
        }else {
            $data = DB::table('transaksi')->join('master_siswa', 'transaksi.id_siswa', '=', 'master_siswa.id')->join('master_kelas', 'master_kelas.id', '=', 'transaksi.id_kelas')
            ->where('id_siswa', $id)->get();
            }
        return view('laporan_pembayaran', ['data' => $data]);
    }

}

