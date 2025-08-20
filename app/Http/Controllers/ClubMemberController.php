<?php
// app/Http/Controllers/ClubMemberController.php
namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;

class ClubMemberController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Список участников клуба
    public function index(Club $club)
    {

        $this->authorize('manage_club_members', $club);

        $members = $club->members()->with('roles')->get();
        $availableRoles = $club->roles()->get();
        $globalRoles = Role::where('scope', 'global')->get();

        return view('clubs.members.index', compact('club', 'members', 'availableRoles', 'globalRoles'));
    }

    // Добавление участника в клуб
    public function store(Request $request, Club $club)
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

        return redirect()->back()->with('success', 'Пользователь успешно добавлен');
    }

    // Удаление участника из клуба
    public function destroy(Club $club, User $user)
    {
        $this->authorize('manage_club_members', $club);

        // Отзываем все роли клуба у пользователя
        $user->roles()->wherePivot('club_id', $club->id)->detach();

        return redirect()->back()->with('success', 'Пользователь удален из клуба');
    }
}
