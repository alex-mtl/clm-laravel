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

        initComponent() {

            // Container mode - find the target container
            this.popup = document.getElementById(this.container);
            if (!this.popup) {
                console.error('Container not found:', this.container);
                return;
            }

            this.popup.addEventListener('submit', (e) => {
                this.handleSubmit(e);
            });
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
                this.popup.innerHTML = this.formHtml;
                closeBtn = this.popup.parentElement.querySelector('.form-close')
                    closeBtn.classList.remove('hidden')
                    closeBtn.addEventListener('click', () => {
                        closeBtn.classList.add('hidden')
                        this.open = false;
                        this.popup.innerHTML = '';
                    });
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
            const btnid = e.target.getAttribute('btnid');
            if( '{{ $btnid }}' !== btnid) {
                return;
            }
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
        initComponent();

    "
>
    <!-- Hidden input to store endpoint -->
    <input type="hidden" x-ref="endpoint" value="{{ $endpoint }}">

    <!-- Trigger button (optional) -->
    <span class="action-btn {{ $class }}" title="{{ $title }}" @click="fetchForm" :disabled="loading">{{ $icon }}


    </span>
</div>
