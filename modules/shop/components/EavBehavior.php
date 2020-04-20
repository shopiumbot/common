<?php

namespace core\modules\shop\components;

use core\modules\shop\models\AttributeOption;
use Yii;
use yii\base\Exception;
use yii\db\ActiveRecord;
use yii\db\Query;

class EavBehavior extends \yii\base\Behavior
{

    /**
     * @access public
     * @var string name of the table where data is stored. Required to be set on init behavior.
     * @default ''
     */
    public $tableName = '';

    /**
     * @access public
     * @var string prefix for each attribute.
     * @default ''
     */
    public $attributesPrefix = '';

    /**
     * @access protected
     * @var string owner model FK name. If not set automatically assign to model's primaryKey.
     * @default ''
     */
    protected $modelTableFk = '';

    /**
     * @access public
     * @var string name of the column to store entity name.
     * @default 'entity'
     */
    public $entityField = 'entity';

    /**
     * @access public
     * @var string name of the column to store attribute key.
     * @default 'attribute'
     */
    public $attributeField = 'attribute';

    /**
     * @access public
     * @var string name of the column to store value.
     * @default 'value'
     */
    public $valueField = 'value';

    /**
     * @access public
     * @var string caching component Id.
     * @default ''
     */
    public $cacheId = 'cache';

    /**
     * @access protected
     * @var \yii\caching\Cache cache component object.
     * @default NULL
     */
    protected $cache = NULL;

    /**
     * @access protected
     * @var array attributes store.
     * @default new array
     */
    protected $attributes = [];

    /**
     * @access protected
     * @var array changed attributes list.
     * @default new array
     */
    protected $changedAttributes = [];

    /**
     * @access protected
     * @var array safe attributes list.
     * @default new array
     */
    protected $safeAttributes = [];

