<?php
// app/Http/Controllers/RoleController.php
namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Список ролей для клуба
    public function index(Club $club)
    {
        $this->authorize('manage_club_members', $club);

        $roles = $club->roles()->with('permissions')->get();
        $globalRoles = Role::where('scope', 'global')->get();
        $cols = collect([
            [
                'name' => 'Роль',
                'class' => 'w-10',
                'prop' => 'name'
            ],
            [
                'name' => 'Код',
                'class' => 'w-10',
                'prop' => 'slug'
            ],
            [
                'name' => 'Описание',
                'class' => 'w-10',
                'prop' => 'description'
            ],
            [
                'name' => 'Действия',
                'class' => 'w-10',
                'prop' => 'actions'
            ],

        ])->map(fn($item) => (object)$item);
        $permissions = Permission::all();

        return view('roles.index', [
            ...compact('club', 'roles', 'globalRoles', 'permissions'),
            'cols' => $cols
        ]);
    }

    public function create(Request $request, Club $club)
    {
        $this->authorize('manage_club_members', $club);

        return view('clubs.roles.form',[
            'role' => new Role(),
            'club' => $club,
            'mode' => 'create',
        ]);

    }

    public function show(Request $request, Club $club, Role $role)
    {
        $this->authorize('manage_club_members', $club);

        return view('clubs.roles.form',[
            'role' => $role,
            'club' => $club,
            'mode' => 'show',
        ]);

    }

    public function edit(Request $request, Club $club, Role $role)
    {
        $this->authorize('manage_club_members', $club);

        return view('clubs.roles.form',[
            'role' => $role,
            'club' => $club,
            'mode' => 'edit',
        ]);

    }

    public function update(Request $request, Club $club, Role $role)
    {
        $this->authorize('manage_club_members', $club);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        $role = $club->roles()->update(
            [
            'name' => $request->name,
            'slug' => $request->slug ?: Str::slug($request->name . '_' . $club->id),
            'description' => $request->description,
            'scope' => 'club'
        ]);

        if ($request->has('permissions')) {
            $role->permissions()->attach($request->permissions);
        }

        return redirect()->route('clubs.roles.index', $club)
            ->with('success', 'Роль успешно обновлена');
    }

    // Создание новой роли для клуба
    public function store(Request $request, Club $club)
    {
        $this->authorize('manage_club_members', $club);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        $role = $club->roles()->create([
            'name' => $request->name,
            'slug' => Str::slug($request->name . '_' . $club->id),
            'description' => $request->description,
            'scope' => 'club'
        ]);

        if ($request->has('permissions')) {
            $role->permissions()->attach($request->permissions);
        }

        return redirect()->route('clubs.roles.index', $club)
            ->with('success', 'Роль успешно создана');
    }

    // Назначение роли пользователю
    public function assignRole(Request $request, Club $club)
    {
        $this->authorize('manage_club_members', $club);

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role_id' => 'required|exists:roles,id'
        ]);

        $user = User::find($request->user_id);
        $role = Role::find($request->role_id);

        // Проверка, что роль принадлежит клубу или является глобальной
        if ($role->scope === 'club' && $role->club_id !== $club->id) {
            abort(403, 'Нельзя назначить роль из другого клуба');
        }

        $user->roles()->syncWithoutDetaching([
            $role->id => ['club_id' => $role->scope === 'club' ? $club->id : null]
        ]);

        return redirect()->back()->with('success', 'Роль успешно назначена');
    }

    // Отзыв роли у пользователя
    public function revokeRole(Request $request, Club $club, User $user, Role $role)
    {
        $this->authorize('manage_club_members', $club);

        // Проверка, что роль принадлежит клубу или является глобальной
        if ($role->scope === 'club' && $role->club_id !== $club->id) {
            abort(403, 'Нельзя отозвать роль из другого клуба');
        }

        $user->roles()->detach($role->id);

        return redirect()->back()->with('success', 'Роль успешно отозвана');
    }

    // Обновление прав роли
    public function updatePermissions(Request $request, Club $club, Role $role)
    {
        $this->authorize('manage_club_members', $club);

        if ($role->scope === 'global' || $role->club_id !== $club->id) {
            abort(403, 'Нельзя изменить эту роль');
        }

        $request->validate([
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        $role->permissions()->sync($request->permissions);

        return redirect()->back()->with('success', 'Права роли обновлены');
    }

    public function destroy(Request $request, Club $club, Role $role)
    {
        $this->authorize('manage_club_members', $club);
        $role->delete();
        return redirect()->route('clubs.roles.index');
    }
}
