async function updateGameState(updatedGame) {
    try {
        const response = await fetch(`/games/${updatedGame.id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                props: updatedGame
            })
        });

        if (!response.ok) throw new Error('Network response was not ok');

        const data = await response.json();
        return data.props; // Return the updated props from server

    } catch (error) {
        console.error('Update failed:', error);
        // Fallback to local state if update fails
        return updatedGame;
    }
}
 function getPayload(phase) {
    if (phase === 'shuffle-slots') {
        payload = {
            phase: phase,
            // table: document.querySelector('input[name="table"]').value,
            slots: Array.from(document.querySelectorAll('[name^="slots["]'))
                .reduce((acc, input) => {
                    const matches = input.name.match(/slots\[(\d+)\]\[user_id\]/);
                    if (matches) acc[matches[1]] = {user_id: input.value};
                    return acc;
                }, {})
        };
    } else if (phase === 'shuffle-roles') {
        payload = {
            phase: phase,
            // table: document.querySelector('input[name="table"]').value,
            slots: Array.from(document.querySelectorAll('[name^="slots["]'))
                .reduce((acc, input) => {
                    const matches = input.name.match(/slots\[(\d+)\]\[role\]/);
                    if (matches) acc[matches[1]] = {role: input.value};
                    return acc;
                }, {})
        };
    } else if (phase === 'night') {
        const subPhase = document.querySelector('input[name="sub_phase"]')?.value;
        if (subPhase === 'empty') {
            payload = {
                phase: phase,
                subPhase: 'don-watch',
                timer: 10,
            };
        } else if (subPhase === 'don-watch') {
            payload = {
                phase: phase,
                subPhase: 'sheriff-watch',
                timer: 10,
            };
        } else if (subPhase === 'sheriff-watch') {
            payload = {
                phase: phase,
                subPhase: 'sheriff-watch',
                timer: 10,
            };
        }
    }
    return payload
}


window.submitGameState = async function () {
    // Collect only the values you need
    const phase = document.querySelector('input[name="phase"]').value;
    const id = document.querySelector('input[name="game_id"]').value;
    const payload = getPayload(phase);
    console.log(payload);

    try {
        const response = await fetch('/games/'+id+'/host', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
        });

        const data = await response.json();
        if (!response.ok) {
            throw new Error(data.message);
        } else
        if (data.redirect) {
            window.location.href = data.redirect;
        }
        // window.location.href = data.redirect || window.location.href;
    } catch (error) {
        console.error('Error:', error);
        // window.location.reload();
        alert('Error:', error);
    }
}

window.submitGamePhaseBack = async function () {
    // Collect only the values you need
    const phase = document.querySelector('input[name="phase"]').value;
    const id = document.querySelector('input[name="game_id"]').value;
    // const payload = getPhaseBack(phase);
    const payload = { phase: 'back' };
    // console.log(payload);

    try {
        const response = await fetch('/games/'+id+'/phase', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
        });

        const data = await response.json();
        if (!response.ok) {
            throw new Error(data.message);
        } else
        if (data.redirect) {
            window.location.href = data.redirect;
        }
        // window.location.href = data.redirect || window.location.href;
    } catch (error) {
        console.error('Error:', error);
        // window.location.reload();
        alert('Error:', error);
    }
}

window.toggleRoleVisibility = function () {
    document.querySelectorAll('.role-container .role-icon').forEach(element => {
        element.classList.toggle('hidden');
    });
}

window.removeSlotResponse = function (data) {
    if (data.slot) {
        slot = document.querySelector(`.slot-row[data-slot="${data.slot}"]`);
        slot.setAttribute('data-status', data.status);

        btnAdd = document.querySelector(`#slot-${data.slot}-eliminate-btn`);
        btnAdd.classList.add('hidden');

        btnRemove = document.querySelector(`#slot-${data.slot}-restore-btn`);
        btnRemove.classList.remove('hidden');
    }

}

window.restoreSlotResponse = function (data) {
    if (data.slot) {
        slot = document.querySelector(`.slot-row[data-slot="${data.slot}"]`);
        slot.setAttribute('data-status', data.status);

        btnAdd = document.querySelector(`#slot-${data.slot}-restore-btn`);
        btnAdd.classList.add('hidden');

        btnRemove = document.querySelector(`#slot-${data.slot}-eliminate-btn`);
        btnRemove.classList.remove('hidden');
    }

}


window.timerPause = function(seconds) {
    timer = Alpine.$data(document.querySelector('.timer'));
    timer.pause();
    btn = document.querySelector('#timer-pause-btn span');
    btn.textContent  = 'play_circle';
    btn.setAttribute('endpoint', 'timerResume()');
    btn.setAttribute('onclick', 'timerResume()');
}

window.timerResume = function(seconds) {
    timer = Alpine.$data(document.querySelector('.timer'));
    timer.resume();
    btn = document.querySelector('#timer-pause-btn span');
    btn.textContent  = 'pause_circle';
    btn.setAttribute('endpoint', 'timerPause()');
    btn.setAttribute('onclick', 'timerPause()');
}

window.timerReset = function(option) {
    timer = Alpine.$data(document.querySelector('.timer'));
    switch (option) {
        case "1":
            seconds = 15;
            break; // case
        case "2":
            seconds = 30;
            break; // case
        case "3":
            seconds = 45;
            break; // case
        case "4":
            seconds = 60;
            break;
        default:
            seconds = 60;
            break;
    }// case
    timer.reset(seconds);
    btn = document.querySelector('#timer-pause-btn span');
    if (btn.getAttribute('onclick') === 'timerPause()') {
        timer.resume()
    }
}

window.timerResetOptions = function(option) {
    timerOptions = Alpine.$data(document.querySelector('#timer-options'));
    timerOptions.open = true;
}

window.setSpeakerOptions = function(option) {
    timerOptions = Alpine.$data(document.querySelector('#speaker-options'));
    timerOptions.open = true;
}

window.setSpeaker = function(option) {
    slot = Alpine.$data(document.querySelector('.slot-row[data-slot="${option}"]'));
    slot.classList.add('active-speaker');// case
    timer.reset(seconds);
    btn = document.querySelector('#timer-pause-btn span');
    if (btn.getAttribute('onclick') === 'timerPause()') {
        timer.resume()
    }
}




