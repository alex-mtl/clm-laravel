<div id="{{ $btnid }}" class="input-block {{ $invisible  }}"
     x-data="{
         name: '{{ $name }}',
         options: @js($options),
         selected: '{{ $selected ?? '' }}',
         readonly: {{ $readonly ? 'true' : 'false' }},
         invisible: {{ $invisible ? 'true' : 'false' }},
         class: '{{ $class ?? '' }}',
         loadUrl: null,
         open: false,
         callback: {{ $callback ?? 'null'}},

         // Initialize component
         init() {
            this.invisible = {{ $invisible ? 'true' : 'false' }}
             // Set default selection if empty
             if (!this.selected && Object.keys(this.options).length > 0) {
                 this.selected = Object.keys(this.options)[0];
             }
         },

         // Get display label for an option
         getOptionLabel(option) {
             return typeof option === 'object' ? option.label : option;
         },

         // Get link for an option (if applicable)
         getOptionLink(option) {
             return typeof option === 'object' ? option.link : '';
         },

         // Computed property for selected label
         get selectedLabel() {
             if (!this.selected)  return 'Select...';

             const option = this.options[this.selected];

             return this.getOptionLabel(option) ?? 'Select...';
         },

         // Toggle dropdown visibility
         toggle() {
             if (this.readonly) return;
             this.open = !this.open;
         },

         // Handle option selection
         select(value, link = '') {
             this.selected = value;
             this.open = false;
             if (this.callback) {
                 this.callback(value);
             }

             if (this.loadUrl) {
                 this.fetchData(value);
             } else if (link) {
                 window.location.href = link;
             }
         },

         // AJAX data loading
         fetchData(selectedValue) {
             fetch(`${this.loadUrl}?selected=${selectedValue}`, {
                 headers: {
                     'Accept': 'application/json',
                     'X-Requested-With': 'XMLHttpRequest'
                 }
             })
             .then(response => response.json())
             .then(data => {
                 console.log('Server response:', data);
             });
         }
     }"
{{--     style="position: relative;"--}}
>
    <!-- Hidden select for form submission -->
    <select
        x-model="selected"

        name="{{ $name }}" style="display: none;" {{ $attributes }}>
        @foreach($options as $value => $olabel)
            <option value="{{ $value }}">{{ is_array($olabel) ? $olabel['label'] : $olabel }}</option>
        @endforeach
    </select>

    @if(!empty($label))
        <label for="visible-{{ $name }}" class="input-label">
            {{ $label }}
        </label>
    @endif

    @if(!$invisible || $invisible==='false')
        <div @click="toggle()" class="dropdwown-menu pretty-input {{ $class ?? '' }}">
            <span class="styled-input" x-text="selectedLabel" ></span>
            @if(!$readonly)
            <span class="dropdwown-menu-arrow"
                  x-text="open ? '▲' : '▼'"
                  :style="{ transform: open ? 'rotate(180deg)' : '' }">
            </span>
            @endif
        </div>
    @endif

    @if(!$readonly)
        <div x-show="open" @click.outside="open = false" class="dropdwown-select-options">
            <template x-for="(option, value) in options" :key="value">
                <div
                    class = "select-option"
                    @click="select(value, getOptionLink(option))"
                     :style="{
                         'background-color': selected === value ? '#e5e7eb' : '',
                         'font-weight': selected === value ? 'bold' : 'normal'
                     }"
                     x-text="getOptionLabel(option)">
                </div>
            </template>
        </div>
    @endif
</div>
