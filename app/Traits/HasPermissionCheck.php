<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use App\Models\Permissions;

trait HasPermissionCheck
{
    public function checkPermission(string $permissionName, string $action): ?JsonResponse
    {
        $user = Auth::user();

        $permission = Permissions::where([
            'permission' => $permissionName,
            'role_id' => $user->roleUser->role->id
        ])->first();

        if (!$permission || $permission->$action != 1) {
            return response()->json([
                'status' => 'error',
                'message' => "You do not have permission to {$action} {$permissionName}"
            ], 403);
        }

        return null;
    }
}
