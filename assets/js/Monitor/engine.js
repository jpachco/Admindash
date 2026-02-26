$(document).ready(function() {
engine();

    $('body').on('click','#Contactos', function(){

        engine_btn("Contactos");


    });
    $('body').on('click','#Feedback', function(){

        engine_btn("Feedback");

    });
    $('body').on('click','#Tickets', function(){

        engine_btn("Tickets");

    });
    $('body').on('click','#PreClean', function(){

        engine_btn("PreClean");

    });
    $('body').on('click','#Getin', function(){

        engine_btn("Getin");

    });
    $('body').on('click','#Prueba', function(){

        engine_btn("Prueba");

    });




function engine(){
    var data = '';
    $.ajax({
        type: 'POST',
        url: '../../classes/Monitor/queries/consulta_monitor.php',
        data: data,
        success: function(respuesta){
            if (respuesta != '') {
                $('#content').append(respuesta);


            }else {
                swal('Ooops!!!', 'Presiona F5 para continuar', 'warning');
            }
        },
        error:function(){
            swal('Ooops!!!', 'Contacta a tu administrador', 'error');
        }
    });
}
});