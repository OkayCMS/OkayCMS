const configParamsObj = {
  //placeholder: 'Выберите город...', // Place holder text to place in the select
  minimumResultsForSearch: 3, // Overrides default of 15 set above
  width: "resolve",
  matcher: function (params, data) {
    if ($.trim(params.term) === "") {
      return data;
    }
    if (data.text.toLowerCase().startsWith(params.term.toLowerCase())) {
      return $.extend({}, data, true);
    }
    return null;
  },
};

const whsParams = {
  matcher: function (params, data) {
    if ($.trim(params.term) === "") {
      return data;
    }
    if ($.isNumeric(params.term)) {
      // Шукаємо лише по першому входженню "№...", щоб по запиту "3" знайти лише "Відділення №3",
      // а не "Відділення №1, під'їзд №3"
      let warehouseNumSearch = ~data.text.indexOf("№" + params.term);
      if (
        warehouseNumSearch &&
        ~data.text.indexOf("№") === warehouseNumSearch
      ) {
        return data;
      }
    } else {
      let words = params.term.toLowerCase().split(" ");
      let isMatched = true;
      for (let wordKey in words) {
        let word = words[wordKey];
        if (!~data.text.toLowerCase().indexOf(word)) {
          isMatched = false;
        }
      }
      if (isMatched) {
        return $.extend({}, data, true);
      } else if (~data.text.toLowerCase().indexOf(params.term.toLowerCase())) {
        return $.extend({}, data, true);
      }
    }
    return null;
  },
};

init();
$("select.city_novaposhta").select2(configParamsObj);

$(document).on(
  "change",
  "select.fn_select_warehouses_novaposhta",
  set_warehouse,
);
$(document).on("change", '[data-np-field="redelivery"]', calc_delivery_price);
$(document).on("click", ".np_delivery_types_heading a", changeDeliveryType);
$(document).on(
  "input change paste blur",
  '[data-np-field="warehouse_city"]',
  function () {
    clearWarehouseCitySelection($(this));
  },
);
$(document).on(
  "input change paste blur",
  '[data-np-field="door_city"]',
  function () {
    clearDoorCitySelection($(this));
  },
);
$(document).on(
  "change",
  'input[name="delivery_id"]',
  syncNovaPoshtaDeliveryFields,
);
$(document).on("submit", ".fn_validate_cart", syncNovaPoshtaDeliveryFields);
$(syncNovaPoshtaDeliveryFields);

function npField(deliveryBlock, fieldName) {
  return deliveryBlock.find('[data-np-field="' + fieldName + '"]');
}

function npDeliveryId(deliveryBlock) {
  return deliveryBlock.find(".fn_delivery_novaposhta").data("delivery-id");
}

function npFieldName(deliveryBlock, fieldName) {
  return "novaposhta[" + npDeliveryId(deliveryBlock) + "][" + fieldName + "]";
}

function init() {
  validateCityInputs();

  let delivery_block = getActiveDeliveryBlock();
  let warehouse_city_ref = npField(delivery_block, "warehouse_city_ref").val();

  $("select.city_novaposhta")
    .closest(".delivery_wrap")
    .find("span.deliver_price")
    .text("");

  $(document).on("change", 'input[name="delivery_id"]', calc_delivery_price);
  $(document).on(
    "change",
    '[data-np-field="warehouse_city_ref"]',
    getWarehouses,
  );

  if (warehouse_city_ref) {
    getWarehouses();
  } else if (getSelectedCityRef(delivery_block)) {
    initializeDoorStreetAutocomplete(delivery_block);
    calc_delivery_price();
  }

  setTimeout(validateCityInputs, 300);
  setTimeout(validateCityInputs, 1200);

  $(".np_preloader").remove();
  update_np_payments();
}

$(".fn_delivery_novaposhta input.city_novaposhta").devbridgeAutocomplete({
  serviceUrl: okay.router["OkayCMS_NovaposhtaCost_find_city"],
  minChars: 1,
  maxHeight: 320,
  noCache: true,
  onSelect: function (suggestion) {
    let delivery_block = $(this).closest(".delivery__item");
    let city_input = $(this);

    rememberSelectedCityValue(city_input, suggestion.value);
    npField(delivery_block, "delivery_warehouse_id").val("");
    npField(delivery_block, "warehouse_city_name").val(
      suggestion.data.name || suggestion.value,
    );
    delivery_block
      .find("select.fn_select_warehouses_novaposhta option:selected")
      .prop("selected", false);
    npField(delivery_block, "warehouse_city_ref")
      .val(suggestion.data.ref)
      .trigger("change");
  },
  formatResult: function (suggestion, currentValue) {
    var reEscape = new RegExp(
      "(\\" +
        ["/", ".", "*", "+", "?", "|", "(", ")", "[", "]", "{", "}", "\\"].join(
          "|\\",
        ) +
        ")",
      "g",
    );
    var pattern = "(" + currentValue.replace(reEscape, "\\$1") + ")";
    return (
      "<div style='text-align: left'>" +
      suggestion.value.replace(
        new RegExp(pattern, "gi"),
        "<strong>$1<\/strong>",
      ) +
      "<\/div>"
    );
  },
});

