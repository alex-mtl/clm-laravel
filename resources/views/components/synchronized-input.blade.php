<div class="input-block" x-data="{ value: '{{ $value }}' }">
    <!-- Hidden form input -->
    <input type="hidden" name="{{ $name }}" x-model="value">

    <!-- Visible label -->
    @if($label !== 'null')
        <label for="visible-{{ $name }}" class="input-label">
            {{ $label }}
        </label>
    @endif

    <!-- Styled visible input -->
    <div class="pretty-input ">
        <input
            id="visible-{{ $name }}"
            type="{{ $type }}"
            x-model="value"
            value="{{ $value }}"
            @if($required) required @endif
            @if($readonly) readonly @endif
            class="styled-input"
            placeholder="{{ $placeholder }}"
        >
    </div>
</div>
