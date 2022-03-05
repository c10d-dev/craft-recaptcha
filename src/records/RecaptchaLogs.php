<?php
/**
 * craft-recaptcha plugin for Craft CMS 3.x
 *
 * Integrate reCAPTCHA validation into your forms.
 *
 * @link      https://c10d.dev
 * @copyright Copyright (c) Cédric Givord
 */

namespace c10d\craftrecaptcha\records;

use c10d\craftrecaptcha\CraftRecaptcha;

use Craft;
use craft\db\ActiveRecord;

/**
 * @author    Cédric Givord
 * @package   CraftRecaptcha
 * @since     1.1.0
 */
class RecaptchaLogs extends ActiveRecord
{
    // Public Static Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%craftrecaptcha_logs}}';
    }
}
