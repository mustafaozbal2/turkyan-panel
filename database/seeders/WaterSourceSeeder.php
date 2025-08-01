<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class WaterSourceSeeder extends Seeder
{
    public function run(): void
    {
        $json = File::get(public_path('data/turkey-assets.geojson'));
        $data = json_decode($json);
        foreach ($data->features as $feature) {
            // DÜZELTME: Önce 'natural' özelliğinin var olup olmadığını kontrol et
            if (isset($feature->properties->natural) && $feature->properties->natural === 'water') {
                DB::table('water_sources')->insert([
                    'name' => $feature->properties->name,
                    'type' => $feature->properties->water,
                    'latitude' => $feature->geometry->coordinates[1],
                    'longitude' => $feature->geometry->coordinates[0],
                ]);
            }
        }
    }
}