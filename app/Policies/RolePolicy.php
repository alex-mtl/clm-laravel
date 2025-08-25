<?php
// app/Policies/RolePolicy.php
namespace App\Policies;

use App\Models\Role;
use App\Models\User;

class RolePolicy
{
    public function manage(User $user, Role $role)
    {
//        dd($user->roles);
        if ($role->scope === 'global') {
            return $user->isAdmin();
        }

        return $user->isClubAdmin($role->club_id);
    }

    public function super_admin(User $user, ?Role $role = null)
    {

        return $user->roles()->where('slug', 'super_admin')->exists();
    }
}
