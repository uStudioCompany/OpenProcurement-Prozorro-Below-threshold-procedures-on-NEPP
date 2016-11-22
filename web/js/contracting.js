var AutosaveTimer;
//var FeatureOptionNum = 0;
var FeatureCount = 0;
var web_path = '/';
$(document).ready(function () {


    AddLot();
    AddItem();
    DeleteLot();
    DeleteItem();
    DateFormat();
    DeleteFile();
    RefreshDocumentTypeSelect();
    SetActivePanel();
    FillDocumentLinkItem();
    BeforeSubmitForm();
    BeforeValidateForm();
    HideRemoveItemButton();
    SelectAdditionalClassificationsType();
    SelectItemUnitType();
    SetVars();
    //для обновления типа тендера
    $('#value-amount').trigger('change');
    $('.unit_select').trigger('change');

    SetSelectedDocumentType();
    UploadFile();
    SlideBlock();
    TenderNeedSignCheck();
    SetFieldVisibility();
    ChangeAmount();




    // $('body').on('click', '.delete_cancellation', function () {
    //     if (confirm('Ви впевненi, що хочете видалити?')) {
    //         $(this).closest('.cancel_item').remove();
    //     }
    // });

});
function SetVars() {
    if ($('.item:visible').length == 0) {
        ItemCount = 0;
    } else {
        ItemCount = parseInt($('.item:visible').length - 1);
    }

    if ($('.lot').length < 2) {
        LotCount = 0;
    } else {
        LotCount = parseInt($('.lot').length) - 1;
    }

    if ($('.panel-body:visible').length == 0) {
        ItemFilesCount = 0;
    } else {
        ItemFilesCount = parseInt($('.panel-body:visible').length);
    }

    setTimeout('if ($(".feature:visible").length == 0) { FeatureCount = 1;} else {FeatureCount = parseInt($(".feature:visible").length + 1); }', 1000);
    // if ($('.feature:visible').length == 0) {
    //     console.log(456);
    //     //TODO: если ставить 0, то отпадает валидация
    //     FeatureCount = 1;
    // } else {
    //     FeatureCount = parseInt($('.feature:visible').length + 1);
    //     console.log(FeatureCount);
    // }

    if ($('.additional_persons li').length == 0) {
        AdditionalPersonCount = 0;
    } else {
        AdditionalPersonCount = parseInt($('.additional_persons ul').attr('itemcount'));
    }

    IsPublished = $('#is_published').val();
}
function SetFieldVisibility() {

    $('.field-changes-rationaletypes input[type=checkbox]').click(function () {
        $('.swichRationaleTypes').hide();
        $('.field-changes-rationaletypes input[type=checkbox]:checked').each(function () {
            CheckVal = $(this).val();
            $('.swichRationaleTypes').each(function () {
                if ($(this).hasClass('swichRationaleTypes_' + CheckVal)) {
                    $(this).show();
                }
            })
        })
    })

    $('#contract-terminatetype input').click(function () {
        if($(this).is(':checked')){
            if($(this).val()== 1){
                $('.terminationDetails').show();
            }else {
                $('.terminationDetails').hide();
            }
        }
    })

}

function ChangeAmount() {
    var amount = parseFloat($('.result_amount').val());
    $('body').on('keyup', '.contract_change_amount', function () {
        var change = parseFloat($(this).val());
        var result = amount + change;
        if(!isNaN(result)){
            $('.result_amount').val(amount + change);
        }else{
            $('.result_amount').val(amount);
        }


    })
}



