addScopeJS(['Climb', 'main'], {});
addScopeJS(['Climb', 'active'], {});

Climb.main = function (config = {}) {
  let $elf = {};

  $elf.config = {
    what: '.ClimbUI',
    tabs: {
      container: '.TabBar',
      selector: '.tabBtn',
      class: 'tabBtn',
      all: '.Tabs',
    },
  };
  overwriteDefaults(config, $elf.config);

  /**
   * Initializes the component by attaching event listeners to the specified DOM elements.
   *
   * @return {void}
   */
  $elf.init = function () {
    let container = $($elf.config.what);
    console.group('Climb Editor');
    console.log('Initializing...');
    container.on('add.climb', dispatch.add);
    container.on('remove.climb', dispatch.remove);
    container.on('add-plan.climb', dispatch.add_plan);

    container.on('tab.climb', dispatch.tab.change);
  };

  let dispatch = {
    tab: {
      change: function (e) {
        console.log('tab change');
        // Grab current plugin instance container
        let current = $(e.target).closest($elf.config.what);
        let tabs_btns = current.find(
          $elf.config.tabs.container + ' > ' + $elf.config.tabs.selector
        );
        let all_tabs = current.find('main').children();
        let tab_index = tabs_btns.index(e.target);
        // Set current .TabForm > main"s nth child to active after removing it from others
        let new_tab = all_tabs.eq(tab_index);
        all_tabs.removeClass('active');
        new_tab.addClass('active');
      },
    },
    add: function (e) {
      console.log('add');
      let parent = $(e.target).closest('.inputs');
      let name = parent.attr('id');
      let input_container = $('<div>', { class: 'input-container' });
      let id = parent.find('input').last().attr('name');
      if (!id) {
        id = name + '0';
      } else {
        id = id.substring(id.length - 1);
        id = parseInt(id) + 1;
        id = name + id;
      }
      let input = $('<input>', { type: 'text', name: id });
      let removeButton = $('<button>', {
        type: 'button',
        class: 'remove control',
      });
      removeButton.attr('data-role', 'trigger');
      removeButton.attr('data-action', 'remove.climb');
      removeButton.append($('<i>', { class: 'bi bi-x' }));

      input_container.append(input, removeButton);
      parent.append(input_container);
    },
    remove: function (e) {
      console.log('remove');
      // remove it"s parent
      $(e.target).closest('.input-container').remove();
    },
    add_plan: function (e) {
      console.log('cool_add');
      let parent = $(e.target).closest('.plan_inputs');
      let name = parent.attr('id');
      let input_container = $('<div>', { class: 'input-container' });
      let id = parent.find('input').last().attr('name');
      if (!id) {
        id = name + '0';
      } else {
        id = id.split('-')[0];
        id = id.substring(id.length - 1);
        id = parseInt(id) + 1;
        id = name + id;
      }
      let input = $('<input>', { type: 'text', name: id + '-quantity' });
      let otherInput = $('<input>', { type: 'text', name: id + '-units' });
      let removeButton = $('<button>', {
        type: 'button',
        class: 'remove_plan control',
      });
      removeButton.attr('data-role', 'trigger');
      removeButton.attr('data-action', 'remove.climb');

      removeButton.append($('<i>', { class: 'bi bi-x' }));
      input_container.append(input, otherInput, removeButton);
      parent.append(input_container);
    },
  };

  $elf.init();
  console.log('Finished.');
  console.groupEnd('Climb Editor');
  return $elf;
};

export let ClimbJS = Climb.main;
