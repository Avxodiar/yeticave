'use strict';

(function() {

  /**
   * Проверка статуса запроса
   * @param  int    status     - код статуса
   * @param  string statusText - текст статуса
   * @return string error      - ошибка (при наличии)
   */
  function checkStatus(status, statusText) {
    let error;
    switch(status) {
      case 200:
        break;
      case 400:
        error = 'Неверно сформированный запрос.\n Обновите страницу браузера.';
        break;
      case 401:
        error = 'Пользователь не авторизован.';
        break;
      case 403:
        error = 'Доступ запрещен.';
        break;
      case 404:
        error = 'Ничего не найдено.';
        break;
      case 409:
        error = 'Указана ставка меньше минимальной!';
        break;
      case 410:
        error = 'Указаный лот не существует или снят с аукциона.';
        break;
      case 412:
        error = 'Ставка не указана или неверное значение!';
        break;
      case 503:
        error = 'Сервис временно недоступен.';
        break;
      default:
        error = 'Статус ответа: ' + status + ' ' + statusText;
        break;
    }
    return error;
  }

  /**
  * Обертка XMLHttpRequest
  */
  function XHRequest(method, url, respType, date, onLoad, onError) {
    let xhr = new XMLHttpRequest();
    xhr.responseType = respType;

    xhr.addEventListener('load', function() {
      let error = checkStatus(xhr.status, xhr.statusText);
      if(!error) {
        onLoad(xhr.response);
      } else {
        onError(error);
      }
    });
    xhr.addEventListener('error', function() {
      onError('Ошибка соединения с сервером');
    });
    xhr.addEventListener('timeout', function() {
      onError('Превышено время ожидания ответа: '+ xhr.timeout + 'мс');
    });

    xhr.open(method, url);
    xhr.send(date);
  }

  window.backend = {
    load: function(URL, onLoad, onError) {
      XHRequest('GET', URL, 'json', '', onLoad, onError);
    },
    save: function(URL, date, onLoad, onError) {
      document.body.style.cursor = 'wait';
      XHRequest('POST', URL, 'json', date, onLoad, onError);
      document.body.style.cursor = '';
    }
  }

})();
