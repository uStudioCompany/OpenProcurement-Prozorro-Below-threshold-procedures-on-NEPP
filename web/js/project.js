var AutosaveTimer;
//var FeatureOptionNum = 0;
var FeatureCount = 0;
var web_path = '/';
$(document).ready(function () {
    addGuaranteeBlockMultilot();
    SubmitJoinUserToCompanyForm();
    TenderContactValidate();
    TenderSubmitDrafts();
    AddLot();
    AddItem();
    AddFeature();
    AddEnum();
    DeleteLot();
    DeleteItem();
    DeleteFeature();
    DeleteFeatureEnum();
    DateFormat();
    //UploadFileReplace();
    //SetUploadFileReplace();
    DeleteFile();
    RefreshDocumentTypeSelect();
    SetActivePanel();
    FillDocumentLinkItem();

    RunAutosave();
    AddFeatureOption();
    BeforeSubmitForm();
    BeforeValidateForm();
    HideRemoveItemButton();
    //eee();
    SelectTenderType();
    SelectTenderGuarantee();
    SelectLotGuarantee();
    SetRelatedLot();
    SelectAdditionalClassificationsType();
    SelectItemUnitType();
    SetVars();
    //для обновления типа тендера
    $('.tender_method_select').trigger('change');
    $('.tender_type_select').trigger('change');
    $('.guarantee_select').trigger('change');
    $('#value-amount').trigger('change');
    $('.unit_select').trigger('change');
    //$('.additionalClassifications_select').trigger('change');
    $('body').on('click', function () {
        $('.guarantee_block_lot select').each(function () {
            SelectLotGuarantee();
        });
    });

    SetSelectedDocumentType();
    UploadFile();
    AddAdditionalContactPerson();
    //SetView();
    SetMinimalStepProcent();
    SlideBlock();
    TenderNeedSignCheck();
    ChooseCouse();
    TenderAutosave( $("#tender_simple_create"));


    $('.cancel_lot').click(function () {
        $(this).hide();
        CancelCount++;
        var item_str = ($('#hidden_cancellation_original').html()).replace(/__EMPTY_CANCEL__/gi, CancelCount);
        var item = $(this).closest('.info-block').find('.cancellations_block').append(item_str).find('.cancel_item:last');

        //item.find('.cancellation_id').val(GetUniqID());
        if ($(this).closest('.lot')) {
            item.find('.related_lot').val($(this).closest('.lot').find('.lot_id').val());
            item.find('.cancellation_of').val('lot');
        }
        item.find('.form-control:not(SELECT)').each(function () {
            var parentId = this.id.replace(CancelCount, '__empty_cancel__');
            $(this.form).yiiActiveForm('add', {
                "id": this.id,
                "name": this.name,
                "container": ".field-" + this.id,
                "input": "#" + this.id,
                "validateOnType": true,
                "validate": $(this.form).yiiActiveForm('find', parentId).validate
            });
        });
    });

    $('body').on('click', '.delete_cancellation', function () {
        if (confirm('Ви впевненi, що хочете видалити?')) {
            $(this).closest('.cancel_item').remove();
        }
    });

    DisableFieldsIfPublished();
});

