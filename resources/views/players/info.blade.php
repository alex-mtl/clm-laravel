<div class="data-wrapper {{ session('tab') ? (session('tab') !== 'info' ? 'hidden' : '') : '' }}"  id="tournament-info-data" >
    <h1>{{ $player->name }}</h1>
    @foreach($playerInfo as $info)
        @include('widgets.prop-line', [ 'label' => $info->label, 'value' => $info->value ])
    @endforeach
</div>
