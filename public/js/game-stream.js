let refreshInterval;
let currentGameInstance;


function findKeyByValue(obj, value) {
    for (const [key, val] of Object.entries(obj)) {
        if (val == value) {
            return key;
        }
    }
    return null; // or undefined if not found
}
async function refreshGameState(game) {
    try {
        const response = await fetch(`/games/${game.id}/state/${game.streamKey}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },

        });

        if (!response.ok) throw new Error('Network response was not ok');

        const data = await response.json();
        return data.game; // Return the updated props from server

    } catch (error) {
        console.error('Update failed:', error);
        // Fallback to local state if update fails
        return game;
    }
}

function startGameRefresh(game) {
    currentGameInstance = game;
    // Останавливаем предыдущий интервал если был
    if (refreshInterval) {
        clearInterval(refreshInterval);
    }

    // Запускаем обновление каждые 5 секунд
    refreshInterval = setInterval(async () => {
        try {
            const updatedGame = await refreshGameState(currentGameInstance);

            currentGameInstance = updatedGame;  // Добавить эту строку
            handleGameStateUpdate(updatedGame);

        } catch (error) {
            console.error('Auto-refresh failed:', error);
        }
    }, 5000); // 5 секунд
}

function stopGameRefresh() {
    if (refreshInterval) {
        clearInterval(refreshInterval);
        refreshInterval = null;
    }
}

document.addEventListener('DOMContentLoaded', function() {
    startGameRefresh(currentGame);
});

// Остановка при закрытии страницы
window.addEventListener('beforeunload', stopGameRefresh);

function teamColor(game, slotNumber) {
    const role = game.slots[slotNumber]?.role;
    const teamMap = {
        'sheriff': 'red',
        'citizen': 'red',
        'don': 'black',
        'mafia': 'black'
    };
    return teamMap[role] || '';
}


function sheriffColor(game, slotNumber) {
    const role = game.slots[slotNumber]?.role;
    const teamMap = {
        'sheriff': 'sheriff',
        'citizen': 'red',
        'don': 'black',
        'mafia': 'black'
    };
    return teamMap[role] || '';
}

function handleGameStateUpdate(game) {
    // Ваша логика обработки обновленного состояния
    // console.log('Game state updated:', game);
    const slots = game.slots;

    if (game.showRoles === 'off') {
        document.querySelectorAll('.player-card .role-icon').forEach(element => {
            element.classList.add('hidden');
        })
    } else {
        document.querySelectorAll('.player-card .role-icon').forEach(element => {
            element.classList.remove('hidden');
        })
    }
    for (let i = 1; i <= 10; i++) {
        // slots.forEach((slotData, index) => {
        slot = slots[i]
        const playerCard = document.querySelector(`.player-card[data-slot="${i}"]`);
        if (!playerCard) return;

        playerCard.setAttribute('data-role', slot.role);
        playerCard.setAttribute('data-status', slot.status);
        playerCard.setAttribute('data-avatar', slot.avatar);
        playerCard.setAttribute('data-warn', slot.warns);


        if (slot.status === 'killed') {
            slotDay = findKeyByValue(game.killedList, i)
            playerCard.setAttribute('data-day', slotDay);
            slotStatus = playerCard.querySelector('.player-status')
            slotStatus.textContent = slotDay

            if(game.bestGuess[i] !== undefined) {
                // console.log(i, game.bestGuess[i])
                const bestGuess = playerCard.querySelector('.best-guess')
                bestGuess.innerHTML = Object.values(game.bestGuess[i])
                    .map(value => `<span class="${value.k === 1 ? 'black' : 'red'}">${value.slot}</span>`)
                    .join(' ');
                bestGuess.classList.remove('hidden')
            } else {
                const bestGuess = playerCard.querySelector('.best-guess')
                bestGuess.classList.add('hidden')
            }

            if(game.protocolColor[i] !== undefined) {
                const protocolColor = playerCard.querySelector('.protocol-color')
                protocolColor.textContent = game.protocolColor[i]['slot'] ;
                protocolColor.classList.add(game.protocolColor[i]['color'])
                protocolColor.classList.remove((game.protocolColor[i]['color'] === 'red') ? 'black' : 'red')
                protocolColor.classList.remove('hidden')
            } else {
                const protocolColor = playerCard.querySelector('.protocol-color')
                protocolColor.classList.add('hidden')
            }
        }
        // Update avatar
        const img = playerCard.querySelector('img');
        if (img) img.src = slot.avatar;

        // Update role icon
        const name = playerCard.querySelector('.name');
        name.textContent = slot.name || `Player ${i}`;

    }

    // if (game.killedList.length > 0) {
    if (game.settings['show-killed'] === 'on') {
        killed = document.querySelector('.list[data-type="killed-list"]');
        killed.classList.remove('hidden');

        if (Object.keys(game.killedList).length > 0) {
            // killed.textContent = '\u00A0\u00A0' + Object.values(game.killedList).join(' * ')
            killed.innerHTML = Object.values(game.killedList)
                .map(value => `<span class="${teamColor(game, value)}">${value}</span>`)
                .join(' ');
        } else {
            killed.innerHTML = '';
        }
    } else {
        killed = document.querySelector('.list[data-type="killed-list"]');
        killed.classList.add('hidden');
    }

    if (game.settings['show-voted'] === 'on') {
        voted = document.querySelector('.list[data-type="voted-list"]');
        voted.classList.remove('hidden');
        if (game.votedList.length > 0) {
            // voted.textContent = '\u00A0\u00A0' + game.votedList.join(' * ')
            voted.innerHTML = Object.values(game.votedList)
                .map(value => `<span  class="${teamColor(game, value)}">${value}</span>`)
                .join(' ');
        } else {
            voted.innerHTML = '';
        }
    } else {
        voted = document.querySelector('.list[data-type="voted-list"]');
        voted.classList.add('hidden');
    }

    if (game.donChecks.length > 0) {
        donChecks = document.querySelector('.list[data-type="don-checks-list"]');
        // donChecks.textContent = '\u00A0\u00A0' + game.donChecks.join(' * ')
        donChecks.innerHTML = Object.values(game.donChecks)
            .map(value => `<span class="${sheriffColor(game, value)}">${value}</span>`)
            .join(' ');
    } else {
        donChecks = document.querySelector('.list[data-type="don-checks-list"]');
        donChecks.innerHTML = '';
    }

    if (game.sheriffChecks.length > 0) {
        sheriffChecks = document.querySelector('.list[data-type="sheriff-checks-list"]');
        // sheriffChecks.textContent = '\u00A0\u00A0' + game.sheriffChecks.join(' * ')
        sheriffChecks.innerHTML = Object.values(game.sheriffChecks)
            .map(value => `<span class="${teamColor(game, value)}">${value}</span>`)
            .join(' ');
    } else {
        sheriffChecks = document.querySelector('.list[data-type="sheriff-checks-list"]');
        sheriffChecks.innerHTML = '';
    }

    if (game.nominations.length > 0) {
        nominees = document.querySelector('.list[data-type="candidate-list"]');
        // nominees.textContent = '\u00A0\u00A0' + game.nominations.join(' * ')
        nominees.innerHTML = Object.values(game.nominations)
            .map(value => `<span class="${teamColor(game, value)}">${value}</span>`)
            .join(' ');
    } else {
        nominees = document.querySelector('.list[data-type="candidate-list"]');
        nominees.innerHTML = '';
    }

    if (game.settings['show-subphase'] === 'on') {
        phase = document.querySelector('.list.phase');
        phase.classList.remove('hidden');
        phaseContent = phase.querySelector('.list-content');
        if (game.phaseTitle.length > 0) {
            phaseContent.textContent = game.phaseTitle;
        } else {
            phaseContent.textContent = '';
        }
    } else {
        phase = document.querySelector('.list.phase');
        phase.classList.add('hidden');
    }

    if (game.settings['show-name'] === 'on') {
        gameName = document.querySelector('.list.game-name');
        gameNameText = gameName.querySelector('span');
        gameNameText.textContent = game.name;
        gameName.classList.remove('hidden');
    } else {
        gameName = document.querySelector('.list.game-name');
        gameName.classList.add('hidden');
    }

    if (game.settings['show-judge'] === 'on') {
        judge = document.querySelector('.list.judge');
        judge.classList.remove('hidden');
    } else {
        judge = document.querySelector('.list.judge');
        judge.classList.add('hidden');
    }


}
