document.addEventListener('DOMContentLoaded', function() {
    // Здесь можно добавить JavaScript функциональность
    // Например, обновление статуса сервера и списка топ игроков
    updateServerStatus();
    updateTopPlayers();
});

function updateServerStatus() {
    // Здесь должен быть AJAX запрос к серверу для получения актуального статуса
    document.getElementById('online-players').textContent = '150'; // Пример
}

function updateTopPlayers() {
    // Здесь должен быть AJAX запрос к серверу для получения списка топ игроков
    const topPlayers = ['Игрок1', 'Игрок2', 'Игрок3', 'Игрок4', 'Игрок5'];
    const topPlayersList = document.getElementById('top-players-list');
    topPlayersList.innerHTML = '';
    topPlayers.forEach(player => {
        const li = document.createElement('li');
        li.textContent = player;
        topPlayersList.appendChild(li);
    });
}
