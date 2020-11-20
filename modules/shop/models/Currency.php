<?php

namespace core\modules\shop\models;

use core\components\ActiveRecord;
use core\components\models\Currencies;
use panix\engine\CMS;


/**
 * Class Currency
 * @property integer $id
 * @property boolean $is_main
 * @property string $name
 * @property float $rate
 * @property string $iso
 * @property string $symbol
 * @property string $is_default
 * @property boolean $penny
 * @property string $separator_thousandth
 * @property string $separator_hundredth
 */
class Currency extends ActiveRecord
{

    const MODULE_ID = 'shop';
    public $currency;
    public static $currencies;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%shop__currency}}';
    }

    public function init()
    {
        parent::init();
        self::$currencies = Currencies::find()->cache(86400 * 30)->select('iso')->asArray()->createCommand()->queryColumn();
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            //array('separator_hundredth, separator_thousandth', 'type', 'type' => 'string'),
            [['separator_hundredth', 'separator_thousandth'], 'string', 'max' => 5],
            [['name', 'rate', 'symbol', 'iso', 'penny'], 'required'],
            [['name'], 'trim'],
            [['is_main', 'is_default'], 'boolean'],
            [['name'], 'string', 'max' => 255],
            [['currency'], 'string', 'max' => 3],
            ['currency', 'in', 'range' => self::$currencies],
            [['ordern'], 'integer'],

            [['rate'], 'number'],
            [['name', 'rate', 'symbol', 'iso'], 'safe'],
        ];
    }

    public static function fpSeparator()
    {
        return [
            ' ' => self::t('SPACE'),
            ',' => self::t('COMMA'),
            '.' => self::t('DOT')
        ];
    }

}
