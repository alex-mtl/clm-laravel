<?php
// app/Http/Controllers/RoleController.php
namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GlobalRoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->authorize('super_admin', new Role());
            return $next($request);
        });
    }

    // Список ролей для клуба
    public function index(Club $club)
    {

//        $globalRoles = Role::where('scope', 'global')->get();
        $globalRoles = Role::all();
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
                'name' => 'Уровень',
                'class' => 'w-10',
                'prop' => 'scope'
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

        $sidebarMenu = SuperAdminController::getSidebarMenu('roles');

        return view('global-roles.index', [
            'sidebarMenu' => $sidebarMenu,
            'globalRoles'=> $globalRoles,
            'cols' => $cols
        ]);
    }

    public function create(Request $request)
    {
        $sidebarMenu = SuperAdminController::getSidebarMenu('roles');

        return view('global-roles.form',[
            'sidebarMenu' => $sidebarMenu,
            'role' => new Role(),
            'mode' => 'create',
        ]);

    }

    public function show(Request $request, Role $role)
    {

        $sidebarMenu = SuperAdminController::getSidebarMenu('roles');

        return view('global-roles.form',[
            'sidebarMenu' => $sidebarMenu,
            'role' => $role,
            'mode' => 'show',
        ]);

    }

    public function edit(Request $request, Role $role)
    {
        $sidebarMenu = SuperAdminController::getSidebarMenu('roles');

        return view('global-roles.form',[
            'sidebarMenu' => $sidebarMenu,
            'role' => $role,
            'mode' => 'edit',
        ]);

    }

    public function update(Request $request, Role $role)
    {

        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string',
            'scope' => 'required|string',
            'description' => 'nullable|string',
            'permissions' => 'nullable|array',
            'permissions.*' => 'nullable|exists:permissions,id'
        ]);

        $role = $role->update(
            [
            'name' => $request->name,
            'slug' => $request->slug,
            'description' => $request->description,
            'scope' => $request->scope
        ]);

        if ($request->has('permissions')) {
            $role->permissions()->attach($request->permissions);
        }

        return redirect()->route('roles.index')
            ->with('success', 'Роль успешно обновлена');
    }

    // Создание новой роли для клуба
    public function store(Request $request)
    {
//        dd(auth()->user()->isSuperAdmin());
        $this->authorize('super_admin', Role::class);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'slug' => 'required|string',
            'scope' => 'required|string',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        $role = Role::create([
            'name' => $request->name,
            'slug' => $request->slug,
            'description' => $request->description,
            'scope' => $request->scope
        ]);

        if ($request->has('permissions')) {
            $role->permissions()->attach($request->permissions);
        }

        return redirect()->route('roles.index')
            ->with('success', 'Роль успешно создана');
    }

    // Назначение роли пользователю
    public function assignRole(Request $request, User $user)
    {
        $this->authorize('super_admin', Role::class);

        $request->validate([
            'role_id' => 'required|exists:roles,id'
        ]);

        $role = Role::find($request->role_id);

        // Проверка, что роль принадлежит клубу или является глобальной
        if ($role->scope === 'club') {
            abort(403, 'Эта форма для глобальных ролей');
        }

        $user->roles()->syncWithoutDetaching([$role->id]);

        return redirect()->route('users.index')->with('success', 'Роль успешно назначена');
    }    // Назначение роли пользователю

    public function retractRole(Request $request, User $user)
    {
        $this->authorize('super_admin', Role::class);

        $request->validate([
            'role_id' => 'required|exists:roles,id'
        ]);

        $role = Role::find($request->role_id);

        // Проверка, что роль принадлежит клубу или является глобальной
        if ($role->scope === 'club') {
            abort(403, 'Эта форма для глобальных ролей');
        }

        $user->roles()->detach($role->id);

        return redirect()->route('users.index')->with('success', 'Роль успешно отозвана');
    }

    public function assignClubRole(Request $request, User $user, Club $club)
    {
        $this->authorize('super_admin', Role::class);

        $request->validate([
            'role_id' => 'required|exists:roles,id',
        ]);

        $role = Role::find($request->role_id);

        // Проверка, что роль принадлежит клубу или является глобальной
        if ($role->scope === 'global') {
            abort(403, 'Эта форма для клубных ролей');
        }

        $user->roles()->syncWithoutDetaching([
            $role->id => ['club_id' => $club->id]
        ]);

        return redirect()->route('manage.clubs.index')->with('success', 'Роль успешно назначена');
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

    public function assignRoleForm(Request $request, User $user)
    {
        $this->authorize('super_admin', Role::class);
        $layout = request()->header('X-Ajax-Request') ? 'layouts.ajax' : 'layouts.dashboard';

        $roles = Role::where('scope', 'global')->pluck('name', 'id');

        $sidebarMenu = SuperAdminController::getSidebarMenu('users');

        return view('roles.global-form', [
            'sidebarMenu' => $sidebarMenu,
            'roles' => $roles,
            'role' => new Role(),
            'user' => $user,
            'mode' => 'create',
            'layout' => $layout,
        ]);
    }

    public function assignClubRoleForm(Request $request, User $user, Club $club)
    {
        $this->authorize('super_admin', Role::class);
        $layout = request()->header('X-Ajax-Request') ? 'layouts.ajax' : 'layouts.dashboard';

        $roles = Role::where('scope', 'club')->pluck('name', 'id');


        $sidebarMenu = SuperAdminController::getSidebarMenu('clubs');

        return view('roles.club-form', [
            'sidebarMenu' => $sidebarMenu,
            'roles' => $roles,
            'club' => $club,
            'role' => new Role(),
            'user' => $user,
            'mode' => 'create',
            'layout' => $layout,
        ]);
    }
}
