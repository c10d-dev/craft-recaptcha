<?php
/**
 * craft-recaptcha plugin for Craft CMS
 *
 * Integrate reCAPTCHA validation into your forms.
 *
 * @link      https://c10d.dev
 * @copyright Copyright (c) Cédric Givord
 */

namespace c10d\craftrecaptcha\variables;

use c10d\craftrecaptcha\CraftRecaptcha;

use Craft;
use Twig\Markup;


/**
 * craft-recaptcha Variable
 *
 * Craft allows plugins to provide their own template variables, accessible from
 * the {{ craft }} global variable (e.g. {{ craft.recaptcha }}).
 *
 * https://craftcms.com/docs/plugins/variables
 *
 * @author    Cédric Givord
 * @package   CraftRecaptcha
 * @since     1.0.0
 */
class RecaptchaVariable
{
    // Public Methods
    // =========================================================================

    /**
     * Render the reCAPTCHA bind button script.
     *
     *     {{ craft.recaptcha.renderBindButton() }}
     *
     * @param string $button
     * @param string $id
     * @param array $options
     * @return Markup
     */
    public function renderBindButton(string $button, string $id = 'recaptcha-1', array $options = []): Markup
    {
        return CraftRecaptcha::$plugin->recaptcha->renderBindButton($button, $id, $options);
    }

    /**
     * Render the reCAPTCHA html submit button.
     *
     *     {{ craft.recaptcha.renderSubmitButton() }}
     *
     * @param string $label
     * @param string $id
     * @param array $options
     * @return Markup
     */
    public function renderSubmitButton(string $label = 'Submit', string $id = 'recaptcha-1', array $options = []): Markup
    {
        return CraftRecaptcha::$plugin->recaptcha->renderSubmitButton($label, $id, $options);
    }

    /**
     * Get the reCAPTCHA site key from settings.
     *
     *     {{ craft.recaptcha.sitekey() }}
     *
     * @return string
     */
    public function sitekey(): ?string
    {
        $settings = CraftRecaptcha::$plugin->getSettings();
        return $settings->getSiteKey();
    }
}
