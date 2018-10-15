(function ($) {

  /**
   * @var string The current saved template
   * @since 1.0
   */
  var currentTemplate

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

    var $select = $('#gfpdf_settings\\[template\\]')
    currentTemplate = $select.val()
    $select.parent().change(function () {
      var value = $(this).find('select').val()

      if (currentTemplate !== value) {
        currentTemplate = value

        setTimeout(initialize, 6000)
      }
    })
  })
})(jQuery)