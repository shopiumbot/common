<?php
/**
 * @link https://github.com/wbraganca/yii2-nested-set-behavior
 * @copyright Copyright (c) 2013 Alexander Kochetov
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

namespace panix\engine\behaviors\nestedsets;

use yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\db\Exception;

/**
 * Class NestedSetsBehavior
 * @package panix\engine\behaviors\nestedsets
 */
class NestedSetsBehavior extends Behavior
{
    public $titleAttribute = 'name';
    /**
     * @var \yii\db\ActiveQuery the owner of this behavior.
     */
    public $owner;
    /**
     * @var bool
     */
    public $hasManyRoots = false;
    /**
     * @var string
     */
    public $idAttribute = 'id';
    /**
     * @var string
     */
    public $rootAttribute = 'tree';
    /**
     * @var string
     */
    public $leftAttribute = 'lft';
    /**
     * @var string
     */
    public $rightAttribute = 'rgt';
    /**
     * @var string
     */
    public $levelAttribute = 'depth';
    /**
     * @var bool
     */
    private $_ignoreEvent = false;
    /**
     * @var bool
     */
    private $_deleted = false;
    /**
     * @var int
     */
    private $_id;
    /**
     * @var array
     */
    private static $_cached;
    /**
     * @var int
     */
    private static $_c = 0;

    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_FIND => 'afterFind',
            ActiveRecord::EVENT_BEFORE_DELETE => 'beforeDelete',
            ActiveRecord::EVENT_BEFORE_INSERT => 'beforeInsert',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'beforeUpdate',
        ];
    }

    /**
     * Gets descendants for node.
     * @param int $depth the depth.
     * @return \yii\db\ActiveQuery.
     */
    public function descendants($depth = null)
    {
        /** @var ActiveRecord $owner */
        $owner = $this->owner;

        //$query = $this->owner->find()->orderBy([$this->leftAttribute => SORT_ASC]); //, $this->leftAttribute => SORT_ASC
        $query = $owner->find();
        //$query->addOrderBy([$this->leftAttribute => SORT_ASC]);
        $db = $owner->getDb();
        $query->andWhere($db->quoteColumnName($this->leftAttribute) . '>'
            . $owner->getAttribute($this->leftAttribute));

        $query->andWhere($db->quoteColumnName($this->rightAttribute) . '<'
            . $owner->getAttribute($this->rightAttribute));

        $query->addOrderBy($db->quoteColumnName($this->leftAttribute));

        if ($depth !== null) {
            $query->andWhere($db->quoteColumnName($this->levelAttribute) . '<='
                . ($owner->getAttribute($this->levelAttribute) + $depth));
        }

        if ($this->hasManyRoots) {
            $query->andWhere(
                $db->quoteColumnName($this->rootAttribute) . '=:' . $this->rootAttribute,
                [':' . $this->rootAttribute => $owner->getAttribute($this->rootAttribute)]
            );
        }

        return $query;
    }

    /**
     * Gets children for node (direct descendants only).
     * @return \yii\db\ActiveQuery.
     */
    public function children()
    {
        return $this->descendants(1);
    }

    /**
     * Gets ancestors for node.
     * @param int $depth the depth.
     * @return \yii\db\ActiveQuery.
     */
    public function ancestors($depth = null)
    {
        /** @var ActiveRecord $owner */
        $owner = $this->owner;
        //$query = $this->owner->find()->orderBy([$this->leftAttribute => SORT_ASC]); //panix remove , $this->leftAttribute => SORT_ASC ..duplicate
        $query = $owner->find(); //panix remove , $this->leftAttribute => SORT_ASC ..duplicate
        $db = $owner->getDb();
        $query->andWhere($db->quoteColumnName($this->leftAttribute) . '<'
            . $owner->getAttribute($this->leftAttribute));
        $query->andWhere($db->quoteColumnName($this->rightAttribute) . '>'
            . $owner->getAttribute($this->rightAttribute));
        $query->addOrderBy($db->quoteColumnName($this->leftAttribute));

        if ($depth !== null) {
            $query->andWhere($db->quoteColumnName($this->levelAttribute) . '>='
                . ($owner->getAttribute($this->levelAttribute) - $depth));
        }

        if ($this->hasManyRoots) {
            $query->andWhere(
                $db->quoteColumnName($this->rootAttribute) . '=:' . $this->rootAttribute,
                [':' . $this->rootAttribute => $owner->getAttribute($this->rootAttribute)]
            );
        }

        return $query;
    }

    /**
     * Gets parent of node.
     * @return \yii\db\ActiveQuery.
     */
    public function parent()
    {
        /** @var ActiveRecord $owner */
        $owner = $this->owner;
        $query = $owner->find();
        $db = $owner->getDb();
        $query->andWhere($db->quoteColumnName($this->leftAttribute) . '<'
            . $owner->getAttribute($this->leftAttribute));
        $query->andWhere($db->quoteColumnName($this->rightAttribute) . '>'
            . $owner->getAttribute($this->rightAttribute));
        $query->addOrderBy($db->quoteColumnName($this->rightAttribute));

        if ($this->hasManyRoots) {
            $query->andWhere(
                $db->quoteColumnName($this->rootAttribute) . '=:' . $this->rootAttribute,
                [':' . $this->rootAttribute => $owner->getAttribute($this->rootAttribute)]
            );
        }

        return $query;
    }

    /**
     * Gets previous sibling of node.
     * @return \yii\db\ActiveQuery.
     */
    public function prev()
    {
        /**
         * @var ActiveRecord $owner
         */
        $owner = $this->owner;
        $query = $owner->find();
        $db = $owner->getDb();
        $query->andWhere($db->quoteColumnName($this->rightAttribute) . '='
            . ($owner->getAttribute($this->leftAttribute) - 1));

        if ($this->hasManyRoots) {
            $query->andWhere(
                $db->quoteColumnName($this->rootAttribute) . '=:' . $this->rootAttribute,
                [':' . $this->rootAttribute => $owner->getAttribute($this->rootAttribute)]
            );
        }

        return $query;
    }

    /**
     * Gets next sibling of node.
     * @return \yii\db\ActiveQuery.
     */
    public function next()
    {
        /**
         * @var \yii\db\ActiveQuery $query
         * @var \yii\db\Connection $db
         * @var ActiveRecord $owner
         */
        $owner = $this->owner;
        $query = $owner->find();
        $db = $owner->getDb();
        $query->andWhere($db->quoteColumnName($this->leftAttribute) . '='
            . ($owner->getAttribute($this->rightAttribute) + 1));

        if ($this->hasManyRoots) {
            $query->andWhere(
                $db->quoteColumnName($this->rootAttribute) . '=:' . $this->rootAttribute,
                [':' . $this->rootAttribute => $owner->getAttribute($this->rootAttribute)]
            );
        }

        return $query;
    }

    /**
     * Create root node if multiple-root tree mode. Update node if it's not new.
     * @param boolean $runValidation whether to perform validation.
     * @param array $attributeNames list of attributes.
     * @return boolean whether the saving succeeds.
     */
    public function save($runValidation = true, $attributeNames = null)
    {
        /** @var ActiveRecord $owner */
        $owner = $this->owner;
        if ($runValidation && !$owner->validate($attributeNames)) {
            return false;
        }

        if ($owner->getIsNewRecord()) {
            return $this->makeRoot($attributeNames);
        }

        $this->_ignoreEvent = true;
        $result = $owner->update(false, $attributeNames);
        $this->_ignoreEvent = false;

        return $result;
    }

    /**
     * Create root node if multiple-root tree mode. Update node if it's not new.
     * @param boolean $runValidation whether to perform validation.
     * @param array $attributeNames list of attributes.
     * @return boolean whether the saving succeeds.
     */
    public function saveNode($runValidation = true, $attributeNames = null)
    {
        return $this->save($runValidation, $attributeNames);
    }

    /**
     * Deletes node and it's descendants.
     * @throws Exception.
     * @throws \Exception.
     * @return boolean whether the deletion is successful.
     */
    public function delete()
    {
        /** @var ActiveRecord|self $owner */
        $owner = $this->owner;
        if ($owner->getIsNewRecord()) {
            throw new Exception('The node can\'t be deleted because it is new.');
        }

        if ($this->getIsDeletedRecord()) {
            throw new Exception('The node can\'t be deleted because it is already deleted.');
        }

        $db = $owner->getDb();

        if ($db->getTransaction() === null) {
            $transaction = $db->beginTransaction();
        }

        try {
            $this->_ignoreEvent = true;
            if ($owner->isLeaf()) {
                $result = $owner->delete();
            } elseif ($owner->beforeDelete()) {
                $condition = $db->quoteColumnName($this->leftAttribute) . '>='
                    . $owner->getOldAttribute($this->leftAttribute) . ' AND '
                    . $db->quoteColumnName($this->rightAttribute) . '<='
                    . $owner->getOldAttribute($this->rightAttribute);
                $params = [];

                if ($this->hasManyRoots) {
                    $condition .= ' AND ' . $db->quoteColumnName($this->rootAttribute) . '=:' . $this->rootAttribute;
                    $params[':' . $this->rootAttribute] = $owner->getOldAttribute($this->rootAttribute);
                }

                $result = $owner->deleteAll($condition, $params) > 0;
                $owner->afterDelete();
            }
            $this->_ignoreEvent = false;

            if (!$result) {
                if (isset($transaction)) {
                    $transaction->rollback();
                }

                return false;
            }

            $this->shiftLeftRight(
                $owner->getAttribute($this->rightAttribute) + 1,
                $owner->getAttribute($this->leftAttribute) - $owner->getAttribute($this->rightAttribute) - 1
            );

            if (isset($transaction)) {
                $transaction->commit();
            }

            $this->correctCachedOnDelete();
        } catch (\Exception $e) {
            if (isset($transaction)) {
                $transaction->rollback();
            }

            throw $e;
        }

        return true;
    }

    /**
     * Deletes node and it's descendants.
     * @return boolean whether the deletion is successful.
     */
    public function deleteNode()
    {
        return $this->delete();
    }

    /**
     * Prepends node to target as first child.
     * @param ActiveRecord $target the target.
     * @param boolean $runValidation whether to perform validation.
     * @param array $attributes list of attributes.
     * @return boolean whether the prepending succeeds.
     */
    public function prependTo($target, $runValidation = true, $attributes = null)
    {
        return $this->addNode(
            $target,
            $target->getAttribute($this->leftAttribute) + 1,
            1,
            $runValidation,
            $attributes
        );
    }

    /**
     * Prepends target to node as first child.
     * @param ActiveRecord $target the target.
     * @param boolean $runValidation whether to perform validation.
     * @param array $attributes list of attributes.
     * @return boolean whether the prepending succeeds.
     */
    public function prepend($target, $runValidation = true, $attributes = null)
    {
        /**
         * @var self $target
         * @var ActiveRecord $owner
         */
        $owner = $this->owner;
        return $target->prependTo($owner, $runValidation, $attributes);
    }

    /**
     * Appends node to target as last child.
     * @param ActiveRecord $target the target.
     * @param boolean $runValidation whether to perform validation.
     * @param array $attributes list of attributes.
     * @return boolean whether the appending succeeds.
     */
    public function appendTo($target, $runValidation = true, $attributes = null)
    {
        return $this->addNode(
            $target,
            $target->getAttribute($this->rightAttribute),
            1,
            $runValidation,
            $attributes
        );
    }

    /**
     * Appends target to node as last child.
     * @param ActiveRecord $target the target.
     * @param boolean $runValidation whether to perform validation.
     * @param array $attributes list of attributes.
     * @return boolean whether the appending succeeds.
     */
    public function append($target, $runValidation = true, $attributes = null)
    {
        /**
         * @var self $target
         * @var ActiveRecord $owner
         */
        $owner = $this->owner;
        return $target->appendTo($owner, $runValidation, $attributes);
    }

    /**
     * Inserts node as previous sibling of target.
     * @param ActiveRecord $target the target.
     * @param boolean $runValidation whether to perform validation.
     * @param array $attributes list of attributes.
     * @return boolean whether the inserting succeeds.
     */
    public function insertBefore($target, $runValidation = true, $attributes = null)
    {
        return $this->addNode(
            $target,
            $target->getAttribute($this->leftAttribute),
            0,
            $runValidation,
            $attributes
        );
    }

    /**
     * Inserts node as next sibling of target.
     * @param ActiveRecord $target the target.
     * @param boolean $runValidation whether to perform validation.
     * @param array $attributes list of attributes.
     * @return boolean whether the inserting succeeds.
     */
    public function insertAfter($target, $runValidation = true, $attributes = null)
    {
        return $this->addNode(
            $target,
            $target->getAttribute($this->rightAttribute) + 1,
            0,
            $runValidation,
            $attributes
        );
    }

    /**
     * Move node as previous sibling of target.
     * @param ActiveRecord $target the target.
     * @return boolean whether the moving succeeds.
     */
    public function moveBefore($target)
    {
        return $this->moveNode($target, $target->getAttribute($this->leftAttribute), 0);
    }

    /**
     * Move node as next sibling of target.
     * @param ActiveRecord $target the target.
     * @return boolean whether the moving succeeds.
     */
    public function moveAfter($target)
    {
        return $this->moveNode($target, $target->getAttribute($this->rightAttribute) + 1, 0);
    }

    /**
     * Move node as first child of target.
     * @param ActiveRecord $target the target.
     * @return boolean whether the moving succeeds.
     */
    public function moveAsFirst($target)
    {
        return $this->moveNode(
            $target,
            $target->getAttribute($this->leftAttribute) + 1,
            1
        );
    }

    /**
     * Move node as last child of target.
     * @param ActiveRecord $target the target.
     * @return boolean whether the moving succeeds.
     */
    public function moveAsLast($target)
    {
        return $this->moveNode(
            $target,
            $target->getAttribute($this->rightAttribute),
            1
        );
    }

    /**
     * Move node as new root.
     * @throws Exception.
     * @throws \Exception.
     * @return boolean whether the moving succeeds.
     */
    public function moveAsRoot()
    {
        /** @var ActiveRecord|self $owner */
        $owner = $this->owner;
        if (!$this->hasManyRoots) {
            throw new Exception('Many roots mode is off.');
        }

        if ($owner->getIsNewRecord()) {
            throw new Exception('The node should not be new record.');
        }

        if ($this->getIsDeletedRecord()) {
            throw new Exception('The node should not be deleted.');
        }

        if ($owner->isRoot()) {
            throw new Exception('The node already is root node.');
        }

        $db = $owner->getDb();

        if ($db->getTransaction() === null) {
            $transaction = $db->beginTransaction();
        }

        try {
            $left = $owner->getAttribute($this->leftAttribute);
            $right = $owner->getAttribute($this->rightAttribute);
            $levelDelta = 1 - $owner->getAttribute($this->levelAttribute);
            $delta = 1 - $left;
            $owner->updateAll(
                [
                    $this->leftAttribute => new Expression($db->quoteColumnName($this->leftAttribute)
                        . sprintf('%+d', $delta)),
                    $this->rightAttribute => new Expression($db->quoteColumnName($this->rightAttribute)
                        . sprintf('%+d', $delta)),
                    $this->levelAttribute => new Expression($db->quoteColumnName($this->levelAttribute)
                        . sprintf('%+d', $levelDelta)),
                    $this->rootAttribute => $owner->getPrimaryKey(),
                ],
                $db->quoteColumnName($this->leftAttribute) . '>=' . $left . ' AND '
                . $db->quoteColumnName($this->rightAttribute) . '<=' . $right . ' AND '
                . $db->quoteColumnName($this->rootAttribute) . '=:' . $this->rootAttribute,
                [':' . $this->rootAttribute => $owner->getAttribute($this->rootAttribute)]
            );
            $this->shiftLeftRight($right + 1, $left - $right - 1);

            if (isset($transaction)) {
                $transaction->commit();
            }

            $this->correctCachedOnMoveBetweenTrees(1, $levelDelta, $owner->getPrimaryKey());
        } catch (\Exception $e) {
            if (isset($transaction)) {
                $transaction->rollback();
            }

            throw $e;
        }

        return true;
    }

    /**
     * Determines if node is descendant of subject node.
     * @param ActiveRecord $subj the subject node.
     * @return boolean whether the node is descendant of subject node.
     */
    public function isDescendantOf($subj)
    {
        /** @var ActiveRecord $owner */
        $owner = $this->owner;
        $result = ($owner->getAttribute($this->leftAttribute) > $subj->getAttribute($this->leftAttribute))
            && ($owner->getAttribute($this->rightAttribute) < $subj->getAttribute($this->rightAttribute));

        if ($this->hasManyRoots) {
            $result = $result && ($owner->getAttribute($this->rootAttribute)
                    === $subj->getAttribute($this->rootAttribute));
        }

        return $result;
    }

    /**
     * Determines if node is leaf.
     * @return boolean whether the node is leaf.
     */
    public function isLeaf()
    {
        /** @var ActiveRecord $owner */
        $owner = $this->owner;
        return $owner->getAttribute($this->rightAttribute)
            - $owner->getAttribute($this->leftAttribute) === 1;
    }

    /**
     * Determines if node is root.
     * @return boolean whether the node is root.
     */
    public function isRoot()
    {
        /** @var ActiveRecord $owner */
        $owner = $this->owner;
        return $owner->getAttribute($this->leftAttribute) == 1;
    }

    /**
     * Returns if the current node is deleted.
     * @return boolean whether the node is deleted.
     */
    public function getIsDeletedRecord()
    {
        return $this->_deleted;
    }

    /**
     * Sets if the current node is deleted.
     * @param boolean $value whether the node is deleted.
     */
    public function setIsDeletedRecord($value)
    {
        $this->_deleted = $value;
    }

    /**
     * Handle 'afterFind' event of the owner.
     */
    public function afterFind()
    {
        self::$_cached[get_class($this->owner)][$this->_id = self::$_c++] = $this->owner;
    }

    /**
     * Handle 'beforeInsert' event of the owner.
     * @throws Exception.
     * @return boolean.
     */
    public function beforeInsert()
    {
        if ($this->_ignoreEvent) {
            return true;
        } else {
            throw new Exception('You should not use ActiveRecord::save() or ActiveRecord::insert() methods when NestedSet behavior attached.');
        }
    }

    /**
     * Handle 'beforeUpdate' event of the owner.
     * @throws Exception.
     * @return boolean.
     */
    public function beforeUpdate()
    {
        if ($this->_ignoreEvent) {
            return true;
        } else {
            throw new Exception('You should not use ActiveRecord::save() or ActiveRecord::update() methods when NestedSet behavior attached.');
        }
    }

    /**
     * Handle 'beforeDelete' event of the owner.
     * @throws Exception.
     * @return boolean.
     */
    public function beforeDelete()
    {
        if ($this->_ignoreEvent) {
            return true;
        } else {
            throw new Exception('You should not use ActiveRecord::delete() method when NestedSet behavior attached.');
        }
    }

    /**
     * @param int $key .
     * @param int $delta .
     */
    private function shiftLeftRight($key, $delta)
    {
        /** @var ActiveRecord $owner */
        $owner = $this->owner;
        $db = $owner->getDb();

        foreach ([$this->leftAttribute, $this->rightAttribute] as $attribute) {
            $condition = $db->quoteColumnName($attribute) . '>=' . $key;
            $params = [];

            if ($this->hasManyRoots) {
                $condition .= ' AND ' . $db->quoteColumnName($this->rootAttribute) . '=:' . $this->rootAttribute;
                $params[':' . $this->rootAttribute] = $owner->getAttribute($this->rootAttribute);
            }

            $owner->updateAll(
                [$attribute => new Expression($db->quoteColumnName($attribute) . sprintf('%+d', $delta))],
                $condition,
                $params
            );
        }
    }

    /**
     * @param ActiveRecord|self $target
     * @param int $key .
     * @param int $levelUp .
     * @param boolean $runValidation
     * @param array $attributes
     * @throws Exception
     * @throws \Exception
     * @return boolean
     */
    private function addNode($target, $key, $levelUp, $runValidation, $attributes)
    {
        /** @var ActiveRecord $owner */
        $owner = $this->owner;
        if (!$owner->getIsNewRecord()) {
            throw new Exception('The node can\'t be inserted because it is not new.');
        }

        if ($this->getIsDeletedRecord()) {
            throw new Exception('The node can\'t be inserted because it is deleted.');
        }

        if ($target->getIsDeletedRecord()) {
            throw new Exception('The node can\'t be inserted because target node is deleted.');
        }

        if ($owner->equals($target)) {
            throw new Exception('The target node should not be self.');
        }

        if (!$levelUp && $target->isRoot()) {
            throw new Exception('The target node should not be root.');
        }

        if ($runValidation && !$owner->validate()) {
            return false;
        }

        if ($this->hasManyRoots) {
            $owner->setAttribute($this->rootAttribute, $target->getAttribute($this->rootAttribute));
        }

        $db = $owner->getDb();

        if ($db->getTransaction() === null) {
            $transaction = $db->beginTransaction();
        }

        try {
            $this->shiftLeftRight($key, 2);
            $owner->setAttribute($this->leftAttribute, $key);
            $owner->setAttribute($this->rightAttribute, $key + 1);
            $owner->setAttribute($this->levelAttribute, $target->getAttribute($this->levelAttribute) + $levelUp);
            $this->_ignoreEvent = true;
            $result = $owner->insert(false, $attributes);
            $this->_ignoreEvent = false;

            if (!$result) {
                if (isset($transaction)) {
                    $transaction->rollback();
                }

                return false;
            }

            if (isset($transaction)) {
                $transaction->commit();
            }

            $this->correctCachedOnAddNode($key);
        } catch (\Exception $e) {
            if (isset($transaction)) {
                $transaction->rollback();
            }

            throw $e;
        }

        return true;
    }

    /**
     * @param array $attributes .
     * @throws Exception.
     * @throws \Exception.
     * @return boolean.
     */
    private function makeRoot($attributes)
    {
        /** @var ActiveRecord $owner */
        $owner = $this->owner;

        $owner->setAttribute($this->leftAttribute, 1);
        $owner->setAttribute($this->rightAttribute, 2);
        $owner->setAttribute($this->levelAttribute, 1);

        if ($this->hasManyRoots) {
            $db = $owner->getDb();

            if ($db->getTransaction() === null) {
                $transaction = $db->beginTransaction();
            }

            try {
                $this->_ignoreEvent = true;
                $result = $owner->insert(false, $attributes);
                $this->_ignoreEvent = false;

                if (!$result) {
                    if (isset($transaction)) {
                        $transaction->rollback();
                    }

                    return false;
                }

                if ($owner->getAttribute($this->rootAttribute)) {
                    if (isset($transaction)) {
                        $transaction->commit();
                    }
                    return $result;
                }

                $owner->setAttribute($this->rootAttribute, $owner->getPrimaryKey());
                $primaryKey = $owner->primaryKey();

                if (!isset($primaryKey[0])) {
                    throw new Exception(get_class($this->owner) . ' must have a primary key.');
                }

                $owner->updateAll(
                    [$this->rootAttribute => $owner->getAttribute($this->rootAttribute)],
                    [$primaryKey[0] => $owner->getAttribute($this->rootAttribute)]
                );

                if (isset($transaction)) {
                    $transaction->commit();
                }
            } catch (\Exception $e) {
                if (isset($transaction)) {
                    $transaction->rollback();
                }

                throw $e;
            }
        } else {
            $this->_ignoreEvent = true;
            $result = $owner->insert(false, $attributes);
            $this->_ignoreEvent = false;

            if (!$result) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param ActiveRecord|self $target .
     * @param int $key .
     * @param int $levelUp .
     * @throws Exception.
     * @throws \Exception.
     * @return boolean.
     */
    private function moveNode($target, $key, $levelUp)
    {
        /** @var ActiveRecord $owner */
        $owner = $this->owner;
        if ($owner->getIsNewRecord()) {
            throw new Exception('The node should not be new record.');
        }

        if ($this->getIsDeletedRecord()) {
            throw new Exception('The node should not be deleted.');
        }

        if ($target->getIsDeletedRecord()) {
            throw new Exception('The target node should not be deleted.');
        }

        if ($owner->equals($target)) {
            throw new Exception('The target node should not be self.');
        }

        if ($target->isDescendantOf($owner)) {
            throw new Exception('The target node should not be descendant.');
        }

        if (!$levelUp && $target->isRoot()) {
            throw new Exception('The target node should not be root.');
        }

        $db = $owner->getDb();

        if ($db->getTransaction() === null) {
            $transaction = $db->beginTransaction();
        }

        try {
            $left = $owner->getAttribute($this->leftAttribute);
            $right = $owner->getAttribute($this->rightAttribute);
            $levelDelta = $target->getAttribute($this->levelAttribute) - $owner->getAttribute($this->levelAttribute)
                + $levelUp;


            if ($this->hasManyRoots && $owner->getAttribute($this->rootAttribute) !==
                $target->getAttribute($this->rootAttribute)
            ) {

                foreach ([$this->leftAttribute, $this->rightAttribute] as $attribute) {
                    $owner->updateAll(
                        [$attribute => new Expression($db->quoteColumnName($attribute)
                            . sprintf('%+d', $right - $left + 1))],
                        $db->quoteColumnName($attribute) . '>=' . $key . ' AND '
                        . $db->quoteColumnName($this->rootAttribute) . '=:' . $this->rootAttribute,
                        [':' . $this->rootAttribute => $target->getAttribute($this->rootAttribute)]
                    );
                }

                $delta = $key - $left;
                $owner->updateAll(
                    [
                        $this->leftAttribute => new Expression($db->quoteColumnName($this->leftAttribute)
                            . sprintf('%+d', $delta)),
                        $this->rightAttribute => new Expression($db->quoteColumnName($this->rightAttribute)
                            . sprintf('%+d', $delta)),
                        $this->levelAttribute => new Expression($db->quoteColumnName($this->levelAttribute)
                            . sprintf('%+d', $levelDelta)),
                        $this->rootAttribute => $target->getAttribute($this->rootAttribute),
                    ],
                    $db->quoteColumnName($this->leftAttribute) . '>=' . $left . ' AND '
                    . $db->quoteColumnName($this->rightAttribute) . '<=' . $right . ' AND '
                    . $db->quoteColumnName($this->rootAttribute) . '=:' . $this->rootAttribute,
                    [':' . $this->rootAttribute => $owner->getAttribute($this->rootAttribute)]
                );
                $this->shiftLeftRight($right + 1, $left - $right - 1);

                if (isset($transaction)) {
                    $transaction->commit();
                }

                $this->correctCachedOnMoveBetweenTrees($key, $levelDelta, $target->getAttribute($this->rootAttribute));
            } else {

                $delta = $right - $left + 1;
                $this->shiftLeftRight($key, $delta);

                if ($left >= $key) {
                    $left += $delta;
                    $right += $delta;
                }

                $condition = $db->quoteColumnName($this->leftAttribute) . '>=' . $left . ' AND '
                    . $db->quoteColumnName($this->rightAttribute) . '<=' . $right;
                $params = [];

                if ($this->hasManyRoots) {
                    $condition .= ' AND ' . $db->quoteColumnName($this->rootAttribute) . '=:' . $this->rootAttribute;
                    $params[':' . $this->rootAttribute] = $owner->getAttribute($this->rootAttribute);
                }

                $owner->updateAll(
                    [
                        $this->levelAttribute => new Expression($db->quoteColumnName($this->levelAttribute)
                            . sprintf('%+d', $levelDelta)),
                    ],
                    $condition,
                    $params
                );

                foreach ([$this->leftAttribute, $this->rightAttribute] as $attribute) {
                    $condition = $db->quoteColumnName($attribute) . '>=' . $left . ' AND '
                        . $db->quoteColumnName($attribute) . '<=' . $right;
                    $params = [];

                    if ($this->hasManyRoots) {
                        $condition .= ' AND ' . $db->quoteColumnName($this->rootAttribute) . '=:'
                            . $this->rootAttribute;
                        $params[':' . $this->rootAttribute] = $owner->getAttribute($this->rootAttribute);
                    }

                    $owner->updateAll(
                        [$attribute => new Expression($db->quoteColumnName($attribute)
                            . sprintf('%+d', $key - $left))],
                        $condition,
                        $params
                    );
                }
                $this->shiftLeftRight($right + 1, -$delta);

                if (isset($transaction)) {
                    $transaction->commit();
                }
                $this->correctCachedOnMoveNode($key, $levelDelta);
            }
        } catch (\Exception $e) {
            if (isset($transaction)) {
                $transaction->rollback();
            }

            throw $e;
        }

        return true;
    }

    /**
     * Correct cache for [[delete()]] and [[deleteNode()]].
     */
    private function correctCachedOnDelete()
    {
        /** @var ActiveRecord $owner */
        $owner = $this->owner;
        $left = $owner->getAttribute($this->leftAttribute);
        $right = $owner->getAttribute($this->rightAttribute);
        $key = $right + 1;
        $delta = $left - $right - 1;

        foreach (self::$_cached[get_class($owner)] as $node) {
            /** @var ActiveRecord|self $node */
            if ($node->getIsNewRecord() || $node->getIsDeletedRecord()) {
                continue;
            }

            if ($this->hasManyRoots && $owner->getAttribute($this->rootAttribute)
                !== $node->getAttribute($this->rootAttribute)
            ) {
                continue;
            }

            if ($node->getAttribute($this->leftAttribute) >= $left
                && $node->getAttribute($this->rightAttribute) <= $right
            ) {
                $node->setIsDeletedRecord(true);
            } else {
                if ($node->getAttribute($this->leftAttribute) >= $key) {
                    $node->setAttribute(
                        $this->leftAttribute,
                        $node->getAttribute($this->leftAttribute) + $delta
                    );
                }

                if ($node->getAttribute($this->rightAttribute) >= $key) {
                    $node->setAttribute(
                        $this->rightAttribute,
                        $node->getAttribute($this->rightAttribute) + $delta
                    );
                }
            }
        }
    }

    /**
     * Correct cache for [[addNode()]].
     * @param int $key .
     */
    private function correctCachedOnAddNode($key)
    {
        /** @var ActiveRecord $owner */
        $owner = $this->owner;
        foreach (self::$_cached[get_class($owner)] as $node) {
            /** @var ActiveRecord|self $node */
            if ($node->getIsNewRecord() || $node->getIsDeletedRecord()) {
                continue;
            }

            if ($this->hasManyRoots && $owner->getAttribute($this->rootAttribute)
                !== $node->getAttribute($this->rootAttribute)
            ) {
                continue;
            }

            if ($this->owner === $node) {
                continue;
            }

            if ($node->getAttribute($this->leftAttribute) >= $key) {
                $node->setAttribute(
                    $this->leftAttribute,
                    $node->getAttribute($this->leftAttribute) + 2
                );
            }

            if ($node->getAttribute($this->rightAttribute) >= $key) {
                $node->setAttribute(
                    $this->rightAttribute,
                    $node->getAttribute($this->rightAttribute) + 2
                );
            }
        }
    }

    /**
     * Correct cache for [[moveNode()]].
     * @param int $key .
     * @param int $levelDelta .
     */
    private function correctCachedOnMoveNode($key, $levelDelta)
    {
        /** @var ActiveRecord $owner */
        $owner = $this->owner;
        $left = $owner->getAttribute($this->leftAttribute);
        $right = $owner->getAttribute($this->rightAttribute);
        $delta = $right - $left + 1;

        if ($left >= $key) {
            $left += $delta;
            $right += $delta;
        }

        $delta2 = $key - $left;

        foreach (self::$_cached[get_class($owner)] as $node) {
            /** @var ActiveRecord|self $node */
            if ($node->getIsNewRecord() || $node->getIsDeletedRecord()) {
                continue;
            }

            if ($this->hasManyRoots && $owner->getAttribute($this->rootAttribute)
                !== $node->getAttribute($this->rootAttribute)
            ) {
                continue;
            }

            if ($node->getAttribute($this->leftAttribute) >= $key) {
                $node->setAttribute(
                    $this->leftAttribute,
                    $node->getAttribute($this->leftAttribute) + $delta
                );
            }

            if ($node->getAttribute($this->rightAttribute) >= $key) {
                $node->setAttribute(
                    $this->rightAttribute,
                    $node->getAttribute($this->rightAttribute) + $delta
                );
            }

            if ($node->getAttribute($this->leftAttribute) >= $left
                && $node->getAttribute($this->rightAttribute) <= $right
            ) {
                $node->setAttribute(
                    $this->levelAttribute,
                    $node->getAttribute($this->levelAttribute) + $levelDelta
                );
            }

            if ($node->getAttribute($this->leftAttribute) >= $left
                && $node->getAttribute($this->leftAttribute) <= $right
            ) {
                $node->setAttribute(
                    $this->leftAttribute,
                    $node->getAttribute($this->leftAttribute) + $delta2
                );
            }

            if ($node->getAttribute($this->rightAttribute) >= $left
                && $node->getAttribute($this->rightAttribute) <= $right
            ) {
                $node->setAttribute(
                    $this->rightAttribute,
                    $node->getAttribute($this->rightAttribute) + $delta2
                );
            }

            if ($node->getAttribute($this->leftAttribute) >= $right + 1) {
                $node->setAttribute(
                    $this->leftAttribute,
                    $node->getAttribute($this->leftAttribute) - $delta
                );
            }

            if ($node->getAttribute($this->rightAttribute) >= $right + 1) {
                $node->setAttribute(
                    $this->rightAttribute,
                    $node->getAttribute($this->rightAttribute) - $delta
                );
            }
        }
    }

    /**
     * Correct cache for [[moveNode()]].
     * @param int $key .
     * @param int $levelDelta .
     * @param int $root .
     */
    private function correctCachedOnMoveBetweenTrees($key, $levelDelta, $root)
    {
        /** @var ActiveRecord $owner */
        $owner = $this->owner;
        $left = $owner->getAttribute($this->leftAttribute);
        $right = $owner->getAttribute($this->rightAttribute);
        $delta = $right - $left + 1;
        $delta2 = $key - $left;
        $delta3 = $left - $right - 1;

        foreach (self::$_cached[get_class($owner)] as $node) {
            /** @var ActiveRecord|self $node */
            if ($node->getIsNewRecord() || $node->getIsDeletedRecord()) {
                continue;
            }

            if ($node->getAttribute($this->rootAttribute) === $root) {
                if ($node->getAttribute($this->leftAttribute) >= $key) {
                    $node->setAttribute(
                        $this->leftAttribute,
                        $node->getAttribute($this->leftAttribute) + $delta
                    );
                }

                if ($node->getAttribute($this->rightAttribute) >= $key) {
                    $node->setAttribute(
                        $this->rightAttribute,
                        $node->getAttribute($this->rightAttribute) + $delta
                    );
                }
            } elseif ($node->getAttribute($this->rootAttribute)
                === $owner->getAttribute($this->rootAttribute)
            ) {
                if ($node->getAttribute($this->leftAttribute) >= $left
                    && $node->getAttribute($this->rightAttribute) <= $right
                ) {
                    $node->setAttribute(
                        $this->leftAttribute,
                        $node->getAttribute($this->leftAttribute) + $delta2
                    );
                    $node->setAttribute(
                        $this->rightAttribute,
                        $node->getAttribute($this->rightAttribute) + $delta2
                    );
                    $node->setAttribute(
                        $this->levelAttribute,
                        $node->getAttribute($this->levelAttribute) + $levelDelta
                    );
                    $node->setAttribute($this->rootAttribute, $root);
                } else {
                    if ($node->getAttribute($this->leftAttribute) >= $right + 1) {
                        $node->setAttribute(
                            $this->leftAttribute,
                            $node->getAttribute($this->leftAttribute) + $delta3
                        );
                    }

                    if ($node->getAttribute($this->rightAttribute) >= $right + 1) {
                        $node->setAttribute(
                            $this->rightAttribute,
                            $node->getAttribute($this->rightAttribute) + $delta3
                        );
                    }
                }
            }
        }
    }

    /**
     * Destructor.
     */
    public function __destruct()
    {
        unset(self::$_cached[get_class($this->owner)][$this->_id]);
    }
}
