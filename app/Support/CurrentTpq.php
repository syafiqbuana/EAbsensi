<?php

namespace App\Support;

use App\Models\TpqProfile;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;


class CurrentTpq
{

    public static function get(): ?TpqProfile
    {
        $slug = static::slug();

        if (! $slug || $slug === 'universal') {
            return null;
        }

        return Cache::remember(
            "tpq_profile.{$slug}",
            now()->addMinutes(5),
            fn () => TpqProfile::active()->bySlug($slug)->first()
        );
    }

    public static function name(): ?string
    {
        return static::get()?->name;
    }

    public static function slug(): ?string
    {
        return session('current_tpq_slug')
            ?? request()->route('tpq_slug')
            ?? null;
    }

    public static function id(): ?int
    {
        return session('current_tpq_id') ?? static::get()?->id;
    }

    public static function setFromProfile(TpqProfile $profile): void
    {
        session([
            'current_tpq_id'   => $profile->id,
            'current_tpq_slug' => $profile->slug,
        ]);
    }

    public static function clear(): void
    {
        session()->forget(['current_tpq_id', 'current_tpq_slug']);
    }

    public static function forgetCache(string $slug): void
    {
        Cache::forget("tpq_profile.{$slug}");
    }

    public static function where(Builder $query, string $column = 'tpq_profile_id'): Builder
{
    return $query->where($column, static::id());
}
}
