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
        $end_date = Carbon::parse($request->end_date);

        while (!$start_date->isSameDay($end_date)) {
            $cek = Izin::where([['user_id', "=", $request->user_id], ["tanggal", "=", $start_date]])->first();
            if ($cek) {
            } else {
                Izin::create([
                    "user_id" => $request->user_id,
                    "jenis" => $request->jenis,
                    "keterangan" => $request->keterangan,
                    "tanggal" => $start_date->format("Y-m-d"),
                ]);
                Cekin::create([
                    "user_id" => $request->user_id,
                    "keterangan" => "Izin",
                    "jam" => "Auto Record",
                    "tanggal" => $start_date->format("Y-m-d"),
                    "latitude" => null,
                    "longitude" => null
                ]);
                Cekout::create([
                    "user_id" => $request->user_id,
                    "keterangan" => "Izin",
                    "jam" => "Auto Record",
                    "tanggal" => $start_date->format("Y-m-d"),
                    "kegiatan" => "Izin",
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
}
