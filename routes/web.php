<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ClubController;
use App\Http\Controllers\ClubMembershipController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\ClubMemberController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\GlobalRoleController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\GameParticipantController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\TournamentController;
use App\Http\Controllers\TournamentParticipantController;
use App\Http\Controllers\TournamentPagesController;
use App\Http\Controllers\RequestTypeController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\PlayerPagesController;
use App\Http\Controllers\GamePagesController;
use App\Http\Controllers\ClubPagesController;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::get('/verify-email/{token}', function ($token) {
    $user = User::where('email_verification_token', $token)->first();

    if (!$user) {
        return redirect('/login')->with('error', 'Invalid verification token.');
    }

    $user->update([
        'is_active' => true,
        'email_verified_at' => now(),
        'email_verification_token' => null,
    ]);

    Auth::login($user);

    return redirect('/dashboard')->with('success', 'Email verified successfully!');
})->name('verify.email');

// Страница запроса сброса пароля
Route::get('/forgot-password', [AuthController::class, 'forgotPasswordForm'])->name('password.request');
Route::get('/message-sent', [AuthController::class, 'messageSent'])->name('password.message-sent');
Route::post('/forgot-password', [AuthController::class, 'sendPasswordResetEmail'])->name('password.email');

// Страница сброса пароля
Route::get('/reset-password/{token}', [AuthController::class, 'resetPasswordForm'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');



Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);

Route::get('/auth/facebook', [AuthController::class, 'redirectToFacebook'])->name('auth.facebook');
Route::get('/auth/facebook/callback', [AuthController::class, 'handleFacebookCallback']);

Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])
    ->middleware('auth')
    ->name('dashboard');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/logout', [AuthController::class, 'showLogout'])
    ->middleware('auth')
    ->name('logout.form');

Route::get('/users/management', [UserController::class, 'management'])
    ->middleware('auth')
    ->name('user.management');

Route::get('/users/profile', [UserController::class, 'profile'])
    ->middleware('auth')
    ->name('user.profile');

Route::get('/users/search', [UserController::class, 'searchUsers'])
    ->name('users.search');
Route::resource('users', UserController::class)->middleware('auth')->except(['searchUsers']);
Route::resource('clubs', ClubController::class)->middleware('auth');
Route::resource('countries', CountryController::class)->middleware('auth');
Route::resource('cities', CityController::class)->middleware('auth');
Route::resource('roles', GlobalRoleController::class)->middleware('auth');

Route::get('/users/{user}/role', [GlobalRoleController::class, 'assignRoleForm'])
    ->name('users.roles.assign');
Route::get('/users/{user}/club-role/{club}', [GlobalRoleController::class, 'assignClubRoleForm'])
    ->name('users.roles.club-assign');

Route::post('/users/{user}/role', [GlobalRoleController::class, 'assignRole'])
    ->name('users.roles.store');
Route::post('/users/{user}/role/retract', [GlobalRoleController::class, 'retractRole'])
    ->name('users.roles.retract');
Route::post('/users/{user}/role/{club}', [GlobalRoleController::class, 'assignClubRole'])
    ->name('users.club-roles.store');




Route::get('/set-locale/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'ru'])) {
        session(['locale' => $locale]);
    }
    return back();
});

//Route::get('/', fn() => redirect('/login'));
Route::get('/', function () {
    return view('pages.home');
});

Route::get('/facebook/privacy', function () {
    return view('pages.privacy-facebook');
});

Route::get('/delete-account-info', function () {
    return view('pages.delete-account-info');
});


Route::middleware('auth')->group(function () {
    // Membership routes
    Route::post('/clubs/{club}/join', [ClubMembershipController::class, 'requestJoin'])
        ->name('clubs.join.request');

    Route::post('/join-requests/{joinRequest}/approve', [ClubMembershipController::class, 'approveRequest'])
        ->name('join-requests.approve');

    Route::post('/clubs/{club}/leave', [ClubMembershipController::class, 'leave'])
        ->name('clubs.leave');

    Route::post('/join-requests/{joinRequest}/decline', [ClubMembershipController::class, 'declineRequest'])
        ->name('join-requests.decline');

});


