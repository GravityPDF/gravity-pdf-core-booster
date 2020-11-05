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
    if (!Array.isArray(type)) {
      type = [type]
    }

    $field.find('option').each(function () {
      if ($.inArray(GPDFCOREBOOSTER.form[this.value], type) !== -1) {
        $(this)
          .prop('disabled', false)
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
    if (!Array.isArray(type)) {
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

      $(this).parent().on('click', '.gfpdf-friendly-select-add-all', function () {
        $self.multiSelectGpdf('select_all')
        return false
      })

      $(this).parent().on('click', '.gfpdf-friendly-select-remove-all', function () {
        $self.multiSelectGpdf('deselect_all')
        return false
      })
    })

	var $legacySelectorEnabledMarker = $('#gfpdf_settings\\[form_field_selector_enabled\\]')
	var $shouldFilterFields = $('#gfpdf_settings\\[form_field_selector\\]')
	var $selectorEnabled = $('#gfpdf_settings\\[form_field_filter_fields\\]')
	var $html = $('input[name=gfpdf_settings\\[show_html\\]]')
	var $products = $('input[name=gfpdf_settings\\[group_product_fields\\]]')

	/* Add special conditions to the Field Selector */
	var parent_selector = (version_compare(GPDFCOREBOOSTER.gpdfVersion, '6.0.0-beta1', '>=')) ? 'div' : 'tr'

	$selectorEnabled.on('change', function () {
		if ($(this).prop('checked')) {
			$shouldFilterFields.closest(parent_selector).show()
			$html.trigger('change')
			$products.trigger('change')
		} else {
			$shouldFilterFields.multiSelectGpdf('deselect_all')
			$shouldFilterFields.multiSelectGpdf('refresh')
			$shouldFilterFields.closest(parent_selector).hide()
		}
	})

	if ($legacySelectorEnabledMarker.val() !== '-1') {
		$selectorEnabled.prop('checked', true)
		$legacySelectorEnabledMarker.val('-1')
	}

	/* Toggle HTML fields */
	$html.on('change', function () {
		if (!$selectorEnabled.prop('checked')) {
			return
		}

		switch ($(this).filter(':checked').val()) {
			case 'Yes':
				enableFieldType($shouldFilterFields, 'html')
				break

			default:
				disableFieldType($shouldFilterFields, 'html')
				break
		}
	})

	/* Toggle Product fields */
	$products.on('change', function () {
		if (!$selectorEnabled.prop('checked')) {
			return
		}

		switch ($(this).filter(':checked').val()) {
			case 'Yes':
				disableFieldType($shouldFilterFields, ['product', 'quantity', 'option', 'total', 'shipping', 'tax', 'discount', 'subtotal', 'coupon'])
				break

			default:
				enableFieldType($shouldFilterFields, ['product', 'quantity', 'option', 'total', 'shipping', 'tax', 'discount', 'subtotal', 'coupon'])
				break
		}
	})

  	$selectorEnabled.trigger('change')
  }

  /**
   * Initialize
   */
  $(function () {
    initialize()

    /* Reinitialise on template loaded */
    if (version_compare('5.1.0-beta1', GPDFCOREBOOSTER.gpdfVersion, '<=')) {
      $(document).on('gfpdf_template_loaded', function () {
        initialize()
      })
    } else {
      var $select = $('#gfpdf_settings\\[template\\]')
      currentTemplate = $select.val()
      $select.parent().on('change', function() {
        var value = $(this).find('select').val()

        if (currentTemplate !== value) {
          currentTemplate = value

          setTimeout(initialize, 6000)
        }
      })
    }
  })
})(jQuery)

function version_compare (v1, v2, operator) {
  //       discuss at: http://locutus.io/php/version_compare/
  //      original by: Philippe Jausions (http://pear.php.net/user/jausions)
  //      original by: Aidan Lister (http://aidanlister.com/)
  // reimplemented by: Kankrelune (http://www.webfaktory.info/)
  //      improved by: Brett Zamir (http://brett-zamir.me)
  //      improved by: Scott Baker
  //      improved by: Theriault (https://github.com/Theriault)
  //        example 1: version_compare('8.2.5rc', '8.2.5a')
  //        returns 1: 1
  //        example 2: version_compare('8.2.50', '8.2.52', '<')
  //        returns 2: true
  //        example 3: version_compare('5.3.0-dev', '5.3.0')
  //        returns 3: -1
  //        example 4: version_compare('4.1.0.52','4.01.0.51')
  //        returns 4: 1

  // Important: compare must be initialized at 0.
  var i
  var x
  var compare = 0

  // vm maps textual PHP versions to negatives so they're less than 0.
  // PHP currently defines these as CASE-SENSITIVE. It is important to
  // leave these as negatives so that they can come before numerical versions
  // and as if no letters were there to begin with.
  // (1alpha is < 1 and < 1.1 but > 1dev1)
  // If a non-numerical value can't be mapped to this table, it receives
  // -7 as its value.
  var vm = {
    'dev': -6,
    'alpha': -5,
    'a': -5,
    'beta': -4,
    'b': -4,
    'RC': -3,
    'rc': -3,
    '#': -2,
    'p': 1,
    'pl': 1
  }

  // This function will be called to prepare each version argument.
  // It replaces every _, -, and + with a dot.
  // It surrounds any nonsequence of numbers/dots with dots.
  // It replaces sequences of dots with a single dot.
  //    version_compare('4..0', '4.0') === 0
  // Important: A string of 0 length needs to be converted into a value
  // even less than an unexisting value in vm (-7), hence [-8].
  // It's also important to not strip spaces because of this.
  //   version_compare('', ' ') === 1
  var _prepVersion = function (v) {
    v = ('' + v).replace(/[_\-+]/g, '.')
    v = v.replace(/([^.\d]+)/g, '.$1.').replace(/\.{2,}/g, '.')
    return (!v.length ? [-8] : v.split('.'))
  }
  // This converts a version component to a number.
  // Empty component becomes 0.
  // Non-numerical component becomes a negative number.
  // Numerical component becomes itself as an integer.
  var _numVersion = function (v) {
    return !v ? 0 : (isNaN(v) ? vm[v] || -7 : parseInt(v, 10))
  }

  v1 = _prepVersion(v1)
  v2 = _prepVersion(v2)
  x = Math.max(v1.length, v2.length)
  for (i = 0; i < x; i++) {
    if (v1[i] === v2[i]) {
      continue
    }
    v1[i] = _numVersion(v1[i])
    v2[i] = _numVersion(v2[i])
    if (v1[i] < v2[i]) {
      compare = -1
      break
    } else if (v1[i] > v2[i]) {
      compare = 1
      break
    }
  }
  if (!operator) {
    return compare
  }

  // Important: operator is CASE-SENSITIVE.
  // "No operator" seems to be treated as "<."
  // Any other values seem to make the function return null.
  switch (operator) {
    case '>':
    case 'gt':
      return (compare > 0)
    case '>=':
    case 'ge':
      return (compare >= 0)
    case '<=':
    case 'le':
      return (compare <= 0)
    case '===':
    case '=':
    case 'eq':
      return (compare === 0)
    case '<>':
    case '!==':
    case 'ne':
      return (compare !== 0)
    case '':
    case '<':
    case 'lt':
      return (compare < 0)
    default:
      return null
  }
}