// Автокомплит адреса в корзине из справочника Новой Почты
$(
  ".fn_delivery_novaposhta input.city_novaposhta_for_door",
).devbridgeAutocomplete({
  serviceUrl: okay.router["OkayCMS_NovaposhtaCost_find_city_for_door"],
  minChars: 1,
  noCache: true,
  onSelect: function (suggestion) {
    let delivery_block = $(this).closest(".delivery__item");
    let city_input = $(this);

    rememberSelectedCityValue(city_input, suggestion.value);
    npField(delivery_block, "door_settlement_ref").val(suggestion.ref);
    npField(delivery_block, "city_name").val(suggestion.city);
    npField(delivery_block, "area_name").val(suggestion.area);
    npField(delivery_block, "region_name").val(suggestion.region);

    setStreetAutocomplete(delivery_block, suggestion.ref);
    calc_delivery_price();
  },
  formatResult: function (suggestion, currentValue) {
    var reEscape = new RegExp(
      "(\\" +
        ["/", ".", "*", "+", "?", "|", "(", ")", "[", "]", "{", "}", "\\"].join(
          "|\\",
        ) +
        ")",
      "g",
    );
    var pattern = "(" + currentValue.replace(reEscape, "\\$1") + ")";
    return (
      "<div style='text-align: left'>" +
      suggestion.value.replace(
        new RegExp(pattern, "gi"),
        "<strong>$1<\/strong>",
      ) +
      "<\/div>"
    );
  },
});

$('[name="delivery_id"]').on("change", function () {
  if (
    Number($(this).data("module_id")) !== Number(okay.np_delivery_module_id)
  ) {
    return;
  }
  let delivery_block = $(this).closest(".delivery__item");
  if (isDoorDeliveryBlock(delivery_block)) {
    initializeDoorStreetAutocomplete(delivery_block);
  } else if (npField(delivery_block, "warehouse_city_ref").val()) {
    npField(delivery_block, "warehouse_city_ref").trigger("change");
  }
  update_np_payments();
  select_first_active_payment();
});

