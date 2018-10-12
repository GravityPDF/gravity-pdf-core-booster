(function ($) {

  /**
   * Enable a specific field type for the current field
   *
   * @param $field
   * @param string|array type
   */
  function enableFieldType ($field, type) {
    if (!$.isArray(type)) {
      type = [type]
    }

    $field.find('option').each(function () {
      if ($.inArray(GPDFCOREBOOSTER[this.value], type) !== -1) {
        $(this).prop('disabled', false)
      }
    })

    $field.multiSelectGpdf('refresh')
  }

  /**
   * Disable a specific field type for the current field
   *
   * @param $field
   * @param string|array type
   */
  function disableFieldType ($field, type) {
    if (!$.isArray(type)) {
      type = [type]
    }

    $field.find('option').each(function () {
      if ($.inArray(GPDFCOREBOOSTER[this.value], type) !== -1) {
        $(this)
          .prop('disabled', true)
          .prop('selected', false)
      }
    })

    $field.multiSelectGpdf('refresh')
  }

  /**
   * Initialize
   */
  $(function () {

    /* Initialise our friendly selector */
    $('.gfpdf-friendly-select').each(function () {
      $(this).multiSelectGpdf()

      /* If nothing yet selected, select all */
      if ($(this).val() === null) {
        $(this).multiSelectGpdf('select_all')
      }
    })

    /* Add special conditions to the Field Selector */
    var $fieldSelected = $('#gfpdf_settings\\[form_field_selector\\]')

    /* Toggle HTML fields */
    var $html = $('input[name=gfpdf_settings\\[show_html\\]]')
    $html.change(function () {
      switch ($(this).filter(':checked').val()) {
        case 'Yes':
          enableFieldType($fieldSelected, 'html')
          break

        case 'No':
          disableFieldType($fieldSelected, 'html')
          break
      }
    }).trigger('change')

    /* Toggle Product fields */
    var $products = $('input[name=gfpdf_settings\\[group_product_fields\\]]')
    $products.change(function () {
      switch ($(this).filter(':checked').val()) {
        case 'Yes':
          disableFieldType($fieldSelected, ['product', 'quantity', 'option', 'total', 'shipping', 'tax', 'discount', 'subtotal', 'coupon'])
          break

        case 'No':
          enableFieldType($fieldSelected, ['product', 'quantity', 'option', 'total', 'shipping', 'tax', 'discount', 'subtotal', 'coupon'])
          break
      }
    }).trigger('change')

  })
})(jQuery)