<?php

namespace Database\Seeders;

use App\Models\Sparepart;
use App\Models\Category;
use Illuminate\Database\Seeder;

class SparepartSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil semua kategori
        $categories = Category::all()->keyBy('name');

        // Data sparepart Viar berdasarkan gambar
        $spareparts = [
            // =============================================
            // KATEGORI MESIN
            // =============================================
            [
                'code' => 'SP-VR-001',
                'name' => 'Piston Ring Viar',
                'category' => 'Mesin',
                'purchase_price' => 55000,
                'selling_price' => 75000,
                'stock' => 25,
                'min_stock' => 5,
                'brand' => 'Viar Original',
                'location_rack' => 'A-01',
                'description' => 'Piston ring set Viar original untuk performa mesin optimal'
            ],
            [
                'code' => 'SP-VR-002',
                'name' => 'SKEK Cylinder Karya',
                'category' => 'Mesin',
                'purchase_price' => 5000,
                'selling_price' => 7000,
                'stock' => 30,
                'min_stock' => 10,
                'brand' => 'Karya',
                'location_rack' => 'A-02',
                'description' => 'Skek cylinder berkualitas untuk Viar'
            ],
            [
                'code' => 'SP-VR-003',
                'name' => 'Y-Gasket Cylinder Head Karya',
                'category' => 'Mesin',
                'purchase_price' => 18000,
                'selling_price' => 25000,
                'stock' => 40,
                'min_stock' => 10,
                'brand' => 'Karya',
                'location_rack' => 'A-03',
                'description' => 'Gasket cylinder head Viar'
            ],
            [
                'code' => 'SP-VR-004',
                'name' => 'Gasket Cylinder Block',
                'category' => 'Mesin',
                'purchase_price' => 2000,
                'selling_price' => 3000,
                'stock' => 50,
                'min_stock' => 15,
                'brand' => 'Viar Original',
                'location_rack' => 'A-04',
                'description' => 'Gasket cylinder block Viar'
            ],
            [
                'code' => 'SP-VR-005',
                'name' => 'Gasket Cylinder Head',
                'category' => 'Mesin',
                'purchase_price' => 35000,
                'selling_price' => 47000,
                'stock' => 20,
                'min_stock' => 5,
                'brand' => 'Viar Original',
                'location_rack' => 'A-05',
                'description' => 'Gasket cylinder head Viar original'
            ],

            // =============================================
            // KATEGORI KELISTRIKAN
            // =============================================
            [
                'code' => 'SP-VR-006',
                'name' => 'Kabel Body Viar',
                'category' => 'Kelistrikan',
                'purchase_price' => 45000,
                'selling_price' => 65000,
                'stock' => 15,
                'min_stock' => 5,
                'brand' => 'Viar Original',
                'location_rack' => 'B-01',
                'description' => 'Kabel body lengkap Viar'
            ],
            [
                'code' => 'SP-VR-007',
                'name' => 'Busi Viar NGK',
                'category' => 'Kelistrikan',
                'purchase_price' => 25000,
                'selling_price' => 35000,
                'stock' => 60,
                'min_stock' => 10,
                'brand' => 'NGK',
                'location_rack' => 'B-02',
                'description' => 'Busi NGK original untuk Viar'
            ],
            [
                'code' => 'SP-VR-008',
                'name' => 'Aki Kering Viar 12V',
                'category' => 'Kelistrikan',
                'purchase_price' => 120000,
                'selling_price' => 175000,
                'stock' => 12,
                'min_stock' => 3,
                'brand' => 'GS Astra',
                'location_rack' => 'B-03',
                'description' => 'Aki kering Viar 12V maintenance free'
            ],

            // =============================================
            // KATEGORI KAKI-KAKI
            // =============================================
            [
                'code' => 'SP-VR-009',
                'name' => 'Ban Depan Viar 70/90',
                'category' => 'Kaki-kaki',
                'purchase_price' => 185000,
                'selling_price' => 250000,
                'stock' => 8,
                'min_stock' => 4,
                'brand' => 'IRC',
                'location_rack' => 'C-01',
                'description' => 'Ban depan Viar ukuran 70/90'
            ],
            [
                'code' => 'SP-VR-010',
                'name' => 'Kampas Rem Viar',
                'category' => 'Kaki-kaki',
                'purchase_price' => 35000,
                'selling_price' => 50000,
                'stock' => 35,
                'min_stock' => 10,
                'brand' => 'Viar Original',
                'location_rack' => 'C-02',
                'description' => 'Kampas rem Viar original'
            ],
            [
                'code' => 'SP-VR-011',
                'name' => 'Dust Seal 2015 Series',
                'category' => 'Kaki-kaki',
                'purchase_price' => 18000,
                'selling_price' => 26000,
                'stock' => 25,
                'min_stock' => 8,
                'brand' => 'Viar Original',
                'location_rack' => 'C-03',
                'description' => 'Dust seal series 2015 untuk Viar'
            ],

            // =============================================
            // KATEGORI BODY & AKSESORIS
            // =============================================
            [
                'code' => 'SP-VR-012',
                'name' => 'Y-Rubber R-L Fuel Tank Karya',
                'category' => 'Body & Aksesoris',
                'purchase_price' => 2500,
                'selling_price' => 4000,
                'stock' => 45,
                'min_stock' => 15,
                'brand' => 'Karya',
                'location_rack' => 'D-01',
                'description' => 'Rubber fuel tank kanan-kiri Viar'
            ],
            [
                'code' => 'SP-VR-013',
                'name' => 'Pipe Lock',
                'category' => 'Body & Aksesoris',
                'purchase_price' => 50000,
                'selling_price' => 70000,
                'stock' => 10,
                'min_stock' => 3,
                'brand' => 'Viar Original',
                'location_rack' => 'D-02',
                'description' => 'Pipe lock pengaman knalpot Viar'
            ],
            [
                'code' => 'SP-VR-014',
                'name' => 'Spion Viar',
                'category' => 'Body & Aksesoris',
                'purchase_price' => 55000,
                'selling_price' => 80000,
                'stock' => 18,
                'min_stock' => 5,
                'brand' => 'Viar Original',
                'location_rack' => 'D-03',
                'description' => 'Spion Viar original'
            ],
            [
                'code' => 'SP-VR-015',
                'name' => 'Kunci Kontak Viar',
                'category' => 'Body & Aksesoris',
                'purchase_price' => 45000,
                'selling_price' => 65000,
                'stock' => 12,
                'min_stock' => 3,
                'brand' => 'Viar Original',
                'location_rack' => 'D-04',
                'description' => 'Set kunci kontak Viar'
            ],

            // =============================================
            // KATEGORI OLI & PELUMAS
            // =============================================
            [
                'code' => 'SP-VR-016',
                'name' => 'Y-Screen Oil Filter Karya',
                'category' => 'Oli & Pelumas',
                'purchase_price' => 3000,
                'selling_price' => 5000,
                'stock' => 50,
                'min_stock' => 15,
                'brand' => 'Karya',
                'location_rack' => 'E-01',
                'description' => 'Saringan oli Viar'
            ],
            [
                'code' => 'SP-VR-017',
                'name' => 'Y2-Oil View Glass',
                'category' => 'Oli & Pelumas',
                'purchase_price' => 100,
                'selling_price' => 200,
                'stock' => 100,
                'min_stock' => 20,
                'brand' => 'Viar Original',
                'location_rack' => 'E-02',
                'description' => 'Kaca oli Viar'
            ],
            [
                'code' => 'SP-VR-018',
                'name' => 'Karya Oil',
                'category' => 'Oli & Pelumas',
                'purchase_price' => 12000,
                'selling_price' => 16000,
                'stock' => 40,
                'min_stock' => 10,
                'brand' => 'Karya',
                'location_rack' => 'E-03',
                'description' => 'Oli mesin Viar Karya'
            ],
            [
                'code' => 'SP-VR-019',
                'name' => 'Oli Mesin Viar 1L',
                'category' => 'Oli & Pelumas',
                'purchase_price' => 45000,
                'selling_price' => 60000,
                'stock' => 30,
                'min_stock' => 8,
                'brand' => 'Viar',
                'location_rack' => 'E-04',
                'description' => 'Oli mesin Viar 1 liter'
            ],
        ];

        // Insert data spareparts
        foreach ($spareparts as $item) {
            Sparepart::create([
                'code' => $item['code'],
                'name' => $item['name'],
                'category_id' => $categories[$item['category']]->id,
                'purchase_price' => $item['purchase_price'],
                'selling_price' => $item['selling_price'],
                'stock' => $item['stock'],
                'min_stock' => $item['min_stock'],
                'brand' => $item['brand'],
                'location_rack' => $item['location_rack'],
                'description' => $item['description'],
                'unit' => 'pcs',
                'is_active' => true,
            ]);
        }
    }
}
