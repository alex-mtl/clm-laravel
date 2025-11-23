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
    public function index(Request $request)
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

        // Запрос с фильтрацией и сортировкой
        $query = User::latest()->with(['country', 'city', 'club', 'games', 'tournaments']);

        // Поиск по имени
        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Фильтр по стране
        if ($request->has('country') && $request->country) {
            $query->where('country_id', $request->country);
        }

        // Фильтр по городу
        if ($request->has('city') && $request->city) {
            $query->where('city_id', $request->city);
        }

        // Фильтр по клубу
        if ($request->has('club')) {
            if ($request->club === 'no_club') {
                $query->whereNull('club_id');
            } elseif ($request->club) {
                $query->where('club_id', $request->club);
            }
        }

        // Фильтр по количеству игр
        if ($request->has('min_games') && $request->min_games) {
            $query->has('games', '>=', $request->min_games);
        }
        if ($request->has('max_games') && $request->max_games) {
            $query->has('games', '<=', $request->max_games);
        }

        // Фильтр по количеству турниров
        if ($request->has('min_tournaments') && $request->min_tournaments) {
            $query->has('tournaments', '>=', $request->min_tournaments);
        }
        if ($request->has('max_tournaments') && $request->max_tournaments) {
            $query->has('tournaments', '<=', $request->max_tournaments);
        }

        // Сортировка
        $sortField = $request->get('sort', 'name');
        $sortOrder = $request->get('order', 'asc');

        switch ($sortField) {
            case 'rating':
                $query->orderBy('rating', $sortOrder);
                break;
            case 'games_count':
                $query->withCount('games')->orderBy('games_count', $sortOrder);
                break;
            case 'tournaments_count':
                $query->withCount('tournaments')->orderBy('tournaments_count', $sortOrder);
                break;
            case 'country':
                $query->join('countries', 'users.country_id', '=', 'countries.id')
                    ->orderBy('countries.name', $sortOrder)
                    ->select('users.*');
                break;
            case 'city':
                $query->join('cities', 'users.city_id', '=', 'cities.id')
                    ->orderBy('cities.name', $sortOrder)
                    ->select('users.*');
                break;
            default:
                $query->orderBy('name', $sortOrder);
        }

        $users = $query->paginate(30);

        $sidebarMenu = collect([
            [
                'icon' => 'filter_alt',
                'name' => 'Фильтры',
                'action' => 'filters',
                'handler' => 'filterOptions()', // Убедитесь, что есть скобки
                'active' => $request->anyFilled(['search', 'country', 'city', 'club', 'min_games', 'max_games', 'min_tournaments', 'max_tournaments']),
            ]
        ])->map(function($item) {
            return (object)$item;
        });

        $filterData = [
            'countries' => Country::orderBy('name')->get(),
            'cities' => City::orderBy('name')->get(),
            'clubs' => Club::orderBy('name')->get(),
        ];

        if ($request->ajax()) {
            // Для AJAX запросов возвращаем только частичный HTML
            return view('players.partials.users_table', [
                'users' => $users,
                'cols' => $cols,
            ]);
        }
        return view('players.index', [
            'cols' => $cols,
            'sidebarMenu' => $sidebarMenu,
            'filterData' => $filterData,
            'users' => $users,
            'currentFilters' => $request->all(),
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
