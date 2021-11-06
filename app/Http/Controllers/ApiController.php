<?php

namespace App\Http\Controllers;

use App\Models\User;
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
}
