<?php


namespace core\components;


use panix\ext\tinymce\TinyMceAsset;
use panix\ext\tinymce\TinyMceLangAsset;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\InputWidget;

class TinyMceMarkdown extends InputWidget
{

    /**
     * @var string the language to use. Defaults to null (en).
     */
    public $language;

    /**
     * @var array the options for the TinyMCE JS plugin.
     * Please refer to the TinyMCE JS plugin Web page for possible options.
     * @see https://www.tiny.cloud/docs-4x/
     */
    public $clientOptions = [];

    /**
     * @var bool whether to set the on change event for the editor. This is required to be able to validate data.
     */
    public $triggerSaveOnBeforeValidateForm = true;
    protected $assetsPlugins;

    public function init()
    {
        parent::init();
        $this->assetsPlugins = Yii::$app->getAssetManager()->publish(Yii::getAlias("@vendor/panix/wgt-tinymce/plugins"));


        $defaultClientOptions = [];
        $lang = Yii::$app->language;


        // $defaultClientOptions['contextmenu'] = "link image inserttable | cell row column deletetable";
        $defaultClientOptions['plugins'] = [
            "textpattern"
        ];


        $defaultClientOptions['textpattern_patterns'] = [
            ['start' => '*', 'end' => '*', 'format' => 'italic'],
            ['start' => '**', 'end' => '**', 'format' => 'bold'],
            ['start' => '#', 'format' => 'h1'],
            ['start' => '##', 'format' => 'h2'],
            ['start' => '###', 'format' => 'h3'],
            ['start' => '####', 'format' => 'h4'],
            ['start' => '#####', 'format' => 'h5'],
            ['start' => '######', 'format' => 'h6'],
            ['start' => '1. ', 'cmd' => 'InsertOrderedList'],
            ['start' => '* ', 'cmd' => 'InsertUnorderedList'],
            ['start' => '- ', 'cmd' => 'InsertUnorderedList'],
            ['start' => '//brb', 'replacement' => 'Be Right Back']
        ];

        $defaultClientOptions['selector'] = "#{$this->options['id']}";


        $view = $this->getView();
        $langAssetBundle = TinyMceLangAsset::register($view);
        if ($lang !== null && $lang !== 'en') {
            $langFile = "i18n/{$lang}.js";

            $langAssetBundle->js[] = $langFile;
            $this->clientOptions['language_url'] = $langAssetBundle->baseUrl . "/{$langFile}";
        }


        $this->clientOptions = ArrayHelper::merge($defaultClientOptions, $this->clientOptions);

    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        if ($this->hasModel()) {
            echo Html::activeTextarea($this->model, $this->attribute, $this->options);
        } else {
            echo Html::textarea($this->name, $this->value, $this->options);
        }


        $this->registerClientScript();
    }

    /**
     * Registers tinyMCE js plugin
     */
    protected function registerClientScript()
    {
        $js = [];
        $view = $this->getView();
        TinyMceAsset::register($view);


        if (isset(Yii::$app->controller->module)) {
            if (file_exists(Yii::getAlias(Yii::$app->getModule(Yii::$app->controller->module->id)->uploadAliasPath))) {
                // $moxiemanager_rootpath = Yii::$app->getModule(Yii::$app->controller->module->id)->uploadPath;

            }
        }

        $theme = Yii::$app->settings->get('app', 'theme');


        $bootstrapAsset = \yii\bootstrap4\BootstrapAsset::register($view);

        $this->clientOptions['content_css'][] = $bootstrapAsset->baseUrl . '/css/bootstrap.min.css';

        $options = Json::encode($this->clientOptions);

        $js[] = "tinymce.init($options);";
        if ($this->triggerSaveOnBeforeValidateForm) {
            $js[] = "$('#{$this->options['id']}').parents('form').on('beforeValidate', function() { tinymce.triggerSave(); });";
        }
        $view->registerJs(implode("\n", $js));
    }

}
