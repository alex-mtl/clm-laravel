<div
    class="material-symbols-outlined {{ ($hidden ?? false) === 'true' ? 'hidden' : '' }}"
    @if($btnid  ?? false)
        id="{{$btnid}}"
    @endif
>
    <span
        class="action-btn {{ $class }}"
        title="{{ $title }}"
        onclick="{{ $endpoint }}"
    >{{ $icon }}</span>
</div>
