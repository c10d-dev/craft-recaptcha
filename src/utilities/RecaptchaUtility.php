<?php
/**
 * craft-recaptcha plugin for Craft CMS
 *
 * Integrate reCAPTCHA validation into your forms.
 *
 * @link      https://c10d.dev
 * @copyright Copyright (c) Cédric Givord
 */

namespace c10d\craftrecaptcha\utilities;

use c10d\craftrecaptcha\CraftRecaptcha;
use c10d\craftrecaptcha\records\RecaptchaLogs;

use Craft;
use craft\base\Utility;

/**
 * Craft reCAPTCHA Utility
 *
 * @author    Cédric Givord
 * @package   CraftRecaptcha
 * @since     1.1.0
 */
class RecaptchaUtility extends Utility
{
    // Static
    // =========================================================================

    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return 'reCAPTCHA';
    }

    /**
     * @inheritdoc
     */
    public static function id(): string
    {
        return 'recaptcha';
    }

    /**
     * @inheritdoc
     */
    public static function icon(): ?string
    {
        return Craft::getAlias("@c10d/craftrecaptcha/icon.svg");
    }

    /**
     * @inheritdoc
     */
    public static function badgeCount(): int
    {
        return 0;
    }

    /**
     * @inheritdoc
     */
    public static function contentHtml(): string
    {
        $total = RecaptchaLogs::find()->count();
        $success = $total > 0 ? round((RecaptchaLogs::find()->where(['success' => 1])->count() / $total) * 100) : 0;
        $failure = $total > 0 ? round((RecaptchaLogs::find()->where(['success' => 0])->count() / $total) * 100) : 0;
        $logs = RecaptchaLogs::find()->where(['not', ['requestUrl' => null]])->orderBy('dateCreated desc')->limit(10)->all();
        return Craft::$app->getView()->renderTemplate(
            'craft-recaptcha/utility',
            [
                'total' => $total,
                'success_rate' => $success,
                'failure_rate' => $failure,
                'logs' => $logs,
            ]
        );
    }
}