function getWarehouses(e) {
  let delivery_block =
    e !== undefined && e.target !== undefined
      ? $(e.target).closest(".delivery__item")
      : getActiveDeliveryBlock();
  if (delivery_block.length === 0 || isDoorDeliveryBlock(delivery_block)) {
    return;
  }

  let deliveryTypesBlock = delivery_block.find(".np_delivery_types_block");
  let deliveryTypesHeading = deliveryTypesBlock.find(
    ".np_delivery_types_heading",
  );
  let deliveryTypesContent = deliveryTypesBlock.find(
    ".np_delivery_types_content",
  );
  let cityRef = npField(delivery_block, "warehouse_city_ref").val();
  let selectedWarehouseRef = npField(
    delivery_block,
    "delivery_warehouse_id",
  ).val();
  let selectedDeliveryType = deliveryTypesHeading
    .find("a.active")
    .data("delivery_type");

  if (!cityRef) {
    clearWarehouseSelection(delivery_block);
    clearDeliveryCalculation(delivery_block);
    calc_delivery_price();
    return;
  }

  $.ajax({
    url: okay.router["OkayCMS_NovaposhtaCost_get_warehouses"],
    data: { city: cityRef },
    dataType: "json",
    success: function (data) {
      deliveryTypesHeading.html("");
      deliveryTypesContent.html("");

      if (data.hasOwnProperty("success") && data.success) {
        // Додаємо таби типів доставки
        for (let deliveryTypeKey in data.delivery_types) {
          let deliveryType = data.delivery_types[deliveryTypeKey];

          let deliveryTypeButton = $(
            '<a href="javascript:;" data-delivery_type="fn_delivery_type_' +
              deliveryTypeKey +
              '"><span>' +
              deliveryType.name +
              "</span></a>",
          );
          deliveryTypeButton.appendTo(deliveryTypesHeading);
          let warehousesSelect = $(
            '<select tabindex="1" class="fn_select_warehouses_novaposhta" data-np-field="warehouses" style="width: 100%;" disabled></select>',
          );
          warehousesSelect.attr(
            "name",
            npFieldName(delivery_block, "warehouses"),
          );

          for (let warehouseKey in data.warehouses) {
            let warehouse = data.warehouses[warehouseKey];
            if (deliveryType.typeRefs.includes(warehouse.typeRef)) {
              let option = $(
                '<option value="' +
                  warehouse.name +
                  '" ' +
                  'data-warehouse_ref="' +
                  warehouse.ref +
                  '"' +
                  (selectedWarehouseRef && selectedWarehouseRef == warehouse.ref
                    ? "selected"
                    : "") +
                  ">" +
                  warehouse.name +
                  "</option>",
              );
              warehousesSelect.append(option);
              if (
                selectedWarehouseRef &&
                selectedWarehouseRef == warehouse.ref
              ) {
                selectedDeliveryType = "fn_delivery_type_" + deliveryTypeKey;
              }
            }
          }
          let selectWrap = $(
            '<div class="fn_delivery_type_' + deliveryTypeKey + '"></div>',
          );

          selectWrap.hide();
          warehousesSelect.attr("disabled", false);

          warehousesSelect.appendTo(selectWrap).select2(whsParams);

          attachWarehouseValidationIfAvailable(warehousesSelect);
          selectWrap.appendTo(deliveryTypesContent);
        }

        // Відмічаємо активний таб типу доставки
        // Перший таб, або який був вибраний для попереднього міста, якщо для нового він теж доступний
        let selectedDeliveryTypeButton = deliveryTypesHeading.find(
          'a[data-delivery_type="' + selectedDeliveryType + '"]',
        );
        if (selectedDeliveryTypeButton.length > 0) {
          selectedDeliveryTypeButton.trigger("click");
        } else {
          deliveryTypesHeading.children("a").first().trigger("click");
        }
        if (deliveryTypesHeading.children().length > 1) {
          deliveryTypesHeading.show();
        } else {
          deliveryTypesHeading.hide();
        }
      } else {
        if (
          data.reason === "empty_city_ref" ||
          data.reason === "invalid_city_ref"
        ) {
          npField(delivery_block, "warehouse_city_ref").val("");
          rememberSelectedCityValue(
            npField(delivery_block, "warehouse_city"),
            "",
          );
        }
        clearWarehouseSelection(delivery_block);
        clearDeliveryCalculation(delivery_block);
      }

      calc_delivery_price();
      syncNovaPoshtaDeliveryFields();
    },
  });
}

function changeDeliveryType() {
  let activeDeliveryTypeButton = $(this);
  let activeDelivery = $('input[name="delivery_id"]:checked');
  let deliveryBlock = activeDelivery.closest(".delivery__item");
  let deliveryTypesBlock = deliveryBlock.find(".np_delivery_types_block");
  let deliveryTypesHeading = deliveryTypesBlock.find(
    ".np_delivery_types_heading",
  );
  let deliveryTypesContent = deliveryTypesBlock.find(
    ".np_delivery_types_content",
  );
  deliveryBlock.find(".fn_select_warehouses_novaposhta").attr("disabled", true);
  deliveryTypesContent.children().hide();
  deliveryTypesHeading.children().removeClass("active");

  deliveryTypesContent
    .find("." + activeDeliveryTypeButton.data("delivery_type"))
    .show()
    .find("select")
    .attr("disabled", false)
    .trigger("change");
  activeDeliveryTypeButton.addClass("active");

  return false;
}

function calc_delivery_price(e) {
  if (e !== undefined && $(e.target).is('[data-np-field="redelivery"]')) {
    update_np_payments();
    select_first_active_payment();
  }

  let active_delivery = $('input[name="delivery_id"]:checked');
  if (active_delivery.data("module_id") == okay.np_delivery_module_id) {
    $("#fn_total_delivery_price").text("");
  } else {
    return false;
  }

  let delivery_block = active_delivery.closest(".delivery__item");
  let price_elem = delivery_block.find(".fn_delivery_price");
  let term_elem = delivery_block.find(".term_novaposhta span");
  let delivery_id = active_delivery.val();
  let city_ref = getSelectedCityRef(delivery_block);

  let redelivery = 0;

  if (npField(delivery_block, "redelivery").is(":checked")) {
    redelivery = npField(delivery_block, "redelivery").val();
  }

  if (city_ref) {
    price_elem.text(okay.np_cart_calculate);
    $("#fn_total_delivery_price").text(okay.np_cart_calculate);
    term_elem.text("");

    npField(delivery_block, "delivery_price").val("");
    npField(delivery_block, "delivery_term").val("");
    $.ajax({
      url: okay.router["OkayCMS_NovaposhtaCost_calc"],
      data: {
        city: city_ref,
        redelivery: redelivery,
        delivery_id: delivery_id,
      },
      dataType: "json",
      success: function (data) {
        if (data.hasOwnProperty("price_response")) {
          if (data.price_response.success) {
            price_elem.text(data.price_response.price_formatted);
            npField(delivery_block, "delivery_price").val(
              data.price_response.price,
            );
            delivery_block
              .find('input[name="delivery_id"]')
              .data("total_price", data.price_response.cart_total_price)
              .data("delivery_price", data.price_response.price);

            okay.change_payment_method();
          }
        }

        if (
          data.hasOwnProperty("term_response") &&
          data.term_response.success
        ) {
          npField(delivery_block, "delivery_term").val(data.term_response.term);
          term_elem.text(data.term_response.term);
          term_elem.parent().show();
        } else {
          term_elem.parent().hide();
        }

        update_np_payments();
      },
    });
  } else {
    clearDeliveryCalculation(delivery_block);
  }
}

