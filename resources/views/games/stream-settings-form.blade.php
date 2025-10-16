@extends($layout ?: 'layouts.app')

@section('content')
    <div class="content-main">
        <form
            class="flex-column w-15"
            x-data="{
                streamKey: '{{ old('stream_key', $game->props['stream_key'] ?? $settings['stream-key'] ?? '') }}',
                get streamUrl() {
                    return '{{ url('/games/' . $game->id . '/stream') }}/' + this.streamKey;
                },
                copyToClipboard() {
                    navigator.clipboard.writeText(this.streamUrl).then(() => {
                        alert('Ссылка скопирована в буфер обмена');
                    });
                }
            }"
            x-ref="streamSettingsForm"
            id="stream-settings-form"
            action="{{ route('games.stream.update', [$game->id]) }}" method="POST">
            @csrf
            <button class="hidden" type="submit">Save</button>

            <div class="flex-end gap-1 ml-auto mr-auto">
                <x-synchronized-input
                    name="stream_key"
                    label="Ключ"
                    x-model="streamKey"
                    value="{{ old('stream_key', $game->props['stream_key'] ?? $settings['stream-key'] ?? '') }}"
                    placeholder="Ключ трансляции"
                    required
                    :readonly="false"
                />
                @include('widgets.inline-btn', [
                       'title' => 'Сгенерировать',
                       'class' => 'inline-btn mb-06',
                       'icon' => 'change_circle',
                       'endpoint' => 'generateStreamLink()',
               ])

            </div>

            <x-synchronized-input
                name="show_roles"
                type="checkbox"
                label="Показывать роли"
                x-model="showRoles"
                value="{{ old('show_roles', $game->props['stream']['show-roles'] ?? 'on') }}"
                required
                :readonly="false"
            />

            <x-synchronized-input
                name="show_name"
                type="checkbox"
                label="Название игры"
                x-model="showName"
                value="{{ old('show_name', $game->props['stream']['show-name'] ?? 'on') }}"
                required
                :readonly="false"
            />

            <x-synchronized-input
                name="show_subphase"
                type="checkbox"
                label="Фазу игры"
                x-model="showSubphase"
                value="{{ old('show_subphase', $settings['show-subphase'] ?? 'off') }}"
                required
                :readonly="false"
            />

            <x-synchronized-input
                name="show_judge"
                type="checkbox"
                label="Судья"
                x-model="showJudge"
                value="{{ old('show_judge', $settings['show-judge'] ?? 'off') }}"
                required
                :readonly="false"
            />
{{--            <div class="flex-row gap-05">--}}
                <x-synchronized-input
                    name="show_killed"
                    type="checkbox"
                    label="Отстрелы"
                    x-model="showKilled"
                    value="{{ old('show_killed', $game->props['stream']['show-killed'] ?? 'on') }}"
                    required
                    :readonly="false"
                />

                <x-synchronized-input
                    name="show_voted"
                    type="checkbox"
                    label="Заголосованых"
                    x-model="showVoted"
                    value="{{ old('show_voted', $game->props['stream']['show-voted'] ?? 'off') }}"
                    required
                    :readonly="false"
                />
{{--            </div>--}}



            <div class="flex-row mt-1">
                <div class="flex items-center justify-between gap-2 w100">

                <span class="btn ml-auto mr-auto"
                      x-data
                      {{--                      @click="document.getElementById('eliminate-player-form').submit()">Удалить</span>--}}
                      {{--                      @click="document.getElementById('stream-settings-form').submit()"--}}
                      @click="$refs.streamSettingsForm.requestSubmit()">Сохранить</span>

                </div>
            </div>

        </form>




    </div>
@endsection
