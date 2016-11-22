var web_path = '/';

var files;

$(document).ready(function () {

    AwardComplaints();
    TenderContract();
    DeleteContractFile();
    SetActivePanel();
    ChangeDataInAwardField();
    setUploadContract($('.awarduploadfile_active')) // для дозагрузки после победы
    $('#lot_id_select').change();
    //SetReplaceFile();
    //AddCouse();

    $('.btn-award').click(function (event) { // нажатие на кнопку - выпадает модальное окно
        event.preventDefault();
        var clickedbtn = $(this);
        var TenderID = clickedbtn.data("tender_id");
        var AwardID = clickedbtn.data("award_id");
        var Type = clickedbtn.data("type");

        var url = web_path + 'buyer/tender/award_form/' + TenderID;


        var modalContainer = $('#form-modal');
        var modalBody = modalContainer.find('.modal-body');
        modalBody.html('<img src="' + web_path + 'img/spiner.gif" style="margin: 20px auto;display: block;">');
        modalContainer.modal({show: true});
        $.ajax({
            url: url,
            type: "GET",
            data: {'award_id': AwardID, 'type': Type},
            success: function (data) {
                modalBody.html(data);
                modalContainer.modal({show: true});
            }
        });
    });

    $('.counted_amount').click(function (event) {
        var counted_history = $(this).next().html();
        var modalContainer = $('#form-modal');
        var modalBody = modalContainer.find('.modal-body');
        modalBody.html(counted_history);
        modalContainer.modal({show: true});
    });

    //$('input[type=file]').change(function(){
    //   files = this.files;
    //});

});

function AwardComplaints() {

    $('.award_complaints_btn').click(function (event) {
        var award_complaints = $(this).parent().next().find('div').html();
        var modalContainer = $('#complaints-modal');
        var modalBody = modalContainer.find('.modal-body');
        modalBody.html(award_complaints);
        modalContainer.modal({show: true});
        return false;
    });

    //$('#complaints-modal').on('hidden.bs.modal', function () {
    //$.pjax.reload({
    //    skipOuterContainers:true,
    //    container: '#pjax_awards_complaints'
    //});
    //})

    //$('body').on('click', '.btn_submit_award_complaint', function () {
    //
    //    var textLength = $(this).closest('form').find('textarea').val().length;
    //    if(textLength < 30){
    //        alert('Вiдповiдь повинна бути не меньш нiж 30 символiв.');
    //        return false;
    //    }
    //
    //    var tenderId = $(this).attr('t_id');
    //    var button = $(this);
    //    button.button('loading');
    //    var answer = button.closest('form').find('.answer_text').val();
    //
    //    $.ajax({
    //        url: web_path + 'tender/complaints/' + tenderId,
    //        type: 'POST',
    //        data: button.closest('form').serialize() + '&id=' + tenderId,
    //        success: function (data) {
    //            var answerBlock = '<div class="answer"><h4>Ваша вiдповiдь:</h4><h4>' + answer + '</h4></div>';
    //            button.closest('form').replaceWith(answerBlock);
    //        },
    //        error: function (jqXHR, textStatus, errorThrown) {
    //            alert('Виникла помилка сервера. Спробуйте пiзнiше.');
    //            button.button('reset');
    //            console.log('ОШИБКИ AJAX запроса: ' + textStatus);
    //        }
    //    });
    //    return false;
    //})

    // $('body').on('click', '.btn_submit_award_answer_complaint', function () {
    //
    //     var textLength = $(this).closest('form').find('textarea').val().length;
    //     if(textLength < 30){
    //         alert('Вiдповiдь повинна бути не меньш нiж 30 символiв.');
    //         return false;
    //     }
    //
    //     var button = $(this);
    //     button.button('loading');
    //     // $.ajax({
    //     //     url: document.location.href,
    //     //     type: 'POST',
    //     //     data: button.closest('form').serialize(),
    //     //     success: function (data) {
    //
    //             $.ajax({
    //                 url: document.location.href,
    //                 type: 'POST',
    //                 data: button.closest('form').serialize() + '&type=tendererAction',
    //                 success: function (data) {
    //                     // console.log(data);
    //                     document.location.reload();
    //                 },
    //                 error: function (jqXHR, textStatus, errorThrown) {
    //                     alert('Виникла помилка сервера. Спробуйте пiзнiше.');
    //                 }
    //             });
    //
    //     //     },
    //     //     error: function (jqXHR, textStatus, errorThrown) {
    //     //         alert('Виникла помилка сервера. Спробуйте пiзнiше.');
    //     //         //button.button('reset');
    //     //         //console.log('ОШИБКИ AJAX запроса: ' + textStatus);
    //     //     }
    //     // });
    //
    //     //var tenderId = $(this).attr('t_id');
    //     //var button = $(this);
    //     //button.button('loading');
    //     //var answer = button.closest('form').find('.answer_text').val();
    //     //
    //     //$.ajax({
    //     //    url: web_path + 'tender/complaints/' + tenderId,
    //     //    type: 'POST',
    //     //    data: button.closest('form').serialize() + '&id=' + tenderId,
    //     //    success: function (data) {
    //     //        var answerBlock = '<div class="answer"><h4>Ваша вiдповiдь:</h4><h4>' + answer + '</h4></div>';
    //     //        button.closest('form').replaceWith(answerBlock);
    //     //    },
    //     //    error: function (jqXHR, textStatus, errorThrown) {
    //     //        alert('Виникла помилка сервера. Спробуйте пiзнiше.');
    //     //        button.button('reset');
    //     //        console.log('ОШИБКИ AJAX запроса: ' + textStatus);
    //     //    }
    //     //});
    //     return false;
    // })

}

