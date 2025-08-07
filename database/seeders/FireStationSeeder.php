<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class FireStationSeeder extends Seeder
{
    public function run(): void
    {
        $json = File::get(public_path('data/turkey-assets.geojson'));
        $data = json_decode($json);
        foreach ($data->features as $feature) {
            if (isset($feature->properties->amenity) && $feature->properties->amenity === 'fire_station') {
                // 1. Her itfaiye için bir kullanıcı oluştur
                $stationUser = User::create([
                    'name' => $feature->properties->name,
                    'email' => Str::slug($feature->properties->name, '-') . '@turkyan.com',
                    'password' => Hash::make('password'),
                    'role' => 'itfaiye',
                ]);

                // 2. fire_stations tablosuna kaydet ve kullanıcıyı bağla
                DB::table('fire_stations')->insert([
                    'name' => $feature->properties->name,
                    'phone' => $feature->properties->phone ?? 'Belirtilmemiş',
                    'latitude' => $feature->geometry->coordinates[1],
                    'longitude' => $feature->geometry->coordinates[0],
                    'user_id' => $stationUser->id,
                ]);
            }
        }
    }
}