
<div class="tournament-logo relative w100 ta-center">
    <span class="absolute " style="top:2rem; right:2rem"> max: 2Mb</span>
    <span class="absolute " style="top:4rem; right:2rem"> ratio: 1:1</span>
    @if($tournament->logo ?? false)
        <img src="{{ asset('storage/' . $tournament->logo) }}">
    @else
        <img src="/img/no-tournament-logo.svg">
    @endif
</div>


@if($mode !== 'show')
    <div class="">
        <x-avatar-upload
            :initial-avatar=" asset('storage/' . $tournament->logo)"
            name="logo"
            target-selector=".tournament-logo img"

        />
    </div>
@endif


@if($tournament->banner ?? false)
    <div class="tournament-banner">
        <img src="{{ asset('storage/' . $tournament->banner) }}">
    </div>
@else
    <div class="tournament-banner relative">
        <span class="absolute " style="top:2rem; right:2rem"> max: 5Mb</span>
        <span class="absolute " style="top:4rem; right:2rem"> ratio: 3:1</span>
        <img src="/img/no-tournament-logo.svg">
    </div>
@endif

@if($mode !== 'show')
    <div class="">
        <x-avatar-upload
            :initial-avatar=" asset('storage/' . $tournament->banner)"
            name="banner"
            target-selector=".tournament-banner img"
            aspect-ratio="3:1"
            max-size="5120"
        />
    </div>
@endif
