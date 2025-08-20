<?php
// app/Http/Middleware/CheckPermission.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckPermission
{
    public function handle(Request $request, Closure $next, $permission, $clubIdParam = null)
    {
        $user = $request->user();
        $clubId = null;

        if ($clubIdParam) {
            $clubId = $request->route($clubIdParam);
        }

        if (!$user->hasPermission($permission, $clubId)) {
            abort(403, 'У вас нет прав для выполнения этого действия');
        }

        return $next($request);
    }
}
