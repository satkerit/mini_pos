<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $guarded = [];

    protected static string $cachePrefix = 'settings_';

    public static function get(string $key, mixed $default = null): mixed
    {
        return Cache::remember(
            static::$cachePrefix . $key,
            now()->addHours(24),
            fn () => static::where('key', $key)->value('value') ?? $default
        );
    }

    public static function set(string $key, mixed $value): static
    {
        Cache::forget(static::$cachePrefix . $key);

        return static::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }

    public static function getMany(array $keys): array
    {
        $result = [];
        $missingKeys = [];

        foreach ($keys as $key) {
            $cached = Cache::get(static::$cachePrefix . $key);
            if ($cached !== null) {
                $result[$key] = $cached;
            } else {
                $missingKeys[] = $key;
            }
        }

        if (!empty($missingKeys)) {
            $settings = static::whereIn('key', $missingKeys)->pluck('value', 'key')->toArray();
            foreach ($missingKeys as $key) {
                $value = $settings[$key] ?? null;
                $result[$key] = $value;
                Cache::put(static::$cachePrefix . $key, $value, now()->addHours(24));
            }
        }

        return $result;
    }

    public static function setMany(array $data): void
    {
        $rows = [];
        foreach ($data as $key => $value) {
            Cache::forget(static::$cachePrefix . $key);
            $rows[] = ['key' => $key, 'value' => $value];
        }

        foreach ($rows as $row) {
            static::updateOrCreate(['key' => $row['key']], ['value' => $row['value']]);
        }
    }

    public static function flushCache(): void
    {
        $keys = static::pluck('key');
        foreach ($keys as $key) {
            Cache::forget(static::$cachePrefix . $key);
        }
    }
}
