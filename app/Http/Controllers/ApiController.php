<?php

namespace App\Http\Controllers;

use App\Models\Cekin;
use App\Models\Cekout;
use App\Models\Izin;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiController extends Controller
{
    public function login(Request $request)
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $data =
                [
                    'status' => 'Berhasill',
                    'data' => Auth::user()
                ];
        } else {
            $data =
                [
                    'status' => 'Gagal',
                    'data' => null
                ];
        }
        return response()->json($data);
    }

    public function getDataUser(User $user)
    {
        $data =
            [
                'status' => 'Berhasill',
                'data' => $user
            ];
        return response()->json($data);
    }

    public function getUserCekinCekout(User $user)
    {
        $cekin = Cekin::where([["user_id", "=", $user->id], ["tanggal", "=", Carbon::now()->format('Y-m-d')]])->pluck("jam")->first();
        $cekout = Cekout::where([["user_id", "=", $user->id], ["tanggal", "=", Carbon::now()->format('Y-m-d')]])->pluck("jam")->first();
        $data =
            [
                'status' => 'Berhasill',
                'nama' => $user->name,
                'foto' => $user->foto,
                'cekin' => $cekin,
                'cekout' => $cekout,
            ];
        return response()->json($data);
    }

    public function ubahAlamat(Request $request)
    {
        User::find($request->user_id)
            ->update([
                "alamat" => $request->alamat
            ]);

        $data = [
            "status" => "Berhasil",
            "alamat" => $request->alamat
        ];
        return response()->json($data);
    }
    public function ubahNoTelp(Request $request)
    {
        User::find($request->user_id)
            ->update([
                "no_telp" => $request->no_telp
            ]);
        $data = [
            "status" => "Berhasil",
            "alamat" => $request->no_telp
        ];
        return response()->json($data);
    }

    public function checkin(Request $request)
    {
        // Cek apakah jam 7:30 - 08:30
        if (Carbon::now()->format("H:i") >= "07:30" && Carbon::now()->format("H:i") <= "23:30") {
            // cekek apakah user sudah cekin
            $cek = Cekin::where([['user_id', "=", $request->user_id], ["tanggal", "=", Carbon::now()->format("Y-m-d")]])->first();
            if ($cek) {
                $data = [
                    "status" => "gagal",
                    "keterangan" => "Kamu telah melakukan check in",
                    "data" => null
                ];
            } else {
                Cekin::create([
                    "user_id" => $request->user_id,
                    "keterangan" => "On Time",
                    "jam" => Carbon::now()->format("H:i:s"),
                    "tanggal" => Carbon::now()->format("Y-m-d"),
                    "latitude" => $request->latitude,
                    "longitude" => $request->longitude
                ]);
                $data = [
                    "status" => "berhasil",
                    "keterangan" => "Berhasil melakukan check in",
                    "data" => [
                        "user_id" => $request->user_id,
                        "keterangan" => "On Time",
                        "tanggal" => Carbon::now()->format("Y-m-d"),
                        "latitude" => $request->latitude,
                        "longitude" => $request->longitude
                    ]
                ];
            }
        } else {
            $data = [
                "status" => "gagal",
                "keterangan" => "Kamu belum bisa melakukan check in",
                "data" => null
            ];
        }
        return response()->json($data);
    }

    public function checkout(Request $request)
    {
        // Cek apakah jam 16:30 - 17:30
        if (Carbon::now()->format("H:i") >= "00:30" && Carbon::now()->format("H:i") <= "23:30") {
            // cekek apakah user sudah cekout
            $cek = Cekout::where([['user_id', "=", $request->user_id], ["tanggal", "=", Carbon::now()->format("Y-m-d")]])->first();
            if ($cek) {
                $data = [
                    "status" => "gagal",
                    "keterangan" => "Kamu telah melakukan check out",
                    "data" => null
                ];
            } else {
                Cekout::create([
                    "user_id" => $request->user_id,
                    "keterangan" => "On Time",
                    "jam" => Carbon::now()->format("H:i:s"),
                    "tanggal" => Carbon::now()->format("Y-m-d"),
                    "latitude" => $request->latitude,
                    "longitude" => $request->longitude,
                    "kegiatan" => $request->kegiatan
                ]);
                $data = [
                    "status" => "berhasil",
                    "keterangan" => "Berhasil melakukan check out",
                    "data" => [
                        "user_id" => $request->user_id,
                        "keterangan" => "On Time",
                        "tanggal" => Carbon::now()->format("Y-m-d"),
                        "latitude" => $request->latitude,
                        "longitude" => $request->longitude
                    ]
                ];
            }
        } else {
            $data = [
                "status" => "gagal",
                "keterangan" => "Kamu belum bisa melakukan check out",
                "data" => null
            ];
        }
        return response()->json($data);
    }

    public function izin(Request $request)
    {
        $start_date = Carbon::parse($request->start_date);
        $end_date = Carbon::parse($request->end_date)->addDay();

        Izin::create([
            "user_id" => $request->user_id,
            "jenis" => $request->jenis,
            "keterangan" => $request->keterangan,
            "tanggal_mulai" => $request->start_date,
            "tanggal_selesai" => $request->end_date,
        ]);

        while (!$start_date->isSameDay($end_date)) {
            $cekin = Cekin::where([['user_id', "=", $request->user_id], ["tanggal", "=", $start_date]])->first();
            $cekout = Cekout::where([['user_id', "=", $request->user_id], ["tanggal", "=", $start_date]])->first();
            if ($cekin && $cekout) {
            } else {
                Cekin::create([
                    "user_id" => $request->user_id,
                    "keterangan" => $request->jenis,
                    "jam" => "Auto Record",
                    "tanggal" => $start_date->format("Y-m-d"),
                    "latitude" => null,
                    "longitude" => null
                ]);
                Cekout::create([
                    "user_id" => $request->user_id,
                    "keterangan" => $request->jenis,
                    "jam" => "Auto Record",
                    "tanggal" => $start_date->format("Y-m-d"),
                    "kegiatan" => $request->jenis,
                    "latitude" => null,
                    "longitude" => null
                ]);
            }
            $start_date->addDay();
        }
        $data = [
            "status" => "berhasil",
            "keterangan" => "Izin diproses",
            "data" => [
                "user_id" => $request->user_id,
                "keterangan" => $request->keterangan,
                "tanggal" => $request->start_date
            ]
        ];
        return response()->json($data);
    }

    public function getUserIzin(User $user)
    {
        $izin = Izin::where([['user_id', "=", $user->id]])->get();
        $data = [
            "status" => "berhasil",
            "keterangan" => "Berhasil mengambil data izin",
            "data" => $izin
        ];
        return response()->json($data);
    }

    public function getUserAktivitas(User $user)
    {
        $aktivitas = Cekout::where([['user_id', "=", $user->id]])->get();
        $waktu = Cekout::where([['user_id', "=", $user->id]])->pluck('tanggal');
        // ubah bahasa Carbon ke Indonesia
        $waktu = $waktu->map(function ($item) {
            return Carbon::parse($item)->locale('id')->isoFormat('dddd, D MMMM Y');
        });

        $data = [
            "status" => "berhasil",
            "keterangan" => "Berhasil mengambil data aktivitas",
            "data" => $aktivitas,
            "waktu" => $waktu
        ];
        return response()->json($data);
    }
    public function getUserAbsen(User $user)
    {
        // looping tanggal bulan ini
        $start_date = Carbon::now()->startOfMonth();
        $end_date = Carbon::now()->addDay();
        $hadir = 0;
        $tidak_hadir = 0;
        $izin = 0;

        $data_cekin_cekout = [];
        while (!$start_date->isSameDay($end_date)) {
            $cekin = Cekin::where([['user_id', "=", $user->id], ["tanggal", "=", $start_date]])->first();
            $cekout = Cekout::where([['user_id', "=", $user->id], ["tanggal", "=", $start_date]])->first();
            if ($cekin && $cekout) {
                if ($cekin->keterangan == "On Time" && $cekout->keterangan == "On Time") {
                    $data_cekin_cekout[] = [
                        "tanggal" => Carbon::parse($start_date)->locale('id')->isoFormat('dddd, D MMMM Y'),
                        "jam_cekin" => $cekin->jam,
                        "jam_cekout" => $cekout->jam,
                        "lokasi_cekin" => $cekin->latitude . "," . $cekin->longitude,
                        "lokasi_cekout" => $cekout->latitude . "," . $cekout->longitude,
                    ];
                    $hadir++;
                } elseif ($cekin->keterangan == "Terlambat" && $cekout->keterangan == "On Time") {
                    $data_cekin_cekout[] = [
                        "tanggal" => Carbon::parse($start_date)->locale('id')->isoFormat('dddd, D MMMM Y'),
                        "jam_cekin" => $cekin->jam,
                        "jam_cekout" => $cekout->jam,
                        "lokasi_cekin" => $cekin->keterangan,
                        "lokasi_cekout" => $cekout->latitude . "," . $cekout->longitude,
                    ];
                    $hadir++;
                } elseif ($cekin->keterangan == "On Time" && $cekout->keterangan == "Terlambat") {
                    $data_cekin_cekout[] = [
                        "tanggal" => Carbon::parse($start_date)->locale('id')->isoFormat('dddd, D MMMM Y'),
                        "jam_cekin" => $cekin->jam,
                        "jam_cekout" => $cekout->jam,
                        "lokasi_cekin" => $cekin->latitude . "," . $cekin->longitude,
                        "lokasi_cekout" => $cekout->keterangan,
                    ];
                    $hadir++;
                } elseif ($cekin->keterangan == "Terlambat" && $cekout->keterangan == "Terlambat") {
                    $data_cekin_cekout[] = [
                        "tanggal" => Carbon::parse($start_date)->locale('id')->isoFormat('dddd, D MMMM Y'),
                        "jam_cekin" => $cekin->jam,
                        "jam_cekout" => $cekout->jam,
                        "lokasi_cekin" => $cekin->keterangan,
                        "lokasi_cekout" => $cekout->keterangan,
                    ];
                    $tidak_hadir++;
                } else {
                    $data_cekin_cekout[] = [
                        "tanggal" => Carbon::parse($start_date)->locale('id')->isoFormat('dddd, D MMMM Y'),
                        "jam_cekin" => $cekin->jam,
                        "jam_cekout" => $cekout->jam,
                        "lokasi_cekin" => $cekin->keterangan,
                        "lokasi_cekout" => $cekout->keterangan,
                    ];
                    $izin++;
                }
            } else {
                $tidak_hadir++;
            }
            $start_date->addDay();
        }

        $data = [
            "status" => "berhasil",
            "keterangan" => "Berhasil mengambil data aktivitas",
            "hadir" => $hadir,
            "tidak_hadir" => $tidak_hadir,
            "izin" => $izin,
            "data" => $data_cekin_cekout
        ];
        return response()->json($data);
    }
}
