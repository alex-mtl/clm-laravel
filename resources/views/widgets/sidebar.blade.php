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

    <!-- Фильтры -->
    @if(isset($filterData))
        <div class="sidebar-filters p-2 bg-light rounded" id="filtersPanel" style="display: none;">
            <h6 class="mb-2">Фильтры</h6>

            <!-- Поиск -->
            <div class="mb-3">
                <label class="form-label small">Поиск по имени</label>
                <input type="text"
                       class="form-control form-control-sm"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Введите имя..."
                       onchange="applyFilters()">
            </div>

            <!-- Страна -->
            <div class="mb-3">
                <label class="form-label small">Страна</label>
                <select class="form-select form-select-sm" name="country" onchange="applyFilters()">
                    <option value="">Все страны</option>
                    @foreach($filterData['countries'] ?? [] as $country)
                        <option value="{{ $country->id }}" {{ request('country') == $country->id ? 'selected' : '' }}>
                            {{ $country->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Город -->
            <div class="mb-3">
                <label class="form-label small">Город</label>
                <select class="form-select form-select-sm" name="city" onchange="applyFilters()">
                    <option value="">Все города</option>
                    @foreach($filterData['cities'] ?? [] as $city)
                        <option value="{{ $city->id }}" {{ request('city') == $city->id ? 'selected' : '' }}>
                            {{ $city->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Клуб -->
            <div class="mb-3">
                <label class="form-label small">Клуб</label>
                <select class="form-select form-select-sm" name="club" onchange="applyFilters()">
                    <option value="">Все клубы</option>
                    <option value="no_club" {{ request('club') == 'no_club' ? 'selected' : '' }}>Без клуба</option>
                    @foreach($filterData['clubs'] ?? [] as $club)
                        <option value="{{ $club->id }}" {{ request('club') == $club->id ? 'selected' : '' }}>
                            {{ $club->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Количество игр -->
            <div class="mb-3">
                <label class="form-label small">Количество игр</label>
                <div class="row g-1">
                    <div class="col">
                        <input type="number"
                               class="form-control form-control-sm"
                               name="min_games"
                               value="{{ request('min_games') }}"
                               placeholder="Мин"
                               onchange="applyFilters()">
                    </div>
                    <div class="col">
                        <input type="number"
                               class="form-control form-control-sm"
                               name="max_games"
                               value="{{ request('max_games') }}"
                               placeholder="Макс"
                               onchange="applyFilters()">
                    </div>
                </div>
            </div>

            <!-- Количество турниров -->
            <div class="mb-3">
                <label class="form-label small">Количество турниров</label>
                <div class="row g-1">
                    <div class="col">
                        <input type="number"
                               class="form-control form-control-sm"
                               name="min_tournaments"
                               value="{{ request('min_tournaments') }}"
                               placeholder="Мин"
                               onchange="applyFilters()">
                    </div>
                    <div class="col">
                        <input type="number"
                               class="form-control form-control-sm"
                               name="max_tournaments"
                               value="{{ request('max_tournaments') }}"
                               placeholder="Макс"
                               onchange="applyFilters()">
                    </div>
                </div>
            </div>

            <!-- Кнопки управления фильтрами -->
            <div class="d-grid gap-1">
                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="clearFilters()">
                    Сбросить все фильтры
                </button>
            </div>
        </div>
    @endif
</div>

<script>
    // Делаем функции глобальными
    window.applyFilters = function() {
        const formData = new FormData();
        const filters = document.querySelectorAll('.sidebar-filters input, .sidebar-filters select');

        let hasFilters = false;
        filters.forEach(filter => {
            if (filter.value) {
                formData.append(filter.name, filter.value);
                hasFilters = true;
            }
        });

        // Сохраняем текущую сортировку если есть
        const urlParams = new URLSearchParams(window.location.search);
        const sort = urlParams.get('sort');
        const order = urlParams.get('order');

        if (sort) formData.append('sort', sort);
        if (order) formData.append('order', order);

        // Собираем query string
        const queryString = new URLSearchParams(formData).toString();

        // Если есть фильтры или есть сортировка - применяем, иначе переходим на чистую страницу
        if (hasFilters || sort || order) {
            window.location.href = `${window.location.pathname}?${queryString}`;
        } else {
            window.location.href = window.location.pathname;
        }
    }

    window.clearFilters = function() {
        // Сохраняем только сортировку
        const urlParams = new URLSearchParams(window.location.search);
        const sort = urlParams.get('sort');
        const order = urlParams.get('order');

        if (sort || order) {
            const queryParams = new URLSearchParams();
            if (sort) queryParams.append('sort', sort);
            if (order) queryParams.append('order', order);
            window.location.href = `${window.location.pathname}?${queryParams.toString()}`;
        } else {
            window.location.href = window.location.pathname;
        }
    }

    window.filterOptions = function() {
        // Переключаем видимость фильтров
        const filters = document.getElementById('filtersPanel');
        if (filters) {
            const isVisible = filters.style.display !== 'none';
            filters.style.display = isVisible ? 'none' : 'block';

            // Обновляем активное состояние в меню
            const filterMenuItem = document.querySelector('[data-action="filters"]');
            if (filterMenuItem) {
                if (!isVisible) {
                    filterMenuItem.classList.add('active');
                } else {
                    filterMenuItem.classList.remove('active');
                }
            }
        }
    }

    // Дебаунс для избежания множественных запросов при быстром вводе
    let debounceTimer;
    window.debounceApplyFilters = function() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            applyFilters();
        }, 500); // Задержка 500ms
    }

    // Обновляем обработчики для полей ввода с дебаунсом
    document.addEventListener('DOMContentLoaded', function() {
        const textInputs = document.querySelectorAll('.sidebar-filters input[type="text"], .sidebar-filters input[type="number"]');
        textInputs.forEach(input => {
            input.addEventListener('input', debounceApplyFilters);
        });

        // Показываем фильтры при первом открытии, если есть активные фильтры
        const urlParams = new URLSearchParams(window.location.search);
        const hasActiveFilters = Array.from(urlParams.keys()).some(key =>
            key !== 'sort' && key !== 'order' && key !== 'page'
        );

        if (hasActiveFilters) {
            const filtersPanel = document.getElementById('filtersPanel');
            const filterMenuItem = document.querySelector('[data-action="filters"]');
            if (filtersPanel && filterMenuItem) {
                filtersPanel.style.display = 'block';
                filterMenuItem.classList.add('active');
            }
        }
    });

    document.querySelectorAll('.sidebar-filters input[type="text"], .sidebar-filters input[type="number"]').forEach(input => {
        input.removeAttribute('onchange');
        input.addEventListener('input', debounceApplyFilters);
    });

    document.querySelectorAll('.sidebar-filters select').forEach(select => {
        select.addEventListener('change', applyFilters);
    });
</script>
