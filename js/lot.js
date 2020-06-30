'use strict';

(function() {

let bet = document.querySelector('#cost');
let currCost = document.querySelector('.lot-item__cost');
let minCost = document.querySelector('.lot-item__min-cost span');

let betForm = document.querySelector('.lot-item__form');
let betButton = betForm.querySelector('.button[type=submit]');

let betHistory = document.querySelector('div.history');
let betTable = betHistory.querySelector('.history__list');

betForm.addEventListener('submit', function (evt) {
    evt.preventDefault();
    let min = parseInt(bet.min) || 0;
    let cost = parseInt(bet.value) || 0;
    if(!cost || cost < min) {
        alert('Указанная ставка меньше минимальной или не задана!');
        return false;
    }

    window.backend.save(
        '/lot.php',
        new FormData(betForm),
        function(response) {

            betButton.disabled = true;
            document.body.style.cursor = 'progress';
            alert('Ставка принята');
            //меняем текущую цену
            currCost.innerHTML = response['priceFormat'];
            //меняем мин.ставку
            minCost.innerHTML = response['minPriceFormat'];
            //меняем вашу ставку
            bet.min = parseInt(response['minPrice']);
            bet.value = '';
            bet.placeholder = parseInt(response['minPrice']);

            //перестраиваем таблицу историй ставок
            betHistory.classList.add('hide');
            betHistory.querySelector('h3 > span').textContent = response['bets'].length;
            betTable.innerHTML = '';

            response['bets'].forEach(function(item) {
                let tr = document.createElement('tr');
                tr.className = 'history__item';
                tr.innerHTML = '<tr class="history__item"><td class="history__name">' + item['name'] +
                    '</td><td class="history__price">' + item['price'] +
                    '</td><td class="history__time">' + item['ts'] +
                    '</td></tr>';
                betTable.append(tr);
            });
            betHistory.classList.remove('hide');
            document.body.style.cursor = '';
            betButton.disabled = false;

        },
        function(errorMessage) {
            alert(errorMessage);
        }
    );
});

})();
