<?php

namespace core\modules\contacts\models;


use shopium\mod\discounts\components\DiscountBehavior;
use core\modules\images\models\Image;
use panix\mod\sitemap\behaviors\SitemapBehavior;
use panix\mod\user\models\User;
use Yii;
use panix\engine\CMS;
use core\modules\shop\models\query\ProductQuery;
use yii\caching\DbDependency;
use yii\caching\TagDependency;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use panix\engine\db\ActiveRecord;
use yii\web\NotFoundHttpException;

/**
 * Class Product
 * @property integer $id ID
 */
class Feedback extends ActiveRecord
{


    const route = '/admin/contacts/default';
    const MODULE_ID = 'contacts';

    public static function find2()
    {
        return new ProductQuery(get_called_class());
    }


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%telegram_feedback}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {

        $rules = [];


        $rules[] = [['main_category_id', 'price', 'unit','name'], 'required'];
        $rules[] = ['price', 'commaToDot'];
        $rules[] = [['file'], 'file', 'maxFiles' => Yii::$app->params['plan'][Yii::$app->user->planId]['product_upload_files']];
        $rules[] = [['file'], 'validateLimit'];
        $rules[] = [['name'], 'string', 'max' => 255];
        $rules[] = [['image'], 'image'];
        $rules[] = [['name'], 'unique'];
        $rules[] = [['name'], 'trim'];
        $rules[] = [['description'], 'string'];
		$rules[] = [['unit'], 'default', 'value' => 1];
        $rules[] = [['sku', 'description', 'label', 'discount'], 'default']; // установим ... как NULL, если они пустые
        $rules[] = [['price'], 'double'];
        $rules[] = [['manufacturer_id', 'type_id', 'quantity', 'availability', 'added_to_cart_count', 'ordern', 'category_id', 'currency_id', 'label'], 'integer'];
        $rules[] = [['name', 'description'], 'safe'];

        return $rules;
    }


    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id'])->cache(3600);
    }


}