function AddLot() {
    $('.add_lot').click(function () {
        LotCount++;

        var new_lot = $('#hidden_lot_original .lot').clone();
        new_lot.find('.items_block').remove();
        new_lot.find('.add_item').before('<div class="info-block items_block"><div class="item"></div></div>');
        var lot = $('.lots_block').append(new_lot);


        $('.lot:last input, .lot:last textarea').each(function () {

            $(this).val('');
            FillDocumentLinkItem();

            //добавляем id для лота
            if ($(this).hasClass('lot_id')) {
                $(this).val(GetUniqID());
            }

            //если прошла валидация родительских полей, то убираем ее
            var container = $(this).closest('.form-group');
            container.removeClass('has-error has-success');
            container.find('.help-block').text('');

            var name = $(this).attr('name');
            if (name) {

                var arr = name.split(/__EMPTY_LOT__/);
                name = arr[0] + LotCount + arr[1];
                $(this).attr('name', name);

                var oldId = $(this).attr('id');
                if (oldId) {
                    //console.log(oldId);
                    var id = oldId.replace('__empty_lot__', LotCount);
                    //var id = oldId + ItemCount
                    $(this).attr('id', id);
                }

                var containerOldClass = container.attr('class');
                if (containerOldClass) {
                    containerClass = containerOldClass.replace('__empty_lot__', LotCount);
                    container.removeClass().addClass(containerClass);

                    var validateContainerArr = containerClass.split(' ');
                    var validateContainerClass = validateContainerArr[1];
                }


                //назначаем параметры валидации из родительских елементов
                if ($('#tender_simple_create').length > 0) {
                    var form = $('#tender_simple_create');
                    var attributes = form.yiiActiveForm('find', oldId);

                    if (attributes) {
                        var patternValidatePattern = attributes.validate;
                    }
                }
                if ($('#tender_simple_update').length > 0) {
                    var form = $('#tender_simple_update');
                    var attributes = form.yiiActiveForm('find', oldId);
                    if (attributes) {
                        var patternValidatePattern = attributes.validate;
                    }
                }

                if (attributes !== undefined) {
                    form.yiiActiveForm('add', {
                        "id": id,
                        "name": name,
                        "container": "." + validateContainerClass,
                        "input": "#" + id,
                        "validateOnType": true,
                        "validate": patternValidatePattern
                    });
                }
            }
        })

        new_lot.find('.add_item').click();
        new_lot.find('.item:first').remove();
        setUploadObj(new_lot.find('.uploadfile'));
        SetMinimalStepProcent();

    })
}
function AddItem() {
    $('body').on('click', '.add_item', function () {
        //$('.add_item').click(function () {
        ItemCount++;

        var items = $(this).parent().find('.items_block');
        var currentLot = $(this).closest('.lot');
        var new_item = $('#hidden_item_original .item').clone();
        items.find('.item:last').after(new_item);
        $('.items_block .item:last').find('input[rel=hiddenid]').val(GetUniqID());

        DateFormat();
        HideRemoveItemButton();

        var hidden_classification = $('.hidden_classification');
        var hidden_additionalclassification = $('.hidden_additionalclassification');
        $('.items_block .item:last input:visible, ' +
            '.items_block .item:last .hidden_classification, ' +
            '.items_block .item:last .hidden_additionalclassification, ' +
            '.items_block .item:last .item_id, ' +
            '.items_block .item:last .item_related_lot, ' +
            '.items_block .item:last .hidden_unit_name, ' +
            '.items_block .item:last select, ' +
            '.items_block .item:last textarea', currentLot).each(function () {

            //$(this).val('');
            //$(this).text('');
            FillDocumentLinkItem();
            $('.items_block .item:last .item_id').val(GetUniqID());

            //ставим id связанного лота
            SetRelatedLot();


            //если прошла валидация родительских полей, то убираем ее
            var container = $(this).closest('.form-group');
            container.removeClass('has-error has-success');
            container.find('.help-block').text('');

            var name = $(this).attr('name');
            if (name) {

                var arr = name.split(/__EMPTY_ITEM__/);
                //if (typeof(arr[2]) != "undefined" && arr[2] !== null) {
                //    name = arr[0] + ItemCount + arr[1] + '0' + arr[2];
                //} else {
                //console.log(ItemCount);
                name = arr[0] + ItemCount + arr[1];
                //}
                $(this).attr('name', name);

                var oldId = $(this).attr('id');
                var id = oldId.replace('__empty_item__', ItemCount);
                //var id = oldId + ItemCount
                $(this).attr('id', id);

                var containerOldClass = container.attr('class');
                containerClass = containerOldClass.replace('__empty_item__', ItemCount);
                container.removeClass().addClass(containerClass);

                var validateContainerArr = containerClass.split(' ');
                var validateContainerClass = validateContainerArr[1];


                //назначаем параметры валидации из родительских елементов
                if ($('#tender_simple_create').length > 0) {
                    var form = $('#tender_simple_create');
                    var attributes = form.yiiActiveForm('find', oldId);

                    if (attributes) {
                        var patternValidatePattern = attributes.validate;
                    }
                }
                if ($('#tender_simple_update').length > 0) {
                    var form = $('#tender_simple_update');
                    var attributes = form.yiiActiveForm('find', oldId);
                    if (attributes) {
                        var patternValidatePattern = attributes.validate;
                    }
                }

                if (attributes !== undefined) {
                    //console.log(attributes);
                    form.yiiActiveForm('add', {
                        "id": id,
                        "name": name,
                        "container": "." + validateContainerClass,
                        "input": "#" + id,
                        "validateOnType": true,
                        "validate": patternValidatePattern
                    });
                }
            }


        })

    })
}




