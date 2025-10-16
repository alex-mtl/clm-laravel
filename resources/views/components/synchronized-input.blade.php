<div class="input-block" x-data="{ value: '{{ $value }}' }">
{{--<div class="input-block" x-data="{ value: '{{ $value === 'on' ? 'on' : 'off' }}' }">--}}
    <!-- Hidden form input -->
    @if($type === 'checkbox')
{{--        <input type="hidden" name="{{ $name }}" x-bind:value="value">--}}
        <input type="hidden" name="{{ $name }}" x-bind:value="((value===false) || (value === 'off')) ? 'off' : 'on'">
{{--        <input type="hidden" name="{{ $name }}" x-bind:value="value">--}}
    @else
        <input type="hidden" name="{{ $name }}" x-model="value" x-on:change="{{ $onchange ?? '' }}">
    @endif
    <!-- Visible label -->
    @if($label !== 'null' && $type !== 'checkbox')
        <label for="visible-{{ $name }}" class="input-label">
            {{ $label }}
        </label>
    @endif

    <!-- Styled visible input -->
    <div class="pretty-input ">
        @if($type === 'checkbox')
            <!-- Slide switcher for checkbox -->
            <div class="flex-row space-between w100">
                @if($label !== 'null')
                    <label for="visible-{{ $name }}" class="input-label ml-3 cursor-pointer">
                        {{ $label }}
                    </label>
                @endif
                <label for="visible-{{ $name }}" class="switch ml-auto">
{{--                    <input--}}
{{--                        type="checkbox"--}}
{{--                        id="visible-{{ $name }}"--}}
{{--                        x-model="value"--}}
{{--                        true-value="on"--}}
{{--                        false-value="off"--}}
{{--                        {{ $value === 'on' ? 'checked' : '' }}--}}
{{--                        class="switch-input"--}}
{{--                    >--}}
                    <input
                        type="checkbox"
                        id="visible-{{ $name }}"
                        x-model="value"
                        true-value="on"
                        false-value="off"
                        x-bind:checked="value === 'on'"
                        class="switch-input"
                    >
                    <span class="slider round"></span>
                </label>

            </div>
        @elseif($type === 'textarea')
            <textarea
                id="visible-{{ $name }}"
                x-model="value"
                @if($required) required @endif
                @if($readonly) readonly @endif
                class="styled-input"
                placeholder="{{ $placeholder }}"
            >{{ $value }}</textarea>
        @else
            <input
                id="visible-{{ $name }}"
                type="{{ $type }}"
                x-model="value"
                value="{{ $value }}"
                @if($required) required @endif
                @if($readonly) readonly @endif
                @if($step) step="{{ $step }}" @endif
                class="styled-input"
                placeholder="{{ $placeholder }}"
                x-on:change="{{ $onchange ?? '' }}"
            >
        @endif
    </div>
</div>
