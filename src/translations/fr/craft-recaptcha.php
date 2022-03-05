<?php
/**
 * craft-recaptcha plugin for Craft CMS 3.x
 *
 * Integrate reCAPTCHA validation into your forms.
 *
 * @link      https://c10d.dev
 * @copyright Copyright (c) Cédric Givord
 */

/**
 * craft-recaptcha FR translation
 *
 * Returns an array with the string to be translated (as passed to `Craft::t('craft-recaptcha', '...')`) as
 * the key, and the translation as the value.
 *
 * http://www.yiiframework.com/doc-2.0/guide-tutorial-i18n.html
 *
 * @author    Cédric Givord
 * @package   CraftRecaptcha
 * @since     1.0.0
 */
return [

    'craft-recaptcha plugin loaded' => 'extension craft-recaptcha chargée',

    'Site Key' => 'Clé du site',
    'Enter your reCAPTCHA site key.' => 'Veuillez entrer votre clé de site reCAPTCHA.',

    'Secret Key' => 'Clé secrète',
    'Enter your reCAPTCHA secret key.' => 'Veuillez entrer votre clé secrète reCAPTCHA.',

    'Threshold' => 'Difficulté',
    'Enter your reCAPTCHA threshold preference.' => 'Entrez votre préférence de difficulté pour le reCAPTCHA (nombre entre 0, très facile, et 1, très difficile).',

    'Validate contact forms?' => 'Validation du formulaire de contact ?',
    'Enable to automatically validate reCAPTCHAs when using the official [Craft CMS contact form plugin](https://github.com/craftcms/contact-form).' => 'Activez pour automatiquement valider le reCAPTCHA avec l\'utilisation de l\'extension [Craft CMS contact form](https://github.com/craftcms/contact-form).',

    'Validate users registration?' => 'Validation des enregistrements utilisateurs ?',
    'Enable to automatically validate reCAPTCHAs when using public users registration.' => 'Activez pour automatiquement valider le reCAPTCHA lors de l\'utilisation de l\'enregistrement publique des utilisateurs.',

    'Please verify you are human.' => 'Veuillez valider que vous êtes un humain.',

    'Total count' => 'Nombre total ',
    'Success rate' => 'Taux de succès ',
    'Error rate' => 'Taux d\'erreur ',

    'Logs' => 'Journaux',
    'success' => 'succès',
    'failure' => 'échec',
];
