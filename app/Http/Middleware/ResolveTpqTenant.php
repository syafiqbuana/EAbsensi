<?php

namespace App\Http\Middleware;

use App\Models\TpqProfile;
use App\Support\CurrentTpq;
use Closure;
use Illuminate\Http\Request;
use Spatie\Permission\PermissionRegistrar;
use Symfony\Component\HttpFoundation\Response;

class ResolveTpqTenant
{
    public function handle(Request $request, Closure $next): Response
    {
        $routeSlug = $request->route('tpq_slug');
        $slug = $routeSlug ?? session('current_tpq_slug');

        if ($slug && $slug !== 'universal') {
            $tpq = $routeSlug
                ? TpqProfile::bySlug($slug)->first()
                : TpqProfile::active()->bySlug($slug)->first();

            if (! $tpq) {
                abort(404, 'TPQ tidak ditemukan.');
            }

            if ($tpq->isSuspended()) {
                return response()
                    ->view('tenant.suspended', [
                        'tpq' => $tpq,
                        'audience' => $request->routeIs('filament.admin.*')
                            ? 'tenant-admin'
                            : 'parent',
                    ], 403)
                    ->header('Cache-Control', 'no-store, no-cache, must-revalidate');
            }

            CurrentTpq::setFromProfile($tpq);
            app(PermissionRegistrar::class)->setPermissionsTeamId($tpq->id);

            \Illuminate\Support\Facades\URL::defaults(['tpq_slug' => $tpq->slug]);

            if (auth()->check()) {
                auth()->user()->unsetRelation('roles');
            }
        }

        return $next($request);
    }
}
