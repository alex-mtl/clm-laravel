@extends('layouts.dashboard')

@section('content')
    <div class="clm-border gap-1 w-40">
        <x-custom-dropdown
            name="club_id"
            :options="$clubSelector"
            selected="{{ old('club_id', $club->id ?? 1) }}"
            callback="getUsersByClub"
            placeholder="..."
            label="Клуб"
        />


        <div id="club-users" class="flex-row gap-1">
        </div>

    </div>
@endsection
