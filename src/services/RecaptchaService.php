<?php
/**
 * craft-recaptcha plugin for Craft CMS 3.x
 *
 * Integrate reCAPTCHA validation into your forms.
 *
 * @link      https://c10d.dev
 * @copyright Copyright (c) Cédric Givord
 */

namespace c10d\craftrecaptcha\services;

use c10d\craftrecaptcha\CraftRecaptcha;

use Craft;
use craft\base\Component;
use craft\web\View;
use craft\helpers\Template;


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
    protected $url = 'https://www.google.com/recaptcha/api/siteverify';

    // Public Methods
    // =========================================================================

    public function render(string $id = 'recaptcha-1', array $options = [], string $template = 'craft-recaptcha/_recaptcha')
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

    public function renderBindButton(string $button = 'submit-button', string $id = 'recaptcha-1', array $options = [])
    {
	$options['button'] = $button;
        return $this->render($id, $options, 'craft-recaptcha/_bind-button');
    }

    public function renderSubmitButton(string $label = 'Submit', string $id = 'recaptcha-1', array $options = [])
    {
	$options['label'] = $label;
        return $this->render($id, $options, 'craft-recaptcha/_submit-button');
    }

    public function verify($data)
    {
        $settings = CraftRecaptcha::$plugin->getSettings();
        $params = array(
            'secret' =>  $settings->getSecretKey(),
            'response' => $data
        );
	//Craft::dd($params);

        $curlRequest = curl_init();
        curl_setopt($curlRequest, CURLOPT_URL, $this->url);
        curl_setopt($curlRequest, CURLOPT_POST, true);
        curl_setopt($curlRequest, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($curlRequest, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curlRequest);
        if (!curl_errno($curlRequest) && curl_getinfo($curlRequest, CURLINFO_HTTP_CODE) == 200) {
	    $json = json_decode($response);
	    if ($json->success && $json->hostname == Craft::$app->request->hostName && $json->score >= $settings->threshold) {
		curl_close($curlRequest);
		return true;
	    }
        }

        curl_close($curlRequest);
        return false;
    }
}
