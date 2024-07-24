addScopeJS(['Climbs', 'main'], {});

Climbs.main = function (config = {}) {
  let $elf = this;

  $elf.config = {
    tabs: {
      selector: '.TabBar div',
    },
    add: {
      selector: '.add',
      remove: '.remove',
    },
    plan: {
      selector: '.add_plan',
      remove: '.remove_plan',
    },
    test: {
      selector: '#hello',
    },
  };
  overwriteDefaults(config, $elf.config);

  /**
   * Initializes the component by attaching event listeners to the specified DOM elements.
   *
   * @return {void}
   */
  $elf.init = function () {
    $(`${$elf.config.tabs.selector}`).on('click', dispatch.tabChange);
    $($elf.config.add.selector).on('click', dispatch.add);
    $($elf.config.add.remove).on('click', dispatch.remove);
    $($elf.config.plan.remove).on('click', dispatch.remove);
    $($elf.config.plan.selector).on('click', dispatch.add_plan);
    $($elf.config.test.selector).on('click', dispatch.test);
  };

  let dispatch = {
    test: function () {
      console.log('Hello World');
    },
    tabChange: function () {
      console.log('Hello World');
      var tabId = $(this).attr('tab-activate');
      $('main > div').removeClass('active');
      $('main > div.tab' + tabId).addClass('active');
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
      // remove it's parent
      $(this).closest('.input-container').remove();
    },
    add_plan: function (e) {
      e.preventDefault();
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
  return $elf;
};

export let ClimbsJS = Climbs.main;
