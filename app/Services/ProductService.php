<?php

namespace App\Services;

use App\Models\Product;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ProductService
{
    public function __construct(private ImageService $images)
    {
    }

    public function paginate(?string $search, ?string $source, int $perPage = 10): LengthAwarePaginator
    {
        return Product::query()
            ->with('creator:id,name')
            ->withCount('listings')
            ->when($search, fn ($query) => $query->where('name', 'like', "%{$search}%"))
            ->when($source, fn ($query) => $query->where('source', $source))
            ->latest('id')
            ->paginate($perPage);
    }

    public function create(array $data, User $creator): Product
    {
        $product = Product::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'base_price' => $data['base_price'],
            'thumbnail' => isset($data['thumbnail'])
                ? $this->images->store($data['thumbnail'], 'products')
                : null,
            'created_by' => $creator->id,
            'source' => $creator->isAdmin() ? 'admin' : 'shop_owner',
        ]);

        return $product->load('creator:id,name')->loadCount('listings');
    }

    public function update(Product $product, array $data): Product
    {
        $product->update([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'base_price' => $data['base_price'],
            'thumbnail' => $this->images->replace(
                $product->thumbnail,
                $data['thumbnail'] ?? null,
                (bool) ($data['remove_thumbnail'] ?? false),
                'products'
            ),
        ]);

        return $product->refresh()->load('creator:id,name')->loadCount('listings');
    }

    public function delete(Product $product): void
    {
        DB::transaction(function () use ($product) {
            $product->listings()
                ->whereNotNull('thumbnail')
                ->pluck('thumbnail')
                ->each(fn ($path) => $this->images->delete($path));

            $this->images->delete($product->thumbnail);

            $product->delete();
        });
    }
}
