<div x-data="{
    preview: null,
    initialAvatar: '{{ $initialAvatar }}',
    targetSelector: '{{ $targetSelector }}',
    fileChanged(event) {
        const file = event.target.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = (e) => {
            this.preview = e.target.result;
            // Update the main avatar display
            document.querySelector(this.targetSelector).src = e.target.result;
        };
        reader.readAsDataURL(file);
    },
    resetAvatar() {
        this.preview = null;
        document.querySelector(this.targetSelector).src = this.initialAvatar;
        this.$refs.fileInput.value = '';
    }
}">
    <!-- Hidden file input -->
    <input x-ref="fileInput"
           type="file"
           name="{{ $name }}"
           id="{{ $name }}"
           accept="image/*"
           class="hidden"
           @change="fileChanged">

    <!-- Custom UI -->
    <div class="flex items-center gap-4">

        <!-- Buttons -->
        <div class="flex-column gap-1 center w100">
            <span class="btn"
                    @click="$refs.fileInput.click()"
                    >
                Select Image
            </span>

            <span class="btn"
                    x-show="preview"
                    @click="resetAvatar"
                    >
                Reset
            </span>
        </div>
    </div>
</div>
