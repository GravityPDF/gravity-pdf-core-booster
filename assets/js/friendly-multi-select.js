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
      if ($.inArray(GPDFCOREBOOSTER.form[this.value], type) !== -1) {
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
      if ($.inArray(GPDFCOREBOOSTER.form[this.value], type) !== -1) {
        $(this)
          .prop('disabled', true)
          .prop('selected', false)
      }
    })

    $field.multiSelectGpdf('refresh')
  }

  function initialize () {
    /* Initialise our friendly selector */
    $('.gfpdf-friendly-select').each(function () {
      var $self = $(this)

      $(this).multiSelectGpdf({
        selectableHeader: '<b>' + GPDFCOREBOOSTER.lang.excluded + '</b>',
        selectionHeader: '<b>' + GPDFCOREBOOSTER.lang.included + '</b>',
        selectableFooter: '<a class="gfpdf-friendly-select-add-all" href="#">' + GPDFCOREBOOSTER.lang.addAllFields + '</a>',
        selectionFooter: '<a class="gfpdf-friendly-select-remove-all" href="#">' + GPDFCOREBOOSTER.lang.removeAllFields + '</a>',
      })

      /* If nothing yet selected, select all */
      if ($(this).val() === null) {
        $(this).multiSelectGpdf('select_all')
      }

      $(this).parent().on('click', '.gfpdf-friendly-select-add-all', function () {
        $self.multiSelectGpdf('select_all')
        return false
      })

      $(this).parent().on('click', '.gfpdf-friendly-select-remove-all', function () {
        $self.multiSelectGpdf('deselect_all')
        return false
      })
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
        case 'Disable':
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
  }

  /**
   * Initialize
   */
  $(function () {
    initialize()

    /* Reinitialise on template loaded */
    $(document).on('gfpdf_template_loaded', function () {
      initialize()
    })
  })
})(jQuery)