    /**
     * @access public
     * @var boolean loaded attributes after find model.
     * @default TRUE
     */
    public $preload = TRUE;

    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_FIND => 'afterFind',
            ActiveRecord::EVENT_AFTER_DELETE => 'afterDelete',
            //ActiveRecord::EVENT_BEFORE_INSERT => 'beforeSave',
        ];
    }

    /**
     * Returns owner model id.
     * @access protected
     * @return mixed
     */
    protected function getModelId()
    {
        return $this->owner->{$this->getModelTableFk()};
    }

    /**
     * Returns key for caching model attributes.
     * @access protected
     * @return string
     */
    protected function getCacheKey()
    {
        return __CLASS__ . $this->tableName . $this->attributesPrefix . $this->owner->tableName() . $this->getModelId();
    }

    /**
     * Set owner model FK name.
     * @param string owner model FK name.
     * @return void
     */
    public function setModelTableFk($modelTableFk)
    {
        if (is_string($modelTableFk) && !empty($modelTableFk)) {
            $this->modelTableFk = $modelTableFk;
        }
    }

    /**
     * Returns owner model FK name.
     * @access protected
     * @throws \yii\base\UnknownPropertyException
     * @return string
     */
    protected function getModelTableFk()
    {
        // Check required property modelTableFk.
        if (empty($this->modelTableFk) || !$this->owner->hasAttribute($this->modelTableFk)) {
            // If property modelTableFk not set, trying to get a primary key from model table.
            $this->modelTableFk = $this->owner->getTableSchema()->primaryKey[0];

            if (!is_string($this->modelTableFk)) {
                throw new \yii\base\UnknownPropertyException(Yii::t('app/default', 'Table "{table}" does not have a primary key defined.', array('{table}' => $this->owner->getTableSchema())));
            }
        }
        return $this->modelTableFk;
    }

    /**
     * Strip prefix from attribute key.
     * @access protected
     * @param string $attribute key
     * @return string
     */
    protected function stripPrefix($attribute)
    {
        // Remove prefix if exists.
        if (!empty($this->attributesPrefix) && strpos($attribute, $this->attributesPrefix) === 0) {
            $attribute = substr($attribute, strlen($this->attributesPrefix));
        }
        return $attribute;
    }

    /**
     * Set safe attributes array.
     * @param array $safeAttributes attributes.
     * @return void
     */
    public function setSafeAttributes($safeAttributes)
    {
        $this->safeAttributes[] = $safeAttributes;
    }

    /**
     * Return safe attributes key. If not set returns all keys.
     * @access protected
     * @return array
     */
    protected function getSafeAttributesArray()
    {

        return count($this->safeAttributes) == 0 ? array_keys($this->attributes) : $this->safeAttributes;
    }

    /**
     * @access protected
     * @param string $attribute key
     * @return boolean
     */
    protected function hasSafeAttribute($attribute)
    {
        if (count($this->safeAttributes) > 0) {
            return $this->safeAttributes->contains($attribute);
        }
        return TRUE;
    }

    /**
     * EavBehavior constructor.
     * @param array $config
     */
    public function __construct($config = [])
    {
        // Prepare attributes collection.
        //  $this->attributes = new CAttributeCollection;
        $this->attributes = [];

        //$this->attributes->caseSensitive = true;
        // Prepare safe attributes list.
        $this->safeAttributes = [];

        // Prepare changed attributes list.
        $this->changedAttributes = [];

        parent::__construct($config);
    }

    /**
     * @param \yii\base\Component $owner
     * @throws Exception
     */
    public function attach($owner)
    {
        // Check required property tableName.
        if (!is_string($this->tableName) || empty($this->tableName)) {
            throw new Exception(Yii::t('yii', 'Property "{class}.{property}" is not defined.', array('{class}' => get_class($this), '{property}' => 'tableName')));
        }
        // Prepare translate component for behavior messages.
        /*  if (!Yii::$app->hasComponent(__CLASS__ . 'Messages')) {
          Yii::$app->setComponents(array(
          __CLASS__ . 'Messages' => array(
          'class' => 'CPhpMessageSource',
          'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . 'messages',
          )
          ));
          } */
        // Prepare cache component.
        $this->cache = Yii::$app->{$this->cacheId};
        if (!($this->cache instanceof \yii\caching\Cache)) {
            // If not set cache component, use dummy cache.
            $this->cache = new \yii\caching\DummyCache();
        }
        // Call parent method for convenience.
        parent::attach($owner);
    }

    /**
     * @param Event
     * @return void
     */
    public function afterSave()
    {
        // TODO afterSave не срабатывает если модель не была изменена
        // Save changed attributes.
        //  if ($this->changedAttributes->count > 0) {
        $this->saveEavAttributes($this->changedAttributes);
        // }
        // Call parent method for convenience.
    }

    /**
     * @param Event
     * @return void
     */
    public function afterDelete()
    {    // Delete all attributes.
        $this->deleteEavAttributes([], TRUE);
        // Call parent method for convenience.
    }

    /**
     * @param Event
     * @return void
     */
    public function afterFind()
    {
        /** @var ActiveRecord $owner */
        $owner = $this->owner;
        // Load attributes for model.
        if ($this->preload) {
            if ($owner->getPrimaryKey()) {
                $this->loadEavAttributes($this->getSafeAttributesArray());
            }
        }
    }

    /**
     * @param array $attributes key for save.
     * @return null|\yii\base\Component
     */
    public function saveEavAttributes($attributes)
    {
        // Delete old attributes values from DB.
        $this->getDeleteCommand($attributes)->execute();
        // Process each attributes.


        foreach ($attributes as $attribute) {

            // Skip if null attributes.
            $attr = (isset($this->attributes[$attribute])) ? $this->attributes[$attribute] : NULL;
            if (!is_null($values = $attr)) {
                // Create array of values for convenience.
                if (!is_array($values)) {
                    $values = [$values];
                }
                // Save each value of attribute into DB.
                foreach ($values as $value) {
                    $this->getSaveEavAttributeCommand($this->attributesPrefix . $attribute, $value)->execute();
                }
                // Remove from changed list.
                unset($this->changedAttributes[$attribute]);
            }
        }
        // Save attributes to cache.
        if (count($this->attributes) > 0) {
            $this->cache->set($this->getCacheKey(), $this->attributes);
        } // Or delete cache is attributes not exists.
        else {
            $this->cache->delete($this->getCacheKey());
        }
        // Return model.
        return $this->owner;
    }

    /**
     * @access public
     * @param array $attributes key for load.
     * @return null|\yii\base\Component|ActiveRecord
     */
    public function loadEavAttributes($attributes)
    {
        $data = $this->getLoadEavAttributesQuery($attributes)->all();
        foreach ($data as $row) {
            $attribute = $this->stripPrefix($row[$this->attributeField]);
            $value = $row[$this->valueField];

            if (!is_int($value)) {
                $pattern = '/^(\")(.*)(\")$/i';
                $replacement = '$2';
                $value = preg_replace($pattern, $replacement, $value);
            }

            // Check if value exists.
            $attr = (isset($this->attributes[$attribute])) ? $this->attributes[$attribute] : NULL;
            if (!is_null($current = $attr) && $current != $value) {
                //$value = is_array($current) ? $current[] = $value : array($current, $value);
                // Fix if entity has many values
                if (is_array($current)) {
                    $current[] = $value;
                    $value = $current;
                } else
                    $value = [$current, $value];
            }

            $this->attributes[$attribute] = $value;


        }
        //    print_r($this->attributes);die;
        // echo unserialize('s:3:"123";');

        // Save loaded attributes to cache.
        //$this->cache->set($this->getCacheKey(), $this->attributes->toArray());
        // Return model.

        return $this->owner;


    }

    /**
     * @param array $attributes key for delete.
     * @param bool $save whether auto attributes.
     * @return ActiveRecord|\yii\base\Component
     */
    public function deleteEavAttributes($attributes = [], $save = FALSE)
    {


        // If not set attributes for deleting, delete all.
        if (empty($attributes)) {
            $attributes = array_keys($this->attributes);
        }

        // Delete each attributes.
        foreach ($attributes as $attribute) {
            unset($this->attributes[$attribute]);
            $this->changedAttributes[] = $attribute;
        }
        // Auto save if set.
        if ($save) {
            $this->saveEavAttributes($attributes);
        }
        // Return model.
        return $this->owner;
    }

    /**
     * @param array $attributes values for change.
     * @param bool $save whether auto save attributes.
     * @return ActiveRecord
     */
    public function setEavAttributes($attributes, $save = false)
    {
        // CMS::dump($this->attributes);die;
        foreach ($attributes as $attribute => $value) {
            $this->attributes[$attribute] = $value;
            $this->changedAttributes[] = $attribute;
        }
        // Auto save if set.
        if ($save) {
            $this->saveEavAttributes(array_keys($attributes));
        }
        // Return model.
        return $this->owner;
    }

    /**
     * @param string $attribute key.
     * @param mixed $value value.
     * @param bool $save whether auto save attributes.
     * @return ActiveRecord
     */
    public function setEavAttribute($attribute, $value, $save = FALSE)
    {
        return $this->setEavAttributes([$attribute => $value], $save);
    }

    /**
     * @param array $attributes key for get.
     * @return array
     */
    public function getEavAttributes($attributes = [])
    {

        // Get all attributes if not specified.
        if (empty($attributes)) {
            $attributes = $this->getSafeAttributesArray();
        }
        // Values array.
        $values = [];
        // Queue for load.
        $loadQueue = [];
        foreach ($attributes as $attribute) {


            // Check is safe.
            if ($this->hasSafeAttribute($attribute)) {
                if (isset($this->attributes[$attribute])) {
                    $val = $this->attributes[$attribute];
                } else {
                    $val = NULL;
                }
                $values[$attribute] = $val;
                // If attribute not set and not load, prepare array for loaded.
                if (!$this->preload && $values[$attribute] === NULL) {
                    $loadQueue[] = $attribute;
                }
            }
        }
        // If array for loaded not empty, load attributes.
        if (!$this->preload && count($loadQueue) > 0) {

            $this->loadEavAttributes($attributes);
            foreach ($loadQueue as $attribute) {
                $values[$attribute] = (isset($this->attributes[$attribute])) ? $this->attributes[$attribute] : NULL;
            }
        }

        // Delete load queue.
        unset($loadQueue);
        // Return values.
        return $values;
    }

    public function getEavAttributesValue($attributes = [])
    {

        // Get all attributes if not specified.
        if (empty($attributes)) {
            $attributes = $this->getSafeAttributesArray();
        }

        $test = AttributeOption::find()->where(['IN', 'id', $attributes]);

        $result = [];
        foreach ($test->all() as $test) {
            if ($test->attr) {
                $result[$test->attr->name]['value'] = $test->value;
                $result[$test->attr->name]['name'] = $test->attr->title;
            }
        }

        return $result;
    }

    /**
     * @param string $attribute for get.
     * @return mixed
     */
    public function getEavAttribute($attribute)
    {
        $values = $this->getEavAttributes([$attribute]);
        return $values[$attribute];
    }

    /**
     * Limit current AR query to have all attributes and values specified.
     * @param array $attributes values or key for filter models.
     * @return ActiveRecord
     */
    public function ___withEavAttributes2($attributes = [])
    {
        // If not set attributes, search models with anything attributes exists.
        if (empty($attributes)) {
            $attributes = $this->getSafeAttributesArray();
        }

        // $attributes be array of elements: $attribute => $values
        return $this->getFindByEavAttributes2($attributes);
    }

    protected function getFindByEavAttributes2($attributes)
    {
        /** @var \core\modules\shop\models\Product $owner */
        $owner = $this->owner;
        $pk = '{{%shop__product}}.id';

        $i = 0;
        foreach ($attributes as $attribute => $values) {
            // If search models with attribute name with specified values.
            if (is_string($attribute)) {
                // Get attribute compare operator
                //$attribute = $conn->quoteValue($attribute);
                if (!is_array($values)) {
                    $values = [$values];
                }
                sort($values);


                $cache = \Yii::$app->cache->get("attribute_" . $attribute);
                //anti d-dos убирает лишние значение с запроса.
                if ($cache) {
                    $values = array_intersect($cache[$attribute], $values);
                }
                foreach ($values as $value) {
                    //$value = $conn->quoteValue($value);
                    $owner::find()->join('JOIN', '{{%shop__product_attribute_eav}} eavb' . $i, "$pk=eavb$i.`entity` AND eavb$i.`attribute` = '$attribute' AND eavb$i.`value` = '$value'");
                    $owner::find()->andWhere(['IN', "`eavb$i`.`value`", $values]);
                    /* $criteria->join .= "\nJOIN {$this->tableName} eavb$i"
                      . "\nON t.{$pk} = eavb$i.{$this->entityField}"
                      . "\nAND eavb$i.{$this->attributeField} = $attribute"
                      . "\nAND eavb$i.{$this->valueField} = $value";
                     */

                    $i++;
                }
            } // If search models with attribute name with anything values.
            elseif (is_int($attribute)) {
                $owner::find()->join('JOIN', '{{%shop__product_attribute_eav}} eavb' . $i, "$pk=`eavb$i`.`entity` AND eavb$i.attribute = '$values'");
                //$values = $conn->quoteValue($values);
                /* $this->join .= "\nJOIN {{%shop__product_attribute_eav}} eavb$i"
                         . "\nON t.{$pk} = eavb$i.entity"
                         . "\nAND eavb$i.attribute = $values";*/
                $i++;
            }
        }
        //$this->distinct(true);
        $owner::find()->groupBy("{$pk}");
        // echo $this->createCommand()->getRawSql();die;
        return $owner::find();
    }

    /**
     * @access protected
     * @param string $attribute
     * @param string $value
     * @return yii\db\Command
     */
    protected function getSaveEavAttributeCommand($attribute, $value)
    {
        $data = [
            $this->entityField => $this->getModelId(),
            $this->attributeField => $attribute,
            $this->valueField => $value,
        ];
        return Yii::$app->db->createCommand()->insert($this->tableName, $data);
        /* return $this->owner
          ->getCommandBuilder()
          ->createInsertCommand($this->tableName, $data); */
    }

    /**
     * @access protected
     * @param array $attributes
     * @return yii\db\Query
     */
    public function getLoadEavAttributesQuery($attributes)
    {
        $query = new Query;
        $query->from($this->tableName)->where([$this->entityField => $this->getModelId()]);
        if (!empty($attributes)) {
            $query->andWhere(['IN', $this->attributeField, $attributes]);
        }
        //echo $query->createCommand()->rawSql;die;
        return $query;
    }

    //@todo test
    public function getLoadEavAttributesValueQuery($attributes)
    {
        $query = new Query;
        $query->from($this->tableName);//->where([$this->entityField => $this->getModelId()]);
        if (!empty($attributes)) {
            $query->andWhere(['IN', $this->attributeField, $attributes]);
        }
        //echo $query->createCommand()->rawSql;die;
        return $query;
    }

    /**
     * @access protected
     * @param array $attributes
     * @return yii\db\Command
     */
    protected function getDeleteCommand(array $attributes = [])
    {
        $condition[$this->entityField] = $this->getModelId();
        if (!empty($attributes)) {
            $condition[$this->attributeField] = $attributes;
        }
        return Yii::$app->db->createCommand()->delete($this->tableName, $condition);
    }


}
