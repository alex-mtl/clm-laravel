document.querySelectorAll('.menu-item').forEach(item => {
    item.addEventListener('click', function() {
        document.querySelectorAll('.menu-item.active').forEach(el => {
            el.classList.remove('active');
        });
        item.classList.add('active');
        clubSelect(this.dataset.action);
    });
});

function clubSelect(action) {
    $hide = document.querySelectorAll('div.data-wrapper:not(.hidden)')
    $hide.forEach(div => {
        div.classList.add('hidden')
    });

    document.querySelector('div#club-' + action+'-data').classList.remove('hidden');
}
