<div class="slot-row flex-row gap-05" data-slot="{{ $i }}" data-status="{{ $slot['status'] ?? 'alive' }}">
    <span class="slot-number">{{ $i }}</span>
    @if( !in_array($game->props['phase'], ['shuffle-slots', 'shuffle-roles']) )
        <div class="role-container material-symbols-outlined" >
            <span class="role-icon" data-role="{{  $slot['role'] ?? ''}}"></span>
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
            <a href="/users/{{ $slot['user_id'] }}" >
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
            selected="{{ old('role.'.$i.'role', $slot['role'] ?? 'citizen') }}"
            label=""
            :readonly="$mode === 'show'"
        />
    @endif

    @if( !in_array($game->props['phase'], ['shuffle-slots', 'shuffle-roles']) )

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


    @endif
</div>
