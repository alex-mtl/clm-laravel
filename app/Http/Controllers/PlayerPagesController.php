<?php

namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\Country;
use App\Models\City;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PlayerPagesController extends Controller
{
    public $sidebarMenu;
    public function __construct()
    {
        $this->middleware('auth');

        $this->sidebarMenu = collect([
            [
                'icon' => 'readiness_score',
                'name' => 'Игровая статистика',
                'action' => 'stats',
                'handler' => false,
                'active' => false,
            ],

            [
                'icon' => 'groups',
                'name' => 'Клубы',
                'action' => 'clubs',
                'handler' => false,
                'active' => false,
            ],

            [
                'icon' => 'diversity_1',
                'name' => 'Друзья',
                'action' => 'friends',
                'handler' => false,
                'active' => false,
            ],
            [
                'icon' => 'trophy',
                'name' => 'Турниры',
                'action' => 'tournaments',
                'handler' => false,
                'active' => false,
            ],

            [
                'icon' => 'interpreter_mode',
                'name' => 'Игры',
                'action' => 'games',
                'handler' => false,
                'active' => false,
            ],

//            [
//                'icon' => 'scoreboard',
//                'name' => 'Результаты',
//                'action' => 'results',
//                'handler' => false,
//                'active' => false,
//            ],
//
//            [
//                'icon' => 'balance',
//                'name' => 'Судьи',
//                'action' => 'judges',
//                'handler' => false,
//                'active' => false,
//            ],
        ])->map(function($item) { // Changed from fn() to full function syntax
            return (object)$item;
        });
    }

    public function getSidebarMenu($activeTab)
    {
        $sidebarMenu = collect($this->sidebarMenu)->map(function($item) use ($activeTab) { // Changed from fn() to full function syntax
            $item = (object)$item;
            $item->active = $item->action == $activeTab;
            return $item;
        });
        return $sidebarMenu;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cols = collect([
            [
                'html' => '<span class="material-symbols-outlined">chart_data</span>',
                'name' => "Рейтинг",
                'class' => 'w-5 center',
                'prop' => 'rating',
                'default' => '0',
            ],



        ])->map(fn($item) => (object)$item);
        $users = User::latest()->paginate(30);

        return view('players.index', [
            'cols' => $cols,
            ...compact('users'),
        ]);
    }



    /**
     * Display the specified resource.
     */
    public function show(User $player)
    {
        if ($tab = request()->query('tab') ?? null) {
            session()->flash('tab', $tab);
        }

        $sidebarMenu = $this->getSidebarMenu(session('tab') ?? 'stats');
        $playerInfo = $player->getPlayerInfo();
//        $sidebarMenu = SuperAdminController::getSidebarMenu('users');
        return view('players.show', [
            'player' => $player,
            'sidebarMenu' => $sidebarMenu,
            'playerInfo' => $playerInfo,
//            'styles' => ['player-show.css'],
//            'countries' => Country::all(),
//            'cities' => City::all(),
            'mode' => 'show'
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {

        $clubSelector = Club::getClubSelector();
        $citySelector = City::getCitySelector();
        $countrySelector = Country::getCountrySelector();
        $sidebarMenu = SuperAdminController::getSidebarMenu('users');
//        return view('users.edit', [

        return view('users.show', [
            ...compact('user'),
            'sidebarMenu' => $sidebarMenu,
            'clubSelector' => $clubSelector,
            'citySelector' => $citySelector,
            'countrySelector' => $countrySelector,
//            'styles' => ['user-edit.css'],
            'styles' => ['user-show.css'],
            'mode' => 'edit',
        ]);
    }

    public function profile()
    {

        $user = auth()->user();
        $clubSelector = $user->getClubSelector();
        $countrySelector = Country::getCountrySelector();
        $citySelector = City::getCitySelector();

        return view('users.show', [
            'clubSelector' => $clubSelector,
            'countrySelector' => $countrySelector,
            'citySelector' => $citySelector,
            ...compact('user'),
            'styles' => ['user-show.css'],
            'mode' => 'edit',
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {

//        dd($request->all());
        $request->merge([
            'club_id' => ($request->club_id == 0 || $request->club_id == 'null' )? null : $request->club_id
        ]);
        $validated = $request->validate([
            'name' => 'required|string|max:64',
            'email' => 'required|email|unique:users,email,'.$user->id,
//            'password' => 'nullable|min:8|confirmed',
            'country_id' => 'nullable|exists:countries,id',
            'city_id' => 'nullable|exists:cities,id',
            'club_id' => 'nullable|exists:clubs,id',
            'first_name' => 'nullable|string|max:32',
            'last_name' => 'nullable|string|max:32',
//            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);


        $validated['club_id'] = $validated['club_id'] == 0 ? null : $validated['club_id'];

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('avatar')) {
            $validated['avatar'] = User::saveAvatar($request, $user);
        }

        $user->update($validated);

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }

    public function management()
    {
        $users = User::latest()->paginate(10);
        $fakes = collect([
            (object)[
                'name' => 'AdminUser',
                'email' => 'admin@clm.com',
                'avatar' => '%7Bself::AVATARS_DIR%7D/b322Ftpo.png',
                'roles' => 'Адинистратор',
                'status' => 'Активный',
                'rating' => '2950',
                'reg_date' => '2023-01-01',
                'last_activity' => '2023-01-02',
            ],
            (object)[
                'name' => 'MafiaJudge',
                'email' => 'judge@clm.com',
                'avatar' => '%7Bself::AVATARS_DIR%7D/b322Ftpo.png',
                'roles' => 'Судья',
                'status' => 'Активный',
                'rating' => '3120',
                'reg_date' => '2024-07-01',
                'last_activity' => '2024-07-02',
            ],

            (object)[
                'name' => 'NewbiePlayer',
                'email' => 'player@clm.com',
                'avatar' => null,
                'roles' => 'Игрок',
                'status' => 'Заблокирован',
                'rating' => '387',
                'reg_date' => '2025-01-01',
                'last_activity' => '2025-01-02',
            ],
        ]);
        return view('users.management', [
            ...compact('users'),
            ...compact('fakes'),
            'noFooter' => true,
            'styles' => ['user-management.css']
        ] );
    }
}
