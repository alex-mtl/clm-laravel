<x-ajax-modal
    endpoint="{{ sprintf($col->endpoint, $item->id) }}"
    title="{{ $col->name }}"
    icon="{{ $col->icon ?? 'edit_note' }}"
    class="inline-btn"
/>
