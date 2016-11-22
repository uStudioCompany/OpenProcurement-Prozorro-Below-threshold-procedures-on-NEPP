var web_path = '/';
var ajaxRunBFV = null;

$(document).ready(function () {
    DateFormat();
    // UploadFileReplace();
    // SetUploadFileReplace();
    // DeleteFile();
    SetActivePanel();
    SetVars();
    UploadFile();
    ChooseFeature();
    // ChooseDocuments();
    ShowConfedential();
    // BeforeValidateForm();
    GetAuctionUrl();
    AfterValidateBidForm();
    $('body').on('change', '.select_document_type', function () {ValidateDocument('.select_document_type')});
    $('body').on('change', '.select_document_level', function () {ValidateDocument('.select_document_level')});
    $('body').on('click', '#submit_bid', function () {AfterValidateBidFormProcedure('.select_document_type'); AfterValidateBidFormProcedure('.select_document_level')});
});
function AfterValidateBidForm() {
    $('#bid_form').on('beforeSubmit', function () {
        return (AfterValidateBidFormProcedure('.select_document_type') && AfterValidateBidFormProcedure('.select_document_level'));
    })
}
function ValidateDocument(elem) {
    $(elem).each(function () {
        parentBlock = $(this);
        if (parentBlock.val() == '') {
            parentBlock.css({"border": "1px solid red"});
        } else {
            parentBlock.css({"border": "1px solid green"});
        }
    });
}

//validate dropdownlist of bids document (type and level of bid document should be selected)!
function AfterValidateBidFormProcedure(elem) {
    ValidateDocument(elem);
    var i = 0;
    if ($('select').is(elem)) {
        $(elem).each(function () {
            if ($(this).val() == '') {
                i++;
            }
        });
    } else {
        return true;
    }
    if (i != 1){
        return false;
    } else {
        return true;
    }
}

