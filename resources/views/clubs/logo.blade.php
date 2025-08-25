@if($club->avatar ?? false)
    <div class="club-avatar">
        <img src="{{ asset('storage/' . $club->avatar) }}">
    </div>
@else
    <div class="club-avatar">
        <img src="/img/no-avatar.svg">
    </div>
@endif
