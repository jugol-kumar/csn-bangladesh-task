<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Shop;
use App\Models\ShopProduct;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = $this->user('Haat Admin', 'admin@haat.test', User::ROLE_ADMIN);
        $rafi = $this->user('Rafi Ahmed', 'rafi@haat.test', User::ROLE_OWNER);
        $mim = $this->user('Mim Chowdhury', 'mim@haat.test', User::ROLE_OWNER);
        $this->user('Tanvir Hasan', 'customer@haat.test', User::ROLE_CUSTOMER);

        $pantry = Shop::create([
            'owner_id' => $rafi->id,
            'name' => 'Rafi Pantry',
            'slug' => 'rafi-pantry',
            'description' => 'Dry goods and kitchen staples, packed fresh every Thursday.',
            'address' => 'Lalmatia Block D, Dhaka',
            'phone' => '01700-000001',
        ]);

        $desk = Shop::create([
            'owner_id' => $mim->id,
            'name' => 'Mim Stationery and Tech',
            'slug' => 'mim-stationery-and-tech',
            'description' => 'Notebooks, pens and small desk electronics.',
            'address' => 'Dhanmondi 27, Dhaka',
            'phone' => '01700-000002',
        ]);

        Shop::create([
            'owner_id' => null,
            'name' => 'Old Town Spices',
            'slug' => 'old-town-spices',
            'description' => 'Opening soon.',
            'address' => 'Chawkbazar, Dhaka',
        ]);

        $rice = $this->product($admin, 'Basmati Rice 5kg', 'Aged long-grain rice, vacuum packed.', 950);
        $oil = $this->product($admin, 'Mustard Oil 1L', 'Cold pressed, strong and sharp.', 310);
        $lentil = $this->product($rafi, 'Masoor Dal 1kg', 'Split red lentils, cleaned and sorted.', 140);
        $notebook = $this->product($admin, 'Notebook A5 Ruled', '120 pages, stitched binding.', 120);
        $mouse = $this->product($mim, 'Wireless Mouse', 'Quiet clicks, 2.4GHz receiver, one AA battery.', 790);
        $cable = $this->product($mim, 'USB-C Cable 1m', 'Braided cable for charging and data.', 350);
        $this->product($admin, 'Glass Teapot 800ml', 'Not listed anywhere yet, so customers cannot see it.', 1450);

        $this->list($pantry, $rice, 980);
        $this->list($pantry, $oil, 310);
        $this->list($pantry, $lentil, 150, 'Masoor Dal, 1 kg bag');
        $this->list($pantry, $notebook, 135);

        $this->list($desk, $notebook, 120);
        $this->list($desk, $mouse, 820);
        $this->list($desk, $cable, 350);
        $this->list($desk, $oil, 330);
    }

    private function user(string $name, string $email, string $role): User
    {
        return User::create([
            'name' => $name,
            'email' => $email,
            'password' => 'password',
            'role' => $role,
        ]);
    }

    private function product(User $creator, string $name, string $description, float $price): Product
    {
        return Product::create([
            'created_by' => $creator->id,
            'source' => $creator->isAdmin() ? 'admin' : 'shop_owner',
            'name' => $name,
            'description' => $description,
            'base_price' => $price,
        ]);
    }

    private function list(Shop $shop, Product $product, float $price, ?string $title = null): ShopProduct
    {
        return ShopProduct::create([
            'shop_id' => $shop->id,
            'product_id' => $product->id,
            'title' => $title,
            'price' => $price,
            'is_active' => true,
        ]);
    }
}
