<?php
/**
 * @file
 * Returns the HTML for a single Drupal page.
 *
 * Complete documentation for this file is available online.
 * @see https://drupal.org/node/1728148
 */
?>

<div class="container">

  <header id="header">
    <div class="logo">
      <a href="/" title="Home" rel="home" id="logo">
      </a>
    </div>

    <nav id="main-menu" class="navigation" role="navigation">
      <h2 class="visually-hidden">Main menu</h2>
      <ul class="links">
        <li class="menu-65" data-drupal-link-system-path="about"><a href="/about" title="About us" data-drupal-link-system-path="node/9">About us</a></li>
        <li class="menu-62" data-drupal-link-system-path="blogs"><a href="/blogs" title="Blogs" data-drupal-link-system-path="blogs">Blogs</a></li>
        <li class="menu-63" data-drupal-link-system-path="work"><a href="/work" title="Work" data-drupal-link-system-path="work">Work</a></li>
        <li class="menu-64" data-drupal-link-system-path="contact"><a href="/contact" title="Contact us" data-drupal-link-system-path="contact">Contact</a></li>
        <li class="menu-64" data-drupal-link-system-path="thelab"><a href="/demo" title="Contact us" data-drupal-link-system-path="contact">The Lab</a></li>
      </ul>
    </nav>
  </header>

  <div id="main-wrapper">

    <div id="mobile-container">
      <a id="mobile-nav" href="#mobile-nav">
        <i class="fa fa-2"></i>
      </a>
      <a id="mobile-logo" href="/" title="Home" rel="home" ></a>
    </div>

    <main id="main" role="main">

      <div id="content-wrapper">

        <div id="secondnav">
          <?php
          $tree = menu_tree_all_data('main-menu');
          print drupal_render(menu_tree_output($tree));
          ?>
        </div>

        <div id="page-content">

          <?php if ($title): ?>
            <h1 class="page__title title" id="page-title"><?php print $title; ?></h1>
          <?php endif; ?>

          <?php print render($title_suffix); ?>
          <?php print $messages; ?>
          <?php print render($tabs); ?>
          <?php print render($page['help']); ?>
          <?php if ($action_links): ?>
            <ul class="action-links"><?php print render($action_links); ?></ul>
          <?php endif; ?>
          <?php print render($page['content']); ?>

        </div>

        <aside id="aside_right">
          <?php if ($page['sidebar_second']): ?>
            <?php print render($page['sidebar_second']); ?>
          <?php endif; ?>
        </aside>

      </div>

    </main>

    <footer id="footer" class="footer">

      <div id="footer-top">
        <div class="slogan">
          We'd love to hear from you.
        </div>
        <div class="calltoaction">
          <a href="/contact">Contact</a>
        </div>
      </div>

      <div id="footer-center">
        <div class="contact">
          <h3>Contact</h3>
          <ul>
            <li class="first">info@mariquecalcus.com</li>
          </ul>
        </div>
        <div class="navigation">
          <h3>Navigate</h3>
          <ul>
            <li class="first"><a href="/about">About us</a></li>
            <li class=""><a href="/services">Services</a></li>
            <li class=""><a href="/blogs">Blogs</a></li>
            <li class="last"><a href="/contact">Contact</a></li>
          </ul>
        </div>
        <div class="legal">
          <h3>Legal stuff</h3>
          <ul>
            <li class="first"><a href="/legal/privacy-policy">Privacy Policy</a></li>
          </ul>
        </div>
        <div class="logo">
          <img src="/sites/all/themes/custom/prius/images/logos/logo_medium.svg" alt="" />
        </div>
      </div>

      <div id="footer-bottom">
        <div class="copyright">
          Copyright <?php print date('Y') ?> by Mariquecalcus. All rights reserved.
        </div>
        <div class="social">
          <ul>
            <li><a href="https://www.facebook.com" alt="Friend us on Facebook" target="_blank"><i class="fa fa-facebook"></i></a></li>
            <li><a href="https://plus.google.com" alt="Follow us on Google Plus" target="_blank"><i class="fa fa-google-plus"></i></a></li>
            <li><a href="https://twitter.com" alt="Follow us on Twitter" target="_blank"><i class="fa fa-twitter"></i></a></li>
            <li><a href="https://www.linkedin.com" alt="Network with us on LinkedIn" target="_blank"><i class="fa fa-linkedin"></i></a></li>
          </ul>
        </div>
      </div>

    </footer>

  </div>

</div>



