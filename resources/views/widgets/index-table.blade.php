<div class="content-table">
    <div class="table-head flex-row gap-2 border-bottom">
        @foreach ($cols as $idx => $col)
            <div class="col-{{ $idx }} {{ $col->class ?? 'w-10' }}">{{ $col->name ?? '' }}</div>
        @endforeach
    </div>

    @foreach ($collection as $item)
        <div class=" flex-row gap-2 border-bottom index-line">
            @foreach ($cols as $idx => $col)
                @if ($col->prop === 'actions')
                    @include('widgets.action-buttons')
                @elseif(is_array($col->prop)  && ($col->multiple ?? false))
                    @php
                        $text = '';
                        foreach ($col->prop as $propName) {
                            $text .= data_get($item, $propName) . "\n";
                        }
                        $text = rtrim($text);
                    @endphp
                    <div  class="{{ $col->class ?? 'w-10' }}"> {!! nl2br(e($text)) !!}</div>
                @elseif(($col->prop === 'btn') && $col->ajax)
                    @php
                        $btn = $col;
//                        $btn->item = $item;
                        $btn->endpoint_params = [$item->id];
                    @endphp
                    @include('widgets.btn', ['btn' => $btn])
                @else
                    @php
                        $value = data_get($item, $col->prop);
                        $display = is_iterable($value)
                            ? collect($value)->pluck('name')->implode(', ')
                            : $value;
                    @endphp
                    @if($col?->link ?? false)
                        <div  class="{{ $col->class ?? 'w-10' }}"><a href="{{ sprintf($col->link, $item->id ) }}" >{{ $display }}</a></div>
                    @else
                        <div  class="{{ $col->class ?? 'w-10' }}"> {{ $display }}</div>
                    @endif

                @endif
            @endforeach
        </div>
    @endforeach
    @if(method_exists($collection, 'links'))
        {{ $collection->links() }}
    @endif
</div>

