<?php

namespace core\modules\contacts\models;

use Yii;
use panix\engine\SettingsModel;
use yii\validators\RequiredValidator;

/**
 * Class SettingsForm
 * @package core\modules\contacts\models
 */
class SettingsForm extends SettingsModel
{

    public static $category = 'contacts';
    protected $module = 'contacts';

    public $email;
    public $phone;
    public $address;
    public $feedback_tpl_body;
    public $feedback_captcha;
    public $schedule;


    public function rules()
    {
        return [
            ['schedule', 'validateSchedule', 'skipOnEmpty' => true],
            ['address', 'validateLang', 'skipOnEmpty' => true],
            [['email', 'feedback_captcha'], "required"],
            ['phone', 'validatePhones2', 'skipOnEmpty' => false],
            [['feedback_tpl_body'], 'string'],
        ];
    }

    /**
     * Phone number validation
     *
     * @param $attribute
     */
    public function validatePhones($attribute)
    {
        $items = $this->$attribute;
        if (!is_array($items)) {
            $items = [];
        }
        $multiple = true;
        if (!is_array($items)) {
            $multiple = false;
            $items = (array)$items;
        }
        foreach ($items as $index => $item) {
            $validator = new \yii\validators\NumberValidator();
            $error = null;
            $validator->validate($item, $error);
            if (!empty($error)) {
                $key = $attribute . ($multiple ? '[' . $index . ']' : '');
                $this->addError($key, $error);
            }
        }
    }

    public function validateLang($attribute)
    {
        $requiredValidator = new RequiredValidator();
        // $attributes = Json::decode($this->$attribute);
        $attributes = $this->$attribute;
        foreach ($attributes as $index => $row) {
            $error = null;
            $value = isset($row) ? $row : null;

            $requiredValidator->validate($value, $error);
            if (!empty($error)) {
                $key = $attribute . '[' . $index . ']';

                $this->addError($key, $error);
            }
        }
    }

    public function validatePhones2($attribute)
    {
        $requiredValidator = new RequiredValidator();
        $attributes = Yii::$app->request->post('SettingsForm');
        if (isset($attributes['phone'])) {
            foreach ($attributes['phone'] as $index => $row) {
                $error = null;
                foreach (['number'] as $name) { //, 'name'
                    $error = null;
                    $value = isset($row[$name]) ? $row[$name] : null;
                    $requiredValidator->validate($value, $error);
                    if (!empty($error)) {
                        $key = $attribute . '[' . $index . '][' . $name . ']';
                        // echo $key;
                        $this->addError($key, $error);
                    }
                }
            }
        }
    }

    public function validateSchedule($attribute)
    {
        $requiredValidator = new RequiredValidator();
        // $attributes = Json::decode($this->$attribute);
        $attributes = $this->$attribute;
        // var_dump($attributes);die;
        foreach ($attributes as $index => $row) {
            $error = null;
            /*foreach (['start_time', 'end_time'] as $name) {
                $error = null;
                $value = isset($row[$name]) ? $row[$name] : null;
                $requiredValidator->validate($value, $error);
                if (!empty($error)) {
                    $key = $attribute . '[' . $index . '][' . $name . ']';
                    $this->addError($key, $error);
                }
            }*/
        }
    }

    public static function dayList()
    {
        return [
            0 => self::t('MONDAY'),
            1 => self::t('TUESDAY'),
            2 => self::t('WEDNESDAY'),
            3 => self::t('THURSDAY'),
            4 => self::t('FRIDAY'),
            5 => self::t('SATURDAY'),
            6 => self::t('SUNDAY')
        ];
    }

    public static function defaultSettings()
    {
        return [
            'feedback_captcha' => true,
            'email' => 'me-email@example.com',
            'address' => '',
            'feedback_tpl_body' => '',
            'captcha_class' => '\yii\captcha\Captcha',
            'map_api_key' => ''
        ];
    }
}