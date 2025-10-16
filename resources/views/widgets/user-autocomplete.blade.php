<div class="input-block"
     x-data="{
         query: '{{ $selected['name'] ?? '' }}',
         selected: {{ $selected ? json_encode($selected) : 'null' }},
         results: [],
         open: false,
         loading: false,

         // Search users
         search() {
            const searchQuery = this.query || '';
             if (searchQuery.length < 2) {
                 this.results = [];
                 return;
             }

             this.loading = true;
             fetch('{{ $searchUrl }}?query=' + encodeURIComponent(this.query))
                 .then(response => response.json())
                 .then(data => {
                     this.results = data;
                     this.open = true;
                     this.loading = false;
                 });
         },

         // Select a user
         select(user) {
             this.selected = user;
             this.query = user.name;
             this.open = false;
         },

         // Clear selection
         clear() {
             this.selected = null;
             this.query = '';
             this.results = [];
         }
     }"
>
    <!-- Hidden input for form submission -->
{{--    <input type="hidden" name="{{ $name }}" x-model="selected ? selected.id : ''">--}}
    <input type="hidden" name="{{ $name }}" :value="selected ? selected.id : ''">

    <!-- Visible label -->

    @if(!empty($label))
        <label class="input-label">{{ $label }}</label>
    @endif

    <!-- Search input -->
    <div class="pretty-input">
        <input
            type="text"
            x-model="query"
            @input.debounce.300ms="search"
            @focus="(query || '').length >= 2 && (open = true)"
            @keydown.escape="open = false"
            placeholder="{{ $placeholder ?? 'Search users...' }}"
            class="styled-input"
            x-init="if (selected) query = selected.name || ''"
        >
        <template x-if="loading">
            <span class="loading-indicator">...</span>
        </template>
        <template x-if="selected">
            <span @click="clear"  class="clear-button">×</span>
        </template>
    </div>

    <!-- Results dropdown -->
    <div
        x-show="open && results.length > 0"
        @click.outside="open = false"
        class="dropdwown-select-options"
    >
        <template x-for="user in results" :key="user.id">
            <div
                @click="select(user)"
                class="select-option"
                x-text="user.name"
            ></div>
        </template>
    </div>
</div>
