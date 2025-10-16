function randomString(length = 8) {
    const chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    const array = new Uint32Array(length);
    crypto.getRandomValues(array);
    return Array.from(array, x => chars[x % chars.length]).join("");
}

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
 function getPayload(phase, phaseCode, nextForce = false) {
     const subPhase = document.querySelector('input[name="sub_phase"]')?.value;

     const curPhase = PHASES_ORDER[phaseCode];
     const nextPhaseCode = curPhase['next-phase'];
     const nextPhase = PHASES_ORDER[curPhase['next-phase']];
     // console.log(curPhase, nextPhase);

     if([
         'SHOOTING', 'DON-CHECK', 'SHERIFF-CHECK', 'FIRST-KILL',
         'LAST-SPEECH-KILLED', 'LAST-SPEECH-VOTED', 'SCORE', 'DAY-SPEECH', 'BEST-GUESS'
     ].includes(phaseCode)) {
         if(nextForce) {
             payload = nextPhase;
             payload['phase-code'] = nextPhaseCode;
             payload['day'] = document.querySelector('input[name="game_day"]').value;
         } else {
             payload = curPhase;
             payload['phase-code'] = phaseCode;
             payload['day'] = document.querySelector('input[name="game_day"]').value;
         }

     } else if (phase === 'shuffle-slots') {
        payload = {
            'phase-code': phaseCode,
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
            'phase-code': nextPhaseCode,
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
        game_day = document.querySelector('input[name="game_day"]').value;

        const subPhase = document.querySelector('input[name="sub_phase"]')?.value;
        if (subPhase === 'empty') {
            payload = {
                'phase-code': nextPhaseCode,
                phase: phase,
                subPhase: 'don-watch',
                timer: 10,
            };
        } else if (subPhase === 'cahoot') {
            payload = {
                'phase-code': nextPhaseCode,
                phase: phase,
                subPhase: 'sheriff-sign',
                timer: 10,
            };
        } else if (subPhase === 'sheriff-sign') {
            payload = {
                'phase-code': nextPhaseCode,
                phase: phase,
                subPhase: 'free',
                timer: 20,
            };
        } else if (subPhase === 'free') {
            payload = {
                'phase-code': nextPhaseCode,
                phase: 'day',
                subPhase: 'first-speaker',
                day: parseInt(game_day),
                timer: 60,
            };
        } else if (subPhase === 'don-watch') {
            payload = {
                'phase-code': nextPhaseCode,
                phase: phase,
                subPhase: 'sheriff-watch',
                timer: 10,
            };
        } else if (subPhase === 'sheriff-watch') {
            payload = {
                'phase-code': nextPhaseCode,
                phase: phase,
                subPhase: 'best-guess',
                timer: 10,
            };
        } else if (subPhase === 'best-guess') {
            payload = {
                'phase-code': nextPhaseCode,
                phase: 'day',
                subPhase: 'first-speaker',
                timer: 60,
            };
        } else if (subPhase === 'first-speaker') {
            payload = {
                'phase-code': nextPhaseCode,
                phase: 'day',
                subPhase: 'first-speaker',
                timer: 60,
            };
        }
    } else if (phase === 'day') {
        game_day = document.querySelector('input[name="game_day"]').value;

        const subPhase = document.querySelector('input[name="sub_phase"]')?.value;
        if (subPhase === 'first-speaker' && nextForce) {
            payload = {
                'phase-code': nextPhaseCode,
                phase: phase,
                subPhase: 'voting-round',
            };
        } else if (subPhase === 'first-speaker' && phaseCode === 'DAY-SPEECH') {
            // payload = {
            //     'phase-code': PhaseCode,
            //     phase: phase,
            //     subPhase: 'voting-round',
            // };
        } else if (subPhase === 'voting-round') {
            payload = {
                'phase-code': nextPhaseCode,
                phase: phase,
                subPhase: 'voted-speeches',
            };
        }
    } else if (phase === 'game-over') {
        // payload = {
        //     'phase-code': nextPhaseCode,
        //     phase: phase,
        // };
        payload = {
            'phase-code': nextPhaseCode,
            phase: phase,
        };
    } else if (phase === 'score') {
        payload = {
            'phase-code': nextPhaseCode,
            phase: phase,
            teamwin: document.querySelector('input[name="teamwin"]').value,
            scores: Array.from(document.querySelectorAll('[name^="slots["]'))
                .reduce((acc, input) => {
                    // Разбираем имя input: "slots[123][score_base]"
                    const matches = input.name.match(/slots\[(\d+)\]\[(\w+)\]/);
                    if (matches) {
                        const rowIndex = matches[1]; // "123"
                        const fieldName = matches[2]; // "score_base"

                        if (!acc[rowIndex]) {
                            acc[rowIndex] = {}; // Создаем объект для этой строки
                        }

                        acc[rowIndex][fieldName] = input.value; // Добавляем поле
                    }
                    return acc;
                }, {})
        };
    }
     if (typeof payload === 'undefined') {
        console.log('payload undefined', phase, subPhase);
         return {};
    } else {
         return payload
    }

}


window.submitGameState = async function (nextForce = false) {
    // Collect only the values you need
    const phase = document.querySelector('input[name="phase"]').value;
    const phaseCode = document.querySelector('input[name="phase_code"]').value;
    const id = document.querySelector('input[name="game_id"]').value;
    const payload = getPayload(phase, phaseCode, nextForce);
    // console.log(payload);

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
    const phaseCode = document.querySelector('input[name="phase_code"]').value;
    const day = document.querySelector('input[name="game_day"]').value;
    // const payload = getPhaseBack(phase);
    const payload = {
        phase: 'back',
        'phase-code': phaseCode,
        day: day
    };
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

window.toggleRoleVisibility = function (elem) {
    document.querySelectorAll('.role-container .role-icon, .role-container-title').forEach(element => {
        element.classList.toggle('hidden');
    });
    if (document.querySelectorAll('.role-container .role-icon.hidden').length > 0) {
        elem.textContent = 'Показать роли';
    } else {
        elem.textContent = 'Скрыть роли';
    }
}
window.shuffleRoles = function () {
    const roles = [
        'citizen', 'citizen', 'citizen', 'citizen', 'citizen', 'citizen',
        'mafia', 'mafia',
        'don',
        'sheriff'
    ];

    const roleLabels = {
        'mafia': 'Мафия',
        'sheriff': 'Шериф',
        'citizen': 'Мирный',
        'don': 'Дон',
    }

    // Shuffle roles randomly
    const shuffledRoles = roles.sort(() => Math.random() - 0.5);

    // Assign to slots 1-10
    for (let i = 1; i <= 10; i++) {
        const selectElement = document.querySelector(`select[name="slots[${i}][role]"]`);
        // const selectElement = document.querySelector(`select[name="slots[${i}][role]"]`);
        if (selectElement) {
            selectElement.value = shuffledRoles[i - 1];
            const alpineComponent = selectElement.closest('[x-data]');
            const component = Alpine.$data(alpineComponent);
            component.selected = shuffledRoles[i - 1];

        }
    }
}


window.removeSlotResponse = function (data) {
    if (data.slot) {
        slot = document.querySelector(`.slot-row[data-slot="${data.slot}"]`);
        slot.setAttribute('data-status', data.status);

        btnAdd = document.querySelector(`#slot-${data.slot}-eliminate-btn`);
        btnAdd.classList.add('hidden');

        btnRemove = document.querySelector(`#slot-${data.slot}-restore-btn`);
        btnRemove.classList.remove('hidden');

        btnWarn = document.querySelector(`#slot-${data.slot}-warn-btn`);
        btnWarn.classList.add('hidden');

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

        btnWarn = document.querySelector(`#slot-${data.slot}-warn-btn`);
        btnWarn.classList.remove('hidden');
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
    speakerOptions = Alpine.$data(document.querySelector('#speaker-options'));
    const aliveSlots = Array.from(document.querySelectorAll('.slot-row[data-status="alive"]'))
        .map(row => row.getAttribute('data-slot'));
    speakerOptions.options = aliveSlots;
    speakerOptions.open = true;
}

window.setSpeaker = async function(slotId) {
    const gameId = document.querySelector('input[name="game_id"]').value;
    const response = await fetch(`/games/${gameId}/speaker/${slotId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            'slot': slotId
        })
    });

    console.log(response);
    if (!response.ok) throw new Error('Network response was not ok');

    const data = await response.json();
    if(data.status === 'error') {
        alert(data.message);
        return;
    } else if (data.status === 'ok') {
        document.querySelector('input[name="active_speaker"]').value = data.active_speaker;
        document.querySelector('input[name="speakers"]').value = JSON.stringify(data.speakers);

        // slot = Alpine.$data(document.querySelector('.slot-row[data-slot="${slotId}"]'));
        slot = document.querySelector(`.slot-row[data-slot="${data.active_speaker}"]`);
        slot.setAttribute('data-speaker', 'active');

        const nonActiveSlots = document.querySelectorAll(`.slot-row:not([data-slot="${data.active_speaker}"])[data-speaker="active"]`);
        nonActiveSlots.forEach(slot => {
            slot.setAttribute('data-speaker', '');
        });

        timer = Alpine.$data(document.querySelector('.timer'));
        seconds = data.timer || 60;
        timer.reset(seconds);
        timer.resume();
        // btn = document.querySelector('#timer-pause-btn span');
        // if (btn.getAttribute('onclick') === 'timerPause()') {
        //     timer.resume()
        // }
    }


}

window.updateCandidate = async function(slotId) {
    const gameId = document.querySelector('input[name="game_id"]').value;
    const day = document.querySelector('input[name="game_day"]').value;
    const candidate = document.querySelector(`input[name="slots[${slotId}][candidate]"]`).value;

    const response = await fetch(`/games/${gameId}/candidate/${slotId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            'slot': slotId,
            'candidate': candidate,
            'day': day
        })
    });

    console.log(response);
    if (!response.ok) throw new Error('Network response was not ok');

    const data = await response.json();
    if(data.status === 'error') {
        alert(data.message);
        return;
    } else if (data.status === 'ok') {
        console.log(data);
    }

}

window.nextSpeaker = function() {
    const phase = document.querySelector('input[name="phase"]').value;
    const subPhase = document.querySelector('input[name="sub_phase"]').value;

    const aliveSlots = Array.from(document.querySelectorAll('.slot-row[data-status="alive"]'))
        .map(row => parseInt(row.getAttribute('data-slot')))
        .sort((a, b) => a - b);

    if(['finished', 'score'].includes(phase)) {
        return;
    } else if (phase === 'night') {
        submitGameState();
        return
    } else if (phase === 'day') {
        currentSpeaker = document.querySelector('input[name="active_speaker"]').value;
        let passedSpeakers = JSON.parse(document.querySelector('input[name="speakers"]').value || '[]');

        const aliveSpeakersLeft = Array.from(document.querySelectorAll('.slot-row[data-status="alive"]'))
            .map(row => parseInt(row.getAttribute('data-slot')))
            .filter(slot => !passedSpeakers.includes(slot))
            .sort((a, b) => a - b);

        if(!passedSpeakers.includes(currentSpeaker)) {
            passedSpeakers.push(currentSpeaker);
            document.querySelector('input[name="speakers"]').value = JSON.stringify(passedSpeakers);
        }

        if(
            aliveSpeakersLeft.length === 0
            || subPhase === 'last-speech-killed'
        ) {
            submitGameState(true);
            return;
        }
        let nextSpeaker;
        if (currentSpeaker === 0) {
            // Первый спикер или нет живых слотов
            nextSpeaker = aliveSlots[0];
        } else {
            // Ищем следующий слот больше currentSpeaker
            nextSpeaker = aliveSpeakersLeft.find(slot => slot > currentSpeaker);

            // Если не нашли большего, берем наименьший из доступных
            if (!nextSpeaker) {
                if(aliveSpeakersLeft.length > 0) {
                    nextSpeaker = aliveSpeakersLeft[0];
                    setSpeaker(nextSpeaker);
                    return;
                } else {
                    alert('Who is next speaker?');
                }

            } else {
                setSpeaker(nextSpeaker);
                return;
            }
        }
    }
    // slot = Alpine.$data(document.querySelector('.slot-row[data-slot="${option}"]'));
    // slot.classList.add('active-speaker');// case
    // timer.reset(seconds);
    // btn = document.querySelector('#timer-pause-btn span');
    // if (btn.getAttribute('onclick') === 'timerPause()') {
    //     timer.resume()
    // }
}

window.setScorePhase = async function () {
    document.querySelector('input[name="phase_code"]').value = 'SCORE';
    phaseElem = document.querySelector('input[name="phase"]');
    phaseElem.value = 'game-over';
    // phaseElem.value = 'score';
    document.querySelector('input[name="sub_phase"]').value = 'score';
    submitGameState();
}

window.submitScores = async function () {
    document.querySelector('input[name="phase_code"]').value = 'SCORE-SAVE';
    phaseElem = document.querySelector('input[name="phase"]');
    phaseElem.value = 'score';
    document.querySelector('input[name="sub_phase"]').value = 'score';
    submitGameState();
}

window.teamWin = function (team) {
    teamwin = document.querySelector('input[name="teamwin"]');
    teamwin.value = team;

    red = document.querySelectorAll('span[data-role="citizen"], span[data-role="sheriff"]');

    red.forEach(element => {
        const rowIndex = element.getAttribute('data-slot');
        // console.log('Slot', rowIndex);
        const baseInput  = document.querySelector(`[name="slots[${rowIndex}][score_base]"]`);
        const baseInputVisible  = document.querySelector(`[id="visible-slots[${rowIndex}][score_base]"]`);
        if (baseInput) {
            // Устанавливаем значение
            baseInput.value = (team === 'red') ? '1' : '0';
            baseInputVisible.value = (team === 'red') ? '1' : '0';

            // Триггерим событие input для запуска расчетов
            const event = new Event('input', { bubbles: true });
            baseInputVisible.dispatchEvent(event);
        }
    });

    black = document.querySelectorAll('span[data-role="mafia"], span[data-role="don"]');

    black.forEach(element => {
        const rowIndex = element.getAttribute('data-slot');
        const baseInput  = document.querySelector(`[name="slots[${rowIndex}][score_base]"]`);
        const baseInputVisible  = document.querySelector(`[id="visible-slots[${rowIndex}][score_base]"]`);
        if (baseInput) {
            // Устанавливаем значение
            baseInput.value = (team === 'red') ? '0' : '1';
            baseInputVisible.value = (team === 'red') ? '0' : '1';

            // Триггерим событие input для запуска расчетов
            const event = new Event('input', { bubbles: true });
            baseInputVisible.dispatchEvent(event);
        }
    });

}

window.mechPoints = async function (value) {
    function markValue(strVal) {
        return Number(strVal.replace('m','-').replace('p','').replace('zero','0'));
    }
    // alert(value);
        // Get the index from the dropdown element
        const slot = event.target.closest('[data-slot]').getAttribute('data-slot');

        const role = event.target.closest('[data-slot]').getAttribute('data-role');
        // alert(role);
        if (['citizen', 'sheriff'].includes(role)) {
            return;
        } else if (['mafia', 'don'].includes(role)) {
            const mafiaTeam = document.querySelectorAll('div[data-role="mafia"],div[data-role="don"]');
            let mafiaMarks = [];

            mafiaTeam.forEach(element => {
                const slotNum = element.getAttribute('data-slot');
                const mark = slotNum === slot ? value : element.querySelector('[name$="[mark]"]').value;

                mafiaMarks.push({
                    slot: slotNum,
                    mark: mark,
                    number: markValue(mark)
                });
            });

// Sort by number value
            mafiaMarks.sort((a, b) => a.number - b.number);
            // alert(mafiaMarks)

            try {
                const response = await fetch(`/marks-calc`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        team: 'mafia',
                        marks: mafiaMarks
                    })
                });

                if (!response.ok) throw new Error('Network response was not ok');

                const data = await response.json();
                // alert(data)
                data.marks.forEach(item => {
                    const pointsInput  = document.querySelector(`[name="slots[${item.slot}][score_3]"]`);
                    const pointsInputVisible  = document.querySelector(`[id="visible-slots[${item.slot}][score_3]"]`);
                    if (pointsInput) {
                        // Устанавливаем значение
                        pointsInput.value = item.points;
                        pointsInputVisible.value = item.points;

                        // Триггерим событие input для запуска расчетов
                        const event = new Event('input', { bubbles: true });
                        pointsInputVisible.dispatchEvent(event);
                    }

                });

                // return data.props; // Return the updated props from server

            } catch (error) {
                console.error('Update failed:', error);
                // Fallback to local state if update fails
                return updatedGame;
            }
        }

}

