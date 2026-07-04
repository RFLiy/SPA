<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Faker\Factory as Faker;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        $this->command->warn('>> Memulai proses Database Seeding...');

        $roles = [
            1 => 'Customer',
            2 => 'Owner',
            3 => 'Kurir',
            4 => 'Manager',
            5 => 'super_admin'
        ];

        foreach ($roles as $id => $roleName) {
            DB::table('roles')->updateOrInsert(
                ['id' => $id],
                ['name' => $roleName, 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()]
            );
        }

        $userSkenario = [
            'Manager'     => 2,
            'Owner'       => 2,
            'Kurir'       => 5,
            'Customer'    => 5,
            'super_admin' => 1,
        ];

        $userIdCounter = 1;
        $customerIds = [];
        User::withoutEvents(function () use ($userSkenario, $faker, &$userIdCounter, &$customerIds) {
            foreach ($userSkenario as $roleName => $jumlah) {
                for ($i = 1; $i <= $jumlah; $i++) {
                    if ($roleName === 'super_admin') {
                        $namaUser  = 'Super Admin';
                        $emailUser = 'superadmin@gmail.com';
                    } else {
                        $namaUser  = $roleName . ' ' . $i;
                        $emailUser = strtolower($roleName) . $i . '@gmail.com';
                    }

                    $user = User::create([
                        'id'         => $userIdCounter,
                        'name'       => $namaUser,
                        'email'      => $emailUser,
                        'password'   => bcrypt('password'),
                        'role'       => $roleName,
                        'address'    => $faker->address,
                        'no_tlp'     => $faker->numerify('08##########'),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    $user->assignRole($roleName);

                    if ($roleName === 'Customer') {
                        $customerIds[] = $userIdCounter;
                    }

                    $userIdCounter++;
                }
            }
        });

        $this->command->comment("   ✔ Total " . ($userIdCounter - 1) . " users berpola berhasil dibuat.");

        $materials = ['Leaf Spring', 'Pen Per', 'Bushing Arm', 'U Bolt'];
        foreach ($materials as $index => $matName) {
            DB::table('materials')->updateOrInsert(
                ['id' => $index + 1],
                [
                    'name'        => $matName,
                    'slug'        => Str::slug($matName),
                    'description' => 'Bahan baku berkualitas untuk ' . $matName,
                    'created_at'  => now(),
                    'updated_at'  => now()
                ]
            );
        }

        $categoryCounter = 1;
        foreach ($materials as $matName) {
            $catBerat = $matName . ' Kendaraan Besar';
            DB::table('categories')->updateOrInsert(
                ['id' => $categoryCounter],
                ['name' => $catBerat, 'slug' => Str::slug($catBerat), 'status' => 'active', 'created_at' => now(), 'updated_at' => now()]
            );
            $categoryCounter++;

            $catRingan = $matName . ' Kendaraan Kecil';
            DB::table('categories')->updateOrInsert(
                ['id' => $categoryCounter],
                ['name' => $catRingan, 'slug' => Str::slug($catRingan), 'status' => 'active', 'created_at' => now(), 'updated_at' => now()]
            );
            $categoryCounter++;
        }

        $categories = DB::table('categories')->get();
        foreach ($categories as $cat) {
            $matId = ceil($cat->id / 2);
            $prodName = $cat->name;

            DB::table('products')->updateOrInsert(
                ['id' => $cat->id],
                [
                    'name'        => $prodName,
                    'slug'        => Str::slug($prodName),
                    'category_id' => $cat->id,
                    'material_id' => $matId,
                    'description' => $faker->paragraph,
                    'base_price'  => $faker->randomElement([100000, 150000, 250000, 500000, 750000]),
                    'stock'       => $faker->numberBetween(20, 100),
                    'unit'        => 'pcs',
                    'status'      => 'active',
                    'created_at'  => now(),
                    'updated_at'  => now()
                ]
            );
        }

        $this->command->info('6. Membuat data 20 Transaksi dummy...');
        $statuses = [
            'completed',
            'paid',
            'shipped',
            'cancelled',
            'processing',
            'completed',
            'paid',
            'shipped',
            'completed',
            'completed',
            'completed',
            'paid',
            'shipped',
            'cancelled',
            'processing',
            'completed',
            'paid',
            'shipped',
            'completed',
            'completed'
        ];

        for ($orderId = 1; $orderId <= 20; $orderId++) {
            $selectedCustomer = $faker->randomElement($customerIds);
            $statusTerpilih = $statuses[$orderId - 1];
            $totalHarga = $faker->randomElement([300000, 500000, 1000000, 1200000, 1500000]);

            DB::table('orders')->insert([
                'id'               => $orderId,
                'user_id'          => $selectedCustomer,
                'total'            => $totalHarga,
                'payment_status'   => ($statusTerpilih == 'waiting_payment' || $statusTerpilih == 'cancelled') ? 'pending' : 'paid',
                'payment_method'   => 'midtrans',
                'order_code'       => 'ORD-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6)),
                'shipping_address' => $faker->address,
                'shipping_option'  => 'internal',
                'status'           => $statusTerpilih,
                'created_at'       => now()->subDays(21 - $orderId)->subHours($faker->numberBetween(1, 12)),
                'updated_at'       => now(),
            ]);

            $jumlahItem = $faker->numberBetween(1, 2);
            for ($j = 1; $j <= $jumlahItem; $j++) {
                DB::table('order_items')->insert([
                    'order_id'   => $orderId,
                    'product_id' => $faker->numberBetween(1, 8),
                    'quantity'   => $faker->numberBetween(1, 3),
                    'price'      => $totalHarga / $jumlahItem,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::table('order_statuses')->insert([
                'order_id'    => $orderId,
                'status'      => $statusTerpilih,
                'description' => 'Status pesanan otomatis disetel ke ' . $statusTerpilih,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }

        $this->command->info('✔ Semua data dummy berurutan sukses dimasukkan!');
    }
}
