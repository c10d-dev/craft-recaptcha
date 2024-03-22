<?php
/**
 * craft-recaptcha plugin for Craft CMS
 *
 * Integrate reCAPTCHA validation into your forms.
 *
 * @link      https://c10d.dev
 * @copyright Copyright (c) Cédric Givord
 */

/**
 * craft-recaptcha config.php
 *
 * This file exists only as a template for the craft-recaptcha settings.
 * It does nothing on its own.
 *
 * Don't edit this file, instead copy it to 'craft/config' as 'craft-recaptcha.php'
 * and make your changes there to override default settings.
 *
 * Once copied to 'craft/config', this file will be multi-environment aware as
 * well, so you can have different settings groups for each environment, just as
 * you do for 'general.php'
 */

return [

    'siteKey' => '',
    'secretKey' => '',
    'threshold' => 0.5,
    'validateContactForm' => false,
    'validateUsersRegistration' => false,

];
