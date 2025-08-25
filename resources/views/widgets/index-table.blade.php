<div class="content-table">
    <div class="table-head flex-row gap-2 border-bottom">
        @foreach ($cols as $idx => $col)
            <div class="col-{{ $idx }} {{ $col->class ?? 'w-10' }}">{{ $col->name ?? '' }}</div>
        @endforeach
    </div>

    @foreach ($collection as $item)
        <div class=" flex-row gap-2 border-bottom ">
            @foreach ($cols as $idx => $col)
                @if ($col->prop === 'actions')
                    @include('widgets.action-buttons')
                @elseif(($col->prop === 'btn') && $col->ajax)
                    @include('widgets.btn')
                @else
                    @php
                        $value = data_get($item, $col->prop);
                        $display = is_iterable($value)
                            ? collect($value)->pluck('name')->implode(', ')
                            : $value;
                    @endphp
                    <div  class="{{ $col->class ?? 'w-10' }}"> {{ $display }}</div>
                @endif
            @endforeach
        </div>
    @endforeach
    @if(method_exists($collection, 'links'))
        {{ $collection->links() }}
    @endif
</div>

