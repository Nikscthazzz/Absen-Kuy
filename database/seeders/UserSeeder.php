<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'admin',
            'email' => 'admin@gmail.com',
            'foto' => "hana.png",
            'password' => bcrypt("12345"),
            "departemen" => "Hubungan luar",
            "jabatan" => "Ketua",
            "nip" => "10191077",
            "alamat" => "Jl. Ahmad Yani",
            "no_telp" => "081243942304"
        ]);
        User::create([
            'name' => 'artha',
            'email' => 'artha@gmail.com',
            'foto' => "hana.png",
            'password' => bcrypt("071219"),
            "departemen" => "Hubungan luar",
            "jabatan" => "Ketua",
            "nip" => "10191077",
            "alamat" => "Jl. Ahmad Yani",
            "no_telp" => "081243942304"
        ]);
    }
}
