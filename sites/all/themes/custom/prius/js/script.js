/**
 * @file
 * A JavaScript file for the theme.
 *
 * In order for this JavaScript to be loaded on pages, see the instructions in
 * the README.txt next to this file.
 */

// JavaScript should be made compatible with libraries other than jQuery by
// wrapping it with an "anonymous closure". See:
// - https://drupal.org/node/1446420
// - http://www.adequatelygood.com/2010/3/JavaScript-Module-Pattern-In-Depth
(function ($, Drupal, window, document, undefined) {

/**
 * Minify the navigation at mobile resolutions.
 */
Drupal.behaviors.nav = {
  attach: function (context, settings) {

      $(document).ready(function () {

          var current_width = $(window).width();

          // Provide for classes based on various widths
          if (current_width <= 753)
              $('html').addClass("mobile").removeClass("desktop");

          if (current_width >= 755)
              $('html').addClass("desktop").removeClass("mobile");

          $('.js #mobile-nav').click(function (e) {
              $('body').toggleClass('active');
              e.preventDefault();
          });

          $(window).scroll(function() {
              var scroll = $(window).scrollTop();
              if (scroll >= 100) {
                  $('header').addClass("scrolling");
                  $('main-wrapper').addClass("scrolling");
              } else {
                  $('header').removeClass("scrolling");
                  $('main-wrapper').removeClass("scrolling");
              }
          });

      });

  }
};

})(jQuery, Drupal, this, this.document);
