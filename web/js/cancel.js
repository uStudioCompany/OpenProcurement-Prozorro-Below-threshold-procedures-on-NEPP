var web_path = '/';

$(document).ready(function () {
    DeleteFile();
/*
    $.ajax_upload( '.uploadfile:visible', {
        action: web_path + 'tender/fileupload',
        name: 'FileUpload[file]',
        data: {},
        onSubmit: function (file, ext) {
            this.settings.data = {tenderId: $('#tenders_id').val()};
        },
        onComplete: function (file, response) {
            response = $.parseJSON(response);
            //console.log(response);

            //var new_elem = $('#hidden_document_original').clone();
            //f_button.closest('.add_file_wrapper').prev().append(new_elem);
            //new_elem.show();

            var file_str = ($('#hidden_document_original').html()).replace(/__EMPTY_DOC__/gi,FilesCount);
            var file_item     = $('.document_block').append(file_str).find('.file_wrap:last');

            file_item.find($('.file_original_name')).text(response.model.name);
            file_item.find($('.file_name')).val(response.model.name);
            file_item.find($('.delete_file')).attr('del', response.newName);
            file_item.find($('.real_name')).val(response.newName);
            FilesCount++;
            UploadFileReplace($('.document_block:last').find('.uploadfile_replace:visible'));
        }
    });//*/

    var f_button = $('.uploadfile:visible');

    f_button.uploadFile({
        url: web_path + 'tender/fileupload',
        fileName: 'FileUpload[file]',
        dragDrop: false,
        multiple: false,
        showStatusAfterSuccess: false,
        showStatusAfterError: false,
        showFileCounter: false,
        showFileSize: false,
        maxFileSize: 52428800,
        showAbort: false,
        uploadStr:"Додати файл",
        onSubmit: function (files) {
            f_button.before('<img src="' + web_path + '/img/spiner_16.gif" class="preloader">');
        },
        dynamicFormData: function() {
            var data = {tenderId: $('#tenders_id').val()};
            return data;
        },
        onSuccess: function (file, response) {
            $('.preloader').remove();
            response = $.parseJSON(response);

            var file_str = ($('#hidden_document_original').html()).replace(/__EMPTY_DOC__/gi,FilesCount);
            var file_item     = $('.document_block').append(file_str).find('.file_wrap:last');

            file_item.find($('.file_original_name')).text(response.model.name);
            file_item.find($('.file_name')).val(response.fileName);
            file_item.find($('.delete_file')).attr('del', response.newName);
            file_item.find($('.real_name')).val(response.newName);
            FilesCount++;
            UploadFileReplace($('.file_wrap:last').find('.uploadfile_replace:visible'));
        }
    });

});

function DeleteFile() {
    $('body').on('click', '.delete_file', function () {
        if (confirm('Ви впевненi, що хочете видалити?')) {

            var fileUrl = $(this).parent().find('.delete_file').attr('del');
            console.log(fileUrl);

            $.ajax({
                type: "GET",
                url: web_path + 'tender/filedelete',
                cache: false,
                data: {'file': fileUrl},
                success: function (data) {

                }
            });
            $(this).closest('.file_wrap').remove();
        }

    })


}

function UploadFileReplace(obj) {

    if (obj.is(':visible')) {

        //*
        var f_button = $(obj);

        f_button.uploadFile({
            url: web_path + 'tender/fileupload',
            fileName: 'FileUpload[file]',
            dragDrop: false,
            multiple: false,
            showStatusAfterSuccess: false,
            showStatusAfterError: false,
            showFileCounter: false,
            showFileSize: false,
            maxFileSize: 52428800,
            showAbort: false,
            onSubmit: function (files) {
                f_button.before('<img src="' + web_path + '/img/spiner_16.gif" class="preloader">');
            },
            uploadStr:"Замiнити",
            dynamicFormData: function() {
                var data ={tenderId: $('#tenders_id').val()}
                return data;
            },
            onSuccess: function (file, response) {
                $('.preloader').remove();
                response = $.parseJSON(response);

                var activePanel = $('.panel-body.active');
                activePanel.find($('.file_original_name')).text(response.model.name);
                activePanel.find($('.file_name')).val(response.model.name);
                activePanel.find($('.delete_file')).attr('del', response.newName);
                activePanel.find($('.real_name')).val(response.newName);
            }
        });
        //*/
    }
}