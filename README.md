# reCAPTCHA v3 plugin for Craft CMS 3.x

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


## Using craft-recaptcha

In your template, just add the following twig snippet to render a submit button for your form binded with reCAPTCHA:

```twig
{{ craft.recaptcha.renderSubmitButton() }}
```

Or you can bind reCAPTCHA directly to your own submit button:

```twig
{{ craft.recaptcha.renderBindButton('my-submit-button-html-id') }}
```

You can even create the block yourself and only get the site key variable:

```twig
<button class="g-recaptcha" data-sitekey="{{ craft.recaptcha.sitekey() }}"></button>
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
```


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

In case of using craft-recaptcha to validate a public user registration, just activate the toggle in
the plugin's settings.


---

Brought to you by [Cédric Givord](https://c10d.dev)