window.handleMarkChange = async function(value) {
    // Get the index from the dropdown element
    const dropdown = event.target.closest('[data-index]');
    const index = dropdown ? dropdown.getAttribute('data-index') : null;

    alert(`Value: ${value}, Index: ${index}`);
    // or call your function
    // mechPoints(value, index);
};

window.saveScore = async function () {

}

document.addEventListener("input", function (e) {

    const match = e.target.id.match(/slots\[(\d+)\]\[(score_base|score_1|score_2|score_3|score_4)\]/);
    if(match) {
        const rowIndex = match[1]; // извлекаем индекс строки

        const baseInput  = document.querySelector(`[id="visible-slots[${rowIndex}][score_base]"]`);
        const s1Input    = document.querySelector(`[id="visible-slots[${rowIndex}][score_1]"]`);
        const s2Input    = document.querySelector(`[id="visible-slots[${rowIndex}][score_2]"]`);
        const s3Input    = document.querySelector(`[id="visible-slots[${rowIndex}][score_3]"]`);
        const s4Input    = document.querySelector(`[id="visible-slots[${rowIndex}][score_4]"]`);
        const totalInputVisible = document.querySelector(`[id="visible-slots[${rowIndex}][score_total]"]`);
        const totalInput = document.querySelector(`[name="slots[${rowIndex}][score_total]"]`);

        const base = parseFloat(baseInput?.value) || 0;
        const s1   = parseFloat(s1Input?.value) || 0;
        const s2   = parseFloat(s2Input?.value) || 0;
        const s3   = parseFloat(s3Input?.value) || 0;
        const s4   = parseFloat(s4Input?.value) || 0;

        if (totalInput) {
            totalInput.value = (base + s1 + s2 + s3 + s4).toFixed(2);
            totalInputVisible.value = (base + s1 + s2 + s3 + s4).toFixed(2); // округление под step="0.1"
        }
    }

});

