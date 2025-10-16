{{--<x-ajax-modal--}}
{{--    endpoint="{{ sprintf($col->endpoint, $item->id) }}"--}}
{{--    title="{{ $col->name }}"--}}
{{--    icon="{{ $col->icon ?? 'edit_note' }}"--}}
{{--    class="inline-btn"--}}
{{--/>--}}

{{--callback="{{ $btn->callback ?? '' }}"--}}
<x-ajax-modal
    endpoint="{{ sprintf($btn->endpoint, ...$btn->endpoint_params) }}"
    title="{{ $btn->name }}"
    icon="{{ $btn->icon ?? 'edit_note' }}"
    class="{{ $btn->class ?? 'inline-btn' }}"
    callback="{{ $btn->callback ?? 'null' }}"

/>
