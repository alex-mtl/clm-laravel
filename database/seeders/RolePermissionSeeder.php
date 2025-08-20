<?php
// database/seeders/RolePermissionSeeder.php
namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Создаем основные привилегии
        $permissions = [
            // Глобальные привилегии
            ['name' => 'Управление пользователями', 'slug' => 'manage_users'],
            ['name' => 'Управление клубами', 'slug' => 'manage_clubs'],
            ['name' => 'Управление лигами', 'slug' => 'manage_leagues'],

            // Привилегии клубов
            ['name' => 'Управление клубом', 'slug' => 'manage_club'],
            ['name' => 'Управление составом клуба', 'slug' => 'manage_club_members'],
            ['name' => 'Создание игр', 'slug' => 'create_games'],
            ['name' => 'Управление играми', 'slug' => 'manage_games'],
            ['name' => 'Создание турниров', 'slug' => 'create_tournaments'],
            ['name' => 'Управление турнирами', 'slug' => 'manage_tournaments'],
        ];

        foreach ($permissions as $permission) {
            Permission::create($permission);
        }

        // Создаем глобальные роли
        $globalRoles = [
            [
                'name' => 'Супер администратор',
                'slug' => 'super_admin',
                'description' => 'Имеет все права в системе',
                'scope' => 'global'
            ],
            [
                'name' => 'Администратор',
                'slug' => 'admin',
                'description' => 'Администратор системы',
                'scope' => 'global'
            ],
            [
                'name' => 'Директор лиги',
                'slug' => 'league_director',
                'description' => 'Управляет лигой и клубами в ней',
                'scope' => 'global'
            ],
        ];

        foreach ($globalRoles as $roleData) {
            $role = Role::create($roleData);

            // Назначаем все права супер-админу
            if ($role->slug === 'super_admin') {
                $role->permissions()->attach(Permission::all());
            }

            // Администратору и директору лиги - соответствующие права
            if ($role->slug === 'admin') {
                $role->permissions()->attach(Permission::whereIn('slug', [
                    'manage_users', 'manage_clubs', 'manage_leagues'
                ])->get());
            }

            if ($role->slug === 'league_director') {
                $role->permissions()->attach(Permission::whereIn('slug', [
                    'manage_clubs', 'manage_leagues'
                ])->get());
            }
        }

        // Клубные роли (шаблоны, будут копироваться при создании клуба)
        $clubRoles = [
            [
                'name' => 'Владелец клуба',
                'slug' => 'club_owner',
                'description' => 'Владелец клуба с полными правами',
                'scope' => 'club'
            ],
            [
                'name' => 'Администратор клуба',
                'slug' => 'club_admin',
                'description' => 'Администратор клуба',
                'scope' => 'club'
            ],
            [
                'name' => 'Ведущий клуба',
                'slug' => 'club_host',
                'description' => 'Ведущий мероприятий в клубе',
                'scope' => 'club'
            ],
            [
                'name' => 'Организатор игр',
                'slug' => 'game_organizer',
                'description' => 'Организует игры в клубе',
                'scope' => 'club'
            ],
            [
                'name' => 'Организатор турниров',
                'slug' => 'tournament_organizer',
                'description' => 'Организует турниры в клубе',
                'scope' => 'club'
            ],
        ];

        // Эти роли будут создаваться для каждого клуба отдельно
    }
}
