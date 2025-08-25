<div class="flex-row gap-2 space-between w100">
    <div class="flex">
        <h1>{{ $title }}</h1>
    </div>
    @if($resource > '')
        <div class="flex">
            <div class="material-symbols-outlined">
                @if($ajax ?? false)
                    <x-ajax-modal endpoint="{{ $endpoint }}" title="Добавить" />
                @else

                    <span
                        class=" action-btn "
                        @if($endpoint ?? false)
                            onclick="{{ $endpoint }}"
                        @elseif($resourceItem ?? false)
                            onclick="window.location.href = '{{ route($resource.'.create', $resourceItem) }}';"
                        @else
                            onclick="window.location.href = '{{ route($resource.'.create') }}';"
                        @endif

                        title="Добавить">{{ $icon ?? 'add_circle' }}</span>

                @endif
            </div>
        </div>
    @endif

</div>
