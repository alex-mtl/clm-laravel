<div
    class="material-symbols-outlined"
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
