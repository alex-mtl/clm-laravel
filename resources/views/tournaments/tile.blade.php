<div class="tile">
    <div class="title-wrapper">
        <div class="component-4">
            <div class="div">Регистрация</div>
        </div>
    </div>
    <div class="details-wrapper">
        <div class="details">
            <div class="tournament-name">{{ $tournament->name }}</div>
            <div class="tournament-description">{{ $tournament->description ?? '' }}</div>
            <div class="frame-container">
                <div class="group">
                    <div class="detail-label">Клуб:</div>
                    <div class="detail-value">{{ $tournament->club->name }}</div>
                </div>
                <div class="group">
                    <div class="detail-label">Участники (Квота / Заявки / Одобрено):</div>
                    <div class="detail-value">{{ $tournament->players_quota ?? 0 }} / {{ $tournament->joinRequests()->where('status','pending')->count() ?? 0}} / {{ $tournament->participants->count() ?? 0 }}  игроков</div>
                </div>
                <div class="group">
                    <div class="detail-label">Период:</div>
                    <div class="detail-value">{{ $tournament->date_start_display }} - {{ $tournament->date_end_display }}</div>
                </div>
                <div class="group">
                    <div class="detail-label">Призовой фонд:</div>
                    <div class="detail-value">{{ $tournament->prize }}</div>
                </div>
            </div>
        </div>
        <div class="tournament-buttons-wrapper">
            <a href="/tournaments/{{ $tournament->id }}" class="btn view-btn">
                <div class="">Подробнее</div>
            </a>
            <a href="/tournaments/{{ $tournament->id }}/register" class="btn btn-orange register-btn">
                <div class="">Регистрация</div>
            </a>
        </div>
    </div>
</div>