function TenderContract() {

    $('.tender_contract_btn').click(function (event) {
        var modalContainer = $('#contract-modal');
        modalContainer.modal({show: true});
        var contractForm = $(this).parent().next().html();
        var modalBody = modalContainer.find('.modal-body');
        modalBody.html(contractForm);

        setUploadContract($('.modal-body .uploadcontract'));
        var pjaxContainerId = $(this).closest('td').find('form').parent().attr('id');
        //formId = $('#'+pjaxContainerId).find('form').attr('id');
        formId = $(this).closest('td').find('form').attr('id');
        DateFormat(formId);

        //$('#contract-modal #'+formId).find('.replace_file').each(function () {
        //    $(this).uploadFile({
        //        url: web_path + 'tender/fileupload',
        //        fileName: 'FileUpload[file]',
        //        dragDrop: false,
        //        maxFileCount: 1,
        //        multiple: false,
        //    });
        //})

        //setTimeout("$('.replace_file, .uploadcontract').parent('div').css({'height':'auto','width':'115px'});$('#contract-modal #'+formId).find('.replace_file').each(function (){UploadFileReplace($(this));});", 300);
        //console.log(formId);
        setTimeout("$('#contract-modal #'+formId).find('.replace_file').each(function (){UploadFileReplace($(this),$(this));});", 300);

        //$('#contract-modal').off('hidden.bs.modal').on('hidden.bs.modal', function () {
        //    if ($('#' + pjaxContainerId).length) {
        //$.pjax.reload({
        //    skipOuterContainers: true,
        //    container: '#' + pjaxContainerId,
        //});
        //}
        //})

    });

    $('body').on('click', '.btn_submit_award_contract', function () {

        var button = $(this);
        var tenderId = button.attr('t_id');
        $(this).closest('form').find('.c_doc_example .contract_document_type_select:first').remove();
        //button.button('loading');

        $.ajax({
            url: web_path + 'buyer/tender/contract/' + tenderId,
            type: 'POST',
            data: button.closest('form').serialize(),
            success: function (data) {
                button.closest('form').replaceWith('<h2 class="jumbotron">Iнформацiя незабаром буде оновлена.</h2>');
                document.location.reload();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                button.closest('form').replaceWith('<h2 class="jumbotron">Виникла помилка сервера. Спробуйте пiзнiше.</h2>');
            }
        });
        return false;
    })

    $('body').on('click', '.btn_submit_award_contract_activate', function () {

        var validateErrors = 0;
        $('.contract_info input:visible').each(function () {
            if ($(this).val() == '') {
                $(this).closest('.form-group').addClass('has-error');
                validateErrors++;
            } else {
                $(this).closest('.form-group').removeClass('has-error');
            }
        })
        if (parseFloat($('.contract_value_amount:visible').attr('rel')) < parseFloat($('.contract_value_amount:visible').val())) {
            $('.contract_value_amount:visible').closest('.form-group').addClass('has-error');
            validateErrors++;
        }

        if (validateErrors > 0) return false;


        if (confirm('Контракт буде активовано. Редагувати документи буде неможливо.')) {

            var button = $(this);
            var tenderId = button.attr('t_id');
            var activate = button.attr('activate');
            $(this).closest('form').find('.c_doc_example .contract_document_type_select:first').remove();
            button.button('loading');

            $.ajax({
                url: web_path + 'buyer/tender/contract/' + tenderId,
                type: 'POST',
                data: button.closest('form').serialize() + '&activate=' + activate,
                success: function (data) {
                    button.closest('form').replaceWith('<h2 class="jumbotron">Iнформацiя незабаром буде оновлена.</h2>');
                    document.location.reload();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log(jqXHR.responseText);
                    button.closest('form').replaceWith('<h2 class="jumbotron">Виникла помилка сервера. Спробуйте пiзнiше.</h2> ' + jqXHR + textStatus + errorThrown);
                }
            });
        }
        return false;
    })

}

