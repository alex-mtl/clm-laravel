@extends('layouts.app')

@section('content')
    <div class="tournament-view flex-start gap-2">
        <div class="sidebar flex-column gap-1  clm-border w-20">
            <div class="flex-row gap-1 space-between">
                <div class="club-avatar-wrapper" title="{{ $tournament->name }}">
                    @if($tournament->logo)
                        <div class="club-avatar">
                            <img src="{{ asset('storage/' . $tournament->logo) }}" onerror="this.onerror=null; this.src='/img/no-tournament-logo.svg';">
                        </div>
                    @else
                        <div class="club-avatar">
                            <img src="/img/no-tournament-logo.svg">
                        </div>
                    @endif
                </div>
                <div class="club-avatar-wrapper" title="{{ $tournament->club->name }}">
                    @if($tournament->club->avatar)
                        <div class="club-avatar">
                            <a href="/clubs/{{ $tournament->club->id }}" >
                                <img src="{{ asset('storage/' . $tournament->club->avatar) }}">
                            </a>
                        </div>
                    @else
                        <div class="club-avatar">
                            <a href="/clubs/{{ $tournament->club->id }}" >
                                <img src="/img/no-club.svg">
                            </a>
                        </div>
                    @endif
                </div>

            </div>

{{--            <div>Club: <a href="/clubs/{{ $tournament->club->id }}?tab=tournaments">{{ $tournament->club->name }}</a></div>--}}
            @can('manage_tournament', $tournament)
                <div><a href=" {{ route('clubs.tournaments.edit', [$club, $tournament]) }}">{{ $tournament->name }}</a></div>
            @else
                <div>{{ $tournament->name }}</div>
            @endcan

{{--            @include('tournaments.sidebar', ['tournament' => $tournament])--}}
            @include('widgets.sidebar',
                [
                    'menu' => $sidebarMenu,
                ]
            )
        </div>

        <div class=" clm-border w-50 tournament-banner"
             @if($tournament->banner)
                 style="--clm-background-image: url('{{ asset('storage/' . $tournament->banner) }}')"
            @endif
        >
            @include('tournaments.info')

            @include('tournaments.requests')

{{--            @include('tournaments.games')--}}

            @include('tournaments.participants')

            @include('tournaments.games')

            @include('tournaments.results')

            @include('tournaments.judges')


        </div>

    </div>
@endsection

<script>
    async function calculateScores() {
        const btn = document.getElementById('calculate-scores-btn');

        if (!confirm('Вы уверены, что хотите подсчитать результаты турнира?')) {
            return;
        }

        // Показываем индикатор загрузки
        btn.disabled = true;
        const originalText = btn.textContent;
        btn.textContent = 'Подсчет...';

        try {
            const response = await fetch('{{ route("clubs.tournaments.calculate-scores", [$club, $tournament]) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (response.ok && data.success) {
                alert('✓ ' + data.message);

                // Показываем детальную информацию если есть ошибки
                if (data.data.errors > 0) {
                    console.log('Детали ошибок:', data.data.error_details);
                    alert('Внимание! Некоторые игроки не были обработаны. Проверьте логи для деталей.');
                }

                // Перезагрузка страницы для обновления данных
                window.location.reload();
            } else {
                alert('✗ Ошибка: ' + (data.message || 'Неизвестная ошибка'));
            }
        } catch (error) {
            console.error('Ошибка при подсчете очков:', error);
            alert('✗ Произошла ошибка при выполнении запроса');
        } finally {
            // Восстанавливаем кнопку
            btn.disabled = false;
            btn.textContent = originalText;
        }
    }
</script>