function DisableFieldsIfPublished() {
    //selective_competitiveDialogueUA.stage2 selective_competitiveDialogueEU.stage2
    if (IsPublished && ($('.tender_method_select').val() == 'selective_competitiveDialogueUA.stage2' || $('.tender_method_select').val() == 'selective_competitiveDialogueEU.stage2')) {
    // if (IsPublished && ($('.tender_method_select').val() == 'open_competitiveDialogueUA')) {
        $('.tender-preview input').each(function () {
            $(this).prop('disabled', true);
        });
        $('.tender-preview select').each(function () {
            $(this).prop('disabled', true);
        });
        $('.tender-preview textarea').each(function () {
            $(this).prop('disabled', true);
        });
        $('.tender-preview button').each(function () {
            $(this).prop('disabled', true);
        });
        $('.document_block').each(function () {
            parentBlock = $(this);
            parentBlock.find('input').each(function () {
                $(this).prop('disabled', false);
            });
            parentBlock.find('select').each(function () {
                $(this).prop('disabled', false);
            });
        });
        $('input[name=_csrf]').prop('disabled', false);
        $('input[name=tender_method]').prop('disabled', false);
        $('.itemdeliverydate-startdate').each(function () {
            $(this).prop('disabled', false);
        });
        $('.itemdeliverydate-enddate').each(function () {
            $(this).prop('disabled', false);
        });
        $('.hidden_additionalclassification').each(function () {
            $(this).prop('disabled', false);
        });
        $('#period-enddate').prop('disabled', false);
        $('#is_published').prop('disabled', false);

    }
}

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
function eee() {
    $('.drafts_submit_1').click(function () {
        $.ajax({
            type: "POST",
            url: web_path + 'tender/test',
            cache: false,
            data: $('#tender_simple_create').serialize(),
            success: function (data) {

                $('#tender_simple_create').append(data);

            }
        });
    })
}
function SubmitJoinUserToCompanyForm() {
    $('body').on('click', '#joinToCompany', function () {
        var login = $('input[name="User[username]"]').val();
        var pass = $('input[name="User[password]"]').val();
        var confirmpass = $('input[name="User[confirmPassword]').val();
        var code = $('input[name="Companies[identifier]"]').val();
        var hash = $('input[name="_csrf"]').val();
        if (code != '') {
            $('#key_csrf').val(hash);
            $('input[name="UserJoinRequests[username]"]').val(login);
            $('input[name="UserJoinRequests[password]"]').val(pass);
            $('input[name="UserJoinRequests[confirmPassword]"]').val(confirmpass);
            $('input[name="UserJoinRequests[_joinToIdentifier]"]').val(code);
            $('#join_form').submit();
        }
    })
}

