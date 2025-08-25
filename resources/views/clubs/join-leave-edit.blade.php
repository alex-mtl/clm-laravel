<div class="main-action material-symbols-outlined">
    @if (auth()->id() !== $club->owner_id)
        @auth
            @if($club->members()->where('user_id', auth()->id())->exists())
                {{--                        <form method="POST" id="leave-request" class="hidden" action="{{ route('clubs.leave', $club) }}">--}}
                {{--                            @csrf--}}
                {{--                            <button type="submit" onclick="return confirm('Вы уверены что хотите покинуть клуб?')"></button>--}}
                {{--                        </form>--}}
                {{--                        <div--}}
                {{--                            x-data--}}
                {{--                            @click="document.getElementById('leave-request').submit()"--}}
                {{--                            class="action-btn dark red material-symbols-outlined"--}}
                {{--                            title="Выйти из клуба"--}}
                {{--                        >--}}
                {{--                            <span>person_cancel</span>--}}
                {{--                        </div>--}}

{{--                <div x-data="{ showConfirm: false }"--}}
{{--                     class="action-btn dark red material-symbols-outlined"--}}
{{--                     title="Выйти из клуба"--}}
{{--                >--}}
{{--                    <span @click="showConfirm = true">person_cancel</span>--}}
{{--                    --}}{{--                            <button @click="showConfirm = true" type="button">Delete</button>--}}

{{--                    <div x-show="showConfirm" class="confirmation-dialog" style="z-index: 1000;">--}}
{{--                        <p>Are you sure?</p>--}}
{{--                        <button @click="document.getElementById('leave-request').submit()">Yes</button>--}}
{{--                        <button @click="showConfirm = false">Cancel</button>--}}
{{--                    </div>--}}
{{--                </div>--}}
                <div

                    class="action-btn info success material-symbols-outlined"
                    title="Вы являетесь участником клуба"
                >
                    <span>person_check</span>
                </div>
            @else
                @if($club->joinRequests()->where('user_id', auth()->id())->pending()->exists())
                    <div class="action-btn dark request-status pending material-symbols-outlined"
                         x-data
                         @click=""
                         title="Ваша заявка находится на рассмотрении!"
                    >
                        <span>manage_accounts</span>
                    </div>
                @else
                    <form method="POST" id="join-request" class="hidden"
                          action="{{ route('clubs.join.request', $club) }}">
                        @csrf
                        <button type="submit"></button>
                    </form>
                    <div
                        x-data
                        @click="document.getElementById('join-request').submit()"
                        class="action-btn dark success material-symbols-outlined"
                        title="Присоединиться"
                    >
                        <span>person_add</span>
                    </div>
                @endif
            @endif
        @endauth
        {{--                <div class="btn-large" title="Покинуть">--}}
        {{--                    <b class="b">Покинуть</b>--}}
        {{--                </div>--}}
    @else
        {{--                <div class="btn-large">--}}
        {{--                    <a href="{{ route('clubs.edit', $club) }}"><b class="b">Редактировать</b></a>--}}
        {{--                </div>--}}
        <div
            x-data
            @click="window.location.href = '{{ route('clubs.edit', $club) }}'"
            class="action-btn dark success material-symbols-outlined"
            title="Редактировать"
        >
            <span>edit_note</span>
        </div>
    @endif
</div>