function SetVars() {
    if ($('.panel-body:visible').length == 0) {
        ItemFilesCount = 0;
    } else {
        ItemFilesCount = parseInt($('.panel-body:visible').length);
    }

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
            f_button.before('<img src="' + web_path + '/img/spiner.gif" class="preloader" width="34px">');
        },
        uploadStr: "Додати документ",
        dynamicFormData: function () {
            var data = {tenderId: $('#tender_id').val()}
            return data;
        },
        onSuccess: function (file, response) {
            $('.preloader').remove();
            response = $.parseJSON(response);

            var new_elem = $('.bids_document_block .panel-body:first').clone();
            console.log(new_elem);
            f_button.prev().append(new_elem);
            new_elem.show();

            var activePanel = f_button.prev().find('.panel-body:last');
            console.log(activePanel);
            activePanel.find($('.file_original_name')).text(response.model.name);
            activePanel.find($('.file_name')).val(response.fileName);
            activePanel.find($('.delete_file')).attr('del', response.newName);
            activePanel.find($('.real_name')).val(response.newName);
            //проставляем name с следующим индексом
            activePanel.find('input, select, textarea').each(function () {


                //если прошла валидация родительских полей, то убираем ее
                var container = $(this).closest('.form-group');
                container.removeClass('has-error has-success');
                container.find('.help-block').text('');


                var name = $(this).attr('name');
                if (name) {
                    var arr = name.split(/__EMPTY_DOC__/);
                    name = arr[0] + ItemFilesCount + arr[1];
                    $(this).attr('name', name);

///////////////////////////////////////////////////////////////////
                    if ($(this).attr('id')) {
                        var oldId = $(this).attr('id');
                        var id = oldId.replace('__empty_doc__', ItemFilesCount);
                        //var id = oldId + ItemCount
                        $(this).attr('id', id);

                        var containerOldClass = container.attr('class');
                        console.log(containerOldClass);
                        containerClass = containerOldClass.replace('__empty_doc__', ItemFilesCount);
                        container.removeClass().addClass(containerClass);

                        var validateContainerArr = containerClass.split(' ');
                        var validateContainerClass = validateContainerArr[1];


                        //назначаем параметры валидации из родительских елементов
                        if ($('#bid_form').length > 0) {
                            var form = $('#bid_form');
                            var attributes = form.yiiActiveForm('find', oldId);

                            if (attributes) {
                                var patternValidatePattern = attributes.validate;
                            }
                        }
                        if ($('#bid_form').length > 0) {
                            var form = $('#bid_form');
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


//////////////////////////////////////////////////////////////////////////


                }
            })
            ItemFilesCount++;
            UploadFileReplace($('.panel-body:last').find('.uploadfile_replace'));
            // FillDocumentLinkItem();
            //проставляем первый елемент селекта
            $('.document_link select', activePanel).val($('.document_link select option:first', activePanel).val());
            //ChangeTypeSelect();
            DeleteFile();
        }
    });
}
function UploadFile() {
    //elLink = $('.uploadfile');

    $('.awarduploadfile, .uploadfile:visible').each(function () {
        setUploadObj($(this));
    })

    $('.uploadfile_replace:visible').each(function () {
        UploadFileReplace($(this));
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
            if (fileUrl) {
                $.ajax({
                    type: "GET",
                    url: web_path + 'tender/filedelete',
                    cache: false,
                    data: {'file': fileUrl},
                    success: function (data) {

                    }
                });
            }
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
function DateFormat() {
    $(".picker").datetimepicker({
        locale: $('#current_locale').val(),
        format: "DD/MM/YYYY HH:mm",
        //minDate: "now",
        //keepInvalid: true
    });
}
function ChooseFeature() {

    $('.bid_feature_select').change(function () {
        $(this).closest('.form-group').find('.bid_feature_value input').val($('option:selected', this).attr('rel'));
        $(this).closest('.form-group').find('.bid_feature_value input').blur();
    })

    $('.bid_feature_value input').each(function () {
        // alert((0.07*10).toFixed(10)*10);
        var coast = ($(this).val() * 10).toFixed(10) * 10;
        console.log(coast);
        if (coast <= 30) {
            $(this).val(coast);
            $(this).closest('.row').find('.bid_feature_select option[rel=' + $(this).val() + ']').prop('selected', true);
            if($(this).closest('.row').find('.bid_feature_select option').val() == ''){
                $(this).val('');
            }
        }

    })
}
function ShowConfedential(obj) {

    $('.confidentiality').each(function () {
        // console.log($(this));
        if ($(this).is(':checked')) {
            $(this).closest('.form-group').find('.confidentialityRationale').show();
        }
    })


    $('body').on('click', obj, function () {
        // console.log($(this));
        // console.log($(obj).is(':checked'));
        if ($(obj).is(':checked')) {
            $(obj).closest('.row').find('.confidentialityRationale').show()
        } else {
            $(obj).closest('.row').find('.confidentialityRationale').hide();
        }
    })
}
function BeforeValidateForm() {
    $('#bid_form').on('beforeSubmit', function () {
        var err = 0;
        $('.confidentiality').each(function () {
            // console.log($(this));
            if ($(this).is(':checked')) {
                var textarea = $(this).closest('.form-group').find('.confidentialityRationale textarea');
                if (parseInt(textarea.val().length) < 30) {
                    textarea.after('<div class="help-block">Необхiдно пiдтвердити</div>')
                    // alert(123);
                    err++;

                }
            }
        })
        console.log(err);
        if (err > 0) {
            return false;
        }
    })
}

function GetAuctionUrl() {
    $('.auction_seller_url').click(function () {

        var type = $(this).attr('type');
        var lotId = $(this).attr('lotId');
        var elem = $(this);

        $.ajax({
            type: "GET",
            url: web_path + 'seller/tender/updatebid',
            cache: false,
            data: {'id': $('#tender_id').val()},
            success: function (data) {
                if (data) {
                    data = JSON.parse(data);
                    console.log(data.data.status);
                    if (data.data.status != 'invalid') {
                        if (type == 'multi') {
                            for (var i in data.data.lotValues) {
                                if (lotId == data.data.lotValues[i].relatedLot) {
                                    window.open(
                                        data.data.lotValues[i].participationUrl + '&return_url=' + document.location.href,
                                        '_blank'
                                    );
                                    // document.location.href = data.data.lotValues[i].participationUrl;
                                }
                            }
                        } else {
                            window.open(
                                data.data.participationUrl + '&return_url=' + document.location.href,
                                '_blank'
                            );
                            // document.location.href = data.data.participationUrl;
                        }
                    } else {
                        elem.replaceWith('<span style="color: red">Ваша ставка дисквалiфiкована.</span>')
                    }
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                alert('Нет доступа к серверу.')
            }
        });

        return false;

    })
}

function bidFinancialViability($form,attribute,obj,bids,context,mess) {
    //
    //console.log(bids);
    //mess.push('ffffffffff');
    ajaxRunBFV = $.ajax(
        {
            url:'/seller/tender/bid-financial-viability',
            data: 'json='+JSON.stringify(bids),
            type: 'POST',
            dataType: 'json',
            context: context,
            beforeSend: function () {
                $('#bid_form .spinner').fadeIn(100);
            },
            complete: function () {
                $('#bid_form .spinner').fadeOut(100);
            },
            success: function (data) {
                //debugger;
                if (data.error) {
                    obj.data('error',1);
                    $($form).yiiActiveForm('updateAttribute',attribute.id,[data.message]);
                } else {
                    obj.data('error',0);
                }

                //attribute.validate(attribute);
                //updateInput($form, attribute, mess);

            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log('Error: ' + textStatus + ' | ' + errorThrown);
            }
        });
}