function TenderContactValidate() {
    $('.contact_person').change(function () {
            if ($('.contact_person option:selected').val() != '') {
                $('.contact_group_wrapper .has-error').each(function () {
                    $(this).removeClass('has-error');
                    $('.help-block', this).html('');
                })
            }
            if ($(this).val() != '') {
                //подгружаем данные в поля
                $.ajax({
                    type: "POST",
                    url: web_path + 'persons/getinfo',
                    data: {'id': $(this).val()},
                    cache: false,
                    success: function (data) {
                        $('#contactpoint-name').val(data[0].userSurname + ' ' + data[0].userName + ' ' + data[0].userPatronymic);
                        $('#contactpoint-email').val(data[0].email);
                        $('#contactpoint-telephone').val(data[0].telephone);
                        $('#contactpoint-name_en').val(data[0].userSurname_en + ' ' + data[0].userName_en + ' ' + data[0].userPatronymic_en);
                        $('.contact_point_available_language').val(data[0].availableLanguage);
                    }
                })
            } else {
                $('#contactpoint-name').val('');
                $('#contactpoint-email').val('');
                $('#contactpoint-telephone').val('');
                $('#contactpoint-name_en').val('');
                $('.contact_point_available_language').val('uk');
            }

        }
    )
}
function ValidateFields() {
    $('#tender_simple_create input, #tender_simple_create textarea').each(function () {
        if ($(this).val() != '' || $(this).text() != '') {
            //console.log($(this).attr('name'));
            var id = $(this).attr('id');
            $('#tender_simple_create').yiiActiveForm('validateAttribute', id);
        }

    });


    setInterval('function g(){if($(\'.has-error\').size() == 0){return true;}else{return false;}}', 500);
    //$('#contact-form').yiiActiveForm('validateAttribute', 'contactform-name');
}
function TenderSubmitDrafts() {
    $('.drafts_submit').click(function () {
        if (BeforeSubmitFormProcedure()) {
            //ValidateFields();
            $('#tender_simple_create').off('submit.yiiActiveForm');
            //$('#tender_simple_create').submit();
            return true;
        } else {
            return false;
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
        HideMinimalStepInfoForLimited();
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

function AddEnum() {

    //проставляем feature_count в title каждой вьючи
    $('.feature:visible').each(function(index){
        $(this).find('.feature_title').attr('feature_count',index);
    })

    $('body').on('click', '.enum_block .add_feature_enum', function () {

        // инкрементим количество enum в текущем блоке.
        var enum_count_temp = $(this).closest('.feature').find('.feature_title').attr('enum_count');
        $(this).closest('.feature').find('.feature_title').attr('enum_count', parseInt(enum_count_temp));
        enum_ind = $(this).closest('.feature').find('.feature_title').attr('enum_count');

        feature_ind = $(this).closest('.feature').find('.feature_title').attr('feature_count');

        var new_enum = $('#hidden_feature_original .enum_block .enum').clone();

        new_enum.find('.enum-name span').text(parseInt(enum_ind) + 1);//опция
        // console.log(feature_ind);

        new_enum.find('label').eq(1).attr('for', 'tender-features-' + feature_ind + '-enum-' + enum_ind + '-title');
        new_enum.find('input').eq(0).attr('id', 'tender-features-' + feature_ind + '-enum-' + enum_ind + '-title');
        new_enum.find('input').eq(0).attr('name', 'Tender[features][' + feature_ind + '][enum][' + enum_ind + '][title]').val('');
        new_enum.find('input').eq(0).attr('e', enum_ind);

        new_enum.find('label').eq(2).attr('for', 'tender-features-' + feature_ind + '-enum-' + enum_ind + '-title_en');
        new_enum.find('input').eq(1).attr('id', 'tender-features-' + feature_ind + '-enum-' + enum_ind + '-title_en');
        new_enum.find('input').eq(1).attr('name', 'Tender[features][' + feature_ind + '][enum][' + enum_ind + '][title_en]').val('');
        new_enum.find('input').eq(1).attr('e', enum_ind);


        new_enum.find('label').eq(3).attr('for', 'tender-features-' + feature_ind + '-enum-' + enum_ind + '-value');
        new_enum.find('input').eq(2).attr('id', 'tender-features-' + feature_ind + '-enum-' + enum_ind + '-value');
        new_enum.find('input').eq(2).attr('name', 'Tender[features][' + feature_ind + '][enum][' + enum_ind + '][value]').val('');

        if ($(this).parent().find('.enum').size()) {
            $(this).parent().find('.enum:last').after(new_enum);
        } else {
            $(this).parent().prepend(new_enum);
        }

        $('input:visible', new_enum).each(function (index) {

            $(this).val('');
            $(this).text('');

            //если прошла валидация родительских полей, то убираем ее
            var container = $(this).closest('.form-group');
            container.removeClass('has-error has-success required');
            container.find('.help-block').text('');

            //убираем родительский класс обертки
            var parentWrapperClass = new_enum.parent().find('.enum:first input').eq(index).closest('.form-group').attr('class');
            parentWrapperClass = parentWrapperClass.split(' ');
            //console.log(parentWrapperClass[1]);
            container.removeClass(parentWrapperClass[1]);


            var fId = $(this).attr('id');
            var fName = $(this).attr('name');
            var what = fId.split('-');
            var parentId = 'enum-__empty_feature__-0-' + what[5];
            var containerClass = parentWrapperClass[1] + '-' + feature_ind + '-' + enum_ind;
            $(this).closest('.form-group').addClass(containerClass);

            //назначаем параметры валидации из родительских елементов
            if ($('#tender_simple_create').length > 0) {
                var form = $('#tender_simple_create');
                var attributes = form.yiiActiveForm('find', parentId);
                if (attributes) {
                    var patternValidatePattern = attributes.validate;
                }
            }
            if ($('#tender_simple_update').length > 0) {
                var form = $('#tender_simple_update');
                var attributes = form.yiiActiveForm('find', parentId);
                if (attributes) {
                    var patternValidatePattern = attributes.validate;
                }
            }

            form.yiiActiveForm('add', {
                "id": fId,
                "name": fName,
                "container": "." + containerClass,
                "input": "#" + fId,
                "validateOnType": true,
                "validate": patternValidatePattern
            });

        })
        HideRemoveItemButton();
        //инкрементим счетчик опций
        enum_ind++;
        $(this).closest('.feature').find('.feature_title').attr('enum_count',enum_ind);
    })
}

function AddFeature() {

    $('body').on('click', '.features_block .add_feature', function () {

        var new_feature = $('#hidden_feature_original .feature').clone();
        $(this).before(new_feature);

        HideRemoveItemButton();

        $(this).parent().find('.feature:last input, .feature:last select').each(function () {

            $(this).val('');
            FillDocumentLinkItem();

            //если прошла валидация родительских полей, то убираем ее
            var container = $(this).closest('.form-group');
            container.removeClass('has-error has-success');
            container.find('.help-block').text('');

            var name = $(this).attr('name');
            if (name) {
// console.log(FeatureCount);
                var arr = name.split(/__EMPTY_FEATURE__/);
                name = arr[0] + FeatureCount + arr[1];
                $(this).attr('name', name);
                $(this).attr('feature_count', FeatureCount);

                var oldId = $(this).attr('id');
                if (oldId) {
                    //console.log(oldId);
                    var id = oldId.replace('__empty_feature__', FeatureCount);
                    //var id = oldId + ItemCount
                    $(this).attr('id', id);
                }

                var containerOldClass = container.attr('class');
                containerClass = containerOldClass.replace('__empty_feature__', FeatureCount);
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

        FeatureCount++;
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
function DeleteFeature() {
    $('body').on('click', '.delete_feature', function () {
        if (confirm('Ви впевненi, що хочете видалити?')) {
            $(this).closest('.feature').remove();
            HideRemoveItemButton();
        }
    })
}
function DeleteFeatureEnum() {
    $('body').on('click', '.delete_feature_enum', function () {
        if (confirm('Ви впевненi, що хочете видалити?')) {
            $(this).closest('.enum').remove();
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

function SetRelatedLot() {
    $('.item_related_lot').each(function () {
        if ($(this).hasClass('item_related_lot')) {
            var lot = $(this).closest('.lot').find('.lot_id').val();
            $(this).val(lot);
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

    $('body').on('click', '.uploadfile', function () {
        TenderAutosave($(this).closest('form'));
    })


    $('.uploadfile:visible').each(function () {
        //тут мы проверяем если лот последний, то у него уже загрузка файлов назачена
        var lastLotId = $('.lot:last').find('.lot_id')[0].id;
        var currentLotId = $($(this).parent()).find('.lot_id')[0].id;
        if (lastLotId == currentLotId) return;
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


        //$.ajax_upload(obj, {
        //    action: web_path + 'tender/fileupload',
        //    name: 'FileUpload[file]',
        //    data: {},
        //    onSubmit: function (file, ext) {
        //        this.settings.data = {tenderId: $('#tender_id').val()};
        //    },
        //    onComplete: function (file, response) {
        //        response = $.parseJSON(response);
        //        //console.log(response);
        //
        //        var activePanel = $('.panel-body.active');
        //        activePanel.find($('.file_original_name')).text(response.model.name);
        //        activePanel.find($('.file_name')).val(response.model.name);
        //        activePanel.find($('.delete_file')).attr('del', response.newName);
        //        activePanel.find($('.real_name')).val(response.newName);
        //
        //    }
        //});
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
function RunAutosave() {
    if ($('#tender_simple_create').length > 0) {
        $('#tender_simple_create').keypress(function (e) {
            //console.log(123);
            window.clearTimeout(AutosaveTimer);
            AutosaveTimer = setTimeout('TenderAutosave( $("#tender_simple_create"))', 5000);
        })
    }

    if ($('#tender_simple_update').length > 0) {
        $('#tender_simple_update').keypress(function (e) {
            //console.log(123);
            window.clearTimeout(AutosaveTimer);
            AutosaveTimer = setTimeout('TenderAutosave($("#tender_simple_update"))', 5000);
        })
    }

}
function TenderAutosave(form) {

    //снимаем disabled с типа тендера для того, что бы его отправить  в post
    //$('.tender_type_select').prop('disabled', false);
if(form.length >0) {
    $.ajax({
        type: "POST",
        url: web_path + 'buyer/tender/create',
        cache: false,
        data: form.serialize() + '&autosave=1',
        success: function (data) {
            //$('.tender_type_select').prop('disabled', true);
            //console.log(data);
            response = $.parseJSON(data);
            if (response.tenderid != '') {
                $('#tender_id').val(response.tenderid);
            }
        },
        error: function () {
            //$('.tender_type_select').prop('disabled', true);
        }
    });
}
}

function AddFeatureOption() {
    $('.add_feature_option').click(function () {
        $(this).parent().find('.feature_option:first').clone().insertAfter('.feature_option:last');
        var lastOption = $('.feature_option:last');
        var featureOptionNum = GetFeatureOptionNum($(this));
    })
}
function GetFeatureOptionNum(obj) {
    var count = obj.parent().find('.feature_option').length;
    return count;
}


function BeforeSubmitForm() {
    $('#tender_simple_create').on('beforeSubmit', function () {
        //$('#tender_simple_create, #tender_simple_update').on('afterValidate', function () {
        BeforeSubmitFormProcedure();
    })
}
function BeforeValidateForm() {
    $('#tender_simple_create').on('beforeValidate', function () {
        if ($('.tender_type_select').val() == 2) { // если мультилот, то ставим сумму всего тендера
            var fullSumm = 0;
            $('.lot_amount:visible').each(function () {
                var lotSumm = parseFloat($(this).val());
                fullSumm += lotSumm;
            })
            $('.tender_full_amount').val(fullSumm);


            //если мультилот, то ставим для тендера минимальный шаг

            var lot_min_step = 99999999999999999999999999999999999999999;
            $('.lot:visible').each(function () {
                $(this).find('.lot_step_amount').each(function () {
                    //console.log($(this).val(), lot_min_step);
                    if (parseFloat($(this).val()) < parseFloat(lot_min_step)) {
                        lot_min_step = $(this).val();
                    }
                })
            })
            $('.tender_step_amount').val(lot_min_step);
        }
    })
}

function BeforeSubmitFormProcedure() {
    $('.hidden_item_original').remove();

    //ставим при сабмите documentOf для файлов и неценовых показателей
    $('.file_wrap .document_link select:visible').each(function () {
        var documentOf = $(this).find('option:selected').parent().attr('rel');
        var name = $(this).attr('name');
        var num = name.match(/\d{1}/);
        $(this).siblings('input').remove();
        $(this).after('<input type="hidden" value="' + documentOf + '" name="Tender[documents][' + num + '][documentOf]">')

    });

    $('.features_block .document_link select:visible').each(function () {
        var documentOf = $(this).find('option:selected').parent().attr('rel');
        var name = $(this).attr('name');
        var num = name.match(/\d{1}/);
        $(this).siblings('input').remove();
        $(this).after('<input type="hidden" value="' + documentOf + '" name="Tender[features][' + num + '][featureOf]">')

    })

    //снимаем disabled с типа тендера для того, что бы его отправить  в post
    $('.tender_type_select').prop('disabled', false);

    //если не европейская процедура, то удаляем дополнительные контактные лица
    if ($('.tender_method_select').val() != 'open_aboveThresholdEU' &&
        $('.tender_method_select').val() != 'open_aboveThresholdUA.defense' &&
        $('.tender_method_select').val() != 'open_competitiveDialogueEU')
    {
        $('.additional_persons').remove();
    }


    //$('.additionalClassifications_block').each(function(){
    //    var dkLib = $('.additionalClassifications_select:visible', this).val();
    //    alert(dkLib);
    //    dkLib = dkLib.split('_')[1];
    //    alert(dkLib);
    //    $('#additionalClassifications_block .'+ dkLib +':visible').siblings().remove();
    //    alert(2);
    //})



    return true;

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
function HideMinimalStepInfoForLimited() {
    if($.inArray($('.tender_method_select').val(), ['limited_negotiation.quick', 'limited_negotiation']) != -1)
    {
        $('.wrapper_minimalStep').each(function () {
            wrapper_minimalStep = $(this);
            wrapper_minimalStep.hide();
            wrapper_minimalStep.find(':input').prop('disabled', true);
        });
        $('.guarantee_block_lot').each(function () {
            guaranteeBlockLot = $(this);
            guaranteeBlockLot.hide();
            guaranteeBlockLot.find(':input').prop('disabled', true);
        });
    }
}
function SelectItemUnitType() {
    $('.unit_select').change(function () {
        $(this).parent('div').find('input').val($('option:selected',this).text());
    });
}

function SelectTenderType() {
    $('.tender_type_select').change(function () {

        if ($(this).val() == '1') {
            $('.lots_marker, .add_lot').hide();
            $('.simple_only').show();
            $('.item').addClass('no_border');
        } else if ($(this).val() == '2') {
            $('.lots_marker, .add_lot').show();
            $('.simple_only').hide();
            $('.item').removeClass('no_border');
            setUploadObj($('.lot:last').find('.uploadfile'));
        }
        HideMinimalStepInfoForLimited();
        FillDocumentLinkItem();
        SetMinimalStepProcent();
        addGuaranteeBlockMultilot();
    });

    $('.tender_method_select').change(function () {
        addGuaranteeBlockMultilot();
        if ($(this).val() == 'open_belowThreshold') {//обычная
            if(!IsPublished){
                $('.tender_type_select').prop('disabled', false);
            }

            $('.periods_wrapper, .periods_wrapper div').show();
            $('.periods_wrapper div').eq(0).hide();

            $('.features_wrapper').show();
            $('.amount_wrapper').show();
            $('.rationale_wrapper').hide();
            $('.eu_procedure').hide();
            $('.negotiation_wrapper').hide();
            $('.guarantee_needed').hide();
            $('.amount_wrapper').show();
            $('.limited_message').addClass('hide');

        }
    });



    if ($('.view_t_type').length > 0) {
        if ($('.view_t_type').val() == '1') {
            $('.lots_marker, .add_lot').hide();
            $('.simple_only').show();
            $('.item').addClass('no_border');
        } else if ($('.view_t_type').val() == '2') {
            $('.lots_marker, .add_lot').show();
            $('.simple_only').hide();
            $('.item').removeClass('no_border');

        }
    }
}

function SelectTenderGuarantee () {
    $('.guarantee_select').change(function () {

        if ($(this).val() == '0') {
            $('.guarantee_amount').hide();
            $('#guarantee-amount').val(0);

        } else if ($(this).val() == '1') {
            $('.guarantee_amount').show();
        }
    });
}
function SelectLotGuarantee() {
    $('.guarantee_block_lot').each(function () {
        parentBlock = $(this);
        parentBlock.find('.guarantee_select_lot').each(function () {
            if ($(this).val() == '0') {
                parentBlock.find('.guarantee_amount_lot').hide();
                parentBlock.find('#guarantee-amount').val(0);
            } else if ($(this).val() == '1') {
                parentBlock.find('.guarantee_amount_lot').show();
            }
        });
    });
}
function SelectAdditionalClassificationsType() {
    
    $('.additionalClassifications_block').each(function () {
        if($(this).find('select').val() == '000'){
            $('.additionalClassifications_input', this).hide();
        }
    })

    $('.additionalClassifications_select:visible').each(function(){
        var parent = $(this).closest('.additionalClassifications_block');
        var value = $(this).val() ? $(this).val() : '';


        parent.find('.classificator-input').attr('url',web_path + 'classificator/' + value.split('_')[1]);
        if(value != '000'){
            parent.find('.additionalClassifications_input').show();
            parent.find('label').eq(1).text('Класифікація згідно ' + $('option:selected',this).text());
        }else{
            parent.find('.additionalClassifications_input').hide();
        }
    })


    $('body').on('change', '.additionalClassifications_select:visible', function () {
        setAdditionalClassifications_select($(this));
    });

}
function setAdditionalClassifications_select(obj) {
    // $('body').on('change', '.additionalClassifications_select:visible', function () {
        var parent = obj.closest('.additionalClassifications_block');
        var value = obj.val() ? obj.val() : '';


        parent.find('.classificator-input').attr('url',web_path + 'classificator/' + value.split('_')[1]).val('');
        if(value != '000'){
            parent.find('.additionalClassifications_input').show();
            parent.find('label').eq(1).text('Класифікація згідно ' + $('option:selected',obj).text());
        }else{
            parent.find('.additionalClassifications_input').hide();
        }
    // }).on('focus', '.additionalClassifications_select:visible', function () {
    //     //var prevValue = $(this).val();
    //     //console.log(prevValue);
    //     //console.log(prevValue);
    // })

    $('.modal-title').text('Код ' + $('option:selected',obj).text());
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
function SetView() {
    $('.is_view input, .is_view select, .is_view textarea').each(function () {
        $(this).addClass('view_convert').attr('disabled', 'disabled');
    })
    $('.is_view button').remove();
}

function AddAdditionalContactPerson() {

    $('.add_contact_person').click(function () {

        if(typeof(sendContactRequest) != "undefined" && sendContactRequest !== null) {
            if (sendContactRequest && sendContactRequest.readyState != 4) {
                $('.preloader').remove();
                sendContactRequest.abort();
            }
        }

        var clickedbtn = $(this);
        var url = web_path + 'persons/getinfo';

        sendContactRequest = $.ajax({
            url: url,
            type: "POST",
            beforeSend: function () {
                $('.additional_persons ul').append('<img src="' + web_path + '/img/spiner.gif" class="preloader" width="34px">');
            },
            data: {id: clickedbtn.attr('cid')},
            success: function (data) {
                $('.preloader').remove();
                $('.additional_persons ul').append('' +
                    '<li class="list-group-item">' +
                    data[0].userSurname + ' ' + data[0].userName + ' ' + data[0].userPatronymic + ' (' + data[0].availableLanguage + ' )' +
                    '<button type="button" class="close remove_person"><span aria-hidden="true">&times;</span></button>' +
                    '<input type="hidden" name="Tender[procuringEntity][additionalContactPoints][' + AdditionalPersonCount + '][name]" value="' + data[0].userSurname + ' ' + data[0].userName + ' ' + data[0].userPatronymic + '">' +
                    '<input type="hidden" name="Tender[procuringEntity][additionalContactPoints][' + AdditionalPersonCount + '][name_en]" value="' + data[0].userName_en + ' ' + data[0].userSurname_en + '">' +
                    '<input type="hidden" name="Tender[procuringEntity][additionalContactPoints][' + AdditionalPersonCount + '][availableLanguage]" value="' + data[0].availableLanguage + '">' +
                    '<input type="hidden" name="Tender[procuringEntity][additionalContactPoints][' + AdditionalPersonCount + '][telephone]" value="' + data[0].telephone + '">' +
                    //'<input type="hidden" name="Tender[procuringEntity][additionalContactPoints][' + AdditionalPersonCount + '][url]" value="' + data[0].url + '">' +
                    '</li>');
                AdditionalPersonCount++;
            }
        });

        return false;
    });

    $('body').on('click', '.remove_person', function () {
        if (confirm('Ви впевненi, що хочете видалити?')) {
            $(this).parent().remove();
        }

    })
}

function SetMinimalStepProcent() {

    if ($('.tender_type_select').val() == 1) {

        $('body').on('keyup', '#minimalstepvalue-amount:visible', function () {

            var amount = parseFloat($('#value-amount').val());
            var amountMinimalStep = parseFloat($('#minimalstepvalue-amount').val());
            var amountMinimalStepProcent = parseFloat($('#minimalstepvalue-amountprocent').val());

            if (!isNaN(amount)) {
                var procent = (amount / 100);
                procent = amountMinimalStep / procent;
                procent = (Math.round(parseFloat(procent) * 100) / 100).toString();
                if (!isNaN(procent)) {
                    $('#minimalstepvalue-amountprocent').val(procent);
                }
            }
        })

        $('body').on('input', '#minimalstepvalue-amountprocent:visible', function () {

            var amount = parseFloat($('#value-amount').val());
            var amountMinimalStep = parseFloat($('#minimalstepvalue-amount').val());
            var amountMinimalStepProcent = parseFloat($('#minimalstepvalue-amountprocent').val());

            if (!isNaN(amount)) {
                var value = (amount / 100) * amountMinimalStepProcent;
                value = (Math.round(parseFloat(value) * 100) / 100).toString();
                if (!isNaN(value)) {
                    $('#minimalstepvalue-amount').val(value);
                }
            }
        })

        $('body').on('keyup', '#value-amount:visible', function () {

            var amount = parseFloat($('#value-amount').val());
            var amountMinimalStep = parseFloat($('#minimalstepvalue-amount').val());
            var amountMinimalStepProcent = parseFloat($('#minimalstepvalue-amountprocent').val());

            if (!isNaN(amount) && !isNaN(amountMinimalStep)) {
                var value = (amount / 100) * amountMinimalStepProcent;
                value = (Math.round(parseFloat(value) * 100) / 100).toString();
                if (!isNaN(value)) {
                    $('#minimalstepvalue-amount').val(value);
                }
            } else if (!isNaN(amount) && !isNaN(amountMinimalStepProcent)) {
                var procent = (amount / 100) * amountMinimalStep;
                procent = (Math.round(parseFloat(procent) * 100) / 100).toString();
                if (!isNaN(procent)) {
                    $('#minimalstepvalue-amountprocent').val(procent);
                }

            }
        })

    } else if ($('.tender_type_select').val() == 2) {

        $('.lot_amount_block:visible').each(function () {

            var lot_amount = $('.lot_amount:visible', this);
            var lot_step_amount = $('.lot_step_amount:visible', this);
            var lot_step_amount_procent = $('.lot_step_amount_procent:visible', this);


            $('body').on('keyup', '.lot_step_amount:visible', this, function () {

                var amount = parseFloat(lot_amount.val());
                var amountMinimalStep = parseFloat(lot_step_amount.val());
                var amountMinimalStepProcent = parseFloat(lot_step_amount_procent.val());

                if (!isNaN(amount)) {
                    var procent = (amount / 100);
                    procent = amountMinimalStep / procent;
                    procent = (Math.round(parseFloat(procent) * 100) / 100).toString();
                    if (!isNaN(procent)) {
                        lot_step_amount_procent.val(procent);
                    }
                }
            })

            $('body').on('keyup', '.lot_step_amount_procent:visible', this, function () {

                var amount = parseFloat(lot_amount.val());
                var amountMinimalStep = parseFloat(lot_step_amount.val());
                var amountMinimalStepProcent = parseFloat(lot_step_amount_procent.val());

                if (!isNaN(amount)) {
                    var value = (amount / 100) * amountMinimalStepProcent;
                    value = (Math.round(parseFloat(value) * 100) / 100).toString();
                    if (!isNaN(value)) {
                        lot_step_amount.val(value);
                    }
                }
            })

            $('body').on('keyup', '.lot_amount:visible', this, function () {

                var amount = parseFloat(lot_amount.val());
                var amountMinimalStep = parseFloat(lot_step_amount.val());
                var amountMinimalStepProcent = parseFloat(lot_step_amount_procent.val());

                if (!isNaN(amount) && !isNaN(amountMinimalStep)) {
                    var value = (amount / 100) * amountMinimalStepProcent;
                    value = (Math.round(parseFloat(value) * 100) / 100).toString();
                    if (!isNaN(value)) {
                        lot_step_amount.val(value);
                    }
                } else if (!isNaN(amount) && !isNaN(amountMinimalStepProcent)) {
                    var procent = (amount / 100) * amountMinimalStep;
                    procent = (Math.round(parseFloat(procent) * 100) / 100).toString();
                    if (!isNaN(procent)) {
                        lot_step_amount_procent.val(procent);
                    }

                }
            })

        })

    }

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

function ChooseCouse(){
    $('.negotiation_wrapper input[type=radio]').click(function () {
        if($(this).val() == 'quick'){
            $('.tender_method_select').val('limited_negotiation.quick');
        }
    })
}

function addGuaranteeBlockMultilot() {
    tenderType = $('.tender_type_select').val();
    tenderMethod = $('.tender_method_select').val();
    if ((tenderMethod == 'open_aboveThresholdUA') || (tenderMethod == 'open_aboveThresholdEU')) {
        if (tenderType == '1') {
            $('.guarantee_block').show();
            $('.guarantee_block_lot').hide();
        } else if (tenderType == '2') {
            $('.guarantee_block').hide();
            $('.guarantee_block_lot').show();
            SelectLotGuarantee();
        }
    } else {
        $('.guarantee_block_lot').hide();
    }
}