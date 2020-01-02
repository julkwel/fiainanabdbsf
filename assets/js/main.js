import ClassicEditor from '@ckeditor/ckeditor5-build-classic';

$(document).ready(function () {

    $('.datetime-picker').datetimepicker();

    $(document).on('change', '.custom-file-input', function (event) {
        $(this).next('.custom-file-label').html(event.target.files[0].name);
    });

    if (document.querySelector('#fiainana_description')) {
        ClassicEditor
            .create(document.querySelector('#fiainana_description'))
            .then(editor => {
                console.log(editor);
            })
            .catch(error => {
                console.error(error);
            });
    }

    $('#submit-fbdb').on('click', function () {
        var url = $(this).data('url');
        $.ajax(url,
            {
                data: {
                    message: $('#fbdb-contact').serializeArray()
                },
                method: 'POST',
                success: function () {
                    alert('Misaotra amin\'ny hafatra nalefanao')
                },
                err: function (err) {
                    alert('Misy olana ny fifandraisana amin\'izao fotoana izao');
                    console.log(err)
                }
            }
        )
    });

    $('.filtre-fiainana').on('click', function () {
        var search = $(this).val();
        var url = $(this).data('url');
        $.ajax(url,
            {
                data: {
                    search: search,
                },
                success: function (data) {
                    $('.content-block').html(data)
                },
                err: function (err) {
                    console.log(err)
                }
            }
        );
    });
    $('.table').DataTable();
});