function DeleteLot() {
    $('body').on('click', '.delete_lot', function () {
        if (confirm('Ви впевненi, що хочете видалити?')) {
            $(this).closest('.lot').remove();
            HideRemoveItemButton();
        }
    })
}
function DeleteItem() {
    $('body').on('click', '.delete_item', function () {
        if (confirm('Ви впевненi, що хочете видалити?')) {
            $(this).closest('.item').remove();
            HideRemoveItemButton();
        }
    })
}


function DeleteFile() {
    $('body').on('click', '.delete_file', function () {
        if (confirm('Ви впевненi, що хочете видалити?')) {

            var fileUrl = $(this).parent().find('.delete_file').attr('del');

            $.ajax({
                type: "GET",
                url: web_path + 'tender/filedelete',
                cache: false,
                data: {'file': fileUrl},
                success: function (data) {

                }
            });
        }
        if ($('.panel-body').length == 1) {
            $(this).closest('.panel-body').hide();
        } else {
            $(this).closest('.panel-body').remove();
        }
    })


}


function setUploadObj(obj) {

    var f_button = $(obj);

    f_button.uploadFile({
        url: web_path + 'tender/fileupload',
        fileName: 'FileUpload[file]',
        dragDrop: false,
        //maxFileCount: 1,
        multiple: false,
        showStatusAfterSuccess: false,
        showStatusAfterError: false,
        showFileCounter: false,
        showFileSize: false,
        maxFileSize: 52428800,
        showAbort: false,
        onSubmit: function (files) {
            f_button.before('<img src="' + web_path + 'img/spiner.gif" class="preloader" width="34px">');
        },
        uploadStr: "Додати",
        dynamicFormData: function () {
            var data = {tenderId: $('#tender_id').val()}
            return data;
        },
        onSuccess: function (file, response) {
            $('.preloader').remove();
            response = $.parseJSON(response);

                var new_elem = $('.panel-body:first').clone();
                console.log(new_elem);
                f_button.prev().append(new_elem);
                new_elem.show();

                var activePanel = f_button.prev().find('.panel-body:last');
                activePanel.find($('.file_original_name')).text(response.model.name);
                activePanel.find($('.file_name')).val(response.fileName);
                activePanel.find($('.delete_file')).attr('del', response.newName);
                activePanel.find($('.real_name')).val(response.newName);
                //проставляем name с следующим индексом
                activePanel.find('input, select').each(function () {


                    var name = $(this).attr('name');
                    if (name) {
                        var arr = name.split(/__EMPTY_DOC__/);
                        name = arr[0] + ItemFilesCount + arr[1];
                        $(this).attr('name', name);
                    }
                })
                ItemFilesCount++;
                UploadFileReplace($('.panel-body:last').find('.uploadfile_replace'));
                FillDocumentLinkItem();
                //проставляем первый елемент селекта
                $('.document_link select', activePanel).val($('.document_link select option:first', activePanel).val());
                //ChangeTypeSelect();
            }
        });
}
function UploadFile() {
    //elLink = $('.uploadfile');

    // $('body').on('click', '.uploadfile', function () {
    //     TenderAutosave($(this).closest('form'));
    // })


    $('.uploadfile:visible').each(function () {
        setUploadObj($(this));
    })

}

