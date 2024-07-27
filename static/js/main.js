import { ClimbJS } from './climbs/climb.js';

addScopeJS(['Climb', 'active', {}]);

// The main entry point for the application
// Any and all javascript functions should be defined in the onReadyHandle function
// Approach is mapped to this wrapper function to allow for the same benefits as document ready
// This is a good place to put any global event handlers, or any other global functions

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
let onReadyHandle = function (element, selector, markup) {
  // this sets up any .Interface found inside the block changed by server responses
  // including doc ready
  $(element)
    .find('.Interface')
    .each(function (_, Markup) {
      let api = '/server.php';
      // Check if Markup has data-api attribute
      if ($(Markup).attr('data-api')) api = $(Markup).attr('data-api');

      Markup.Interface = new Interface({ Markup: Markup, api: api });
    });

  // account for when the element itself is the Interface
  if ($(element).hasClass('Interface')) {
    let api = '/service/api.json';
    if ($(element).attr('data-api')) api = $(element).attr('data-api');
    element.Interface = new Interface({ Markup: element, api: api });
  }

  // Normal document ready stuff, but relative to el
  // el.find(...).myPlugin(..)
  // ...

  $(element)
    .find('ul.Toolbar > li > .visual')
    .click(function () {
      $('ul.Toolbar > li').removeClass('active');
      $(this).parent().addClass('active');
    });

  $(element)
    .find('ul.Toolbar li .visual')
    .click(function () {
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
        // $('.breadcrumbs').append(selectedEle);
        var selectedOption = $(this).children('label').text();
        $('#menuButtonText').text(selectedOption);
      }
    });

  // $(element)
  //   .find('.Oyster .breadcrumbs')
  //   .on('click', 'li', function (e) {
  //     $('.Oyster .breadcrumbs li').removeClass('active');
  //     e.stopPropagation();
  //     $(this).addClass('active');
  //   });
  $(element)
    .find('#menuButton')
    .click(function (e) {
      e.stopPropagation();
      $('.breadcrumbs').slideToggle();
      $(this).toggleClass('collapsed');
    });
  $(element)
    .find('.backBtn')
    .click(function (e) {
      if ($('li.active').parent('ul').parent('li').length > 0) {
        $('li.active')
          .parent('ul')
          .parent('li')
          .children('.visual')
          .trigger('click');
      }
    });

  let x = {};
  console.log($(element));
  if ($(element).hasClass('ClimbUI')) {
    x = ClimbJS({ what: $(element) });
  }

  // // --> Add your custom event handlers here
  // $('.add').click(function (e) {
  //   e.preventDefault();
  //   console.log('add');
  //   let parent = $(this).closest('.inputs');
  //   let name = parent.attr('id');
  //   let container = $('<div>', { class: 'input-container' });
  //   let id = parent.find('input').last().attr('name');
  //   if (!id) {
  //     id = name + '0';
  //   } else {
  //     id = id.substring(id.length - 1);
  //     id = parseInt(id) + 1;
  //     id = name + id;
  //   }
  //   let input = $('<input>', { type: 'text', name: id });
  //   let removeButton = $('<button>', { type: 'button', class: 'remove' });
  //   removeButton.append($('<i>', { class: 'bi bi-x' }));

  //   container.append(input, removeButton);
  //   parent.append(container);
  // });

  // $('.remove').click(function (e) {
  //   e.preventDefault();
  //   console.log('remove');
  //   // remove it's parent
  //   $(this).closest('.input-container').remove();
  // });

  // $('.add_plan').click(function (e) {
  //   e.preventDefault();
  //   console.log('add');
  //   let parent = $(this).closest('.plan_inputs');
  //   let name = parent.attr('id');
  //   let container = $('<div>', { class: 'input-container' });
  //   let id = parent.find('input').last().attr('name');
  //   if (!id) {
  //     id = name + '0';
  //   } else {
  //     id = id.substring(id.length - 1);
  //     id = parseInt(id) + 1;
  //     id = name + id;
  //   }
  //   let input = $('<input>', { type: 'text', name: id + '-quantity' });
  //   let otherInput = $('<input>', { type: 'text', name: id + '-units' });
  //   let removeButton = $('<button>', {
  //     type: 'button',
  //     class: 'remove_plan',
  //   });

  //   $('.remove_plan').click(function (e) {
  //     e.preventDefault();
  //     console.log('remove');
  //     // remove it's parent
  //     $(this).parent().remove();
  //   });
  //   removeButton.append($('<i>', { class: 'bi bi-x' }));
  //   container.append(input, otherInput, removeButton);
  //   parent.append(container);
  // });

  // $('.TabBar div').click(function () {
  //   var tabId = $(this).attr('tab-activate');
  //   $('main > div').removeClass('active');
  //   $('main > div.tab' + tabId).addClass('active');
  //   // animateCSS('.' + tabId, 'fadeIn');
  // });
  // --> End DOM Ready stuff
};

$(document).ready(function () {
  Interface.prototype.RefreshComplete = onReadyHandle;
  onReadyHandle(document, null, null);
});
