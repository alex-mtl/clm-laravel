<div class="input-block" x-data="{
    value: '{{ $value }}',
    showPicker: false,
    currentMonth: new Date().getMonth(),
    currentYear: new Date().getFullYear(),
    today: new Date(),

    get daysInMonth() {
        return new Date(this.currentYear, this.currentMonth + 1, 0).getDate();
    },

    get firstDayOfMonth() {
        return new Date(this.currentYear, this.currentMonth, 1).getDay();
    },

    get monthName() {
        return new Date(this.currentYear, this.currentMonth).toLocaleString('default', { month: 'long' });
    },

    selectDate(day) {
        const selectedDate = new Date(this.currentYear, this.currentMonth, day);
        this.value = selectedDate.toISOString().split('T')[0];
        this.showPicker = false;
    },

    nextMonth() {
        if (this.currentMonth === 11) {
            this.currentMonth = 0;
            this.currentYear++;
        } else {
            this.currentMonth++;
        }
    },

    prevMonth() {
        if (this.currentMonth === 0) {
            this.currentMonth = 11;
            this.currentYear--;
        } else {
            this.currentMonth--;
        }
    },

    isToday(day) {
        const date = new Date(this.currentYear, this.currentMonth, day);
        return date.toDateString() === this.today.toDateString();
    },

    isSelected(day) {
        if (!this.value) return false;
        const selectedDate = new Date(this.value);
        return selectedDate.getDate() === day &&
               selectedDate.getMonth() === this.currentMonth &&
               selectedDate.getFullYear() === this.currentYear;
    }
}">
    <!-- Hidden form input -->
    <input type="hidden" name="{{ $name }}" x-model="value">

    <!-- Visible label -->
    <label for="visible-{{ $name }}" class="input-label">
        {{ $label }}
    </label>

    <!-- Styled visible input -->
    <div class="pretty-input relative">
        <input
            id="visible-{{ $name }}"
            type="date"
            x-model="value"
            @if(!$readonly)
                @click="showPicker = !showPicker"
                @focus="showPicker = true"
                @blur="setTimeout(() => showPicker = false, 200)"
            @endif
            @if($required) required @endif
            @if($readonly) readonly @endif
            class="styled-input"
            placeholder="{{ $placeholder }}"
        >

        <!-- Date Picker Dropdown -->
        <div

            x-show="showPicker"
            x-transition
            class="date-picker-dropdown"
            style="min-width: 280px;"
            @click.outside="showPicker = false"
        >
            <!-- Month/Year Navigation -->
            <div class="flex items-center justify-between p-2 border-b">
                <button
                    type="button"
                    @click="prevMonth"
                    class="p-1 rounded hover:bg-gray-100"
                >
                    &lt;
                </button>
                <span x-text="`${monthName} ${currentYear}`" class="font-medium"></span>
                <button
                    type="button"
                    @click="nextMonth"
                    class="p-1 rounded hover:bg-gray-100"
                >
                    &gt;
                </button>
            </div>

            <!-- Day Names -->
            <div class="calendar">
                <template x-for="day in ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa']">
                    <div x-text="day"></div>
                </template>
            </div>

            <!-- Calendar Days -->
            <div class="calendar-week">
                <!-- Empty cells for days before 1st of month -->
                <template x-for="i in firstDayOfMonth">
                    <div></div>
                </template>

                <!-- Days of month -->
                <template x-for="day in Array.from({length: daysInMonth}, (_, i) => i + 1)">
                    <button
                        type="button"
                        @click="selectDate(day)"
                        :class="{
                            'bg-blue-500 text-white': isSelected(day),
                            'font-bold': isToday(day),
                            'hover:bg-gray-100': !isSelected(day)
                        }"
                        class="w-8 h-8 rounded-full flex items-center justify-center text-sm"
                    >
                        <span x-text="day"></span>
                    </button>
                </template>
            </div>
        </div>
    </div>
</div>
