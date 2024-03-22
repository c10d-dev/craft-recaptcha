# reCAPTCHA v3 plugin for Craft CMS

Integrate reCAPTCHA validation into your forms.


## Requirements

This plugin requires Craft CMS 3.4 or later.


## Installation

To install the plugin, follow these instructions.

1. Open your terminal and go to your Craft project:

        cd /path/to/project

2. Then tell Composer to load the plugin:

        composer require c10d/craft-recaptcha

3. In the Control Panel, go to Settings → Plugins and click the “Install” button for craft-recaptcha.


## Configuring craft-recaptcha

1. [Sign up for reCAPTCHA API key](https://www.google.com/recaptcha/admin/create).
2. Open the Craft admin and go to Settings → Plugins → Craft reCAPTCHA → Settings.
3. Add your `site key` and `secret key`, then save.
4. Add the reCAPTCHA template tag and js to your forms.

NOTE: you can change the difficulty threshold directly in the settings! (default is 0.5)


## Using craft-recaptcha

In your template, just add the following twig snippet to render a submit button for your form binded with reCAPTCHA:

```twig
{{ craft.recaptcha.renderSubmitButton('Send Request') }}
```

Or you can bind reCAPTCHA directly to your own submit button:

```twig
{{ craft.recaptcha.renderBindButton('my-submit-button-html-id') }}
<button id="my-submit-button-html-id" type="submit">Send<button>
```

You can even create the block yourself and only get the site key variable:

```twig
<button class="g-recaptcha" data-sitekey="{{ craft.recaptcha.sitekey() }}"></button>
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
```

NOTE: After this step is done, if you’re using the CraftCMS [Contact Form](https://plugins.craftcms.com/contact-form) plugin or you're using craft-recaptcha to validate a public user registration, just activate the corresponding toggle in the plugin's settings, you're all set! (the recaptcha will be automatically verified on submission)


## Verify the reCAPTCHA

On the server side, you can use this to verify that the reCAPTCHA was done:

```php
use c10d\craftrecaptcha\CraftRecaptcha;

// [ ... ]

$captcha = Craft::$app->getRequest()->getParam('g-recaptcha-response');
$isValid = CraftRecaptcha::$plugin->recaptcha->verify($captcha);
if (!$isValid) {
    // ERROR: you can push an error here
}
```


---

Brought to you by [Cédric Givord](https://c10d.dev)
