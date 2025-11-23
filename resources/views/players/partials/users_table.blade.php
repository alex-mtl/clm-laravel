<!-- Таблица пользователей -->
<div id="usersTable">
    <table class="table">
        <thead>
        <tr>
            @foreach($cols as $col)
                <th class="{{ $col->class }}">{{ $col->html ?: $col->name }}</th>
            @endforeach
        </tr>
        </thead>
        <tbody>
        @foreach($users as $user)
            <tr>
                <!-- Ваши колонки данных -->
                <td>{{ $user->name }}</td>
                <td>{{ $user->rating }}</td>
                <!-- ... остальные колонки ... -->
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

<!-- Пагинация -->
@if($users->hasPages())
    <div class="pagination">
        {{ $users->links() }}
    </div>
@endif