function UploadFileReplace(obj) {

    if (obj.is(':visible')) {


        obj.uploadFile({
            url: web_path + 'tender/fileupload',
            fileName: 'FileUpload[file]',
            dragDrop: false,
            //maxFileCount: 1,
            multiple: false,
            showStatusAfterSuccess: false,
            showStatusAfterError: false,
            showFileCounter: false,
            showFileSize: false,
            //maxFileSize: 52428800,
            showAbort: false,
            uploadStr: "Замiнити",
            formData: {tenderId: $('#tender_id').val()},
            onSubmit: function (files) {
                obj.parent().append('<img src="' + web_path + '/img/spiner.gif" class="preloader" width="34px">');
            },
            onSuccess: function (file, response) {
                $('.preloader').remove();
                response = $.parseJSON(response);

                var activePanel = $('.panel-body.active');
                activePanel.find($('.file_original_name')).text(response.model.name);
                activePanel.find($('.file_name')).val(response.model.name);
                activePanel.find($('.delete_file')).attr('del', response.newName);
                activePanel.find($('.real_name')).val(response.newName);
                //activePanel.find('.download_link a').remove();
            }
        });


    }
}
function SetActivePanel() {
    $('body').on('click', '.uploadfile_replace', function () {
        //удаляем ранее загруженный файл
        if (confirm('Ви впевненi, що хочете замiнити файл?')) {

            var fileUrl = $(this).parent().find('.delete_file').attr('del');

            $.ajax({
                type: "GET",
                url: web_path + 'tender/filedelete',
                cache: false,
                data: {'file': fileUrl},
                success: function (data) {

                }
            });
        } else {
            return false;
        }

        $('.panel-body').removeClass('active');
        $(this).closest('.panel-body').addClass('active');
    })

}

function GetUniqID() {
    var ALPHABET = '0123456789abcdef';

    var ID_LENGTH = 32;

    var generate = function () {
        var rtn = '';
        for (var i = 0; i < ID_LENGTH; i++) {
            rtn += ALPHABET.charAt(Math.floor(Math.random() * ALPHABET.length));
        }
        return rtn;
    }
    return generate;

}
function GetItemsToDocumentTypeSelect(lot) {

    var option = '';
    if (lot && lot.length) {
        lot.find('.items_block .item:visible').each(function () {
            var itemText = $(this).find('.item-description').val();
            var id = $(this).find('.item_id').val();

            if (itemText == '') {
                itemText = 'Товар без названия';
            }
            option += '<option value="' + id + '">' + itemText + '</option>';
        })
    } else {
        $('.items_block .item:visible').each(function () {
            var itemText = $(this).find('.item-description').val();
            var id = $(this).find('.item_id').val();

            if (itemText == '') {
                itemText = 'Товар без названия';
            }
            option += '<option value="' + id + '">' + itemText + '</option>';
        })
    }

    return option;
}
function GetLotsToDocumentTypeSelect(lot) {

    var option = '';
    if (lot) {
        var id = lot.find('.lot_id').val();
        if (id !== undefined) {
            option += '<option value="' + id + '">Поточний лот</option>';
        }

    }
    return option;
}
function RefreshDocumentTypeSelect() {
    $('body').on('change keyup paste', '.item-description:visible, .lot_title', function () {
        FillDocumentLinkItem();
    });
}
function FillDocumentLinkItem() {


    $('.document_link select:visible optgroup').each(function () {

        var value = $(this).attr('rel');
        var SelectedOption = $(this).parent().val();
        //если простой тендер

        if ($('.tender_type_select').val() == 1) {

            if (value == 'item') {
                $(this).show().html('').append(GetItemsToDocumentTypeSelect());
            } else if (value == 'lot') {
                $(this).hide();
            } else if (value == 'tender') {
                $(this).show().html('').append('<option value="tender">Тендер</option>');
            }


        } else if ($('.tender_type_select').val() == 2) { // если мультилот

            var Select = $(this).parent();

            if (Select.closest('.features_block').parent().hasClass('lots_marker') ||
                Select.closest('.document_block').parent().hasClass('lots_marker')
            ) { // если в лоте


                if (value == 'item') {
                    $(this).html('').append(GetItemsToDocumentTypeSelect($(this).closest('.lot')));
                } else if (value == 'tender') {
                    $(this).hide();
                } else if (value == 'lot') {
                    $(this).html('').append(GetLotsToDocumentTypeSelect($(this).closest('.lot')));
                }

            } else {

                if (value == 'item') { // если не в лоте
                    $(this).hide();
                } else if (value == 'tender') {
                    $(this).html('').append('<option value="tender">Все оголошення</option>');
                } else if (value == 'lot') {
                    $(this).hide();
                }


            }


        }
        //ставим обратно выбранный пункт
        $(this).parent().val(SelectedOption);
    })
}

