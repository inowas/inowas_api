loadWhenReady = function(){
    var r=0, open_scenarios=false;
    $('#scenarios').find('.flip').click(function() {
        open_scenarios = !open_scenarios;

        if (open_scenarios) {
            r = 0;
            $( "#toolbox" ).fadeOut( "fast" );
            $( "#models_label" ).fadeOut( "fast" );
            $("#scenarios").stop().animate({left: r+'px'}, 800);
        }

        if (!open_scenarios){
            r = -500;
            $("#scenarios").stop().animate({left: r+'px'}, 800);
            $( "#toolbox" ).fadeIn( "fast" );
            $( "#models_label" ).fadeIn( "fast" );
        }
    });

    var h=0, open_models=false;
    $('#models_label').click(function() {
        open_models = !open_models;
        if (!open_models) {
            h = 0;
            $("#models").stop().animate({height: h+'px'}, 800);
        }

        if (open_models){
            h = $( window ).height()-40;
            $("#models").stop().animate({height: h+'px'}, 800);
        }
    });

    var h_results=0, open_results=false;
    $('#results_label').click(function() {
        I.results.initialize(I.model.baseModelId);
        open_results = !open_results;
        if (!open_results) {
            h_results = 0;
            $("#map").stop().animate({marginTop: h_results+'px'}, 800);
        }

        if (open_results){
            h_results = -($( window ).height()-40);
            $("#map").stop().animate({marginTop: h_results+'px'}, 800);
        }
    });

    $('.list-group-item.boundary_list_item').hover(
        function () {
            $(this).find('.tools_menu').show();
        },
        function () {
            $(this).find('.tools_menu').hide();
        }
    );
};