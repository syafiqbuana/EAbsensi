<?php

namespace App\Support;

use App\Models\TpqProfile;
use Illuminate\Support\Facades\Cache;

class CurrentTpq
{
    /**
     * Get the active TpqProfile for the current request.
     */
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

    /**
     * Get the current TPQ slug from session or route.
     */
    public static function slug(): ?string
    {
        return session('current_tpq_slug')
            ?? request()->route('tpq_slug')
            ?? null;
    }

    /**
     * Get the current TPQ id from session or resolved profile.
     */
    public static function id(): ?int
    {
        return session('current_tpq_id') ?? static::get()?->id;
    }

    /**
     * Set the active TPQ context from a TpqProfile instance.
     */
    public static function setFromProfile(TpqProfile $profile): void
    {
        session([
            'current_tpq_id'   => $profile->id,
            'current_tpq_slug' => $profile->slug,
        ]);
    }

    /**
     * Clear the active TPQ context from the session.
     */
    public static function clear(): void
    {
        session()->forget(['current_tpq_id', 'current_tpq_slug']);
    }

    /**
     * Forget the cache for a given slug.
     */
    public static function forgetCache(string $slug): void
    {
        Cache::forget("tpq_profile.{$slug}");
    }
}
