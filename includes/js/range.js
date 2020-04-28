$( function() {
    $( "#slider" ).slider({
        value:100,
        min: 50,
        max: 100,
        step: 1,
        slide: function( event, ui ) {
            $( "#id_goal" ).val( ui.value );
        }
    });
    $( "#id_goal" ).val($( "#slider" ).slider( "value" ) );
} );