window.copyStreamLink = function() {

    const streamKey = document.querySelector("input[name='props_stream_key']").value;
    const link = window.location.origin + '/stream/' + streamKey;
    navigator.clipboard.writeText(link);
}

// window.copyStreamLink = function() {
//     const streamKey = document.querySelector("input[name='stream_key']").value;
//     const link = window.location.origin + '/stream/' + streamKey;
//     navigator.clipboard.writeText(link);
// }

window.openStreamLink = function() {
    const streamKey = document.querySelector("input[name='props_stream_key']").value;
    const link = window.location.origin + '/stream/' + streamKey;

    window.open(link, '_blank');
}



// window.openStreamLink = function() {
//     const streamKey = document.querySelector("input[name='stream_key']").value;
//     const link = window.location.origin + '/stream/' + streamKey;
//
//     window.open(link, '_blank');
// }

window.generateStreamLink = function() {
    const streamKey = randomString(8);
    document.querySelector("input[name='stream_key']").value = streamKey;
    document.querySelector("input[id='visible-stream_key']").value = streamKey;
}

window.startStream = async function(gameId) {
    try {
        const response = await fetch(`/games/${gameId}/stream/start`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                // props: updatedGame
            })
        });

        if (!response.ok) throw new Error('Network response was not ok');

        const data = await response.json();
        if(data.status === 'error') {
            alert(data.message);
            return;
        } else if (data.status === 'ok') {
            btn = document.querySelector('#start-stream-btn span.inline-btn');
            btn.classList.add('success');
        }
        // return data.game; // Return the updated props from server

    } catch (error) {
        console.error('Update failed:', error);
        // Fallback to local state if update fails
        return game;
    }
}

