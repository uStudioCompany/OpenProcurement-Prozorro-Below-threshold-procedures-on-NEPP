$(document).ready(function () {
    //$('.classificator-input').on('focus', '', {}, onModal);
    $('body').on('focus', '.classificator-input', {}, onModal);

    $('body').on('keyup', '#classificator-modal #search', function () {
        onSearch($(this), 'name');
    });
    $('body').on('keyup', '#classificator-modal #search_code', function () {
        onSearch($(this), 'code');
    });
    // $('#classificator-modal #search').on('keyup', '', {}, onSearch('name'));
    // $('#classificator-modal #search_code').on('keyup', '', {}, onSearch('code'));

});

function onModal(e) {
    var pid = 0;
    $('.modal-title').text($(this).parent('div').parent('div').find('label').text());
    $('#classificator-modal #mbody').children().remove();
    $('#classificator-modal #selected-item p').empty();
    $('#classificator-modal #btn-ok').off();

    $('#classificator-modal #search').val('');

    $('#classificator-modal').modal();

    $('#classificator-modal #btn-ok').on('click', '', {'input': $(this)}, onOK);
    $('#classificator-modal #btn-ok').attr('disabled', 'disabled');

    $.ajaxSetup({url: $(this).attr('url')});

    // Для Планов, все итемы в пределах групы плана...
    if ($(this).attr('parent-id')) {
        if ($($(this).attr('parent-id')).val()) {
            pid = $($(this).attr('parent-id')).val();
        }
    }
    
    loadItems({'pid': pid, 'no_select': $(this).hasClass('no-head-select')}, $('#classificator-modal #mbody'));
}

function onSearch(obj, param) {
    // debugger;

    // if (obj.val().length >= 3) {
        $('#classificator-modal #mbody').children().remove();
        if (ajaxRun && ajaxRun.readyState != 4) {
            ajaxRun.abort();
        }

        if(param == 'name') {
            loadItems({'name': obj.val(), 'id':$('#classificator-modal #search_code').val()}, $('#classificator-modal #mbody'));
        }else if(param == 'code'){
            loadItems({'id': obj.val(), 'name':$('#classificator-modal #search').val()}, $('#classificator-modal #mbody'));
        }
    // }

    if (!obj.val().length) {
        $('#classificator-modal #mbody').children().remove();
        loadItems({'pid': 0, 'no_select': obj.hasClass('no-head-select')}, $('#classificator-modal #mbody'));
    }
}

function loadItems(param, node) {
    console.log(param);
    ajaxRun = $.ajax(
        {
            data: param,
            type: 'GET',
            dataType: 'html',
            context: node,
            beforeSend: function () {
                $('#classificator-modal .spinner').fadeIn();
            },
            complete: function () {
                $('#classificator-modal .spinner').fadeOut(300);
            },
            success: function (data) {
                var items = $(data).find('.item');

                if (items.size()) {                 
                    this.append(items);
                    this.children('.item').each(function () {
                        if(param.no_select && $(this).attr('pid') == 0 && $(this).hasClass('has-chield')) {
                            $(this).addClass('no-select');
                        }
                                                   
                        $(this).on('click', '', {}, onItem);
                        // console.log($(this).attr('id'), 'pid:', $(this).attr('pid'), 'no_select:', $(this).hasClass('no-select'));
                    });
                } else {
                    $(this).parent().addClass('no-children');
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log('Error: ' + textStatus + ' | ' + errorThrown);
            }
        });
}

function onItem(e) {
    e.stopPropagation();

    if (!$(this).children('.item-name').hasClass('selected')) {
        $('#classificator-modal .selected').removeClass('selected');
        $(this).children('.item-name').addClass('selected');

        $('#classificator-modal .selected-code').text($(this).attr('id'));
        $('#classificator-modal .selected-name').text($(this).children('.item-name').find('.item-descr').text());
        
        if($(this).hasClass('no-select')) {
            $('#classificator-modal #btn-ok').attr('disabled', 'disabled');
        } else {
            $('#classificator-modal #btn-ok').removeAttr('disabled');
        }
    }

    if ($(this).hasClass('expand')) {
        $(this).children('.item-child').slideUp(300);
        $(this).removeClass('expand');

        $(this).children('.item-name').find('i').removeClass('fa-minus-square-o');
        $(this).children('.item-name').find('i').addClass('fa-plus-square-o');

    } else {
        $(this).children('.item-name').find('i').removeClass('fa-plus-square-o');
        $(this).children('.item-name').find('i').addClass('fa-minus-square-o');

        if (!$(this).children('.item-child').children().size()) {
            loadItems({'pid': $(this).attr('id')}, $(this).children('.item-child'));
        }

        if (!$(this).children('.item-name').hasClass('no-children')) {
            $(this).children('.item-child').slideDown(300);
            $(this).addClass('expand');

        }
    }
}

function onOK(e) {
    var
        descr = $.trim($('#classificator-modal .selected-name').text()),
        code = $.trim($('#classificator-modal .selected-code').text());

    e.data.input.val(descr);
    e.data.input.next('input').val(code);

    e.data.input.trigger('blur');
    $('#classificator-modal').modal('hide');
}
