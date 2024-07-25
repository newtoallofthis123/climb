addScopeJS(['Climb', 'main'], {});
addScopeJS(['Climb', 'active'], {});

Climb.main = function (config = {}) {
  let $elf = this;

  $elf.config = {
    what: '.ClimbUi',
    tabs: {
      container: '.TabBar',
      selector: '.tabBtn',
      class: 'tabBtn',
    },
  };
  overwriteDefaults(config, $elf.config);

  /**
   * Initializes the component by attaching event listeners to the specified DOM elements.
   *
   * @return {void}
   */
  $elf.init = function () {
    console.group('Climb Editor');
    console.log('Initializing...');
    $($elf.config.what).on('add.climb', dispatch.add);
    $($elf.config.what).on('remove.climb', dispatch.remove);
    $($elf.config.what).on('add-plan.climb', dispatch.add_plan);
    $($elf.config.what)
      .find($elf.config.tab.container)
      .on('tab.climb', dispatch.tab.change);
  };

  let dispatch = {
    tab: {
      change: function (e) {
        console.log('tab change');
        // Grab current plugin instance container
        let current = $(e.target).closest($elf.config.what);
        let new_tab = null;
        let new_tab_btn = null;
        let all_tabs = current
          .find($elf.config.tabs.container + ' > main')
          .children();

        current
          .find($elf.config.tabs.container + ' > ' + tabs.selector)
          .removeClass('active');

        //is e.target already .tabBtn ?
        //okay makes sense
        if ($(e.target).hasClass($elf.config.tabs.selector)) {
          new_tab_btn = $(e.target);
        } else {
          new_tab_btn = $(e.target).closest($elf.config.tabs.selector);
        }

        let tab_index = new_tab_btn.index();

        // Set current .TabForm > main"s nth child to active after removing it from others
        new_tab = all_tabs.eq(tab_index);
        all_tabs.removeClass('active');
        new_tab.addClass('active');
      },
    },
    add: function (e) {
      e.preventDefault();
      console.log('add');
      let parent = $(this).closest('.inputs');
      let name = parent.attr('id');
      let container = $('<div>', { class: 'input-container' });
      let id = parent.find('input').last().attr('name');
      if (!id) {
        id = name + '0';
      } else {
        id = id.substring(id.length - 1);
        id = parseInt(id) + 1;
        id = name + id;
      }
      let input = $('<input>', { type: 'text', name: id });
      let removeButton = $('<button>', { type: 'button', class: 'remove' });
      removeButton.append($('<i>', { class: 'bi bi-x' }));

      container.append(input, removeButton);
      parent.append(container);
    },
    remove: function (e) {
      e.preventDefault();
      console.log('remove');
      // remove it"s parent
      $(this).closest('.input-container').remove();
    },
    add_plan: function (e) {
      e.preventDefault(); // wont be needed with intent action delegation (interface does this for u)
      // fair // i guess it is still needed until we do the debug step...maybe
      console.log('add');
      let parent = $(this).closest('.plan_inputs');
      let name = parent.attr('id');
      let container = $('<div>', { class: 'input-container' });
      let id = parent.find('input').last().attr('name');
      if (!id) {
        id = name + '0';
      } else {
        id = id.substring(id.length - 1);
        id = parseInt(id) + 1;
        id = name + id;
      }
      let input = $('<input>', { type: 'text', name: id + '-quantity' });
      let otherInput = $('<input>', { type: 'text', name: id + '-units' });
      let removeButton = $('<button>', {
        type: 'button',
        class: 'remove_plan',
      });
      removeButton.append($('<i>', { class: 'bi bi-x' }));
      container.append(input, otherInput, removeButton);
      parent.append(container);
    },
  };

  $elf.init();
  console.log('Finished.');
  console.groupEnd('Climb Editor');
  return $elf;
};

export let ClimbJS = Climb.main;
