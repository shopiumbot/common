<?php

namespace core\modules\shop\components;

/**
 * Find objects by external id
 */


use Yii;
use yii\db\Query;
use core\modules\images\models\Image;
use core\modules\shop\models\Attribute;
use core\modules\shop\models\AttributeOption;
use core\modules\shop\models\Category;
use core\modules\shop\models\Manufacturer;
use core\modules\shop\models\Product;

class ExternalFinder
{


    const OBJECT_CATEGORY = 1;
    const OBJECT_ATTRIBUTE = 2;
    const OBJECT_PRODUCT = 3;
    const OBJECT_MANUFACTURER = 4;
    const OBJECT_ATTRIBUTE_OPTION = 5;
    const OBJECT_IMAGE = 6;
    const OBJECT_MAIN_CATEGORY = 7;
    public $cacheData;
    public $table;

    public function __construct($table = '{{%exchange}}')
    {
        $this->table = $table;

    }

    /**
     * @param $type
     * @param $externalId
     * @param bool $loadModel
     * @param bool $object_id
     * @return array|mixed $query
     */
    public function getObject($type, $externalId, $loadModel = true, $object_id = false)
    {

        if (isset($this->cacheData[$type][$externalId]))
            return $this->cacheData[$type][$externalId];

        if ($object_id) {
            $query = Yii::$app->db->createCommand()
                //->select("*")
                ->from($this->table)
                ->where('object_type=:type AND external_id=:externalId AND object_id=:object_id', [
                    ':type' => $type,
                    ':externalId' => $externalId,
                    ':object_id' => $object_id
                ])
                ->limit(1)
                ->queryOne();
        } else {

            $query = (new Query())
                ->select('*')
                ->from($this->table)
                ->where('object_type=:type AND external_id=:externalId', [
                    ':type' => $type,
                    ':externalId' => $externalId
                ])
                ->limit(1)
                ->createCommand()
                ->queryOne();

        }

        if ($query === false)
            return false;

        if ($loadModel === true && $query['object_id']) {
            switch ($type) {
                case self::OBJECT_CATEGORY:
                    $data = Category::findOne($query['object_id']);
                    $this->cacheData[$type][$externalId] = $data;
                    return $data;
                    break;

                case self::OBJECT_MAIN_CATEGORY:
                    $data = Category::findOne($query['object_id']);
                    $this->cacheData[$type][$externalId] = $data;
                    return $data;
                    break;

                case self::OBJECT_ATTRIBUTE:
                    $data = Attribute::findOne($query['object_id']);
                    $this->cacheData[$type][$externalId] = $data;
                    return $data;
                    break;

                case self::OBJECT_PRODUCT:
                    $data = Product::findOne($query['object_id']);
                    $this->cacheData[$type][$externalId] = $data;
                    return $data;
                    break;

                case self::OBJECT_MANUFACTURER:
                    $data = Manufacturer::findOne($query['object_id']);
                    return $data;
                    break;

                case self::OBJECT_ATTRIBUTE_OPTION:
                    $data = AttributeOption::findOne($query['object_id']);
                    return $data;
                    break;

                case self::OBJECT_IMAGE:
                    $data = Image::findOne($query['object_id']);
                    $this->cacheData[$type][$externalId] = $data;
                    return $data;
                    break;
            }
        }
        $this->cacheData[$type][$externalId] = $query['object_id'];
        return $query['object_id'];
    }

    public function removeByExternal($type, $external_id)
    {


        $query = Yii::$app->db->createCommand()->delete(
            $this->table,
            'object_type=:type AND external_id=:external_id',
            [
                ':type' => $type,
                ':external_id' => $external_id
            ]
        )->execute();
    }

    public function removeByObject($type, $object_id)
    {
        $query = Yii::$app->db->createCommand()->delete(
            $this->table,
            'object_type=:type AND object_id=:object_id',
            [
                ':type' => $type,
                ':object_id' => $object_id
            ]
        )->execute();

    }

    /**
     * Create external
     *
     * @param $type
     * @param $id
     * @param $externalId
     * @return int
     */
    public function createExternalId($type, $id, $externalId)
    {
        Yii::$app->db->createCommand()->insert($this->table, [
            'object_type' => $type,
            'object_id' => $id,
            'external_id' => $externalId
        ])->execute();
    }
}
