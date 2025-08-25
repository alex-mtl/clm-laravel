document.querySelectorAll('.sidebar-menu-item').forEach(item => {
    item.addEventListener('click', function() {
        document.querySelectorAll('.sidebar-menu-item.active').forEach(el => {
            el.classList.remove('active');
        });
        item.classList.add('active');
        sidebarMenuSelect(this.dataset.action);
    });
});

function sidebarMenuSelect(action) {
    $hide = document.querySelectorAll('div.data-wrapper:not(.hidden)')
    $hide.forEach(div => {
        div.classList.add('hidden')
    });

    document.querySelector('div#tournament-' + action+'-data').classList.remove('hidden');
}
