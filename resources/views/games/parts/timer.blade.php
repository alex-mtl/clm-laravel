<div class="flex-row">
    <span class="stopwatch timer" x-data="stopwatch(60)" x-text="formatTime()"></span>
</div>
<div class="flex-row w100 space-between gap-1">
    @include('widgets.inline-btn', [
        'btnid' => 'timer-pause-btn',
        'title' => 'Пауза',
        'icon' => 'pause_circle',
        'class' => 'inline-btn',
        'endpoint' => 'timerPause()'
    ])

    @include('widgets.inline-btn', [
        'btnid' => 'timer-reset-btn',
        'title' => 'Заново',
        'icon' => 'change_circle',
        'class' => 'inline-btn',
        'endpoint' => 'timerResetOptions()',
        'callback '=> "timerReset",
    ])

{{--    @if($game->props['phase'] === 'day')--}}
{{--        @include('widgets.inline-btn', [--}}
{{--            'title' => 'Дать слово '.($game->props['phase'] !== 'day'),--}}
{{--            'icon' => 'account_circle',--}}
{{--            'class' => 'inline-btn',--}}
{{--            'endpoint' => 'setSpeakerOptions()',--}}
{{--            'callback '=> "setSpeaker",--}}
{{--            'hidden' => ($game->props['phase'] !== 'day') ? 'true' : 'false'--}}
{{--        ])--}}
{{--    @endif--}}

    @include('widgets.inline-btn', [
        'title' => 'Следующий',
        'icon' => 'expand_circle_right',
        'class' => 'inline-btn '. (in_array($game->props['phase-code'],
            ['DAY-SPEECH', 'SPEECH', 'NIGHT-CAHOOT', 'SHERIFF-SIGN', 'FREE']) ? 'active' : ''),
        'endpoint' => 'nextSpeaker()'
    ])


</div>
<div class="w-5 canter ml-auto mr-auto">
<x-custom-dropdown
    btnid="timer-options"
    name="timer_options"
    :options="['1' => '15', '2'=>'30', '3' => '45', '4' => '60']"
    selected="{{ old('timer_options', '4' ) }}"
    placeholder="60"
    callback="timerReset"
    :readonly="false"
    :invisible="true"
    label=""
/>
</div>
<div class="w-10 canter ml-auto mr-auto">
    <x-custom-dropdown
        btnid="speaker-options"
        name="timer_options"
        :options="$speakerOptions"
        selected="{{ old('timer_options', '4' ) }}"
        placeholder="60"
        callback="timerReset"
        :readonly="false"
        :invisible="true"
        label=""
    />
</div>
<script>
    function stopwatch(initialSeconds) {
        return {
            seconds: initialSeconds,
            interval: null,
            running: false,

            start(sec) {
                this.reset(sec);
                this.$el.classList.toggle('zero', this.seconds === 0);
                this.resume();
            },

            pause() {
                clearInterval(this.interval);
                this.running = false;
            },

            resume() {
                if (!this.running && this.seconds > 0) {
                    this.running = true;
                    this.interval = setInterval(() => {
                        if (this.seconds <= 0) {
                            this.pause();
                            this.$el.classList.toggle('zero', this.seconds === 0);
                            return;
                        }
                        this.seconds--;
                    }, 1000);
                }
            },

            reset(sec = null) {
                this.pause();
                this.seconds = sec !== null ? sec : initialSeconds;
            },

            formatTime() {
                const mins = Math.floor(this.seconds / 60);
                const secs = this.seconds % 60;
                return `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
            }
        };
    }

    document.addEventListener('DOMContentLoaded', () => {
        timer = Alpine.$data(document.querySelector('.timer'));
        timer.start({{  $game->props['timer'] ?? 0 }});
    });
</script>
