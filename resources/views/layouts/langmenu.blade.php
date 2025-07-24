
<div class="lang-switcher">
    <button class="lang-trigger" aria-expanded="false">
        <img src="/img/globe.svg" alt="Language selector">
    </button>
    <span class="lang-current">{{  strtoupper($app->getLocale()) }}</span>

    <div class="lang-dropdown" hidden>
        <a href="/set-locale/en" class="btn">English (EN)</a>
        <a href="/set-locale/ru" class="btn">Русский (RU)</a>
    </div>
</div>
<style>
    .lang-current {
        position: absolute;
        bottom: 0.5rem;
        right: 0.25rem;
        padding: 0;
        font-size: 1rem;
        line-height: 1rem;
    }
    .lang-switcher {
        position: relative;
        display: inline-block;
    }

    .lang-trigger {
        background: none;
        border: none;
        cursor: pointer;
        padding: 0.5rem;
    }

    .lang-trigger img {
        /*width: 20px;*/
        /*height: 20px;*/
    }

    .lang-dropdown {
        position: absolute;
        right: 0;
        background: white;
        border: 1px solid #ddd;
        border-radius: 4px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        min-width: 120px;
        z-index: 100;
    }

    .lang-dropdown a {
        display: block;
        padding: 0.5rem 1rem;
        margin: 2px;
        /*color: #333;*/
        /*text-decoration: none;*/
    }

    .lang-dropdown a:hover {
        /*background-color: #f5f5f5;*/
    }
</style>
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