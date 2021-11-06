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
            'name' => 'Sallie Trixie Zebada Mansurina',
            'email' => 'admin@gmail.com',
            'foto' => "Sallie Trixie Zebada Mansurina.jpeg",
            'password' => bcrypt("12345"),
            "departemen" => "Keprofesian",
            "jabatan" => "Ketua",
            "nip" => "10191077",
            "alamat" => "Jl. Ahmad Yani",
            "no_telp" => "081243942304"
        ]);
        User::create([
            'name' => 'Arya Candra',
            'email' => 'artha@gmail.com',
            'foto' => "Arya Candra.jpg",
            'password' => bcrypt("071219"),
            "departemen" => "Hubungan luar",
            "jabatan" => "Ketua",
            "nip" => "11191012",
            "alamat" => "Jl. Ahmad Yani",
            "no_telp" => "081243942304"
        ]);
    }
}
