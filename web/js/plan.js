var web_path = '/';
FOCUS = 0;

$(document).ready(function () {

    SelectAdditionalClassificationsType();
    $('.additionalClassifications_select').trigger('change');

    $('.add_item_plan').click(function () {
        ItemCount++;
        var item_str = ($('#item_new_element').html()).replace(/__EMPTY_ITEM__/gi, ItemCount);
        var item = $('.items_block').append(item_str).find('.item:last');

        item.find('.item_id').val(getNewId());
        item.find('.form-control:not(SELECT)').each(function () {
            var parentId = this.id.replace(ItemCount, '__empty_item__');
            $(this.form).yiiActiveForm('add', {
                "id": this.id,
                "name": this.name,
                "container": ".field-" + this.id,
                "input": "#" + this.id,
                "validateOnType": true,
                "validate": $(this.form).yiiActiveForm('find', parentId).validate
            });
        });
            item.parent().removeClass('hide');
        // console.log(item);
        //проставляем нужный справочник
        //var value = $('.additionalClassifications_select:visible').val();
        //$('.item:last').find('.hidden_scheme_dk').val(value.split('_')[1]);
    });



    $('.add_kekv_plan').click(function () {

        $('.hide').eq(0).removeClass('hide');
        checkKekvButton($(this));

    });



    $('body')
        .on('click', '.delete_item', function () {
            if (confirm('Ви впевненi, що хочете видалити?')) {
                $(this).closest('.item').remove();
            }
        })
        .on('focus', '.picker', function () {
            $(this).datetimepicker({
                locale: curLocale,
                minDate: moment().startOf('year'),
                format: "MM/YYYY"
            });
        }).on('focus', '.picker_date', function () {
            $(this).datetimepicker({
                locale: curLocale,
                format: "DD/MM/YYYY"
            });
        }).on('focus', '.picker_year', function () {
            $(this).datetimepicker({
                locale: curLocale,
                minDate: moment().startOf('year'),
                maxDate: moment().startOf('year').add(1, 'year'),
                format: "YYYY"
            });
        });

    $('#plan_create').keypress(function (e) {
        //console.log($(this).serialize());
        var data = $(this).serialize();
        window.clearTimeout(AutoSaveTimer);
        AutoSaveTimer = setTimeout(function () {
            //console.log(data);
            $.post(web_path + 'plan/create', data + '&autoSave=1', function (data) {
                console.log(data);
                if (data.id != '') {
                    $('#id').val(data.id);
                }
            }, 'json');//*/
        }, 1000);
    });

    $('#plan_drafts').click(function () {
        $('#plan_create').off('submit.yiiActiveForm');
        //$(this).closest('form').submit();
        return true;
    });

    $('#plan_cancel').click(function () {
        $('#plan_create').off('submit.yiiActiveForm');
        return true;
    });


    checkKekvButton($('.add_kekv_plan'));

});

function checkKekvButton(obj) {
    if($('.hide').length == 0){
        obj.hide();
    }
}
function getNewId() {
    var rtn = '';
    for (var i = 0; i < 32; i++) {
        rtn += '0123456789abcdef'.charAt(Math.floor(Math.random() * '0123456789abcdef'.length));
    }
    return rtn;
}

function SelectAdditionalClassificationsType() {


    //    if($(this).find('select').val() == '000'){
    //        $('.classificator-input').val('none');
    //        $('.additionalClassifications_input', this).hide();
    //
    //    }
    //})


    $('body').on('change', '.additionalClassifications_select:visible', function () {
        var parent = $(this).closest('.additionalClassifications_block');
        var value = $(this).val() ? $(this).val() : '';

        if(FOCUS ==1) {
            parent.find('.classificator-input').attr('url', web_path + 'classificator/' + value.split('_')[1]).val('');
        }
        if(value != '000'){
            parent.find('.additionalClassifications_input').show();
            console.log(value.split('_')[2]);
            parent.find('label').eq(1).text('Код ' + $('option:selected',this).text());
        }else{
            parent.find('.additionalClassifications_input').hide();
        }

        $('.hidden_scheme_dk').val(value.split('_')[0]);

        //проставляем для итемов соответствующий справочник
        $('.item .additionalClassifications_block').each(function(){
            var parent = $(this);
            var value = $('.additionalClassifications_select:visible').val() ? $('.additionalClassifications_select:visible').val() : '';
            if(FOCUS ==1){
                parent.find('.classificator-input').attr('url',web_path + 'classificator/' + value.split('_')[1]).val('');
            }

            if(value != '000'){
                parent.find('.additionalClassifications_input').show();
                parent.find('label').eq(1).text('Код ' + value.split('_')[0]);
            }else{
                parent.find('.additionalClassifications_input').hide();
            }
        })

        if($('.additionalClassifications_select').val() == '000') {
            $('.classificator-input-description').val('Не визначено');
            $('.hidden_dk_classificator_id').val('000');
            $('.additionalClassifications_input').hide();
        }

    }).on('focus', '.additionalClassifications_select:visible', function () {
        FOCUS = 1;
        console.log(FOCUS);
    }).on('blur', '.additionalClassifications_select:visible', function () {
        FOCUS = 0;
        console.log(FOCUS);
    })



}


