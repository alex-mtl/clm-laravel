@if($user->avatar ?? false)
    <div class="user-avatar">
        <img src="{{ asset('storage/' . $user->avatar) }}">
    </div>
@else
    <div class="user-avatar">
        <img src="/img/no-avatar.svg">
    </div>
@endif
