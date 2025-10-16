<div x-data="{
    preview: null,
    initialAvatar: '{{ $initialAvatar }}',
    targetSelector: '{{ $targetSelector }}',
    maxSize: {{ $maxSize ?? 2048 }}, // in KB
    aspectRatio: '{{ $aspectRatio ?? '0:0' }}', // format 'width:height'
    cropper: null,
    needsCropping: false,
    croppedBlob: null,
    popup: null,

    fileChanged(event) {
        const file = event.target.files[0];
        if (!file) return;

        // Check file size
{{--        if (file.size > this.maxSize * 1024) {--}}
{{--            alert(`File size must be less than ${this.maxSize}KB`);--}}
{{--            this.resetAvatar();--}}
{{--            return;--}}
{{--        }--}}

        // Check aspect ratio if specified
{{--        if (this.aspectRatio !== '0:0' ) {--}}
            const img = new Image();
            img.onload = () => {
                const [targetWidth, targetHeight] = this.aspectRatio.split(':').map(Number);
                const actualRatio = img.width / img.height;
                const targetRatio = targetWidth / targetHeight;

                if ((Math.abs(actualRatio - targetRatio) > 0.1) || (file.size > this.maxSize * 1024)) {
                    // Needs cropping - initialize cropper
                    this.needsCropping = true;
                    this.initCropper(file, targetRatio);
                } else {
                    // No cropping needed
                    this.needsCropping = false;
                    this.setPreview(file);
                }
            };
            img.src = URL.createObjectURL(file);
{{--        } else {--}}
{{--            // No aspect ratio requirement--}}
{{--            this.needsCropping = false;--}}
{{--            this.setPreview(file);--}}
{{--        }--}}
    },

    initCropper(file, targetRatio) {
        const reader = new FileReader();
        reader.onload = (e) => {
            // Use existing popup template
            const template = document.getElementById('popup-template');
            this.popup = template.content.cloneNode(true).querySelector('.popup');
            document.body.appendChild(this.popup);

            // Set popup content
            const popupBody = this.popup.querySelector('.popup-body');
            popupBody.innerHTML = `
                <h3 class='text-lg font-bold mb-4'>Crop Image</h3>
                <div class='cropper-container mb-4' style='height: 400px;'>
                    <img id='temp-cropper-image' src='${e.target.result}' style='max-width: 100%;'>
                </div>
                <div class='flex gap-1'>
                    <button type='button' id='cancel-crop' class='btn'>Cancel</button>
                    <button type='button' id='apply-crop' class='btn'>Apply Crop</button>
                </div>
            `;

            // Show popup
            this.popup.classList.add('active');

            // Initialize cropper
            if (this.cropper) {
                this.cropper.destroy();
            }

            const tempImg = this.popup.querySelector('#temp-cropper-image');
            this.cropper = new Cropper(tempImg, {
                aspectRatio: targetRatio,
                viewMode: 2,
                guides: true,
                autoCropArea: 1,
                responsive: true
            });

            // Add event listeners
            this.popup.querySelector('#apply-crop').addEventListener('click', () => this.applyCrop());
            this.popup.querySelector('#cancel-crop').addEventListener('click', () => this.cancelCrop());
            this.popup.querySelector('.popup-close').addEventListener('click', () => this.cancelCrop());
            this.popup.querySelector('.popup-backdrop').addEventListener('click', () => this.cancelCrop());
        };
        reader.readAsDataURL(file);
    },

    applyCrop() {
        if (!this.cropper) return;

        // Get cropped canvas
        const canvas = this.cropper.getCroppedCanvas();
        const maxSizeBytes = this.maxSize * 1024;

        // Convert to blob
        const optimizeImage = (quality = 0.9) => {
            canvas.toBlob((blob) => {
                if (blob.size <= maxSizeBytes || quality <= 0.1) {
                    // Create a new file from the blob
                    const croppedFile = new File([blob], 'cropped-image.jpg', {
                        type: 'image/jpeg',
                        lastModified: new Date().getTime()
                    });

                    // Store for form submission
                    this.croppedBlob = croppedFile;

                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(croppedFile);
                    this.$refs.fileInput.files = dataTransfer.files;

                    // Set preview
                    this.setPreview(croppedFile);

                    // Clean up
                    this.cleanupCropper();
                } else {
                    // Reduce quality and try again
                    optimizeImage(quality - 0.1);
                }
            }, 'image/jpeg', quality);

        };

        optimizeImage(0.9);
    },

    cancelCrop() {
        this.cleanupCropper();
        this.resetAvatar();
    },

    cleanupCropper() {
        // Hide and remove popup
        if (this.popup) {
            this.popup.classList.remove('active');
            setTimeout(() => {
                if (this.popup && this.popup.parentNode) {
                    this.popup.parentNode.removeChild(this.popup);
                }
                this.popup = null;
            }, 300);
        }

        // Clean up cropper
        if (this.cropper) {
            this.cropper.destroy();
            this.cropper = null;
        }
    },

    setPreview(file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            this.preview = e.target.result;
            document.querySelector(this.targetSelector).src = e.target.result;
        };
        reader.readAsDataURL(file);
    },

    resetAvatar() {
        this.preview = null;
        this.needsCropping = false;
        this.croppedBlob = null;
        document.querySelector(this.targetSelector).src = this.initialAvatar;
        this.$refs.fileInput.value = '';
        this.cleanupCropper();
    },

    // Handle form submission
    getFileForSubmission() {
        if (this.croppedBlob) {
            return this.croppedBlob;
        } else if (this.$refs.fileInput.files.length > 0) {
            return this.$refs.fileInput.files[0];
        }
        return null;
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
            <span class="btn" @click="$refs.fileInput.click()">
                Select Image
            </span>

            <span class="btn" x-show="preview" @click="resetAvatar">
                Reset
            </span>
        </div>
    </div>
</div>

<style>
    .cropper-container {
        max-width: 100%;
    }
    .cropper-modal {
        opacity: 0.5;
    }
</style>
