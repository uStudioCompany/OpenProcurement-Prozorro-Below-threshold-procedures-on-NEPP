var web_path = '/';

$(document).ready(function () {
    ChangePrequalification();
    AddCouse();
    SetUploadQualificationFile();
    AfterValidateForm();
})

function AfterValidateForm() {

    $('.btn-submitform_qualification,.btn-submitform_award').click(function () {
        var form = $(this).closest('form');
        form.off('beforeSubmit');
        form.on('afterValidate', function () {
            if ($('#tender_method').val() != 'limited_reporting') {
                if (form.find('.prequalification_file').length == 0) {
                    form.find('.ajax-file-upload-container').html('Необхiдно завантажити файл протоколу.');
                    form.on('beforeSubmit', function (event, jqXHR, settings) {
                        return false;
                    })
                }
            }
        })
    })
}


function ChangePrequalification() {
    $('.choose_prequalification').change(function () {
        if ($(this).val() == 'active') {

            $(this).parent().find('.active').show();
            $(this).parent().find('.unsuccessful').hide();

        } else if ($(this).val() == 'unsuccessful') {
            $(this).parent().find('.unsuccessful').show();
            $(this).parent().find('.active').hide();
        }
    })
}

function AddCouse() {
    $('#qualifications-cause input, #award-cause input').click(function () {
        if ($(this).is(':checked')) {
            var textbox = $(this).closest('.unsuccessful').find('textarea');
            var textboxText = textbox.text();
            textbox.text(textboxText + ' ' + $(this).val());
        }
    })
}

function SetUploadQualificationFile() {
    $('.uploadfile').each(function () {
        setUploadObj($(this));
    })
}

function setUploadObj(obj) {

    var f_button = $(obj);
    var count = 0;


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
        uploadStr: "Додати файл",
        formData: {tenderId: $('#tender_id').val()},
        onSubmit: function (files) {
            f_button.before('<img src="' + web_path + '/img/spiner.gif" class="preloader" width="34px">');
        },
        onSuccess: function (file, response) {
            $('.preloader').remove();
            response = $.parseJSON(response);

            $('.prequalification_file').remove();
            $('.ajax-file-upload-container').html('');
            f_button.after(
                '<div class="row prequalification_file">' +
                '<div class="col-md-4">' + response.model.name + '</div>' +
                '<div class="col-md-4"><input type="hidden" class="form-control file_original_name" name="documents[' + count + '][title]" value="' + response.model.name + '"></div>' +
                '<input class="file_original_name" type="hidden" name="documents[' + count + '][original_name]" value="' + response.model.name + '">' +
                '<input type="hidden" class="real_name" name="documents[' + count + '][realName]" value="' + response.newName + '">' +
                '</div>');

        }
    });


}