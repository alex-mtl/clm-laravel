<?php
// app/Models/Role.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'roles';
    protected $fillable = ['name', 'slug', 'description', 'scope', 'club_id'];

    public function permissions()
    {
        return $this->belongsToMany(Permission::class)
            ->using(RolePermission::class)
            ->withTimestamps();
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_role')->withPivot('club_id');
    }

    public function club()
    {
        return $this->belongsTo(Club::class);
    }

    // Глобальные роли (не привязанные к клубу)
    public function isGlobal()
    {
        return in_array($this->slug, ['super_admin', 'admin', 'league_director']);
    }
}
