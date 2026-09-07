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
        $slug = $request->route('tpq_slug') ?? session('current_tpq_slug');

        if ($slug && $slug !== 'universal') {
            $tpq = TpqProfile::active()->bySlug($slug)->first();

            if (! $tpq) {
                abort(404, 'TPQ tidak ditemukan.');
            }

            // Set ke session dan Permission team
            CurrentTpq::setFromProfile($tpq);
            app(PermissionRegistrar::class)->setPermissionsTeamId($tpq->id);

            // Set default parameter URL untuk semua pembuatan route (misal route logout & navigation items)
            \Illuminate\Support\Facades\URL::defaults(['tpq_slug' => $tpq->slug]);

            if (auth()->check()) {
                auth()->user()->unsetRelation('roles');
            }
        }

        return $next($request);
    }
}
