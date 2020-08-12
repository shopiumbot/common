<?php

namespace core\modules\shop\components;


use core\modules\images\models\Image;
use Yii;
use core\modules\shop\models\Product;
use panix\engine\CMS;
use yii\helpers\BaseFileHelper;

class ProductsDuplicator extends \yii\base\Component
{

    /**
     * @var array
     */
    private $_ids;

    /**
     * @var array
     */
    private $duplicate;

    /**
     * @var string to be appended to the end of product name
     */
    private $_suffix;

    public function __construct()
    {
        $this->_suffix = ' (' . Yii::t('shop/admin', 'копия') . ')';
        parent::__construct([]);
    }

    /**
     * Creates copy of many products.
     *
     * @param array $ids of products to make copy
     * @param array $duplicate list of product parts to copy: images, etc...
     * @return array of new product ids
     */
    public function createCopy(array $ids, array $duplicate = [])
    {

        $this->duplicate = $duplicate;
        $new_ids = [];

        foreach ($ids as $id) {
            $model = Product::findOne($id);

            if ($model) {
                $new_ids[] = $this->duplicateProduct($model)->id;
            }
        }

        return $new_ids;
    }

    /**
     * Duplicate one product and return model
     *
     * @param Product $model
     * @return Product
     */
    public function duplicateProduct(Product $model)
    {

        $product = new Product;
        $product->attributes = $model->attributes;

        $product->name .= $this->getSuffix();
        $product->main_category_id = $model->mainCategory->id;
        $product->added_to_cart_count = 0;
        $product->scenario = 'duplicate';
        if ($product->validate()) {
            if ($product->save()) {
                foreach ($this->duplicate as $feature) {
                    $method_name = 'copy' . ucfirst($feature);

                    if (method_exists($this, $method_name))
                        $this->$method_name($model, $product);
                }

                $categories = [];
                if ($model->categories) {
                    foreach ($model->categories as $category) {
                        $categories[] = $category->primaryKey;
                    }
                }
                $product->setCategories($categories, $model->mainCategory->id);
                return $product;
            } else {
                die(__FUNCTION__ . ': Error save');
                return false;
            }
        } else {

            print_r($product->getErrors());
            die;
        }
    }

    /**
     * Creates copy of product images
     *
     * @param Product $original
     * @param Product $copy
     */
    protected function copyImages(Product $original, Product $copy)
    {

        $images = $original->getImages();

        if (!empty($images)) {
            /** @var Image $image */
            foreach ($images as $image) {


                $uniqueName = \panix\engine\CMS::gen(10);

                $absolutePath = Yii::getAlias($image->path) . DIRECTORY_SEPARATOR . $original->primaryKey . DIRECTORY_SEPARATOR . $image->filePath;
                $pictureFileName = $uniqueName . '.' . pathinfo($absolutePath, PATHINFO_EXTENSION);

                $path = Yii::getAlias($image->path) . DIRECTORY_SEPARATOR . $copy->primaryKey;
                $newAbsolutePath = $path . DIRECTORY_SEPARATOR . $pictureFileName;


                $image_copy = new Image();

                $image_copy->product_id = $copy->id;
                $image_copy->is_main = $image->is_main;
                $image_copy->filePath = $pictureFileName;
                $image_copy->path = $image->path;
                $image_copy->urlAlias = $copy->getAlias();

                if ($image_copy->validate()) {
                    if ($image_copy->save()) {
                        BaseFileHelper::createDirectory($path, 0775, true);
                        if (file_exists($absolutePath))
                            copy($absolutePath, $newAbsolutePath);
                    }
                } else {
                    print_r($image_copy->getErrors());
                    die(__FUNCTION__ . ': Error validate');
                }
            }
        }
    }

    /**
     * Creates copy of EAV attributes
     *
     * @param Product $original
     * @param Product $copy
     */
    protected function copyAttributes(Product $original, Product $copy)
    {
        $attributes = $original->getEavAttributes();

        if (!empty($attributes)) {
            foreach ($attributes as $key => $val) {
                Yii::$app->db->createCommand()->insert('{{%shop__product_attribute_eav}}', [
                    'entity' => $copy->id,
                    'attribute' => $key,
                    'value' => $val
                ])->execute();
            }
        }
    }
    /**
     * @param $str string product suffix
     */
    public function setSuffix($str)
    {
        $this->_suffix = $str;
    }

    /**
     * @return string
     */
    public function getSuffix()
    {
        return $this->_suffix;
    }

}