// Группа маршрутов для управления клубами
Route::prefix('clubs/{club}')->middleware(['auth'])->group(function() {
    // Управление ролями
    Route::prefix('roles')->group(function() {
        Route::get('/', [RoleController::class, 'index'])->name('clubs.roles.index');
        Route::get('/create', [RoleController::class, 'create'])->name('clubs.roles.create');
        Route::get('/{role}', [RoleController::class, 'show'])->name('clubs.roles.show');
        Route::get('/{role}/edit', [RoleController::class, 'edit'])->name('clubs.roles.edit');
        Route::delete('/{role}', [RoleController::class, 'destroy'])->name('clubs.roles.destroy');
        Route::post('/', [RoleController::class, 'store'])->name('clubs.roles.store');
        Route::put('/{role}', [RoleController::class, 'update'])->name('clubs.roles.update');
        Route::post('/assign', [RoleController::class, 'assignRole'])->name('clubs.roles.assign');
        Route::put('/{role}/permissions', [RoleController::class, 'updatePermissions'])->name('clubs.roles.update_permissions');
        Route::delete('/{user}/{role}', [RoleController::class, 'revokeRole'])->name('clubs.roles.revoke');
    });

    // Управление участниками
    Route::prefix('members')->group(function() {
        Route::get('/', [ClubMemberController::class, 'index'])->name('clubs.members.index');
        Route::post('/', [ClubMemberController::class, 'store'])->name('clubs.members.store');
        Route::delete('/{user}', [ClubMemberController::class, 'destroy'])->name('clubs.members.destroy');
        Route::get('/remove/{user}', [ClubMemberController::class, 'removeForm'])
            ->name('clubs.members.removeForm');
    });
});

Route::prefix('clubs/{club}')->group(function () {
    Route::resource('events', EventController::class)->names([
        'index' => 'clubs.events.index',
        'create' => 'clubs.events.create',
        'store' => 'clubs.events.store',
        'show' => 'clubs.events.show',
        'edit' => 'clubs.events.edit',
        'update' => 'clubs.events.update',
        'destroy' => 'clubs.events.destroy'
    ]);
});

Route::resource('request-types', RequestTypeController::class)
    ->middleware('auth'); // Add auth protection

Route::prefix('clubs/{club}')->group(function () {
    Route::resource('tournaments', TournamentController::class)->names([
        'index' => 'clubs.tournaments.index',
        'create' => 'clubs.tournaments.create',
        'store' => 'clubs.tournaments.store',
        'show' => 'clubs.tournaments.show',
        'edit' => 'clubs.tournaments.edit',
        'update' => 'clubs.tournaments.update',
        'destroy' => 'clubs.tournaments.destroy'
    ]);
});


Route::prefix('tournaments/{tournament}')->group(function () {
    Route::resource('participants', TournamentParticipantController::class)
        ->only(['index', 'create', 'store', 'destroy']);
});

Route::prefix('events/{event}')->group(function () {
    Route::resource('games', GameController::class);

    Route::prefix('games/{game}')->group(function () {
        Route::resource('participants', GameParticipantController::class)
            ->except(['show']);
    });
});

Route::get('/tournaments', [TournamentPagesController::class, 'index'])
    ->name('tournaments.index');

Route::get('/tournaments/{tournament}', [TournamentPagesController::class, 'show'])
    ->name('tournaments.show');
Route::get('/tournaments/{tournament}/requests/create', [TournamentPagesController::class, 'applicationForm'])
    ->name('tournaments.requests.create');
Route::post('/tournaments/{tournament}/requests', [TournamentPagesController::class, 'applicationStore'])
    ->name('tournaments.requests.store');

Route::post('/tournaments/{tournament}/requests/{request}/approve', [TournamentPagesController::class, 'applicationApprove'])
    ->name('tournaments.requests.approve');
Route::post('/tournaments/{tournament}/requests/{request}/decline', [TournamentPagesController::class, 'applicationDecline'])
    ->name('tournaments.requests.decline');

Route::get('/tournaments/{tournament}/partcipants/{user}/remove', [TournamentPagesController::class, 'confimationForm'])
    ->name('tournaments.partcipants.removeForm');
Route::post('/tournaments/{tournament}/partcipants/{user}/remove', [TournamentPagesController::class, 'participantRemove'])
    ->name('tournaments.partcipants.remove');


Route::get('/tournaments/{tournament}/judges/create', [TournamentPagesController::class, 'judgeCreate'])
    ->name('tournaments.judges.create');
Route::post('/tournaments/{tournament}/judges', [TournamentPagesController::class, 'judgeStore'])
    ->name('tournaments.judges.store');
Route::get('/tournaments/{tournament}/judges/{judge}/delete', [TournamentPagesController::class, 'deleteJudgeForm'])
    ->name('tournaments.judges.deleteForm');
Route::post('/tournaments/{tournament}/judges/{judge}', [TournamentPagesController::class, 'judgeDelete'])
    ->name('tournaments.judges.delete');

Route::get('/tournaments/{tournament}/couples/create', [TournamentPagesController::class, 'coupleCreate'])
    ->name('tournaments.couples.create');
Route::post('/tournaments/{tournament}/couples', [TournamentPagesController::class, 'coupleStore'])
    ->name('tournaments.couples.store');

Route::get('/tournaments/{tournament}/games/wizard', [TournamentPagesController::class, 'wizardForm'])
    ->name('tournaments.games.wizard');

Route::put('/tournaments/{tournament}/games/wizard', [TournamentPagesController::class, 'eventsUpdate'])
    ->name('tournaments.events.update');


Route::get('/tournaments/{tournament}/games/schedule', [TournamentPagesController::class, 'scheduleForm'])
    ->name('tournaments.games.schedule');




