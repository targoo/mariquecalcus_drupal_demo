<?php

/**
 * @file
 * Definition of Drupal\honeypot\Tests\HoneypotFormTest.
 */

namespace Drupal\honeypot\Tests;

use Drupal\simpletest\WebTestBase;
use Drupal\Core\Database\Database;
use Drupal\comment\Plugin\Field\FieldType\CommentItemInterface;

/**
 * Test the functionality of the Honeypot module for an admin user.
 */
class HoneypotFormTest extends WebTestBase {
  protected $adminUser;
  protected $webUser;
  protected $node;

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = array('honeypot', 'node', 'comment', 'contact');

  public static function getInfo() {
    return array(
      'name' => 'Honeypot form protections',
      'description' => 'Ensure that Honeypot protects site forms properly.',
      'group' => 'Honeypot',
    );
  }

  public function setUp() {
    // Enable modules required for this test.
    parent::setUp();

    // Set up required Honeypot configuration.
    $honeypot_config = \Drupal::config('honeypot.settings');
    $honeypot_config->set('element_name', 'url');
    // Disable time_limit protection.
    $honeypot_config->set('time_limit', 0);
    // Test protecting all forms.
    $honeypot_config->set('protect_all_forms', TRUE);
    $honeypot_config->set('log', FALSE);
    $honeypot_config->save();

    // Set up other required configuration.
    $user_config = \Drupal::config('user.settings');
    $user_config->set('verify_mail', TRUE);
    $user_config->set('register', USER_REGISTER_VISITORS);
    $user_config->save();

    // Create an Article node type.
    if ($this->profile != 'standard') {
      $this->drupalCreateContentType(array('type' => 'article', 'name' => 'Article'));
      // Create comment field on article.
      $this->container->get('comment.manager')->addDefaultField('node', 'article');
    }

    // Set up admin user.
    $this->adminUser = $this->drupalCreateUser(array(
      'administer honeypot',
      'bypass honeypot protection',
      'administer content types',
      'administer users',
      'access comments',
      'post comments',
      'skip comment approval',
      'administer comments',
    ));

    // Set up web user.
    $this->webUser = $this->drupalCreateUser(array(
      'access comments',
      'post comments',
      'create article content',
      'access site-wide contact form',
    ));

    // Set up example node.
    $this->node = $this->drupalCreateNode(array(
      'type' => 'article',
      'comment' => CommentItemInterface::OPEN,
    ));
  }

  /**
   * Make sure user login form is not protected.
   */
  public function testUserLoginNotProtected() {
    $this->drupalGet('user');
    $this->assertNoText('id="edit-url" name="url"', 'Honeypot not enabled on user login form.');
  }

  /**
   * Test user registration (anonymous users).
   */
  public function testProtectRegisterUserNormal() {
    // Set up form and submit it.
    $edit['name'] = $this->randomName();
    $edit['mail'] = $edit['name'] . '@example.com';
    $this->drupalPostForm('user/register', $edit, t('Create new account'));

    // Form should have been submitted successfully.
    $this->assertText(t('A welcome message with further instructions has been sent to your e-mail address.'), 'User registered successfully.');
  }

  public function testProtectUserRegisterHoneypotFilled() {
    // Set up form and submit it.
    $edit['name'] = $this->randomName();
    $edit['mail'] = $edit['name'] . '@example.com';
    $edit['url'] = 'http://www.example.com/';
    $this->drupalPostForm('user/register', $edit, t('Create new account'));

    // Form should have error message.
    $this->assertText(t('There was a problem with your form submission. Please refresh the page and try again.'), 'Registration form protected by honeypot.');
  }

  public function testProtectRegisterUserTooFast() {
    // Enable time limit for honeypot.
    $honeypot_config = \Drupal::config('honeypot.settings')->set('time_limit', 5)->save();

    // Set up form and submit it.
    $edit['name'] = $this->randomName();
    $edit['mail'] = $edit['name'] . '@example.com';
    $this->drupalPostForm('user/register', $edit, t('Create new account'));

    // Form should have error message.
    $this->assertText(t('There was a problem with your form submission. Please wait 6 seconds and try again.'), 'Registration form protected by time limit.');
  }

  /**
   * Test comment form protection.
   */
  public function testProtectCommentFormNormal() {
    $comment = 'Test comment.';

    // Disable time limit for honeypot.
    $honeypot_config = \Drupal::config('honeypot.settings')->set('time_limit', 0)->save();

    // Log in the web user.
    $this->drupalLogin($this->webUser);

    // Set up form and submit it.
    $edit["comment_body[0][value]"] = $comment;
    $this->drupalPostForm('comment/reply/node/' . $this->node->id() . '/comment', $edit, t('Save'));
    $this->assertText(t('Your comment has been queued for review'), 'Comment posted successfully.');
  }

  public function testProtectCommentFormHoneypotFilled() {
    $comment = 'Test comment.';

    // Log in the web user.
    $this->drupalLogin($this->webUser);

    // Set up form and submit it.
    $edit["comment_body[0][value]"] = $comment;
    $edit['url'] = 'http://www.example.com/';
    $this->drupalPostForm('comment/reply/node/' . $this->node->id() . '/comment', $edit, t('Save'));
    $this->assertText(t('There was a problem with your form submission. Please refresh the page and try again.'), 'Comment posted successfully.');
  }

  public function testProtectCommentFormHoneypotBypass() {
    // Log in the admin user.
    $this->drupalLogin($this->adminUser);

    // Get the comment reply form and ensure there's no 'url' field.
    $this->drupalGet('comment/reply/node/' . $this->node->id() . '/comment');
    $this->assertNoText('id="edit-url" name="url"', 'Honeypot home page field not shown.');
  }

  /**
   * Test node form protection.
   */
  public function testProtectNodeFormTooFast() {
    // Log in the admin user.
    $this->drupalLogin($this->webUser);

    // Reset the time limit to 5 seconds.
    $honeypot_config = \Drupal::config('honeypot.settings')->set('time_limit', 5)->save();

    // Set up the form and submit it.
    $edit["title[0][value]"] = 'Test Page';
    $this->drupalPostForm('node/add/article', $edit, t('Save'));
    $this->assertText(t('There was a problem with your form submission.'), 'Honeypot node form timestamp protection works.');
  }

  /**
   * Test node form protection.
   */
  public function testProtectNodeFormPreviewPassthru() {
    // Log in the admin user.
    $this->drupalLogin($this->webUser);

    // Post a node form using the 'Preview' button and make sure it's allowed.
    $edit["title[0][value]"] = 'Test Page';
    $this->drupalPostForm('node/add/article', $edit, t('Preview'));
    $this->assertNoText(t('There was a problem with your form submission.'), 'Honeypot not blocking node form previews.');
  }

  public function testProtectContactForm() {
    $this->drupalLogin($this->adminUser);

    // Submit the admin form so we can verify the right forms are displayed.
    $this->drupalPostForm('admin/config/content/honeypot', array(
      'form_settings[feedback_contact_message_form]' => TRUE,
      'protect_all_forms' => FALSE,
    ), t('Save configuration'));

    $this->drupalLogin($this->webUser);
    $this->drupalGet('contact/feedback');
    $this->assertField('url', 'Honeypot field is added to Contact form.');
  }
}
