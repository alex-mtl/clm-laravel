<div
    class="slot-row flex-row gap-05"
    data-slot="{{ $i }}"
    data-status="{{ $slot['status'] ?? 'alive' }}"
    data-warns="{{ $slot['warns'] ?? 0 }}"
    data-role="{{  $slot['role'] ?? ''}}"
    data-speaker="{{ ( $game->props['days']['D'.$day]['active_speaker'] ?? null ) === $i ? 'active' : '' }}"
>
    <span class="slot-number">{{ $i }}</span>
    @if( !in_array($game->props['phase'], ['shuffle-slots', 'shuffle-roles']) )
        <div class="role-container material-symbols-outlined" title="{{ $slot['role-title'] ?? ''}}">
            <span class="role-icon" data-role="{{ $slot['role'] ?? ''}}" data-slot="{{ $i }}"></span>
        </div>
    @endif
    @if($game->props['phase'] === 'shuffle-slots')
        @include('widgets.user-autocomplete', [
            'name' => "slots[{$i}][user_id]",
            'label' => '',
            'selected' => ['id' => $slot['user_id']?? null, 'name' => $slot['name']?? null],
            'searchUrl' => route('users.search'),
//                            'placeholder' => 'Type to search users...'
            'placeholder' => '...'
        ])
    @else

        <div class="player-avatar">
            <span class="player-warns" onclick="removeWarning({{ $game->id }}, {{ $i }})">
            </span>
            <a href="/players/{{ $slot['user_id'] }}" >
                <img src="{{ $slot['avatar'] }}">
            </a>
        </div>

        <div class="w-10 x-10">{{ $slot['name'] ?? '' }}</div>

    @endif


    @if($game->props['phase'] === 'shuffle-roles')
        <x-custom-dropdown
            class="w-10"
            name="slots[{{$i}}][role]"
            :options="$gameRoles"
            selected="{{ old('slots.'.$i.'role', $slot['role'] ?? 'citizen') }}"
            label=""
            :readonly="$mode === 'show'"
        />
    @endif

    @if( !in_array($game->props['phase'], ['shuffle-slots', 'shuffle-roles', 'game-over', 'finished']) )
        <span class="w-4">
            <x-synchronized-input
                name="slots[{{$i}}][candidate]"
                label=""
                value="{{ old('slots.'.$i.'candidate', $slot['candidate'] ?? 0) }}"
                placeholder="0"
                step="1"
                max="10"
                min="0"
                onchange="updateCandidate({{ $i }})"
                type="number"
            />
        </span>

            <x-ajax-modal
                btnid="slot-{{$i}}-eliminate-btn"
                hidden="{{ $slot['status'] !== 'alive' ? 'true' : 'false' }}"
                endpoint="{{ route('games.slots.eliminateForm', ['game' => $game->id, 'slot' => $i]) }}"
                title="Удаление"
                class="inline-btn"
                callback="removeSlotResponse"
                icon="frame_person_off"
            />

            <x-ajax-modal
                btnid="slot-{{$i}}-restore-btn"
                hidden="{{ ($slot['status'] === 'alive') ? 'true' : 'false' }}"
                endpoint="{{ route('games.slots.restoreForm', ['game' => $game->id, 'slot' => $i]) }}"
                title="Вернуть"
                class="inline-btn "
                callback="restoreSlotResponse"
                icon="frame_person"
            />


            @include('widgets.inline-btn', [
                'title' => 'Фол',
                'icon' => 'report',
                'btnid' => 'slot-' . $i . '-warn-btn',
                'class' => 'inline-btn',
                'endpoint' => 'addWarning(' . $game->id .',' . $i .')',
                'endpoint_params' => [$game->id, $i],
                'hidden' => (($slot['status'] !== 'alive') ? 'true' : 'false'),
    //            'callback '=> "startStreamHandler",
            ])




{{--            <x-slot-selector--}}
{{--                name="slots[{{$i}}][candidate]"--}}
{{--                selected-slot="{{ old('slots.'.$i.'.candidate', $slot['candidate'] ?? 0) }}"--}}
{{--                slot-availability="[--}}
{{--                    1 => true,--}}
{{--                    2 => true,--}}
{{--                    3 => false,--}}
{{--                    4 => false,--}}
{{--                    5 => true,--}}
{{--                    6 => true,--}}
{{--                    7 => false,--}}
{{--                    8 => true,--}}
{{--                    9 => true,--}}
{{--                    10 => true--}}
{{--                ]"--}}
{{--            />--}}

    @endif
    @if($game->props['phase-code'] === 'SCORE')
        @include('games.parts.score')
    @elseif($game->props['phase'] === 'finished')
        @include('games.parts.results')
    @endif
</div>
