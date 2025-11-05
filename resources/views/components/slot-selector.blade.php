<div x-data="{
    selectedSlot: {{ $selectedSlot ?? 0 }},
    showSelector: true,
    slotAvailability: {{ json_encode($slotAvailability ?? []) }},
    slotRoles: {{ json_encode($slotRoles ?? []) }},
    callback: {{ $callback ?? 'null'}},
    slots: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10], // Add this line

    get displayValue() {
        return this.selectedSlot > 0 ? this.selectedSlot : 'X';
    },

    isSlotAvailable(slot) {
        if (slot === 0) return true; // 'X' всегда доступно
{{--        console.log(this.slotAvailability, slot,this.slotAvailability[slot]);--}}
        return this.slotAvailability[slot] ?? true;
    },

    selectSlot(slot) {
        if (!this.isSlotAvailable(slot)) return;
        this.selectedSlot = slot;
        console.log(slot);
        if (this.callback)
            this.callback(`{{ $name ?? 'slot' }}`, slot);
{{--        this.showSelector = false;--}}
    },

    toggleSelector() {
        this.showSelector = !this.showSelector;
    }
}">
    <!-- Hidden input for form submission -->
    <input type="hidden" name="{{ $name ?? 'slot' }}"
           x-model="selectedSlot"
           x-on:change="{{ $callback ? $callback . '(this.name, this.value)' : '' }}">

    <!-- Display element -->
{{--    <div class="slot-selector-display"--}}
{{--         @click="toggleSelector()"--}}
{{--         :class="{--}}
{{--             'selected': selectedSlot > 0,--}}
{{--             'has-options': Object.values(slotAvailability).some(available => available)--}}
{{--         }">--}}
{{--        <span x-text="displayValue"></span>--}}
{{--    </div>--}}

    <!-- Selector panel -->
    @if($label !== 'null' && $label !== '')
        <label class="input-label" >
            {{ $label }}
        </label>
    @endif
    <div x-show="showSelector"
{{--         @click.outside="showSelector = false"--}}
         class="slot-selector-panel ">

        <div class="flex-row ml-auto mr-auto">
        <!-- 'X' option (value 0) -->
{{--        <div class="slot-option clear-option"--}}
{{--             @click="selectSlot(0)"--}}
{{--             :class="{ 'active': selectedSlot === 0 }"--}}
{{--             title="Никто">--}}
{{--            <span>0</span>--}}
{{--        </div>--}}

        <!-- Number slots -->
{{--        <template x-for="slot in [ 1, 2, 3, 4, 5, 6, 7, 8, 9, 10]" :key="slot">--}}
        <template x-for="slot in slots" :key="slot">
            <div class="slot-option"
                 @click="selectSlot(slot)"
                 :data-slot="slot"
                 :class="{
                     'active': selectedSlot === slot,
                     'available': isSlotAvailable(slot),
                     'unavailable': !isSlotAvailable(slot),
                     [slotRoles[slot] ?? 'citizen']: true
                 }"
                 :title="!isSlotAvailable(slot) ? 'Недоступно' : ''">
                <span x-text="slot"></span>
            </div>
        </template>
        </div>
    </div>
</div>

