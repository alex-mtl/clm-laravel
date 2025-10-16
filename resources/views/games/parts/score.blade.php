<span class="w-4 hidden">
    <x-synchronized-input
        name="slots[{{$i}}][score_base]"
        label=""
        value="{{ old('slots.'.$i.'score_base', $slot['score_base'] ?? 0) }}"
        placeholder="0"
        step="0.1"
        visible="false"
        type="number"
    />
</span>
<span class="w-4">
    <x-synchronized-input
        name="slots[{{$i}}][score_1]"
        label=""
        value="{{ old('slots.'.$i.'score_1', $slot['score_1'] ?? 0) }}"
        placeholder="0"
        step="0.1"
        type="number"
    />
</span>
<span class="w-4">
    <x-synchronized-input
        name="slots[{{$i}}][score_2]"
        label=""
        value="{{ old('slots.'.$i.'score_2', $slot['score_2'] ?? 0) }}"
        placeholder="0"
        step="0.1"
        type="number"
    />
</span>
<span class="w-4 hidden">
    <x-synchronized-input
        name="slots[{{$i}}][score_5]"
        label=""
        value="{{ old('slots.'.$i.'score_5', $slot['score_5'] ?? 0) }}"
        placeholder="0"
        step="0.1"
        type="number"
    />
</span>
<span class="w-4">
    <x-custom-dropdown
        name="slots[{{$i}}][mark]"
        :options="$marks"
        selected="{{ old('slots.'.$i.'mark', $slot['mark'] ?? 'zero') }}"
        {{--                        selected="{{ 'zero' }}"--}}
        label=""
        {{--                        x-on:change="mechPoints(this, {{ $i }})"--}}
        {{--                        x-on:change="alert($event.target.value)"--}}
        :callback="'mechPoints'"
        {{--                        :callback="'handleMarkChange'"--}}
        data-slot="{{ $i }}"
    />
</span>
<span class="w-4">
    <x-synchronized-input
        name="slots[{{$i}}][score_3]"
        label=""
        value="{{ old('slots.'.$i.'score_3', $slot['score_3'] ?? 0) }}"
        placeholder="0"
        step="0.1"
        type="number"
    />
</span>
<span class="w-4">
    <x-synchronized-input
        name="slots[{{$i}}][score_4]"
        label=""
        value="{{ old('slots.'.$i.'score_4', $slot['score_4'] ?? 0) }}"
        placeholder="0"
        step="0.1"
        type="number"
    />
</span>

<span class="w-4">
    <x-synchronized-input
        name="slots[{{$i}}][score_total]"
        label=""
        value="{{ old('slots.'.$i.'score_total', $slot['score_total'] ?? 0) }}"
        placeholder="0"
        step="0.1"
        type="number"
    />
</span>

