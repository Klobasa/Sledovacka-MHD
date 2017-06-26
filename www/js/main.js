$(function () {


    $(document).ready(function () {

//        $('#frm-insertForm-line').change(function () {
//        $('#frm-insertForm-line').on('change blur click', function () {
        $('body').on('change', '#frm-insertForm-line', function () {



//            alert($(this).closest('optgroup').attr('label'));
            var typeOfTransport = $(this).find(':selected').attr('data-vozidlo');
            console.log(typeOfTransport);

            $('#typVozu [value="' + typeOfTransport + '"]').prop('checked', true);
            if (typeOfTransport == 0) {
                $('#typVozu [value="1"], #typVozu [value="2"], #typVozu [value="3"]').prop('checked', false);
            }

        });



    });

});