Route::get('/players', [PlayerPagesController::class, 'index'])
    ->name('players.index');

Route::get('/players/{player}', [PlayerPagesController::class, 'show'])
    ->name('players.show');

Route::get('/games/{game}/host', [GamePagesController::class, 'host'])
    ->name('games.host');

Route::get('/stream/{key}', [GamePagesController::class, 'stream'])
    ->name('games.stream');

Route::get('/games/{game}/stream', [GamePagesController::class, 'streamSettingsForm'])
    ->name('games.stream.settings');

Route::post('/games/{game}/stream', [GamePagesController::class, 'streamSettingsUpdate'])
    ->name('games.stream.update');

Route::get('/games/{game}/state/{key}', [GamePagesController::class, 'streamState'])
    ->name('games.stream.state');

Route::post('/games/{game}/stream/start', [GamePagesController::class, 'streamStart'])
    ->name('games.stream.start');

Route::post('/games/{game}/warn/{slot}', [GamePagesController::class, 'warn'])
    ->name('games.slots.warn');

Route::post('/games/{game}/speaker/{slot}', [GamePagesController::class, 'speaker'])
    ->name('games.slots.speaker');

Route::post('/games/{game}/candidate/{slot}', [GamePagesController::class, 'candidate'])
    ->name('games.slots.candidate');

Route::post('/games/{game}/host', [GamePagesController::class, 'update'])
    ->name('games.update');

Route::post('/marks-calc', [GamePagesController::class, 'marksCalc'])
    ->name('games.marksCalc');



Route::post('/games/{game}/phase', [GamePagesController::class, 'phase'])
    ->name('games.phase');


Route::get('/games/{game}/slots/{slot}/eliminate', [GamePagesController::class, 'eliminateForm'])
    ->name('games.slots.eliminateForm');

Route::post('/games/{game}/slots/{slot}/eliminate', [GamePagesController::class, 'eliminate'])
    ->name('games.slots.eliminate');

Route::get('/games/{game}/voting', [GamePagesController::class, 'votingForm'])
    ->name('games.votingForm');

Route::post('/games/{game}/voting', [GamePagesController::class, 'voting'])
    ->name('games.voting');

Route::get('/games/{game}/shooting', [GamePagesController::class, 'shootingForm'])
    ->name('games.shootingForm');

Route::post('/games/{game}/shooting', [GamePagesController::class, 'shooting'])
    ->name('games.shooting');

Route::get('/games/{game}/don-check', [GamePagesController::class, 'donCheckForm'])
    ->name('games.donCheckForm');

Route::post('/games/{game}/don-check', [GamePagesController::class, 'donCheck'])
    ->name('games.donCheck');

Route::get('/games/{game}/protocol-color', [GamePagesController::class, 'protocolColorForm'])
    ->name('games.protocolColorForm');

Route::post('/games/{game}/protocol-color', [GamePagesController::class, 'protocolColor'])
    ->name('games.protocolColor');

Route::get('/games/{game}/best-guess', [GamePagesController::class, 'bestGuessForm'])
    ->name('games.bestGuessForm');

Route::post('/games/{game}/best-guess', [GamePagesController::class, 'bestGuess'])
    ->name('games.bestGuess');

Route::get('/games/{game}/sheriff-check', [GamePagesController::class, 'sheriffCheckForm'])
    ->name('games.sheriffCheckForm');

Route::post('/games/{game}/sheriff-check', [GamePagesController::class, 'sheriffCheck'])
    ->name('games.sheriffCheck');

Route::get('/games/{game}/update-nominees', [GamePagesController::class, 'updateNominees'])
    ->name('games.slots.updateNominees');

Route::get('/games/{game}/slots/{slot}/restore', [GamePagesController::class, 'restoreForm'])
    ->name('games.slots.restoreForm');

Route::post('/games/{game}/slots/{slot}/restore', [GamePagesController::class, 'restore'])
    ->name('games.slots.restore');

Route::get('/games/{game}/host/reset-timer', [GamePagesController::class, 'resetTimerForm'])
    ->name('games.host.resetTimerForm');

Route::get('/games/{game}/delete', [GamePagesController::class, 'deleteForm'])
    ->name('games.deleteForm');
Route::post('/games/{game}/delete', [GamePagesController::class, 'delete'])
    ->name('games.delete');

//Route::get('/test-email', function () {
//    Mail::raw('Test email content', function ($message) {
//        $message->to(env('TEST_EMAIL'))
//            ->subject('Test from Laravel');
//    });
//
//    return 'Email sent!';
//});

Route::get('/manage/clubs', [ClubPagesController::class, 'index'])
    ->middleware('auth')
    ->name('manage.clubs.index');

//
//Route::post('/telegram/webhook', [TelegramController::class, 'webhook'])
//    ->middleware('telegram.bot');
//
//Route::get('/telegram/data', [TelegramController::class, 'getData'])
//    ->middleware('telegram.bot');

