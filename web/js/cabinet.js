var web_path = '/';
$(document).ready(function () {
    CheckCookie();
    readAllEvents();
});

/**
 * проверяем есть ли кука (показ событий при заходе в кабинет), 
 * если нет, то устанавливаем ее в момент закрытия модального окна
 *
 */
function CheckCookie() {
    if (!getCookie('cabinet_notifications')) {
        $('#modalEvents').click();
        //момент закрытия
        $('#events').on('hidden.bs.modal', function () {
            var expire = getSecondsToTomorrow() + 2 * 3600;
            setCookie('cabinet_notifications', '1', {
                expires: expire
            });
        });
    }
}
/**
 * Получаем куку по имени
 * 
 * @param name
 * @returns {*}
 */
function getCookie(name) {
    var matches = document.cookie.match(new RegExp(
        "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
    ));
    return matches ? decodeURIComponent(matches[1]) : undefined;
}
/**
 * Устанавливаем куку с задаными параметрами
 * 
 * @param name
 * @param value
 * @param options
 */
function setCookie(name, value, options) {
    options = options || {};

    var expires = options.expires;

    if (typeof expires == "number" && expires) {
        var d = new Date();
        d.setTime(d.getTime() + expires * 1000);
        expires = options.expires = d;
    }
    if (expires && expires.toUTCString) {
        options.expires = expires.toUTCString();
    }

    value = encodeURIComponent(value);

    var updatedCookie = name + "=" + value;

    for (var propName in options) {
        updatedCookie += "; " + propName;
        var propValue = options[propName];
        if (propValue !== true) {
            updatedCookie += "=" + propValue;
        }
    }

    document.cookie = updatedCookie;
}
/**
 * Возращает время оставшееся до завтра
 * 
 * @returns {number}
 */
function getSecondsToTomorrow() {
    var now = new Date();

    // создать объект из завтрашней даты, без часов-минут-секунд
    var tomorrow = new Date(now.getFullYear(), now.getMonth(), now.getDate() + 1);

    var diff = tomorrow - now; // разница в миллисекундах
    return Math.round(diff / 1000); // перевести в секунды
}
/**
 * Прочитывает(удалаяет) нотификацию в базе
 * 
 * @param type
 * @param eventId
 * @param userId
 */
function readEvent(type, eventId, userId) {
    var info = {'type' : type, 'eventId' : eventId, 'userId' : userId};
    $.ajax({
        url: web_path + 'seller/cabinet/read-event',
        type: 'POST',
        data: info,
        success: function (data) {
        },
        error: function (error) {
        }
    });
}
/**
 * Прочитывает(удалаяет) все нотификации в базе текущего пользователя
 */
function readAllEvents() {
    $('body').on('click', '#readAll', function () {
        $.ajax({
            url: web_path + 'seller/cabinet/read-all-events' ,
            type: 'POST',
            success: function (data) {
            },
            error: function (error) {
            }
        });
        $('#events').modal('toggle');
        $('#modalEvents').addClass('hidden');
    });
}