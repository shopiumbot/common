<?php

namespace core\modules\shop\models;


use Yii;
use yii\helpers\ArrayHelper;
use panix\engine\behaviors\nestedsets\NestedSetsBehavior;
use core\modules\shop\models\query\CategoryQuery;
use panix\engine\CMS;
use core\components\ActiveRecord;
use panix\engine\behaviors\UploadFileBehavior;

/**
 * Class Category
 * @package core\modules\shop\models
 *
 * @property integer $id
 * @property integer $tree
 * @property integer $lft
 * @property integer $rgt
 * @property integer $depth
 * @property string $name
 * @property string $full_path
 * @property integer $switch
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $countItems Relation of getCountItems()
 */
class Category extends ActiveRecord
{

    const MODULE_ID = 'shop';
    const route = '/admin/shop/category';
    const route_update = 'index';
    public $parent_id;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%shop__category}}';
    }

    public static function find()
    {
        return new CategoryQuery(get_called_class());
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'trim'],
            [['name'], 'required'],
            [['chunk'], 'integer','min'=>1,'max'=>3],
            [['name', 'icon'], 'string', 'max' => 255],
            [['icon'], 'default'],
        ];
    }


    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $a['tree'] = [
            'class' => NestedSetsBehavior::class,
            'hasManyRoots' => false
        ];
        return ArrayHelper::merge($a, parent::behaviors());
    }

    /**
     * Relation ProductCategoryRef
     * @return int
     */
    public function getCountItems()
    {
        return $this->hasMany(ProductCategoryRef::class, ['category' => 'id'])->count();
    }

    public static function flatTree()
    {
        $result = [];
        $categories = Category::find()->orderBy(['lft' => SORT_ASC])->all();
        array_shift($categories);

        foreach ($categories as $c) {
            /**
             * @var self $c
             */
            if ($c->depth > 2) {
                $result[$c->id] = str_repeat(html_entity_decode('&mdash;'), $c->depth - 2) . ' ' . $c->name;
            } else {
                $result[$c->id] = ' ' . $c->name;
            }
        }

        return $result;
    }

    public function beforeSave($insert)
    {
        $this->rebuildFullPath();
        return parent::beforeSave($insert);
    }

    public function rebuildFullPath()
    {
        // Create category full path.
        $ancestors = $this->ancestors()
            //->orderBy('depth')
            ->all();
        if ($ancestors) {
            // Remove root category from path
            unset($ancestors[0]);

            $parts = [];
            foreach ($ancestors as $ancestor)
                $parts[] = $ancestor->name;

            $parts[] = $this->name;
            $this->path_hash = md5(implode('/', array_filter($parts)));
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {


        $childrens = $this->descendants()->all();
        if ($childrens) {
            foreach ($childrens as $children) {
                $children->saveNode(false);
            }
        }
        Yii::$app->cache->delete('CategoryUrlRule');
        return parent::afterSave($insert, $changedAttributes);
    }


    /**
     * @return string
     */
    public function title()
    {
        $value = $this->name;
        return $value;
    }


}
