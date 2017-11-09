(function ($) {
  $(function () {
    var $radio = $('input[name=gfpdf_settings\\[display_uploaded_images\\]]')

    $radio.change(function () {
      var val = $(this).val()
      if (val === 'Yes') {
        $('input[name=gfpdf_settings\\[uploaded_images_max_height\\]]').parents('tr').show()
        $('input[name=gfpdf_settings\\[display_uploaded_images_format\\]]').parents('tr').show()
        $('input[name=gfpdf_settings\\[group_uploaded_images\\]]').parents('tr').show()
      } else {
        $('input[name=gfpdf_settings\\[uploaded_images_max_height\\]]').parents('tr').hide()
        $('input[name=gfpdf_settings\\[display_uploaded_images_format\\]]').parents('tr').hide()
        $('input[name=gfpdf_settings\\[group_uploaded_images\\]]').parents('tr').hide()
      }
    })

    $radio.filter(':checked').trigger('change')
  })
})(jQuery)