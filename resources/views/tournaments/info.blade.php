<div class="data-wrapper {{ session('tab') ? (session('tab') !== 'info' ? 'hidden' : '') : '' }}"  id="tournament-info-data" >
    <h1>{{ $tournament->name }}</h1>
    <div class="tournament-description">{{ $tournament->description ?? '' }}</div>
    @foreach($tournamentInfo as $info)
        @include('widgets.prop-line', ['prop' => $info])
    @endforeach
</div>
