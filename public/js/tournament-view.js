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

function deleteCouple(el) {
    const form = el.closest('form');
    rows = form.querySelectorAll('.flex-row');
    if (rows.length > 1) {
        el.closest('.flex-row').remove();
    } else {
        el.closest('.flex-row').querySelectorAll('input').forEach(input => input.value = '');
        showToast('All player pairs has been cleared!')
    }
}
// function addCouple() {
//     const form = document.querySelector('form#couples-form');
//     last = form.querySelector('.flex-row:last-of-type');
//     const deepClone = last.content.cloneNode(true);
//     form.add(deepClone);
//
// }

function addCouple() {
    const form = document.querySelector('form#couples-form');

    // Get all couple rows (excluding the button row)
    const coupleRows = form.querySelectorAll('.flex-row.gap-05');
    const lastCouple = coupleRows[coupleRows.length - 1];

    // Clone the couple row
    const newCouple = lastCouple.cloneNode(true);

    // Clear the values in the new clone
    const hiddenInputs = newCouple.querySelectorAll('input[type="hidden"]');
    const textInputs = newCouple.querySelectorAll('input[type="text"]');

    hiddenInputs.forEach(input => input.value = '');
    textInputs.forEach(input => input.value = '');

    // Update the index in the name attributes
    const newIndex = coupleRows.length + 1;
    hiddenInputs.forEach(input => {
        input.name = input.name.replace(/couples\[\d+\]/, `couples[${newIndex}]`);
    });

    // Insert before the button row (last .flex-row)
    const buttonRow = form.querySelector('button.hidden[type="submit"]');
    form.insertBefore(newCouple, buttonRow);

    // el.closest('.flex-row').querySelectorAll('input').forEach(input => input.value = '');

    // Reinitialize Alpine.js on the new elements
    Alpine.initTree(newCouple);

    newCouple.closest('.flex-row').querySelectorAll('input').forEach(input => input.value = '');
}
