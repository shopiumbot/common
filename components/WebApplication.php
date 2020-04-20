<?php

namespace core\components;

use Yii;
use yii\web\Application;
use panix\engine\Html;
/**
 * Class WebApplication
 * @package panix\engine
 * @property array $counters
 * @property \panix\engine\components\Settings $settings The user component. This property is read-only.
 * @property ManagerLanguage $languageManager The user component. This property is read-only.
 * @property \panix\engine\db\Connection $db The database connection. This property is read-only.
 *
 */
class WebApplication extends Application
{

    const version = '2.0.0-alpha';
    public $counters = [];

    public function run()
    {
        $this->language = 'ru';
        return parent::run();
    }


    public function getModulesInfo()
    {
        $modules = $this->getModules();
        if (YII_DEBUG)
            unset($modules['debug'], $modules['gii'], $modules['admin']);
        $result = [];
        foreach ($modules as $name => $className) {
            //$info = $this->getModule($name)->info;
            if (isset($this->getModule($name)->info))
                $result[$name] = $this->getModule($name)->info;
        }

        return $result;
    }

    public static function powered()
    {
        return Yii::t('app/default', 'COPYRIGHT', [
            'year' => date('Y'),
            'by' => Html::a('PIXELION CMS', '//pixelion.com.ua')
        ]);
    }

    public function getVersion()
    {
        return self::version;
    }

    public function init()
    {
        foreach ($this->getModules() as $id => $module) {
            if (isset($module['class'])) {
                $reflectionClass = new \ReflectionClass($module['class']);
                $this->setAliases([
                    '@' . $id => realpath(dirname($reflectionClass->getFileName())),
                ]);
                $this->registerTranslations($id);
            }
        }
        $modulesList = array_filter(glob(Yii::getAlias('@app/modules/*')), 'is_dir');
        foreach ($modulesList as $module) {
            $id = basename($module);
            $this->setAliases([
                '@' . $id => realpath(Yii::getAlias("@app/modules/{$id}")),
            ]);
            $this->registerTranslations($id);
        }

        parent::init();
    }


    /**
     * @param string $id
     * @param string $path
     * @return array
     */
    public function getTranslationsFileMap($id, $path)
    {
        $lang = $this->language;
        $result = [];
        $basePath = realpath(Yii::getAlias("{$path}/{$lang}"));

        if (is_dir($basePath)) {
            $fileList = \yii\helpers\FileHelper::findFiles($basePath, [
                'only' => ['*.php'],
                'recursive' => false
            ]);
            foreach ($fileList as $path) {
                $result[$id . '/' . basename($path, '.php')] = basename($path);
            }
        }
        return $result;
    }

    public function registerTranslations($id)
    {
        $path = '@' . $id . '/messages';
        $this->i18n->translations[$id . '/*'] = Yii::createObject([
            'class' => 'yii\i18n\PhpMessageSource',
            'sourceLanguage' => 'en-US',
            'basePath' => $path,
            'fileMap' => $this->getTranslationsFileMap($id, $path)
        ]);
    }



}