function setUploadContract(obj) {

    var f_button = $(obj);
    if ($('.contract_file_block').length) {
        var count = $('.contract_file_block').size();
    } else {
        var count = 0;
    }


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
        showCancel: false,
        uploadStr: "Додати документ",
        formData: {tenderId: $('#tender_id').val()},
        onSubmit: function (files) {
            f_button.before('<img src="' + web_path + '/img/spiner.gif" class="preloader" width="34px">');
        },
        onSuccess: function (file, response) {
            $('.preloader').remove();
            response = $.parseJSON(response);


            if (f_button.hasClass('awarduploadfile_active')) {

                $('.award_active_document_block').append('' +
                    '<div class="margin_b_20 row contract_file_block panel-body">' +
                    '<div class="col-md-4"><input type="text" class="form-control file_original_name" name="documents[' + count + '][title]" value="' + response.model.name + '"></div>' +
                    '<input class="file_original_name" type="hidden" name="documents[' + count + '][original_name]" value="' + response.model.name + '">' +
                    // '<div class="col-md-4">' + typeSelect + '</div>' +
                    '<button type="button" class="btn btn-default delete_file" del="' + response.newName + '">Видалити</button>' +
                    // '<div class="replace_file_wrap"><a role="button" class="btn btn-warning col-md-2 replace_file" del="' + response.newName + '" href="javascript:void(0)"></a></div>' +
                    '<input type="hidden" class="real_name" name="documents[' + count + '][realName]" value="' + response.newName + '">' +
                    '</div>');

            } else {

                var typeSelect = $('.contract_document_type_select')[0].outerHTML;
                typeSelect = typeSelect.replace('__CONTRACT_DOC__', count);

                $('.modal_document_block').append('' +
                    '<div class="margin_b_20 row contract_file_block panel-body">' +
                    '<div class="col-md-4"><input type="text" class="form-control file_original_name" name="documents[' + count + '][title]" value="' + response.model.name + '"></div>' +
                    '<input class="file_original_name" type="hidden" name="documents[' + count + '][original_name]" value="' + response.model.name + '">' +
                    '<div class="col-md-4">' + typeSelect + '</div>' +
                    //'<button type="button" class="btn btn-default delete_file" del="' + response.newName + '">Видалити</button>' +
                    '<div class="replace_file_wrap"><a role="button" class="btn btn-warning col-md-2 replace_file" del="' + response.newName + '" href="javascript:void(0)"></a></div>' +
                    '<input type="hidden" class="real_name" name="documents[' + count + '][realName]" value="' + response.newName + '">' +
                    '</div>');
                $('.contract_document_type_select:last').show();

                $('.replace_file').each(function () {
                    UploadFileReplace($(this));
                });
                setTimeout("$('.replace_file').parent().css({'display':'block'});", 10);
                count++;
                $('.contract_buttons_block').removeClass('hidden');
            }

        }
    });

}
function UploadFileReplace(obj, f_button) {

    if (obj) {

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
            maxFileSize: 52428800,
            showAbort: false,
            uploadStr: "Замiнити",
            onSubmit: function (files) {
                f_button.before('<img src="' + web_path + '/img/spiner.gif" class="preloader" width="34px">');
            },
            formData: {tenderId: $('#tender_id').val()},
            //onSubmit: function (file, ext) {
            //    this.settings.data = {tenderId: $('#tender_id').val()};
            //},
            onSuccess: function (file, response) {
                $('.preloader').remove();
                response = $.parseJSON(response);

                var activePanel = $('.panel-body.active');

                activePanel.find($('.file_original_name')).val(response.model.name);
                activePanel.find($('.file_name')).val(response.model.name);
                activePanel.find($('.delete_file')).attr('del', response.newName);
                activePanel.find($('.real_name')).val(response.newName);
                activePanel.find('.download_link a').remove();
            }
        });
    }
}

