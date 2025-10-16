<div x-data="{
    selectedSlot: {{ $selectedSlot ?? 0 }},
    showSelector: false,
    slotAvailability: @json($slotAvailability ?? []),

    get displayValue() {
        return this.selectedSlot > 0 ? this.selectedSlot : 'X';
    },

    isSlotAvailable(slot) {
        if (slot === 0) return true; // 'X' всегда доступно
        return this.slotAvailability[slot] ?? true;
    },

    selectSlot(slot) {
        if (!this.isSlotAvailable(slot)) return;
        this.selectedSlot = slot;
        this.showSelector = false;
    },

    toggleSelector() {
        this.showSelector = !this.showSelector;
    }
}">
    <!-- Hidden input for form submission -->
    <input type="hidden" name="{{ $name ?? 'slot' }}" x-model="selectedSlot">

    <!-- Display element -->
    <div class="slot-selector-display"
         @click="toggleSelector()"
         :class="{
             'selected': selectedSlot > 0,
             'has-options': Object.values(slotAvailability).some(available => available)
         }">
        <span x-text="displayValue"></span>
    </div>

    <!-- Selector panel -->
    <div x-show="showSelector"
         @click.outside="showSelector = false"
         class="slot-selector-panel">
        <!-- 'X' option (value 0) -->
        <div class="slot-option clear-option"
             @click="selectSlot(0)"
             :class="{ 'active': selectedSlot === 0 }"
             title="Очистить выбор">
            <span>X</span>
        </div>

        <!-- Number slots -->
        <template x-for="slot in [ 1, 2, 3, 4, 5, 6, 7, 8, 9, 10]" :key="slot">
            <div class="slot-option"
                 @click="selectSlot(slot)"
                 :class="{
                     'active': selectedSlot === slot,
                     'available': isSlotAvailable(slot),
                     'unavailable': !isSlotAvailable(slot)
                 }"
                 :title="!isSlotAvailable(slot) ? 'Недоступно' : ''">
                <span x-text="slot"></span>
            </div>
        </template>
    </div>
</div>

<style>
    .slot-selector-display {
        width: 50px;
        height: 50px;
        border: 2px solid #ccc;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        font-weight: bold;
        cursor: pointer;
        transition: all 0.2s;
    }

    .slot-selector-display:hover {
        border-color: #666;
    }

    .slot-selector-display.selected {
        border-color: #3b82f6;
        background-color: #eff6ff;
        color: #3b82f6;
    }

    .slot-selector-display.has-options:hover {
        border-color: #3b82f6;
    }

    .slot-selector-panel {
        position: absolute;
        background: white;
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 10px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        z-index: 1000;
        display: flex;
        gap: 5px;
        margin-top: 5px;
    }

    .slot-option {
        width: 40px;
        height: 40px;
        border: 2px solid #e5e7eb;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        transition: all 0.2s;
    }

    .slot-option.available {
        cursor: pointer;
        border-color: #e5e7eb;
    }

    .slot-option.available:hover {
        border-color: #9ca3af;
        background-color: #f9fafb;
    }

    .slot-option.active {
        border-color: #3b82f6;
        background-color: #3b82f6;
        color: white;
    }

    .slot-option.unavailable {
        cursor: not-allowed;
        background-color: #f3f4f6;
        color: #9ca3af;
        border-color: #d1d5db;
        opacity: 0.6;
    }

    .clear-option {
        border-color: #ef4444 !important;
        color: #ef4444;
    }

    .clear-option:hover {
        background-color: #fef2f2 !important;
        border-color: #dc2626 !important;
    }

    .clear-option.active {
        background-color: #ef4444 !important;
        color: white !important;
    }
</style>
