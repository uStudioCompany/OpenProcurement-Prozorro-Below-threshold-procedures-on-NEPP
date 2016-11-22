$(document).ready(function () {
    SlideBlock();
})

function SlideBlock(){

    //var h = $(".b-sticky-right2__trigger").height();
    //$('.b-sticky-right__trigger').height(h);

    var rb = getCookie('show_rb');
    if(rb == 'no'){
        $(".b-sticky-right2__trigger").css({
            'width': '0px',
            'padding': '0px'
        });
    }


    $('.b-sticky-right__trigger').click(function(){

        var w = $(".b-sticky-right2__trigger").width();

        if(w > 0) {
            $(".b-sticky-right2__trigger").stop().animate({
                width: '0px',
                padding: '5px 0px 5px 0px'
            }, 500);
            //$('.b-sticky-right__trigger').animate({
            //    transform: 'rotate(150deg)',
            //    border:'1px solid red'
            //}, 1000);
            setCookie('show_rb', 'no', {
                'expires' : 31536000,
                'path' : '/'
            });
        }else{
            $(".b-sticky-right2__trigger").stop().animate({
                width: '237px',
                padding: '5px'
            }, 500);
            //$('.b-sticky-right__trigger:before').animate({
            //    transform: 'rotate(180deg)',
            //    border:'1px solid red'
            //}, 1000);
            setCookie('show_rb', 'yes', {
                'expires' : 31536000,
                'path' : '/'
            });
        }
    })
}

function getCookie(name) {
    var matches = document.cookie.match(new RegExp("(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"));
    return matches ? decodeURIComponent(matches[1]) : '';
}

function setCookie(name, value, props) {
    props = props || {};
    var exp = props.expires;
    if ( typeof exp == "number" && exp) {
        var d = new Date();
        d.setTime(d.getTime() + exp * 1000);
        exp = props.expires = d;
    }
    if (exp && exp.toUTCString) {
        props.expires = exp.toUTCString();
    }
    value = encodeURIComponent(value);
    var updatedCookie = name + "=" + value;
    for (var propName in props) {
        updatedCookie += "; " + propName;
        var propValue = props[propName];
        if (propValue !== true) {
            updatedCookie += "=" + propValue;
        }
    }
    document.cookie = updatedCookie;
}

function deleteCookie(name) {
    setCookie(name, null, {
        expires : -1,
        'path' : '/'
    });
}
function SetAuctionMode(obj) {

    if ($(obj).is(':checked')) {
        setCookie('auction-mode', 'test', {
            'expires': 35936000,
            'path': '/'
        });
        document.location.reload();
    } else {
        setCookie('auction-mode', 'prod', {
            'expires': 35936000,
            'path': '/'
        });
        document.location.reload();
    }
}