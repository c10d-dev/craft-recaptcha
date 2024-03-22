<?php
/**
 * craft-recaptcha plugin for Craft CMS
 *
 * Integrate reCAPTCHA validation into your forms.
 *
 * @link      https://c10d.dev
 * @copyright Copyright (c) Cédric Givord
 */

namespace c10d\craftrecaptcha\services;

use c10d\craftrecaptcha\CraftRecaptcha;
use c10d\craftrecaptcha\records\RecaptchaLogs;

use Craft;
use craft\base\Component;
use craft\web\View;
use craft\helpers\App;
use craft\helpers\Template;
use Twig\Markup;


/**
 * RecaptchaService Service
 *
 * All of your plugin’s business logic should go in services, including saving data,
 * retrieving data, etc. They provide APIs that your controllers, template variables,
 * and other plugins can interact with.
 *
 * https://craftcms.com/docs/plugins/services
 *
 * @author    Cédric Givord
 * @package   CraftRecaptcha
 * @since     1.0.0
 */
class RecaptchaService extends Component
{
    protected string $url = 'https://www.google.com/recaptcha/api/siteverify';

    // Public Methods
    // =========================================================================

    public function render(string $id = 'recaptcha-1', array $options = [], string $template = 'craft-recaptcha/_recaptcha'): Markup
    {
        $settings = CraftRecaptcha::$plugin->getSettings();

        // push recaptcha js file at the end of the html
        Craft::$app->view->registerJsFile('https://www.google.com/recaptcha/api.js?hl=' . Craft::$app->language, ['async']);

        // override options with plugin settings
        $options['sitekey'] = $settings->getSiteKey();

        // inject raw html
        return Template::raw(
            Craft::$app->view->renderTemplate($template, [
                'id' => $id,
                'options' => $options,
            ], View::TEMPLATE_MODE_CP)
        );
    }

    public function renderBindButton(string $button = 'submit-button', string $id = 'recaptcha-1', array $options = []): Markup
    {
        $options['button'] = $button;
        return $this->render($id, $options, 'craft-recaptcha/_bind-button');
    }

    public function renderSubmitButton(string $label = 'Submit', string $id = 'recaptcha-1', array $options = []): Markup
    {
        $options['label'] = $label;
        return $this->render($id, $options, 'craft-recaptcha/_submit-button');
    }

    public function verify($data): bool
    {
	if (App::env('CRAFT_RECAPTCHA_SKIP_VERIFICATION') ?? false) {
            return true;
	}

        $settings = CraftRecaptcha::$plugin->getSettings();
        $params = array(
            'secret' =>  $settings->getSecretKey(),
            'response' => $data
        );
        $log = new RecaptchaLogs();
        $log->siteId = Craft::$app->sites->getCurrentSite()->id;

        $curlRequest = curl_init();
        curl_setopt($curlRequest, CURLOPT_URL, $this->url);
        curl_setopt($curlRequest, CURLOPT_POST, true);
        curl_setopt($curlRequest, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($curlRequest, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curlRequest);

        if (Craft::$app->config->general->devMode) {
            $log->requestUrl = Craft::$app->request->getUrl();
            $log->requestBody = Craft::$app->request->getRawBody();
            $log->captchaJson = $response;
        }

        if (!curl_errno($curlRequest) && curl_getinfo($curlRequest, CURLINFO_HTTP_CODE) == 200) {
            $json = json_decode($response);
            if ($json->success && $json->hostname == Craft::$app->request->hostName && $json->score >= $settings->threshold) {
                curl_close($curlRequest);
                $log->success = true;
                $log->save(false);
                return true;
            }
        }

        curl_close($curlRequest);
        $log->success = false;
        $log->save(false);
        return false;
    }
}
