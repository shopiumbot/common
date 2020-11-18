<?php

namespace core\components;

use panix\engine\CMS;
use panix\engine\emoji\EmojiAsset;
use panix\ext\tinymce\TinyMceAsset;
use Yii;
use panix\ext\tinymce\TinyMce as BaseTinyMce;
use yii\helpers\Html;
use yii\helpers\Json;

class TinyMceTelegram extends BaseTinyMce
{


    public function init()
    {
        parent::init();
        //emoticons
        EmojiAsset::register($this->view);


        $assetsPlugins = Yii::$app->getAssetManager()->publish(Yii::getAlias("@telegram/components/tinymce-plugin-emoji"));

        $this->clientOptions['emoji_add_space'] = false; // emoji are quite wide, so a space is added automatically after each by default; this disables that extra space
        $this->clientOptions['emoji_show_groups'] = true;   // hides the tabs and dsiplays all emojis on one page
        $this->clientOptions['emoji_show_subgroups'] = true;    // hides the subheadings
        $this->clientOptions['emoji_show_tab_icons'] = true;


        $this->clientOptions['contextmenu'] = "link";
        $this->clientOptions['plugins'] = [
            "autoresize advlist autolink charmap print preview",
            "searchreplace visualblocks code fullscreen link",
            "insertdatetime contextmenu paste tinymceEmoji"//responsivefilemanager
        ];
        $this->clientOptions['link_title'] = false;
        $this->clientOptions['target_list'] = false;

        $this->clientOptions['formats'] = [
            //bold: {inline : 'span', 'classes' : 'bold'},
            // italic: {inline : 'span', 'classes' : 'italic'},
            // underline: {inline : 'span', 'classes' : 'underline', exact : true},
            'underline' => ['inline' => 'u'],
            'italic' => ['inline' => 'em'],
            'bold' => ['inline' => 'strong'],
            'strikethrough' => ['inline' => 's'],

        ];


        $this->clientOptions['menubar'] = false;
        $this->clientOptions['statusbar'] = false;
        $this->clientOptions['toolbar'] = "undo redo | bold italic underline strikethrough code | link tinymceEmoji"; // strikethrough blockquote
        $this->clientOptions['image_advtab'] = true;
        $this->clientOptions['forced_root_block'] = ''; // p
        $this->clientOptions['keep_styles'] = false;
        $this->clientOptions['remove_trailing_brs'] = true;

        // $this->clientOptions['valid_elements'] = 'a[href],code,pre,strong,b,i,em,s,strike,u,ins,br';
        /* $this->clientOptions['external_plugins'] = array_merge(
             ["tinymceEmoji" => $assetsPlugins[1] . "/plugin.js",],
             $this->clientOptions['external_plugins']
         );*/
        $this->clientOptions['external_plugins'] = ["tinymceEmoji" => $assetsPlugins[1] . "/plugin.js"];
        $this->clientOptions['entities'] = '160,nbsp,162,cent,8364,euro,163,pound';

        // CMS::dump($this->clientOptions);die;

    }
    protected function registerClientScript()
    {
        $js = [];
        $view = $this->getView();
        TinyMceAsset::register($view);


        $options = Json::encode($this->clientOptions);

        $js[] = "tinymce.init($options);";
        if ($this->triggerSaveOnBeforeValidateForm) {
            $js[] = "$('#{$this->options['id']}').parents('form').on('beforeValidate', function() { tinymce.triggerSave(); });";
        }
        $view->registerJs(implode("\n", $js));
    }

}
