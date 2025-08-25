@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Участники клуба {{ $club->name }}</h1>

        <div class="mb-4">
            <button class="btn btn-primary" data-toggle="modal" data-target="#addMemberModal">
                Добавить участника
            </button>
        </div>

        <table class="table">
            <thead>
            <tr>
                <th>Имя</th>
                <th>Роли</th>
                <th>Действия</th>
            </tr>
            </thead>
            <tbody>
            @foreach($members as $member)
                <tr>
                    <td>{{ $member->name }}</td>
                    <td>
                        @foreach($member->roles as $role)
                            @if($role->pivot->club_id == $club->id || $role->scope == 'global')
                                <span class="badge badge-primary">
                                    {{ $role->name }}
                                </span>
                            @endif
                        @endforeach
                    </td>
                    <td>
                        <button class="btn btn-sm btn-info assign-role-btn"
                                data-user-id="{{ $member->id }}"
                                data-toggle="modal"
                                data-target="#assignRoleModal">
                            Назначить роль
                        </button>
                        <form action="{{ route('clubs.members.destroy', [$club, $member]) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Вы уверены?')">
                                Удалить
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>


    <!-- Модальное окно добавления участника -->
    <div class="modal fade" id="addMemberModal" tabindex="-1" role="dialog" aria-labelledby="addMemberModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('clubs.members.store', $club) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="addMemberModalLabel">Добавить участника</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="user_id">Пользователь</label>
                            <select class="form-control" id="user_id" name="user_id" required>
                                <option value="">Выберите пользователя</option>
                                @foreach(App\Models\User::whereNotIn('id', $club->members->pluck('id'))->get() as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="role_id">Роль</label>
                            <select class="form-control" id="role_id" name="role_id" required>
                                <option value="">Выберите роль</option>
                                @foreach($availableRoles as $role)
                                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                                @endforeach
                                @foreach($globalRoles as $role)
                                    <option value="{{ $role->id }}">{{ $role->name }} (глобальная)</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Отмена</button>
                        <button type="submit" class="btn btn-primary">Добавить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Модальное окно назначения роли -->
    <div class="modal fade" id="assignRoleModal" tabindex="-1" role="dialog" aria-labelledby="assignRoleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="assignRoleForm" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="assignRoleModalLabel">Назначить роль</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="user_id" id="assign_user_id">
                        <div class="form-group">
                            <label for="assign_role_id">Роль</label>
                            <select class="form-control" id="assign_role_id" name="role_id" required>
                                <option value="">Выберите роль</option>
                                @foreach($availableRoles as $role)
                                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                                @endforeach
                                @foreach($globalRoles as $role)
                                    <option value="{{ $role->id }}">{{ $role->name }} (глобальная)</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Отмена</button>
                        <button type="submit" class="btn btn-primary">Назначить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('.assign-role-btn').click(function() {
                var userId = $(this).data('user-id');
                $('#assign_user_id').val(userId);
                $('#assignRoleForm').attr('action', '/clubs/{{ $club->id }}/roles/assign');
            });
        });
    </script>
@endpush
