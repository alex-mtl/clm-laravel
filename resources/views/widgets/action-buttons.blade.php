@if($ajax ?? false)
    <x-ajax-modal
        endpoint="{{ route($resource.'.show', $item) }}"
        title="Подробнее"
        :icon="'mystery'"
        :class="'inline-btn'"/>

    <x-ajax-modal
        endpoint="{{ route($resource.'.edit', $item) }}"
        title="Редактировать"
        :icon="'edit_note'"
        :class="'inline-btn'"/>
@else
    <div  class="col-{{ $idx }} {{ $col->class ?? '' }} flex-row gap-1 action-buttons">

        @if($parent ?? false)
            <div class="action-btn" onclick="window.location.href = '{{ route($resource.'.show', [$parent, $item]) }}';" title="Подробнее">
                <span class="material-symbols-outlined" onclick="window.location.href = '{{ route($resource.'.show', [$parent, $item]) }}';">mystery</span>
            </div>
        @else
            <div class="action-btn" onclick="window.location.href = '{{ route($resource.'.show', $item) }}';" title="Подробнее">
                <span class="material-symbols-outlined" onclick="window.location.href = '{{ route($resource.'.show', $item) }}';">mystery</span>
            </div>
        @endif


        <div class="action-btn" onclick="window.location.href = '{{ route($resource.'.edit', ($parent ?? false) ? [$parent, $item] : $item) }}';" title="Редактировать">
            <span class="material-symbols-outlined" >edit_note</span>
        </div>
        {{--                        <a href="{{ route($resource.'.edit', $item) }}">Edit</a>--}}
        <form class="hidden" id="delete-form-{{ $item->id }}" action="{{ route($resource.'.destroy', ($parent ?? false) ? [$parent, $item] : $item) }}" method="POST">
            @csrf @method('DELETE')
            <button type="submit">Delete</button>
        </form>
        <div class="action-btn danger" onclick="document.getElementById('delete-form-{{ $item->id }}').submit();" title="Удалить">
            <span class="material-symbols-outlined" >cancel</span>
        </div>

    </div>
@endif