window.addWarning = async function(gameId, slotId) {
    try {

        const response = await fetch(`/games/${gameId}/warn/${slotId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                'slot': slotId
            })
        });

        if (!response.ok) throw new Error('Network response was not ok');

        const data = await response.json();
        if(data.status === 'error') {
            alert(data.message);
            return;
        } else if (data.status === 'ok') {
            slot = document.querySelector(`.slot-row[data-slot="${data.slot}"]`);
            slot.setAttribute('data-warns', data.warns);
            if(data.warns === 4) {
                removeSlotResponse({
                    status: 'eliminated',
                    slot: data.slot
                });
                // console.log(data);
            }

        }
        // return data.game; // Return the updated props from server

    } catch (error) {
        console.error('Update failed:', error);
        // Fallback to local state if update fails
        return game;
    }
}


window.streamSettingsCallback = function(data) {
    if(data.status === 'ok') {
        inputKey = document.querySelector("input[name='props_stream_key']");
        inputKey.value = data.stream['stream-key'];

        inputShowRoles = document.querySelector("input[name='stream_show_roles']");
        inputShowRoles.checked = data.stream['show-roles'];
    }
    console.log(data);
}

window.votingHandler = function(data) {
    if(data.status === 'ok') {
        if (data.no_voting === true) {
            document.querySelector('input[name="phase_code"]').value = 'SHOOTING';
            document.querySelector('input[name="phase"]').value = 'night';
            document.querySelector('input[name="sub_phase"]').value = 'shooting';
            document.querySelector('input[name="game_day"]').value = data.day;
            submitGameState();

        } else {
            console.log(data)
            if(data?.guilty.length > 0 ) {
                for (let i = 0; i < data.guilty.length; i++) {
                    removeSlotResponse({
                        status: 'voted',
                        slot: data.guilty[i]
                    });
                }
                document.querySelector('input[name="phase_code"]').value = 'LAST-SPEECH-VOTED';
                document.querySelector('input[name="phase"]').value = 'day';
                document.querySelector('input[name="sub_phase"]').value = 'last-speech-voted';
                document.querySelector('input[name="game_day"]').value = data.day;
                submitGameState();

            }
        }
    }
    console.log(data);
}
window.shootingHandler = function(data) {
    if(data.status === 'ok') {
        if(data.result === 'killed') {
            removeSlotResponse({
                status: 'killed',
                slot: data.target
            });
        }
        document.querySelector('input[name="game_day"]').value = data.day;
        document.querySelector('input[name="phase_code"]').value = 'DON-CHECK';
        document.querySelector('input[name="phase"]').value = 'night';
        document.querySelector('input[name="sub_phase"]').value = 'don-check';

        submitGameState();
    }
    // console.log(data);
}

window.donCheckHandler = function(data) {
    if(data.status === 'ok') {
        document.querySelector('input[name="phase_code"]').value = 'SHERIFF-CHECK';
        document.querySelector('input[name="phase"]').value = 'night';
        document.querySelector('input[name="sub_phase"]').value = 'sheriff-check';
        document.querySelector('input[name="game_day"]').value = data.day;

        submitGameState();
    }
    // console.log(data);
}

window.sheriffCheckHandler = function(data) {
    if(data.status === 'ok') {
        switch(data.nextPhase) {
            case 'BEST-GUESS':
                document.querySelector('input[name="phase_code"]').value = 'BEST-GUESS';
                document.querySelector('input[name="phase"]').value = 'night';
                document.querySelector('input[name="sub_phase"]').value = 'best-guess';
                break;
            case 'LAST-SPEECH-KILLED':
                document.querySelector('input[name="phase_code"]').value = 'LAST-SPEECH-KILLED';
                document.querySelector('input[name="phase"]').value = 'day';
                document.querySelector('input[name="sub_phase"]').value = 'last-speech-killed';
                break;
            case 'DAY-SPEECH':
            default:
                document.querySelector('input[name="phase_code"]').value = 'DAY-SPEECH';
                document.querySelector('input[name="phase"]').value = 'day';
                document.querySelector('input[name="sub_phase"]').value = 'first-speaker';

        }
        document.querySelector('input[name="game_day"]').value = data.day;


        submitGameState();
    }
    // console.log(data);
}

window.protocolColorHandler = function(data) {
    if(data.status === 'ok') {
        document.querySelector('input[name="phase_code"]').value = 'DAY-SPEECH';
        document.querySelector('input[name="phase"]').value = 'day';
        document.querySelector('input[name="sub_phase"]').value = 'first-speaker';
        document.querySelector('input[name="game_day"]').value = data.day;
        submitGameState();
    }
    console.log(data);
}




window.bestGuessHandler = function(data) {
    if(data.status === 'ok') {
        document.querySelector('input[name="phase_code"]').value = 'LAST-SPEECH-KILLED';
        document.querySelector('input[name="phase"]').value = 'night';
        document.querySelector('input[name="sub_phase"]').value = 'last-speech-killed';

        submitGameState();
    }
    console.log(data);
}


window.updateNominee = async function(elem) {
    // const votes = document.querySelector('#voting-form input[name="elem"]').value;
    const alive = document.querySelector('#voting-form input[name="alive"]').value;
    const maxIdx = document.querySelector('#voting-form input[name="max_idx"]').value;

    candidates = document.querySelectorAll('#voting-form input[name^="candidate["]');
    let total = 0;
    for (const input of candidates) {
        if (input.name.endsWith(`][${maxIdx}]`)) {
            console.log(input.name);
        } else {
            total += (parseFloat(input.value) || 0);
        }



    };

    lastCandidate = document.querySelector('#voting-form input[name$="][' + maxIdx + ']"]')
    visibleCandidate = document.querySelector('#voting-form input[id$="][' + maxIdx + ']"]')
    console.log(lastCandidate)
    lastCandidate.value = alive - total;
    visibleCandidate.value = alive - total;
}

window.startGameActions = function() {
    const phaseCode = document.querySelector('input[name="phase_code"]').value;

    if(phaseCode === 'VOTING') {
        const nominees = document.querySelector('input[name="nominees"]').value;
        if(nominees.length === 0) {

        }
    }

}




document.addEventListener('DOMContentLoaded', function() {
    startGameActions();
});



