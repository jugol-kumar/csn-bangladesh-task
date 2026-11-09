<?php

namespace App\Services;

use App\Models\Shop;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ShopService
{
    public function __construct(private ImageService $images)
    {
    }

    public function paginate(?string $search, int $perPage = 10): LengthAwarePaginator
    {
        return Shop::query()
            ->with('owner:id,name,email')
            ->withCount('listings')
            ->when($search, fn ($query) => $query->where('name', 'like', "%{$search}%"))
            ->latest('id')
            ->paginate($perPage);
    }

    public function create(array $data): Shop
    {
        $shop = Shop::create(array_merge(
            ['is_active' => true],
            $data,
            ['slug' => $this->uniqueSlug($data['name'])]
        ));

        return $shop->load('owner:id,name,email')->loadCount('listings');
    }

    public function update(Shop $shop, array $data): Shop
    {
        if ($shop->name !== $data['name']) {
            $data['slug'] = $this->uniqueSlug($data['name'], $shop->id);
        }

        $shop->update($data);

        return $shop->refresh()->load('owner:id,name,email')->loadCount('listings');
    }

    public function delete(Shop $shop): void
    {
        DB::transaction(function () use ($shop) {
            $shop->listings()
                ->whereNotNull('thumbnail')
                ->pluck('thumbnail')
                ->each(fn ($path) => $this->images->delete($path));

            $shop->delete();
        });
    }

    private function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'shop';
        $slug = $base;
        $counter = 2;

        while (
            Shop::where('slug', $slug)
                ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $base.'-'.$counter++;
        }

        return $slug;
    }
}
