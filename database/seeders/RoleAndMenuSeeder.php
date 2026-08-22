<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleAndMenuSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Roles
        $roles = [
            ['name' => 'Super Admin', 'slug' => 'super-admin'],
            ['name' => 'Admin', 'slug' => 'admin'],
            ['name' => 'User', 'slug' => 'user'],
            ['name' => 'Visitor', 'slug' => 'visitor'],
            ['name' => 'Pemilik Kost', 'slug' => 'pemilik'],
            ['name' => 'Penyewa', 'slug' => 'penyewa'],
        ];

        $roleIds = [];
        foreach ($roles as $role) {
            DB::table('roles')->updateOrInsert(
                ['slug' => $role['slug']],
                [
                    'name' => $role['name'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
            // Get the actual ID
            $roleIds[$role['slug']] = DB::table('roles')->where('slug', $role['slug'])->first()->id;
        }

        // 2. Menus
        $menus = [
            ['name' => 'Dashboard', 'slug' => 'dashboard', 'path' => '/dashboard', 'icon' => 'ri-home-smile-line', 'order_no' => 1],
            ['name' => 'User Management', 'slug' => 'user-management', 'path' => null, 'icon' => 'ri-user-settings-line', 'order_no' => 2],
            ['parent' => 'User Management', 'name' => 'Users', 'slug' => 'user.index', 'path' => '/admin/user', 'icon' => 'ri-user-line', 'order_no' => 1],
            ['parent' => 'User Management', 'name' => 'Roles', 'slug' => 'role.index', 'path' => '/admin/role', 'icon' => 'ri-shield-user-line', 'order_no' => 2],
            ['parent' => 'User Management', 'name' => 'Menus', 'slug' => 'menu.index', 'path' => '/admin/menu', 'icon' => 'ri-menu-search-line', 'order_no' => 3],
            ['parent' => 'User Management', 'name' => 'Permissions', 'slug' => 'permission.index', 'path' => '/admin/permission', 'icon' => 'ri-lock-password-line', 'order_no' => 4],

            // KostKu: Master Data
            ['name' => 'Master Data', 'slug' => 'master-data', 'path' => null, 'icon' => 'ri-building-line', 'order_no' => 3],
            ['parent' => 'Master Data', 'name' => 'Data Kost', 'slug' => 'kost.index', 'path' => '/admin/kost', 'icon' => 'ri-building-2-line', 'order_no' => 1],
            ['parent' => 'Master Data', 'name' => 'Data Kamar', 'slug' => 'kamar.index', 'path' => '/admin/kamar', 'icon' => 'ri-door-open-line', 'order_no' => 2],
            ['parent' => 'Master Data', 'name' => 'Tipe Kamar', 'slug' => 'tipe-kamar.index', 'path' => '/admin/tipe-kamar', 'icon' => 'ri-layout-masonry-line', 'order_no' => 3],
            ['parent' => 'Master Data', 'name' => 'Fasilitas', 'slug' => 'fasilitas.index', 'path' => '/admin/fasilitas', 'icon' => 'ri-checkbox-multiple-line', 'order_no' => 4],
            ['parent' => 'Master Data', 'name' => 'Lokasi', 'slug' => 'lokasi.index', 'path' => '/admin/lokasi', 'icon' => 'ri-map-pin-line', 'order_no' => 5],

            // KostKu: Transaksi
            ['name' => 'Transaksi', 'slug' => 'transaksi', 'path' => null, 'icon' => 'ri-exchange-dollar-line', 'order_no' => 4],
            ['parent' => 'Transaksi', 'name' => 'Booking', 'slug' => 'booking.index', 'path' => '/admin/booking', 'icon' => 'ri-calendar-check-line', 'order_no' => 1],

            ['parent' => 'Transaksi', 'name' => 'Pembayaran', 'slug' => 'payment.index', 'path' => '/admin/payment', 'icon' => 'ri-wallet-3-line', 'order_no' => 2],
            // KostKu: Laporan
            ['name' => 'Laporan', 'slug' => 'laporan', 'path' => null, 'icon' => 'ri-file-chart-line', 'order_no' => 5],
            ['parent' => 'Laporan', 'name' => 'Laporan Booking', 'slug' => 'laporan.booking', 'path' => '/admin/laporan/booking', 'icon' => 'ri-calendar-todo-line', 'order_no' => 1],
            ['parent' => 'Laporan', 'name' => 'Laporan Pembayaran', 'slug' => 'laporan.pembayaran', 'path' => '/admin/laporan/pembayaran', 'icon' => 'ri-money-dollar-circle-line', 'order_no' => 2],
            ['parent' => 'Laporan', 'name' => 'Laporan Pendapatan', 'slug' => 'laporan.pendapatan', 'path' => '/admin/laporan/pendapatan', 'icon' => 'ri-line-chart-line', 'order_no' => 3],

            ['name' => 'Katalog Produk', 'slug' => 'products.index', 'path' => '/admin/products', 'icon' => 'ri-shopping-bag-3-line', 'order_no' => 6],
            ['name' => 'Activity Log', 'slug' => 'activity-log.index', 'path' => '/admin/activity-log', 'icon' => 'ri-history-line', 'order_no' => 7],
            ['name' => 'API Docs', 'slug' => 'api-docs', 'path' => '/api/documentation', 'icon' => 'ri-book-open-line', 'order_no' => 8],
        ];

        $menuIdMap = [];
        foreach ($menus as $m) {
            $parentId = isset($m['parent']) ? ($menuIdMap[$m['parent']] ?? null) : null;

            DB::table('menus')->updateOrInsert(
                ['slug' => $m['slug']],
                [
                    'parent_id' => $parentId,
                    'name' => $m['name'],
                    'path' => $m['path'],
                    'icon' => $m['icon'],
                    'order_no' => $m['order_no'],
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            $dbMenu = DB::table('menus')->where('slug', $m['slug'])->first();
            $menuIdMap[$m['name']] = $dbMenu->id;

            // Assign to Super Admin by default
            DB::table('role_menu')->updateOrInsert(
                ['role_id' => $roleIds['super-admin'], 'menu_id' => $dbMenu->id],
                [
                    'can_create' => true,
                    'can_read' => true,
                    'can_update' => true,
                    'can_delete' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        // 3. Role-based menu permissions
        $assign = function (string $roleSlug, array $slugs, bool $create, bool $read, bool $update, bool $delete) use ($roleIds) {
            foreach ($slugs as $slug) {
                $menuId = DB::table('menus')->where('slug', $slug)->value('id');
                if (! $menuId) {
                    continue;
                }
                DB::table('role_menu')->updateOrInsert(
                    ['role_id' => $roleIds[$roleSlug], 'menu_id' => $menuId],
                    [
                        'can_create' => $create,
                        'can_read' => $read,
                        'can_update' => $update,
                        'can_delete' => $delete,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        };

        $kostMenus = ['kost.index', 'kamar.index', 'tipe-kamar.index', 'fasilitas.index', 'lokasi.index'];
        $transaksiMenus = ['booking.index', 'payment.index'];
        $laporanMenus = ['laporan.booking', 'laporan.pembayaran', 'laporan.pendapatan'];

        // Admin: kelola penuh domain kost + modul user dasar (tanpa hapus)
        $assign('admin', array_merge(['dashboard', 'user.index', 'activity-log.index'], $kostMenus, $transaksiMenus, $laporanMenus), true, true, true, false);

        // Pemilik: kelola kost & kamar miliknya (scoping di service), lihat transaksi & laporan
        $assign('pemilik', array_merge(['dashboard'], $kostMenus), true, true, true, false);
        $assign('pemilik', $transaksiMenus, false, true, true, false);
        $assign('pemilik', $laporanMenus, false, true, false, false);

        // Penyewa & User: hanya dashboard (fitur penyewa ada di halaman publik)
        $assign('penyewa', ['dashboard'], false, true, false, false);
        $assign('user', ['dashboard'], false, true, false, false);

        // Visitor: read-only dashboard & katalog produk
        $assign('visitor', ['dashboard', 'products.index'], false, true, false, false);
    }
}
