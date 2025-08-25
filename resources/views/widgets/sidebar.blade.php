<div class="sidebar-menu-wrapper flex-column gap-1 ">
    @foreach ($menu as $item)
        <div class="sidebar-menu-item {{ $item->active ? 'active' : '' }}"
             data-action="{{ $item->action }}"
             onclick="{{ $item->handler }}"
        >
            <span class="sidebar-menu-icon material-symbols-outlined">{{ $item->icon ?? (($item->active) ? 'check_box' : 'check_box_outline_blank') }}</span>
            <span class="">{{ $item->name }}</span>
            <span class="sidebar-menu-chevron material-symbols-outlined">chevron_right</span>
        </div>
    @endforeach

</div>
