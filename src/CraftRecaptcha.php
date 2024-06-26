<?php
/**
 * craft-recaptcha plugin for Craft CMS
 *
 * Integrate reCAPTCHA validation into your forms.
 *
 * @link      https://c10d.dev
 * @copyright Copyright (c) Cédric Givord
 */

namespace c10d\craftrecaptcha;

use c10d\craftrecaptcha\services\RecaptchaService;
use c10d\craftrecaptcha\variables\RecaptchaVariable;
use c10d\craftrecaptcha\models\SettingsModel;
use c10d\craftrecaptcha\utilities\RecaptchaUtility;

use Craft;
use craft\base\Model;
use craft\base\Plugin;
use craft\contactform\models\Submission;
use craft\elements\User;
use craft\events\PluginEvent;
use craft\events\RegisterComponentTypesEvent;
use craft\events\RegisterUrlRulesEvent;
use craft\services\Plugins;
use craft\services\Utilities;
use craft\web\UrlManager;
use craft\web\twig\variables\CraftVariable;

use yii\base\Event;
use yii\base\ModelEvent;


/**
 * Craft plugins are very much like little applications in and of themselves. We’ve made
 * it as simple as we can, but the training wheels are off. A little prior knowledge is
 * going to be required to write a plugin.
 *
 * For the purposes of the plugin docs, we’re going to assume that you know PHP and SQL,
 * as well as some semi-advanced concepts like object-oriented programming and PHP namespaces.
 *
 * https://docs.craftcms.com/v3/extend/
 *
 * @author    Cédric Givord
 * @package   CraftRecaptcha
 * @since     1.0.0
 *
 * @property  RecaptchaService $recaptcha
 * @property  SettingsModel $settings
 * @method    SettingsModel getSettings()
 */
class CraftRecaptcha extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * Static property that is an instance of this plugin class so that it can be accessed via
     * CraftRecaptcha::$plugin
     *
     * @var CraftRecaptcha
     */
    public static CraftRecaptcha $plugin;

    // Public Properties
    // =========================================================================

    /**
     * To execute your plugin’s migrations, you’ll need to increase its schema version.
     *
     * @var string
     */
    public string $schemaVersion = '1.0.0';

    /**
     * Set to `true` if the plugin should have a settings view in the control panel.
     *
     * @var bool
     */
    public bool $hasCpSettings = true;

    /**
     * Set to `true` if the plugin should have its own section (main nav item) in the control panel.
     *
     * @var bool
     */
    public bool $hasCpSection = false;

    // Public Methods
    // =========================================================================

    /**
     * Set our $plugin static property to this class so that it can be accessed via
     * CraftRecaptcha::$plugin
     *
     * Called after the plugin class is instantiated; do any one-time initialization
     * here such as hooks and events.
     *
     * If you have a '/vendor/autoload.php' file, it will be loaded for you automatically;
     * you do not need to load it in your init() method.
     *
     */
    public function init(): void
    {
        parent::init();
        self::$plugin = $this;

        // Register our utilities
        Event::on(
            Utilities::class,
            Utilities::EVENT_REGISTER_UTILITIES,
            function (RegisterComponentTypesEvent $event) {
                $event->types[] = RecaptchaUtility::class;
            }
        );

        // Register our variables
        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function (Event $event) {
                /** @var CraftVariable $variable */
                $variable = $event->sender;
                $variable->set('recaptcha', RecaptchaVariable::class);
            }
        );

        // Set up contact form hook.
        $settings = CraftRecaptcha::$plugin->getSettings();
        if (class_exists(Submission::class) && $settings->validateContactForm) {
            Event::on(Submission::class, Submission::EVENT_BEFORE_VALIDATE, function (ModelEvent $event) {
                /** @var Submission $submission */
                $submission = $event->sender;

                $captcha = Craft::$app->getRequest()->getParam('g-recaptcha-response');
                $isValid = CraftRecaptcha::$plugin->recaptcha->verify($captcha);
                if (!$isValid) {
                    $submission->addError('recaptcha', Craft::t('craft-recaptcha', 'Please verify you are human.'));
                    $event->isValid = false;
                }
            });
        }

        // Set up user registration hook.
        if ($settings->validateUsersRegistration && Craft::$app->getRequest()->getIsSiteRequest()) {
            Event::on(User::class, User::EVENT_BEFORE_VALIDATE, function (ModelEvent $event) {
                /** @var User $user */
                $user = $event->sender;

                // Only validate captcha on new users
                if ($user->id === null && $user->uid === null) {
                    $captcha = Craft::$app->getRequest()->getParam('g-recaptcha-response');
                    $isValid = CraftRecaptcha::$plugin->recaptcha->verify($captcha);
                    if (!$isValid) {
                        $user->addError('recaptcha', Craft::t('craft-recaptcha', 'Please verify you are human.'));
                        $event->isValid = false;
                    }
                }
            });
        }

        /**
         * Logging in Craft involves using one of the following methods:
         *
         * Craft::trace(): record a message to trace how a piece of code runs. This is mainly for development use.
         * Craft::info(): record a message that conveys some useful information.
         * Craft::warning(): record a warning message that indicates something unexpected has happened.
         * Craft::error(): record a fatal error that should be investigated as soon as possible.
         *
         * Unless `devMode` is on, only Craft::warning() & Craft::error() will log to `craft/storage/logs/web.log`
         *
         * It's recommended that you pass in the magic constant `__METHOD__` as the second parameter, which sets
         * the category to the method (prefixed with the fully qualified class name) where the constant appears.
         *
         * To enable the Yii debug toolbar, go to your user account in the AdminCP and check the
         * [] Show the debug toolbar on the front end & [] Show the debug toolbar on the Control Panel
         *
         * http://www.yiiframework.com/doc-2.0/guide-runtime-logging.html
         */
        Craft::info(
            Craft::t(
                'craft-recaptcha',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );
    }

    // Protected Methods
    // =========================================================================

    /**
     * Creates and returns the model used to store the plugin’s settings.
     *
     * @return Model|null
     */
    protected function createSettingsModel(): ?Model
    {
        return new SettingsModel();
    }

    /**
     * Returns the rendered settings HTML, which will be inserted into the content
     * block on the settings page.
     *
     * @return string The rendered settings HTML
     */
    protected function settingsHtml(): ?string
    {
        return Craft::$app->view->renderTemplate(
            'craft-recaptcha/settings',
            [
                'settings' => $this->getSettings()
            ]
        );
    }
}
