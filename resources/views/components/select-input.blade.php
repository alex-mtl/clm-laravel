<div class="input-block">
    <!-- Visible label -->
    <label for="{{ $name }}" class="input-label">
        {{ $label }}
    </label>

    <!-- Styled select -->
    <div class="pretty-input">
        <select
            name="{{ $name }}"
            id="{{ $name }}"
            @if($required) required @endif
            @if($readonly) readonly @endif
            class="styled-input"
        >
            @if($placeholder)
                <option value="">{{ $placeholder }}</option>
            @endif

            @foreach($options as $key => $value)
                <option value="{{ $key }}"
                    {{ $selected == $key ? 'selected' : '' }}>
                    {{ $value }}
                </option>
            @endforeach
        </select>
    </div>
</div>
