document.addEventListener("DOMContentLoaded", function(event) {

  // -------------------------------------------------------------------- Helper
  function toArray(thing) {
    return Array.prototype.slice.call(thing);
  }

  function disableInput(input, value) {
    input.setAttribute('disabled', true);
    if (input.value !== undefined) {
      value = value || '';
      input.value = value;
      if (value === '') {
        input.removeAttribute('readonly');
      } else {
        input.setAttribute('readonly', 'readonly');
      }
    }
  }

  function enableInput(input, value) {
    input.removeAttribute('disabled');
    if (input.value !== undefined) {
      value = value || '';
      input.value = value;
      if (value === '') {
        input.removeAttribute('readonly');
      } else {
        input.setAttribute('readonly', 'readonly');
      }
    }
  }

  function disableOption(option) {
    option.setAttribute('disabled', true);
    option.setAttribute('hidden', true);
  }

  function enableOption(option) {
    option.removeAttribute('disabled');
    option.removeAttribute('hidden');
  }

  function hideBox(box) {
    box.classList.add('d-none');
  }

  function showBox(box) {
    box.classList.remove('d-none');
  }


  // ------------------------------------------------------- Form lists upadters
  function updateSelectiveInputs(attributes) {
    var items = document.getElementsByClassName('t4r-addtool-selective');
    var itemsBox = document.querySelector('.t4r-addtool-selective-box');
    var attributeCount = 0;

    toArray(items).forEach(function (container) {
      var inputs = container.getElementsByClassName('form-control');
      if (inputs.length > 0) {
        var pattern = RegExp('(^|,)' + inputs[0].id + '(,|$)');
        if (pattern.test(attributes)) {
          attributeCount += 1;
          container.classList.remove('d-none');
        } else {
          container.classList.add('d-none');
        }
      }
    });

    // check for null to support customer tool search
    if (itemsBox !== null) {
      if (attributeCount) {
        itemsBox.classList.remove('d-none');
      } else {
        itemsBox.classList.add('d-none');
      }
    }
  }

  function updateSubOptions(options) {
    var suboption = document.querySelector('#suboption');
    var defaultOption = suboption[0].cloneNode(true);

    suboption.innerHTML = '';
    suboption.appendChild(defaultOption);

    options.forEach(function (option) {
      var optionElement = document.createElement('option');
      optionElement.value = option;
      optionElement.textContent =
        option.charAt(0).toUpperCase().concat(option.slice(1));
      suboption.appendChild(optionElement);
    });
  }

  function updateSubTypes(validSubTypes) {
    var subtype = document.querySelector('#subtype');
    var options = toArray(subtype.getElementsByTagName('option'));

    options.forEach(function (option) {
      var pattern = RegExp('(^|,)' + option.value + '(,|$)');
      if (pattern.test(validSubTypes)) {
        enableOption(option);
      } else {
        disableOption(option)
      }
    });
  }

  function updatePowerSources(validPowerSources) {
    var powersource = document.querySelector('#powersource');
    var options = toArray(powersource.getElementsByTagName('option'));

    options.forEach(function (option) {
      var pattern = RegExp('(^|,)' + option.value + '(,|$)');
      if (pattern.test(validPowerSources)) {
        enableOption(option);
      } else {
        disableOption(option);
      }
    });
  }


  // ----------------------------------------------------------- Event Listeners
  var removeAccessory = function removeAccessory(id) {
    var accessories = document.querySelector('#accessories');
    var allAccessories = toArray(accessories.querySelectorAll('.accessory'));
    var nextAccessories = toArray(accessories.querySelectorAll(
      '.accessory[data-id="' + id +'"] ~ .accessory'
    ));

    accessories.removeChild(
      accessories.querySelector('.accessory[data-id="' + id +'"]')
    );

    nextAccessories.forEach(function updateId(accessory) {
      var oldIndex = parseInt(accessory.dataset.index, 10);
      var inputs = toArray(accessory.querySelectorAll('input'));

      accessory.dataset.index = oldIndex - 1;

      inputs.forEach(function (input) {
        input.setAttribute(
          'name',
            'accessories[' +
            accessory.dataset.index + '][' +
            input.dataset.name + ']'
        );
      });
    });
  }
  window.removeAccessory = removeAccessory;

  function addAccessory(e) {
    var accessories = document.querySelector('#accessories');
    var template = document.querySelector('#accessory-template');
    var accessory = template.content.querySelector('.accessory');
    var inputs = toArray(template.content.querySelectorAll('input'));

    var counter = parseInt(accessories.dataset.counter, 10);
    var index = accessories.querySelectorAll('.accessory').length;

    accessory.dataset.id = counter;
    accessory.dataset.index = index;

    inputs.forEach(function (input) {
      input.value = '';
      input.setAttribute(
        'name',
          'accessories[' +
          index + '][' +
          input.dataset.name + ']'
      );
    });

    accessories.appendChild(template.content.cloneNode(true));
    accessories.dataset.counter = counter + 1;

    var allAccessories = accessories.querySelectorAll('.accessory');
    allAccessories[allAccessories.length - 1]
      .querySelector('.accessory-btn-remove')
      .addEventListener('click', function (e) {
        removeAccessory(counter);
      });
  }

  function onSubTypeChange(e) {
    var typeSelected = document.querySelector('#type input:checked');
    var subtypeSelected = e.target.options[e.target.selectedIndex];
    var powerSourceSelected = document.querySelector(
      '#powersource option:checked'
    );
    var suboption = document.querySelector('#suboption');
    var options = [];

    if (subtypeSelected.dataset.options !== undefined) {
       options = subtypeSelected.dataset.options.split(',');

       if (suboption !== null) {
         updateSubOptions(options);
         enableInput(suboption);
       }
    }

    updateSelectiveInputs(
      subtypeSelected.dataset.attributes + ',' +
      typeSelected.dataset.attributes
    );

    var accessoriesBox = document.querySelector('#accessories-box');
    if (typeSelected.value === 'power') {
      if (accessoriesBox !== null) {
        enableInput(accessoriesBox);
        showBox(accessoriesBox);
      }
    } else {
      if (accessoriesBox !== null) {
        disableInput(accessoriesBox);
        hideBox(accessoriesBox);
      }
    }

    var batteriesBox = document.querySelector('#batteries-box');
    if (powerSourceSelected.value === 'cordless') {
      if (batteriesBox !== null) {
        enableInput(batteriesBox);
        showBox(batteriesBox);
      }
    } else {
      if (batteriesBox !== null) {
        disableInput(batteriesBox);
        hideBox(batteriesBox);
      }
    }
  }

  function onPowerSourceChange(e) {
    var type = document.querySelector('#type');
    var powerSourceSelected = e.target[e.target.selectedIndex];
    var subtype = document.querySelector('#subtype');

    var accessoriesBox = document.querySelector('#accessories-box');
    var batteriesBox = document.querySelector('#batteries-box');

    updateSubTypes(powerSourceSelected.dataset.subtypes);
    enableInput(subtype);

    if (accessoriesBox !== null) {
      disableInput(accessoriesBox);
      hideBox(accessoriesBox);
    }

    if (batteriesBox !== null) {
      disableInput(batteriesBox);
      hideBox(batteriesBox);
    }
  }

  function onTypeChange(e) {
    var type = e.target;
    var powersource = document.querySelector('#powersource');
    var subtype = document.querySelector('#subtype');
    var suboption = document.querySelector('#suboption');

    var accessoriesBox = document.querySelector('#accessories-box');
    var batteriesBox = document.querySelector('#batteries-box');

    updatePowerSources(type.dataset.powersources);
    updateSubTypes(type.dataset.subtypes);
    updateSelectiveInputs('');

    if (type.value === 'power' || type.value === 'all') {
      enableInput(powersource);
      disableInput(subtype);
    } else {
      enableInput(powersource, 'manual');
      enableInput(subtype);
    }

    if (suboption !== null) {
      disableInput(suboption);
    }

    if (accessoriesBox !== null) {
      disableInput(accessoriesBox);
      hideBox(accessoriesBox);
    }

    if (batteriesBox !== null) {
      disableInput(batteriesBox);
      hideBox(batteriesBox);
    }
  }


  // --------------------------------------- Attach event listeners for Add tool
  var addTool = document.querySelector('#t4r-addtool');
  if (addTool !== null) {
    var type = document.querySelector('#type');
    var powersource = document.querySelector('#powersource');
    var subtype = document.querySelector('#subtype');
    var suboption = document.querySelector('#suboption');
    var accessoryAdd = document.querySelector('#accessory-btn-add');

    type.addEventListener('change', onTypeChange)
    powersource.addEventListener('change', onPowerSourceChange);
    subtype.addEventListener('change', onSubTypeChange);
    accessoryAdd.addEventListener('click', addAccessory);


    var typeSelected = document.querySelector('#type input:checked');
    var powerSourceSelected = powersource[powersource.selectedIndex];
    var subtypeSelected = subtype.options[subtype.selectedIndex];
    var accessoriesBox = document.querySelector('#accessories-box');
    var batteriesBox = document.querySelector('#batteries-box');

    if (typeSelected !== null && typeSelected.value !== '') {
      if (typeSelected.value !== 'power') {
        updateSubTypes(typeSelected.dataset.subtypes);
      } else {
        updateSubTypes(powerSourceSelected.dataset.subtypes);
      }

      if (subtypeSelected.value !== '') {
        var oldOption = suboption[suboption.selectedIndex];
        var options = subtypeSelected.dataset.options.split(',');

        updateSubOptions(options);
        updateSelectiveInputs(
          subtypeSelected.dataset.attributes + ',' +
          typeSelected.dataset.attributes
        );

        suboption.value = oldOption.value;

        if (typeSelected.value === 'power') {
          enableInput(accessoriesBox);
          showBox(accessoriesBox);
        } else {
          disableInput(accessoriesBox);
          hideBox(accessoriesBox);
        }

        if (powerSourceSelected.value === 'cordless') {
          enableInput(batteriesBox);
          showBox(batteriesBox);
        } else {
          disableInput(batteriesBox);
          hideBox(batteriesBox);
        }
      }
    }
  }

  // --------------------------- Attach event listeners for customer tool search
  var toolSearch = document.querySelector('#t4r-customer-tool-search');
  if (toolSearch !== null) {
    var type = document.querySelector('#type');
    var powersource = document.querySelector('#powersource');
    var subtype = document.querySelector('#subtype');

    type.addEventListener('change', onTypeChange)
    powersource.addEventListener('change', onPowerSourceChange);
    subtype.addEventListener('change', onSubTypeChange);

    var typeSelected = document.querySelector('#type input:checked');
    var powerSourceSelected = powersource[powersource.selectedIndex];
    var subtypeSelected = subtype.options[subtype.selectedIndex];

    if (typeSelected !== null && typeSelected.value !== '') {
      if (typeSelected.value !== 'power') {
        updateSubTypes(typeSelected.dataset.subtypes);
      } else {
        updateSubTypes(powerSourceSelected.dataset.subtypes);
      }
    }
  }

  // -------------------------------------------------------------- Date Pickers
  if (document.querySelector('#start-date') !== null) {
    jQuery('#start-date').datetimepicker({
      timepicker: false,
      format: 'Y-m-d'
    });
  }

  if (document.querySelector('#end-date') !== null) {
    jQuery('#end-date').datetimepicker({
      timepicker: false,
      format: 'Y-m-d'
    });
  }

});