function SetSelectedDocumentType() {
    $('.related_id').each(function () {
        var fId = $(this).val();
        var select = $(this).siblings('select');
        if (fId == '') {
            fId = 'tender';
        }
        select.val(fId);

        //назначаем тригер замены файлов
        var obj = $(this).closest('.panel-body').find('.uploadfile_replace');
        UploadFileReplace(obj);
    })
}





function BeforeSubmitForm() {
    $('#contracts').on('beforeSubmit', function () {
        $('#contracts').on('beforeValidate', function () {
            $('.swichRationaleTypes').each(function () {
                $(this).find('input:hidden').not('input[name="Changes[value][amount]"]').val('');
            })
        })
    })
}
function BeforeValidateForm() {
    $('#contracts').on('beforeValidate', function () {
        $('.swichRationaleTypes').each(function () {
            $(this).find('input:hidden').not('input[name="Changes[value][amount]"]').val('');
        })
    })
}



function HideRemoveItemButton() {
    if ($('.items_block .item:visible').length == 1) {
        $('.items_block .item .delete_item').hide();
    } else {
        $('.items_block .item .delete_item').show();
    }

    if ($('.lot:visible').length == 1) {
        $('.lot .delete_lot').hide();
    } else {
        $('.lot .delete_lot').show();
    }

    $('.features_block .feature').each(function () {
        if ($('.enum', this).length == 1) {
            $('.enum .delete_feature_enum', this).hide();
        } else {
            $('.enum .delete_feature_enum', this).show();
        }

    })

}

function SelectItemUnitType() {
    $('.unit_select').change(function () {
        $(this).parent('div').find('input').val($('option:selected',this).text());
    });
}



function SelectAdditionalClassificationsType() {
    
    $('.additionalClassifications_block').each(function () {
        if($(this).find('select').val() == '000'){
            $('.additionalClassifications_input', this).hide();
        }
    })
    
    
    $('body').on('change', '.additionalClassifications_select:visible', function () {
        var parent = $(this).closest('.additionalClassifications_block');
        var value = $(this).val() ? $(this).val() : '';


        parent.find('.classificator-input').attr('url',web_path + 'classificator/' + value.split('_')[1]).val('');
        if(value != '000'){
            parent.find('.additionalClassifications_input').show();
            parent.find('label').eq(1).text('Класифікація згідно ' + value.split('_')[0]);
        }else{
            parent.find('.additionalClassifications_input').hide();
        }
    }).on('focus', '.additionalClassifications_select:visible', function () {
        //var prevValue = $(this).val();
        //console.log(prevValue);
        //console.log(prevValue);
    })
}


function ComareDateFormat(date) {

    if (date) {
        var parts = date.split("/");
        var time = parts[2].split(' ');
        var time = time[1].split(':');
        return new Date(parts[2].substring(0, 4), parts[1] - 1, parts[0], time[0], time[1]);
    }

}
function DateFormat() {
    $(".picker").datetimepicker({
        locale: $('#current_locale').val(),
        format: "DD/MM/YYYY HH:mm",
        //minDate: "now",
        //keepInvalid: true
    });
}






function TenderNeedSignCheck() {
    $('.need_tender_esign').click(function () {
        if ($('.need_tender_esign').is(':checked')) {
            $('.sign_btn').show();
        } else {
            $('.sign_btn').hide();
        }
    })

}
