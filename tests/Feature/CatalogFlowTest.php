<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Shop;
use App\Models\ShopProduct;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CatalogFlowTest extends TestCase
{
    use RefreshDatabase;

    private function ownerWithShop(string $name = 'Pantry'): array
    {
        $owner = User::factory()->owner()->create();
        $shop = Shop::create([
            'owner_id' => $owner->id,
            'name' => $name,
            'slug' => strtolower($name),
        ]);

        return [$owner, $shop];
    }

    private function product(User $creator, string $name, float $price = 100): Product
    {
        return Product::create([
            'created_by' => $creator->id,
            'source' => $creator->isAdmin() ? 'admin' : 'shop_owner',
            'name' => $name,
            'base_price' => $price,
        ]);
    }

    public function test_customers_only_see_products_placed_in_a_shop(): void
    {
        $admin = User::factory()->admin()->create();
        [, $shop] = $this->ownerWithShop();

        $listed = $this->product($admin, 'Listed Rice');
        $this->product($admin, 'Unlisted Teapot');

        ShopProduct::create(['shop_id' => $shop->id, 'product_id' => $listed->id, 'price' => 120]);

        $this->getJson('/api/storefront/products')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'Listed Rice')
            ->assertJsonPath('data.0.price', 120.0);

        $this->getJson('/api/storefront/shops/pantry/products')
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function test_closed_shops_and_hidden_listings_are_not_visible(): void
    {
        $admin = User::factory()->admin()->create();
        [, $shop] = $this->ownerWithShop();
        $product = $this->product($admin, 'Rice');

        $listing = ShopProduct::create(['shop_id' => $shop->id, 'product_id' => $product->id, 'price' => 120, 'is_active' => false]);

        $this->getJson('/api/storefront/products')->assertOk()->assertJsonCount(0, 'data');
        $this->getJson("/api/storefront/shops/pantry/products/{$listing->id}")->assertNotFound();

        $listing->update(['is_active' => true]);
        $shop->update(['is_active' => false]);

        $this->getJson('/api/storefront/products')->assertOk()->assertJsonCount(0, 'data');
        $this->getJson('/api/storefront/shops/pantry')->assertNotFound();
    }

    public function test_admin_login_rejects_shop_owner_credentials(): void
    {
        $owner = User::factory()->owner()->create(['email' => 'owner@example.com']);

        $this->postJson('/api/admin/login', ['email' => $owner->email, 'password' => 'password'])
            ->assertStatus(422);

        $this->postJson('/api/login', ['email' => $owner->email, 'password' => 'password'])
            ->assertOk()
            ->assertJsonPath('user.role', 'shop_owner');
    }

    public function test_common_login_rejects_admin_credentials(): void
    {
        $admin = User::factory()->admin()->create(['email' => 'admin@example.com']);

        $this->postJson('/api/login', ['email' => $admin->email, 'password' => 'password'])
            ->assertStatus(422);

        $this->postJson('/api/admin/login', ['email' => $admin->email, 'password' => 'password'])
            ->assertOk()
            ->assertJsonPath('user.role', 'admin');
    }

    public function test_owner_and_customer_cannot_use_the_admin_api(): void
    {
        Sanctum::actingAs(User::factory()->owner()->create());
        $this->getJson('/api/admin/shops')->assertForbidden();

        Sanctum::actingAs(User::factory()->create());
        $this->getJson('/api/admin/dashboard')->assertForbidden();
    }

    public function test_admin_manages_a_shop_and_assigns_an_owner(): void
    {
        Sanctum::actingAs(User::factory()->admin()->create());
        $owner = User::factory()->owner()->create();

        $this->postJson('/api/admin/shops', ['name' => 'Corner Store', 'owner_id' => $owner->id])
            ->assertCreated()
            ->assertJsonPath('data.slug', 'corner-store')
            ->assertJsonPath('data.owner.id', $owner->id);

        $this->postJson('/api/admin/shops', ['name' => 'Second Store', 'owner_id' => $owner->id])
            ->assertStatus(422)
            ->assertJsonValidationErrors('owner_id');
    }

    public function test_admin_creates_a_shop_owner_account(): void
    {
        Sanctum::actingAs(User::factory()->admin()->create());
        $shop = Shop::create(['name' => 'Open Shop', 'slug' => 'open-shop']);

        $this->postJson('/api/admin/owners', [
            'name' => 'New Owner',
            'email' => 'new@example.com',
            'password' => 'secret-pass',
            'shop_id' => $shop->id,
        ])->assertCreated()->assertJsonPath('data.role', 'shop_owner');

        $this->assertSame('New Owner', $shop->fresh()->owner->name);
    }

    public function test_owner_adds_a_catalog_product_with_their_own_price(): void
    {
        $admin = User::factory()->admin()->create();
        [$owner, $shop] = $this->ownerWithShop();
        $product = $this->product($admin, 'Rice', 950);

        Sanctum::actingAs($owner);

        $this->postJson('/api/owner/listings', ['product_id' => $product->id, 'price' => 999])
            ->assertCreated()
            ->assertJsonPath('data.price', 999.0);

        $this->assertDatabaseHas('shop_products', ['shop_id' => $shop->id, 'product_id' => $product->id, 'price' => 999]);

        $this->postJson('/api/owner/listings', ['product_id' => $product->id, 'price' => 999])
            ->assertStatus(422)
            ->assertJsonValidationErrors('product_id');
    }

    public function test_product_created_by_an_owner_joins_the_shared_catalog(): void
    {
        [$first] = $this->ownerWithShop('First');
        [$second] = $this->ownerWithShop('Second');

        Sanctum::actingAs($first);
        $this->postJson('/api/owner/catalog', ['name' => 'Handmade Soap', 'base_price' => 80])
            ->assertCreated();

        $this->assertDatabaseHas('products', ['name' => 'Handmade Soap', 'source' => 'shop_owner', 'created_by' => $first->id]);

        Sanctum::actingAs($second);
        $this->getJson('/api/owner/catalog')
            ->assertOk()
            ->assertJsonFragment(['name' => 'Handmade Soap']);
    }

    public function test_owner_cannot_change_a_product_they_did_not_create(): void
    {
        $admin = User::factory()->admin()->create();
        [$owner] = $this->ownerWithShop();
        $product = $this->product($admin, 'Rice');

        Sanctum::actingAs($owner);

        $this->putJson("/api/owner/catalog/{$product->id}", ['name' => 'Hacked', 'base_price' => 1])->assertForbidden();
        $this->deleteJson("/api/owner/catalog/{$product->id}")->assertForbidden();
    }

    public function test_owner_cannot_touch_another_shops_listing(): void
    {
        $admin = User::factory()->admin()->create();
        [$owner] = $this->ownerWithShop('Mine');
        [, $other] = $this->ownerWithShop('Theirs');
        $product = $this->product($admin, 'Rice');
        $listing = ShopProduct::create(['shop_id' => $other->id, 'product_id' => $product->id, 'price' => 100]);

        Sanctum::actingAs($owner);

        $this->putJson("/api/owner/listings/{$listing->id}", ['price' => 1])->assertNotFound();
        $this->deleteJson("/api/owner/listings/{$listing->id}")->assertNotFound();
    }

    public function test_guests_cannot_reach_protected_endpoints(): void
    {
        $this->getJson('/api/me')->assertUnauthorized();
        $this->getJson('/api/owner/listings')->assertUnauthorized();
        $this->getJson('/api/admin/shops')->assertUnauthorized();
    }
}
