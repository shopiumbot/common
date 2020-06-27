<?php

namespace core\modules\images\behaviors;


use panix\engine\CMS;
use panix\engine\components\ImageHandler;
use Yii;
use yii\base\Behavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use core\modules\images\models;
use core\modules\images\models\Image;
use yii\helpers\BaseFileHelper;
use yii\web\ForbiddenHttpException;
use yii\web\UploadedFile;

/**
 * Class ImageBehavior
 *
 * @property ActiveQuery $imageQuery
 * @package core\modules\images\behaviors
 */
class ImageBehavior extends Behavior
{
    public $attribute;
    public $createAliasMethod = false;
    public $path = '@uploads';
    protected $_file;
    private $imageQuery;

    public function attach($owner)
    {

        parent::attach($owner);


    }

    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'afterSave',
            ActiveRecord::EVENT_AFTER_UPDATE => 'afterSave',
            //ActiveRecord::EVENT_AFTER_FIND=>'test'
            ActiveRecord::EVENT_BEFORE_UPDATE => 'beforeSave',
            ActiveRecord::EVENT_AFTER_DELETE => 'afterDelete',

        ];
    }

    public function beforeSave()
    {
        /** @var ActiveRecord $owner */
        $owner = $this->owner;
        $owner->file = \yii\web\UploadedFile::getInstances($owner, 'file');
        if (count($owner->file) > Yii::$app->params['plan'][Yii::$app->user->planId]['product_upload_files']) {
            throw new ForbiddenHttpException();
        }

    }

    public function afterSave()
    {
        if (!Yii::$app instanceof \yii\console\Application) {
            $this->updateMainImage();
            $this->updateImageTitles();
        }
    }

    /**
     * @var ActiveRecord|null Model class, which will be used for storing image data in db, if not set default class(models/Image) will be used
     */

    /**
     *
     * Method copies image file to module store and creates db record.
     *
     * @param $file |string UploadedFile Or absolute url
     * @param bool $is_main
     * @param string $alt
     * @return bool|Image
     * @throws \Exception
     */
    public function attachImage($file, $is_main = false, $alt = '')
    {
        $uniqueName = \panix\engine\CMS::gen(10);


        if (!$this->owner->primaryKey) {
            throw new \Exception('Owner must have primaryKey when you attach image!');
        }


        if (!is_object($file)) {
            $pictureFileName = $uniqueName . '.' . pathinfo($file, PATHINFO_EXTENSION);
        } else {
            $pictureFileName = $uniqueName . '.' . $file->extension;
        }
        $path = Yii::getAlias($this->path) . DIRECTORY_SEPARATOR . $this->owner->primaryKey;
        $newAbsolutePath = $path . DIRECTORY_SEPARATOR . $pictureFileName;

        BaseFileHelper::createDirectory($path, 0775, true);


        $image = new Image;
        $image->product_id = $this->owner->primaryKey;
        $image->filePath = $pictureFileName;
        $image->path = $this->path;
        $image->urlAlias = $this->getAlias($image);

        if (!$image->save()) {

            return false;
        }

        if (count($image->getErrors()) > 0) {

            $ar = array_shift($image->getErrors());

            unlink($newAbsolutePath);
            throw new \Exception(array_shift($ar));
        }
        $img = $this->owner->getImage();

        //If main image not exists
        if ($img == null || $is_main) {
            $this->setMainImage($image);
        }

        /** @var ImageHandler $img */
        if (is_object($file)) {
            $file->saveAs($newAbsolutePath);
        } else {
            copy($file, $newAbsolutePath);
        }
        $img = Yii::$app->img->load($newAbsolutePath);
        if ($img->getHeight() > Yii::$app->params['maxUploadImageSize']['height'] || $img->getWidth() > Yii::$app->params['maxUploadImageSize']['width']) {
            $img->resize(Yii::$app->params['maxUploadImageSize']['width'], Yii::$app->params['maxUploadImageSize']['height']);
        }
        if ($img->save($newAbsolutePath)) {
            //   unlink($runtimePath);
        }

        return $image;
    }

    /**
     * Sets main image of model
     * @param $img
     * @throws \Exception
     */
    public function setMainImage($img)
    {

        if ($this->owner->primaryKey != $img->product_id) {
            throw new \Exception('Image must belong to this model');
        }
        $counter = 1;
        /* @var $img Image */
        $img->setMain(true);
        $img->urlAlias = $this->getAliasString() . '-' . $counter;
        $img->save();


        $images = $this->owner->getImages();
        foreach ($images as $allImg) {

            if ($allImg->id == $img->id) {
                continue;
            } else {
                $counter++;
            }

            $allImg->setMain(false);
            $allImg->urlAlias = $this->getAliasString() . '-' . $counter;
            $allImg->save();
        }

    }


    /**
     * Returns model images
     * First image alwats must be main image
     * @return array|yii\db\ActiveRecord[]
     */
    public function getImages($additionWhere = false)
    {

        $finder = $this->getImagesFinder($additionWhere);

        if (Yii::$app->getModule('images')->className === null) {
            $imageQuery = Image::find();
        } else {
            $class = Yii::$app->getModule('images')->className;
            $imageQuery = $class::find();
        }
        $imageQuery->where($finder);
        $imageQuery->orderBy(['is_main' => SORT_DESC]);
//'ordern' => SORT_DESC,
        $imageRecords = $imageQuery->all();

        return $imageRecords;
    }

    public function getImagesCount()
    {
        return Image::find()->where(['product_id' => $this->owner->primaryKey])->count();
    }

    /**
     * returns main model image
     * @param $main
     * @return array|null|ActiveRecord
     */
    public function getImage($main = 1)
    {
        $wheres['product_id'] = $this->owner->primaryKey;
        if ($main)
            $wheres['is_main'] = 1;
        $query = Image::find()->where($wheres);

        //echo $query->createCommand()->rawSql;die;
        $img = $query->one();

        if (!$img) {
            return NULL;
        }

        return $img;
    }


    /**
     * returns model image by name
     * @return array|null|ActiveRecord
     */
    public function getImageByName($name)
    {
        $query = Image::find()->where([
            'product_id' => $this->owner->primaryKey,
        ]);
        $query->andWhere(['name' => $name]);
        //    $imageQuery = Image::find();

        //$finder = $this->getImagesFinder(['name' => $name]);
        //$imageQuery->where($finder);
        //$imageQuery->orderBy(['is_main' => SORT_DESC, 'id' => SORT_ASC]);
        //$imageQuery->orderBy(['ordern' => SORT_DESC]);

        $img = $query->one();
        if (!$img) {
            return NULL;
        }

        return $img;
    }

    /**
     * Remove all model images
     */
    public function afterDelete()
    {
        $images = $this->owner->getImages();
        if (count($images) < 1) {
            return true;
        } else {
            foreach ($images as $image) {
                $this->owner->removeImage($image);
            }

            $path = Yii::getAlias($this->path) . DIRECTORY_SEPARATOR . $this->owner->primaryKey;
            BaseFileHelper::removeDirectory($path);
        }
    }


    /**
     * removes concrete model's image
     * @param Image $img
     * @throws \Exception
     * @return bool
     */
    public function removeImage(Image $img)
    {

        $storePath = Yii::$app->getModule('images')->getStorePath();

        $fileToRemove = $storePath . DIRECTORY_SEPARATOR . $img->filePath;
        if (preg_match('@\.@', $fileToRemove) and is_file($fileToRemove)) {
            unlink($fileToRemove);
        }
        $img->delete();
        return true;
    }

    private function getImagesFinder($additionWhere = false)
    {
        $base = [
            'product_id' => $this->owner->primaryKey,
        ];

        if ($additionWhere) {
            $base = \yii\helpers\BaseArrayHelper::merge($base, $additionWhere);
        }

        return $base;
    }

    /** Make string part of image's url
     * @return string
     * @throws \Exception
     */
    private function getAliasString()
    {
        if ($this->createAliasMethod) {
            $string = $this->owner->{$this->createAliasMethod}();
            if (!is_string($string)) {
                throw new \Exception("Image's url must be string!");
            } else {
                return $string;
            }

        } else {
            return substr(md5(microtime()), 0, 10);
        }
    }

    /**
     *
     * Обновить алиасы для картинок
     * Зачистить кэш
     */
    public function getAlias()
    {
        $aliasWords = $this->getAliasString();
        $imagesCount = count($this->owner->getImages());

        return $aliasWords . '-' . intval($imagesCount + 1);
    }

    protected function updateMainImage()
    {
        $post = Yii::$app->request->post('AttachmentsMainId');
        if ($post) {

            Image::updateAll(['is_main' => 0], 'product_id=:pid', ['pid' => $this->owner->primaryKey]);

            $customer = Image::findOne($post);
            if ($customer) {
                $customer->is_main = 1;
                $customer->update();
            }
        }
    }

    protected function updateImageTitles()
    {
        if (sizeof(Yii::$app->request->post('attachment_image_titles', []))) {
            foreach (Yii::$app->request->post('attachment_image_titles', []) as $id => $title) {
                if (!empty($title)) {
                    $customer = Image::findOne($id);
                    if ($customer) {
                        $customer->update();
                    }
                }
            }
        }
    }

}
