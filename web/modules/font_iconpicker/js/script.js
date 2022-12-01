/**
 * @file
 * Font Iconpicker behavior.
 */

(($, Drupal) => {
  Drupal.behaviors.fontIconpicker = {
    attach(context, settings) {
      $("select.font-iconpicker-element", context)
        .once("jsFontIconpicker")
        .each((index, element) => {
          // Initialize icon picker.
          $(element).fontIconPicker({
            theme: `fip-${settings.font_iconpicker.theme}`,
            emptyIcon: settings.font_iconpicker.empty_icon,
            hasSearch: settings.font_iconpicker.has_search,
            iconGenerator: function (icon) {
              let iconClass = [icon];

              if (settings.font_iconpicker.additional_class) {
                iconClass.push(settings.font_iconpicker.additional_class);
              }

              return '<i class="' + iconClass.join(' ')  + '"></i>';
            }
          });
        });
    }
  };
})(jQuery, Drupal);
