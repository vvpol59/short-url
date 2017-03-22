/**
 * Created by vvpol on 20.03.2017.
 */
"use strict";
(function (window) {
    var http, submitBtn, checkBtn, urlBox, shortUrlBox;

    /**
     * Отправка запроса на сервер
     * @param fun
     * @param url
     * @param onSuccess
     */
    function post(fun, url, onSuccess) {
        http.open('post', '?fun=' + fun + '&url=' + url);
        http.onreadystatechange =  function(){
            if(http.readyState == 4){
                var response,
                    resp = http.responseText;
                if (resp[0] == '{'){
                    response = JSON.parse(resp);
                } else {
                    response.error = resp;
                }
                if (response.error){
                    alert(response.error);
                } else {
                    onSuccess(response.result);
                }
            }
        };
        http.send();
    }

    // Обработчик полной загрузки страницы
    window.addEventListener('DOMContentLoaded', function(){
        // Инициализация объекта для запросов
        http = new XMLHttpRequest();
        //
        submitBtn = document.getElementsByClassName('submit-btn')[0];
        checkBtn = document.getElementsByClassName('taste-btn')[0];
        urlBox = document.getElementsByClassName('url-original')[0];
        shortUrlBox = document.getElementsByClassName('url-in')[0];
        // Очистка инпутов
        urlBox.value = '';
        shortUrlBox.value = '';
        // Для выделения текста короткого УРЛ
        shortUrlBox.addEventListener('click', function(){
            this.select();
        });
        shortUrlBox.addEventListener('focus', function(){
            this.select();
        });
        // Обработчик кнопки отправки
        submitBtn.addEventListener('click', function(){
            var url = urlBox.value;
            shortUrlBox.value = '';
            post('make', url, function(response){
                shortUrlBox.value = response;
                checkBtn.href = response;
            });
        });

    });
})(window);