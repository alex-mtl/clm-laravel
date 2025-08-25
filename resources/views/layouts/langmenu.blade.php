
<div class="lang-switcher">
    <a href="javascript:void(0)" class="lang-trigger" aria-expanded="false">
        <img src="/img/globe.svg" alt="Language selector">
    </a>
    <span class="lang-current">{{  strtoupper($app->getLocale()) }}</span>

    <div class="lang-dropdown" hidden>
        <a href="/set-locale/en" class="btn">English (EN)</a>
        <a href="/set-locale/ru" class="btn">Русский (RU)</a>
    </div>
</div>

<script>
    class LangSwitcher {
        constructor(container) {
            this.container = container;
            this.trigger = container.querySelector('.lang-trigger');
            this.dropdown = container.querySelector('.lang-dropdown');

            this.trigger.addEventListener('click', (e) => {
                e.stopPropagation();
                this.toggleDropdown();
            });

            // Close when clicking outside
            document.addEventListener('click', () => {
                this.closeDropdown();
            });
        }

        toggleDropdown() {
            const isExpanded = this.trigger.getAttribute('aria-expanded') === 'true';
            this.trigger.setAttribute('aria-expanded', !isExpanded);
            this.dropdown.hidden = isExpanded;
        }

        closeDropdown() {
            this.trigger.setAttribute('aria-expanded', 'false');
            this.dropdown.hidden = true;
        }
    }

    // Initialize all language switchers
    document.querySelectorAll('.lang-switcher').forEach(el => {
        new LangSwitcher(el);
    });
</script>
