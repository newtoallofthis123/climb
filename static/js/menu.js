$('ul.Toolbar > li > .visual').click(function () {
    $('ul.Toolbar > li').removeClass('menu-active');
    $(this).parent().addClass('menu-active');
});
$('ul.Toolbar li .visual').click(function () {
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

$('.LiveMenu .breadcrumbs').on('click', 'li', function (e) {
    $('.LiveMenu .breadcrumbs li').removeClass('active');
    e.stopPropagation();
    $(this).addClass('active');
});
$(document).ready(function () {
    $('#menuButton').click(function (e) {
        e.stopPropagation();
        $('.breadcrumbs').slideToggle();
        $(this).toggleClass('collapsed');
    });
    $('.backBtn').click(function (e) {
        if ($('li.active').parent('ul').parent('li').length > 0) {
            $('li.active')
                .parent('ul')
                .parent('li')
                .children('.visual')
                .trigger('click');
        }
    });
});
