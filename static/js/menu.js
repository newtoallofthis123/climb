/**
 * onReadyHandle(element, selector, markup)
 * A function to give AJAX induced DOM mutations the same benefits as document ready
 * 
 * Sometimes plugins have special delta functions separate from initialization -- in those
 * cases you will want to specialize this function with that updater function and leave init in the normal 
 * document ready.
 * 
 * @var element The element being altered
 * @var selector The selector (a query selector or a function) describing which DOM elements to alter
 * @markup the resulting payload of an Intent call. Usually HTML markup, could technically be JSON, raw text, etc
 * 
 */
let onReadyHandle = function(element,selector,markup){
	// We place things that would normally go into doc ready into onReadyHandle
	// This allows us to initialize blocks updated by arbitrary JS

	$(element).find(".Interface").each( function(instance, Markup){ 
		let api = "/service/api.json";
		// Check if Markup has data-api attribute
		if($(Markup).attr("data-api"))
			api = $(Markup).attr("data-api");

		Markup.Interface=new Interface({Markup: Markup, api: api});
	});

	// Normal document ready stuff, but relative to el
	// el.find(...).myPlugin(..)
	// ...

    $(element).find('ul.Toolbar > li > .visual').click(function () {
        $('ul.Toolbar > li').removeClass('menu-active');
        $(this).parent().addClass('menu-active');
    });

    $(element).find('ul.Toolbar li .visual').click(function () {
        if ($(this).parent().find('ul').length > 0) {
            $('.breadcrumbs').html('');
            $('ul.Toolbar li').removeClass('active');
            $(this).parent().addClass('active');
            $('ul.Toolbar li').removeClass('selected');
            $(this).parents('li').addClass('selected');
            var selectedEle = '';
            $(this)
                .parents('li')
                .each(function () {
                    if (!$(this).hasClass('active')) {
                        selectedEle += "<li><div class='visual'>";
                        selectedEle += $(this).find('.visual').html();
                        selectedEle += '</div></li>';
                    }
                });
            selectedEle += "<li><div class='visual'>";
            selectedEle += $(this).html();
            selectedEle += '</div></li>';
            $('.breadcrumbs').append(selectedEle);
            var selectedOption = $(this).children('label').text();
            $('#menuButtonText').text(selectedOption);
        }
    });

    $(element).find('.LiveMenu .breadcrumbs').on('click', 'li', function (e) {
        $('.LiveMenu .breadcrumbs li').removeClass('active');
        e.stopPropagation();
        $(this).addClass('active');
    });
    $(element).find('#menuButton').click(function (e) {
        e.stopPropagation();
        $('.breadcrumbs').slideToggle();
        $(this).toggleClass('collapsed');
    });
    $(element).find('.backBtn').click(function (e) {
        if ($('li.active').parent('ul').parent('li').length > 0) {
            $('li.active')
                .parent('ul')
                .parent('li')
                .children('.visual')
                .trigger('click');
        }
    });
};


$(document).ready(function () {
    onReadyHandle(document, null, null);
});