function update_np_payments() {
  const payment_method_ids = get_np_payment_method_ids();
  const redelivery_enabled = $('input[name="delivery_id"]:checked')
    .closest(".fn_delivery_item")
    .find('[data-np-field="redelivery"]')
    .prop("checked");

  if (redelivery_enabled) {
    for (const payment_id of payment_method_ids) {
      if (okay.np_redelivery_payments_ids.includes(payment_id)) {
        $(`.fn_payment_method__item_${payment_id}`).show();
      } else {
        $(`.fn_payment_method__item_${payment_id}`).hide();
      }
    }
  } else {
    for (const payment_id of payment_method_ids) {
      if (okay.np_redelivery_payments_ids.includes(payment_id)) {
        $(`.fn_payment_method__item_${payment_id}`).hide();
      } else {
        $(`.fn_payment_method__item_${payment_id}`).show();
      }
    }
  }
}

function select_first_active_payment() {
  const payment_method_elements = $('[name="payment_method_id"]');
  for (const element of payment_method_elements) {
    const id = element.attributes.id.nodeValue;
    if (!$(`#${id}`).closest(".fn_payment_method__item").is(":hidden")) {
      $(`#${id}`).trigger("click");
      break;
    }
  }
}

function get_np_payment_method_ids() {
  let deliveryInput = $('input[name="delivery_id"]:checked')
    .closest(".fn_delivery_item")
    .find('[data-np-field="redelivery"]')
    .closest(".fn_delivery_item")
    .find('[name="delivery_id"]');

  if (deliveryInput.data("payment_method_ids") !== undefined) {
    return String(deliveryInput.data("payment_method_ids"))
      .split(",")
      .map(Number);
  } else {
    return [];
  }
}

function set_warehouse() {
  let delivery_block = $(this).closest(".delivery__item");
  if ($(this).val() != "") {
    npField(delivery_block, "delivery_warehouse_id").val(
      $(this).children(":selected").data("warehouse_ref"),
    );
  } else {
    npField(delivery_block, "delivery_warehouse_id").val("");
  }
}

function getActiveDeliveryBlock() {
  return $('input[name="delivery_id"]:checked').closest(".delivery__item");
}

function isDoorDeliveryBlock(delivery_block) {
  return npField(delivery_block, "door_delivery").length > 0;
}

function getSelectedCityRef(delivery_block) {
  if (isDoorDeliveryBlock(delivery_block)) {
    return npField(delivery_block, "door_settlement_ref").val();
  }

  return npField(delivery_block, "warehouse_city_ref").val();
}

function normalizeCityValue(value) {
  return $.trim(value || "");
}

function getSelectedCityValue(city_input) {
  let selectedValue = city_input.data("selected_city_value");
  if (selectedValue === undefined) {
    selectedValue = city_input.attr("data-selected-city-value");
  }

  return normalizeCityValue(selectedValue);
}

function rememberSelectedCityValue(city_input, selectedValue) {
  city_input.data("selected_city_value", normalizeCityValue(selectedValue));
  city_input.attr(
    "data-selected-city-value",
    normalizeCityValue(selectedValue),
  );
}

function cityInputWasChangedOutsideAutocomplete(city_input) {
  return (
    normalizeCityValue(city_input.val()) !== getSelectedCityValue(city_input)
  );
}

function validateCityInputs() {
  $('[data-np-field="warehouse_city"]').each(function () {
    clearWarehouseCitySelection($(this));
  });
  $('[data-np-field="door_city"]').each(function () {
    clearDoorCitySelection($(this));
  });
}

