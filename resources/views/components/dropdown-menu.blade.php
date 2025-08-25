<div x-data="dropdownMenu('{{ $menuOwnerId }}')" style="position: relative;">
    <!-- Dropdown Menu -->
    <div
        x-show="open"
        @click.outside="close()"
        style="
            position: absolute;
            left:-10rem;
            background: white;
            border: 1px solid #ddd;
            min-width: 150px;
            z-index: 1000;
        "
    >
        @foreach($menuItems as $item)
            <div
                style=""
                @click="handleClick(event, {{ json_encode($item) }})"
            >
                {{ $item['label'] }}
            </div>
        @endforeach
    </div>
</div>

<script>
    function dropdownMenu(menuOwnerId) {
        return {
            open: false,
            init() {
                const trigger = document.getElementById(menuOwnerId);
                if (trigger) {
                    trigger.addEventListener('click', (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        this.toggle();
                    });
                }
            },
            toggle() {
                this.open = !this.open;
            },
            close() {
                this.open = false;
            },
            handleClick(event, item) {
                this.close();
                if (item.func) {
                    window[item.func]();
                } else if (item.link) {
                    window.location.href = item.link;
                }
            }
        }
    }
</script>
