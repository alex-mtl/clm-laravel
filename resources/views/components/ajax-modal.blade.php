<div
    class="material-symbols-outlined {{ $hidden === 'true' ? 'hidden' : '' }}"
    id="{{$btnid}}"

    x-data="{
        open: false,
        loading: false,
        error: null,
        formHtml: '',
        popup: null,
        callback: {{ $callback ?? 'null'}},
        container: '{{ $container ?? '' }}',

        // Initialize popup once
{{--        initPopup() {--}}
{{--            const template = document.getElementById('popup-template');--}}
{{--            this.popup = template.content.cloneNode(true).querySelector('.popup');--}}
{{--            document.body.appendChild(this.popup);--}}

{{--            // Close handlers--}}
{{--            this.popup.querySelector('.popup-close').addEventListener('click', () => {--}}
{{--                this.open = false;--}}
{{--            });--}}

{{--            this.popup.querySelector('.popup-backdrop').addEventListener('click', () => {--}}
{{--                this.open = false;--}}
{{--            });--}}

{{--            this.popup.addEventListener('submit', (e) => {--}}
{{--                if (e.target.tagName === 'FORM') {--}}
{{--                    this.handleSubmit(e);--}}
{{--                }--}}
{{--            });--}}
{{--        },--}}
        initComponent() {
            if (this.container) {
                // Container mode - find the target container
                this.popup = document.getElementById(this.container);
                if (!this.popup) {
                    console.error('Container not found:', this.container);
                    return;
                }

            } else {
                // Modal mode - initialize popup
                const template = document.getElementById('popup-template');
                this.popup = template.content.cloneNode(true).querySelector('.popup');
                document.body.appendChild(this.popup);

                // Close handlers for modal only
                this.popup.querySelector('.popup-close').addEventListener('click', () => {
                    this.open = false;
                });

                this.popup.querySelector('.popup-backdrop').addEventListener('click', () => {
                    this.open = false;
                });
            }

            this.popup.addEventListener('submit', (e) => {
                if (e.target.tagName === 'FORM') {
                    this.handleSubmit(e);
                }
            });
        },

        // Toggle popup visibility when open state changes
{{--        togglePopup() {--}}
{{--            if (this.open) {--}}
{{--                this.popup.classList.add('active');--}}
{{--                this.popup.querySelector('.popup-body').innerHTML = this.formHtml;--}}
{{--            } else {--}}
{{--                this.popup.classList.remove('active');--}}
{{--            }--}}
{{--        },--}}

        togglePopup() {
            if (this.open) {
                if (this.container) {
                    // Container mode - insert form
                    this.popup.innerHTML = this.formHtml;
                    closeBtn = this.popup.parentElement.querySelector('.form-close')
                    console.log(closeBtn)
                    closeBtn.classList.remove('hidden')
                    console.log(closeBtn)
                    closeBtn.addEventListener('click', () => {
                        this.open = false;
                    });
                } else {
                    // Modal mode - show popup
                    this.popup.classList.add('active');
                    this.popup.querySelector('.popup-body').innerHTML = this.formHtml;
                }
            } else {
                if (this.container) {
                    // Container mode - clear container
                    this.popup.innerHTML = '';
                    closeBtn = this.popup.parentElement.querySelector('.form-close')
                    closeBtn.classList.add('hidden')
                } else {
                    // Modal mode - hide popup
                    this.popup.classList.remove('active');
                }
            }
        },

        // Original fetch logic (unchanged)
        fetchForm() {
            this.loading = true;
            this.error = null;
            fetch(this.$refs.endpoint.value, {
                headers: {
                    'X-Ajax-Request': 'true'
                }
            })
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.text();
            })
            .then(html => {
                this.formHtml = html;
                this.open = true;
            })
            .catch(err => {
                this.error = 'Failed to load form: ' + err.message;
                console.error(err);
            })
            .finally(() => this.loading = false);
        },

        // Original submit handler (unchanged)
        handleSubmit(e) {
            e.preventDefault();
            const form = e.target;
            const formData = new FormData(form);

            fetch(form.action, {
                method: form.method,
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            })
            .then(response => {
                if (this.callback) {
                    return response.json().then(data => {
                        console.log('Calling callback with data:', data);
{{--                        window[this.callback](data);--}}
                        this.callback(data);
                        this.open = false;
                    });
                }
                if (response.redirected) {
                    window.location.href = response.url;
                } else if (response.ok) {
                    return response.json();
                }
                throw new Error('Submission failed');
            })
            .then(data => {
                if (!this.callback) {
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    } else if (data.reload) {
                        window.location.reload();
                    } else {
                        this.open = false;
                        window.dispatchEvent(new CustomEvent('ajax-form-success', { detail: data }));
                    }
                }
            })
            .catch(err => {
                this.error = 'Submission error: ' + err.message;
                console.error(err);
            });
        }
    }"
    x-init="
        this.callback = '{{ $callback }}' || null;
        initComponent();

        window.addEventListener('open-ajax-modal', (e) => {
            if (e.detail.endpoint === $refs.endpoint.value) {
                this.fetchForm();
            }
        });
    "
    x-effect="togglePopup()"
>
    <!-- Hidden input to store endpoint -->
    <input type="hidden" x-ref="endpoint" value="{{ $endpoint }}">

    <!-- Trigger button (optional) -->
    <span class="action-btn {{ $class }}" title="{{ $title }}" @click="fetchForm" :disabled="loading">{{ $icon }}
{{-- IMPLEMENT LOADING SPINNER--}}
{{--            <template x-if="loading">--}}
{{--                <span>Loading...</span>--}}
{{--            </template>--}}
{{--            <template x-if="!loading">--}}
{{--                {{ $trigger ?? $title }}--}}
{{--            </template>--}}

    </span>
</div>