function clearWarehouseCitySelection(city_input, force) {
  if (force !== true && !cityInputWasChangedOutsideAutocomplete(city_input)) {
    return;
  }

  let delivery_block = city_input.closest(".delivery__item");
  let city_ref_input = npField(delivery_block, "warehouse_city_ref");
  let hadCityRef = city_ref_input.val() !== "";

  rememberSelectedCityValue(city_input, "");
  city_ref_input.val("");
  npField(delivery_block, "warehouse_city_name").val("");
  clearWarehouseSelection(delivery_block);
  clearDeliveryCalculation(delivery_block);

  if (hadCityRef) {
    city_ref_input.trigger("change");
  }
}

function clearDoorCitySelection(city_input, force) {
  if (force !== true && !cityInputWasChangedOutsideAutocomplete(city_input)) {
    return;
  }

  let delivery_block = city_input.closest(".delivery__item");

  rememberSelectedCityValue(city_input, "");
  npField(delivery_block, "door_settlement_ref").val("");
  npField(delivery_block, "city_name").val("");
  npField(delivery_block, "area_name").val("");
  npField(delivery_block, "region_name").val("");
  npField(delivery_block, "street_name").val("");
  npField(delivery_block, "street").val("");
  clearDeliveryCalculation(delivery_block);
}

function clearWarehouseSelection(delivery_block) {
  npField(delivery_block, "delivery_warehouse_id").val("");
  delivery_block.find(".np_delivery_types_heading").html("").hide();
  delivery_block.find(".np_delivery_types_content").html("");
}

function clearDeliveryCalculation(delivery_block) {
  delivery_block.find(".fn_delivery_price").text("");
  npField(delivery_block, "delivery_price").val("");
  npField(delivery_block, "delivery_term").val("");
  delivery_block.find(".term_novaposhta span").text("").parent().hide();
  $("#fn_total_delivery_price").text("");
}

function setStreetAutocomplete(delivery_block, cityRef) {
  delivery_block.find("input.fn_street").devbridgeAutocomplete({
    serviceUrl:
      okay.router["OkayCMS_NovaposhtaCost_find_street"] +
      "?city_ref=" +
      cityRef,
    minChars: 1,
    noCache: true,
    preventBadQueries: false,
    onSelect: function (suggestion) {
      npField(delivery_block, "street_name").val(suggestion.value);
    },
    formatResult: function (suggestion, currentValue) {
      var reEscape = new RegExp(
        "(\\" +
          [
            "/",
            ".",
            "*",
            "+",
            "?",
            "|",
            "(",
            ")",
            "[",
            "]",
            "{",
            "}",
            "\\",
          ].join("|\\") +
          ")",
        "g",
      );
      var pattern = "(" + currentValue.replace(reEscape, "\\$1") + ")";
      return (
        "<div style='text-align: left'>" +
        suggestion.value.replace(
          new RegExp(pattern, "gi"),
          "<strong>$1<\/strong>",
        ) +
        "<\/div>"
      );
    },
  });
}

function initializeDoorStreetAutocomplete(delivery_block) {
  if (!isDoorDeliveryBlock(delivery_block)) {
    return;
  }

  let cityRef = npField(delivery_block, "door_settlement_ref").val();
  if (cityRef) {
    setStreetAutocomplete(delivery_block, cityRef);
  }
}

function attachWarehouseValidationIfAvailable(warehousesSelect) {
  if (typeof attachNovaPoshtaWarehouseValidation === "function") {
    attachNovaPoshtaWarehouseValidation(warehousesSelect);
  }
}

function resetInactiveNovaPoshtaValidation(novaPoshtaBlock) {
  let form = novaPoshtaBlock.closest("form");
  let validator = form.data("validator");
  if (!validator) {
    return;
  }

  novaPoshtaBlock.find("input, select, textarea").each(function () {
    validator.errorsFor(this).remove();
    $(this).removeClass("error");
  });
}

function syncNovaPoshtaDeliveryFields() {
  $(".fn_delivery_novaposhta").each(function () {
    let novaPoshtaBlock = $(this);
    let deliveryItem = novaPoshtaBlock.closest(".delivery__item");
    let isActive = deliveryItem
      .find('input[name="delivery_id"]')
      .is(":checked");

    if (!isActive) {
      resetInactiveNovaPoshtaValidation(novaPoshtaBlock);
      novaPoshtaBlock.find("input, select, textarea").prop("disabled", true);
      return;
    }

    novaPoshtaBlock.find("input, textarea").prop("disabled", false);
    novaPoshtaBlock
      .find("select")
      .not(".fn_select_warehouses_novaposhta")
      .prop("disabled", false);
  });
}