function DeleteContractFile() {
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
            $(this).closest('.row').remove();
        }

    })


}

function SetActivePanel() {
    $('body').on('click', '.replace_file', function () {
        //удаляем ранее загруженный файл
        if (confirm('Ви впевненi, що хочете замiнити файл?')) {

            var fileUrl = $(this).find('.replace_file').attr('del');
            if (fileUrl) {
                $.ajax({
                    type: "GET",
                    url: web_path + 'tender/filedelete?file=' + fileUrl,
                    cache: false,
                    //data: {'file': fileUrl},
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


function submitAward(obj) { // для отмены аварда

    //console.log(obj.file.files);
    var description = '';
    //if (obj.description) {
    //    description = obj.description.value;
    //    if (description.length && description.length > 900) {
    //        description = description.substring(0,900); }
    //    //console.log(description.length);
    //}
    var data = new FormData();
    if (obj.file) {
        var files = obj.file.files;
        if (files.length) {
            $.each(files, function (key, value) {
                data.append(key, value);
            });
        } else if (obj.type.value == '') {
            $(obj).find('.file').css({'border': '1px solid #F00'});
            return false;
        }
    }

    $(obj.btn_cancel).html('<img src="' + web_path + 'img/spiner_16.gif"> &nbsp; ' + $(obj.btn_cancel).text());
    //return false;

    data.append('description', description);
    data.append('tendersId', obj.tendersId.value);
    data.append('awardId', obj.awardId.value);
    data.append('type', obj.type.value);

    $.ajax({
        url: web_path + 'buyer/tender/award/' + obj.tendersId.value,
        type: 'POST',
        data: data,
        cache: false,
        dataType: 'html',
        processData: false, // Не обрабатываем файлы (Don't process the files)
        contentType: false, // Так jQuery скажет серверу что это строковой запрос
        success: function (html) {
            $('#form-modal').find('.modal-body').html(html);
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log('ОШИБКИ AJAX запроса: ' + textStatus);
        }
    });

    return false;
}

function DateFormat(formId) {
    var tender_method = $('#tender_method').val();

    $(".picker").datetimepicker({
        locale: $('#current_locale').val(),
        format: "DD/MM/YYYY HH:mm",
        minDate: new Date(),
        keepInvalid: true
    });


        var min_date = ComareDateFormat($('#' + formId + ' .complaintPeriodEnd').val(), '+1');


    $(".picker_date_signed").datetimepicker({
        locale: $('#current_locale').val(),
        format: "DD/MM/YYYY HH:mm:ss",
        minDate: min_date,
        maxDate: new Date(),
        defaultDate: ComareDateFormat($('#' + formId + ' .complaintPeriodEnd').val(), '+1'),
        keepInvalid: true
    });
}
function ComareDateFormat(date, needSeconds) {

    if (date) {
        var parts = date.split("/");
        var time = parts[2].split(' ');
        var time = time[1].split(':');
        console.log(parseInt(time[2]) + 1);
        if (needSeconds != undefined) {//тут выводятся секунды с корректировкой, указанной в параметрах
            var timeObject = new Date(parts[2].substring(0, 4), parts[1] - 1, parts[0], time[0], time[1], time[2]);
            return timeObject.setSeconds(timeObject.getSeconds() + 1);
        } else {
            return new Date(parts[2].substring(0, 4), parts[1] - 1, parts[0], time[0], time[1]);
        }
    }

}

function ChangeDataInAwardField() {
    if ($('#lot_id_select').length) {
        $('.limited_award_form').each(function () {
            if ($(this)[0].id != $('#lot_id_select').val()) {
                $(this).hide();
                $(this).find(':input').prop('disabled', true);
            } else {
                $(this).show();
                $(this).find(':input').prop('disabled', false);
            }
        });
    } else {
        $('.limited_award_form').show();
    }
}

function changeSubmitValue(obj) {
    var obj = $(obj);
    var button = $('button[name="add_limited_avards"]');
    var value = button.attr('rel');
    var ext_val = obj.find('option:selected').text();
    button.text(value + ' ' + ext_val);
}

//function SetReplaceFile(){
//    $('.replace_file').each(function () {
//        UploadFileReplace($(this));
//    });
//}

//function AddCouse(){
//    $('body').on('click', '#award-cause input', function () {
//        if($(this).is(':checked')){
//            var textbox = $(this).closest('form').find('textarea');
//            console.log(textbox);
//            var textboxText = textbox.text();
//            textbox.text(textboxText +' '+ $(this).val());
//        }
//    })
//}