<?php

namespace core\components;

use Yii;
use yii\base\Module;


/**
 * Class WebModule
 * @package core\components
 */
class WebModule extends Module
{

    public $assetsUrl;
    public $count = false;
    public $routes = [];
    /**
     * @var array Model classes, e.g., ["User" => "app\modules\user\models\User"]
     * Usage:
     *   $user = Yii::$app->getModule("user")->model("User", $config);
     *   (equivalent to)
     *   $user = new \app\modules\user\models\User($config);
     *
     * The model classes here will be merged with/override the [[getDefaultModelClasses()|default ones]]
     */
    public $modelClasses = [];
    /**
     * @var array Storage for models based on $modelClasses
     */
    protected $_models;
    public $icon;
    public $uploadPath;
    public $uploadAliasPath = null;



    /**
     * Get object instance of model
     *
     * @param string $name
     * @param array $config
     * @return \yii\db\ActiveRecord
     */
    public function model($name, $config = [])
    {
        // return object if already created
        if (!empty($this->_models[$name])) {
            return $this->_models[$name];
        }

        // create model and return it
        $className = $this->modelClasses[ucfirst($name)];
        $this->_models[$name] = Yii::createObject(array_merge(["class" => $className], $config));
        return $this->_models[$name];
    }

    public function init()
    {
        // echo Yii::getAlias('@web');die;
        if (!in_array(Yii::$app->id, ['console', 'api'])) {
            if (file_exists(Yii::getAlias("@{$this->id}/assets"))) {
                $assetsPaths = Yii::$app->getAssetManager()->publish(Yii::getAlias("@{$this->id}/assets"));
                $this->assetsUrl = $assetsPaths[1];
            }
        }


        if (Yii::$app->id == 'console') {
            $reflector = new \ReflectionClass($this);
            if (file_exists(dirname($reflector->getFileName()) . DIRECTORY_SEPARATOR . 'commands')) {
                $this->controllerNamespace = $reflector->getNamespaceName() . "\\commands";
            }
        }
        //$this->registerTranslations();

        $this->uploadAliasPath = "@app/web/uploads/content/{$this->id}";
        $this->uploadPath = "/uploads/content/{$this->id}";

        if (method_exists($this, 'getDefaultModelClasses')) {
            $this->modelClasses = array_merge($this->getDefaultModelClasses(), $this->modelClasses);
        }


        parent::init();
    }



    // public function getIcon() {
    //    return $this->_icon;
    // }

    public function setIcon($icon)
    {
        $this->icon = $icon;
    }

    public function getAuthor()
    {
        return 'dev@pixelion.com.ua';
    }

    public function getName()
    {
        return Yii::t($this->id . "/default", 'MODULE_NAME');
    }

    public function getDescription()
    {
        return Yii::t($this->id . "/default", 'MODULE_DESC');
    }

    public function getWidgets()
    {
        if (file_exists(Yii::getAlias("@{$this->id}/widgets"))) {

        }
    }

    public function getAdminMenu()
    {
        return [];
    }
}
