var web_path = '/';

var files;

$(document).ready(function () {
    Complaints();
    SetActivePanel();

    DeleteContractFile();

});

function Complaints() {

    $('.uploadcontract').each(function () {
        setUploadContract($(this));
    })


    $('body').on('click', '.cancel_checkbox', function () {

        if($(this).is(':checked')){
            $(this).closest('form').find('.cancelation_block').show();
        }else{
            $(this).closest('form').find('.cancelation_block').hide();
        }
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
        uploadStr:"Додати файл",
        formData: {tenderId: $('#tender_id').val()},
        onSubmit: function (files) {
            f_button.before('<img src="' + web_path + '/img/spiner.gif" class="preloader" width="34px">');
        },
        onSuccess: function (file, response) {
            $('.preloader').remove();
            response = $.parseJSON(response);
            //var typeSelect = $('.contract_document_type_select')[0].outerHTML;
            //typeSelect = typeSelect.replace('__CONTRACT_DOC__', count);

            f_button.closest('.contract_file_block').append('' +
                '<div class="file_wrapper">'+
                '<div class="col-md-6 panel-body"></div>'+
                '<div class="col-md-6">' +
                '<div class="col-md-4"><input type="text" class="form-control file_original_name" name="documents[' + count + '][title]" value="' + response.fileName + '"></div>' +
                '<input class="file_original_name" type="hidden" name="documents[' + count + '][original_name]" value="' + response.model.name + '">' +
                //'<div class="col-md-4">' + typeSelect + '</div>' +
                    '<button type="button" class="btn btn-danger delete_file" del="' + response.newName + '">Видалити</button>' +
                // '<div class="replace_file_wrap"><a role="button" class="btn btn-warning col-md-2 replace_file" del="' + response.newName + '" href="javascript:void(0)"></a></div>' +
                '<input type="hidden" class="real_name" name="documents[' + count + '][realName]" value="' + response.newName + '">' +
                '</div></div>');
            //$('.contract_document_type_select:last').show();

            $('.replace_file').each(function () {
                UploadFileReplace($(this));
            });
            setTimeout("$('.replace_file').parent().css({'display':'block'});", 10);
            count++;
            //$('.contract_buttons_block').removeClass('hidden');
        }
    });

}
function UploadFileReplace(obj,f_button) {

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
            uploadStr:"Замiнити",
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
            $(this).closest('.file_wrapper').remove();
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