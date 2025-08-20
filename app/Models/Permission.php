<?php
// app/Models/Permission.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $table = 'permissions';
    protected $fillable = ['name', 'slug', 'description'];

    public function roles()
    {
        return $this->belongsToMany(Role::class)
            ->using(RolePermission::class)
            ->withTimestamps();
    }
}
