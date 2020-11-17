<?php

use yii\helpers\Html;
use core\modules\shop\models\Category;

\core\modules\shop\bundles\admin\CategoryAsset::register($this);
\panix\engine\emoji\EmojiAsset::register($this);
?>

<div class="card">
    <div class="card-header">
        <h5><?= Html::encode($this->context->pageName) ?></h5>
    </div>
    <div class="card-body">
        <div class="form-group row">
            <div class="col-12">
                <input class="form-control" placeholder="Поиск..." type="text"
                       onkeyup='$("#CategoryTree").jstree(true).search($(this).val())'/>
            </div>
            <div class="col-12 mt-3">
                <div class="alert alert-info">
                    <?= Yii::t('app/admin', "USE_DND"); ?>
                </div>
            </div>
        </div>

        <?php

        echo \panix\ext\jstree\JsTree::widget([
            'id' => 'CategoryTree',
            'allOpen' => true,
            'data'=>Category::find()->tree(),
            'core' => [
                'force_text' => false,
                'animation' => 0,
                'strings' => [
                    'Loading ...' => Yii::t('app/default', 'LOADING')
                ],
                "themes" => [
                    "stripes" => true,
                    'responsive' => true,
                    "variant" => "large"
                ],
                'check_callback' => true
            ],
            'plugins' => ['dnd', 'contextmenu', 'search'], //, 'wholerow', 'state'
            'contextmenu' => [
                'items' => new yii\web\JsExpression('function($node) {
                var tree = $("#CategoryTree").jstree(true);
                return {
                    "Switch": {
                        "icon":"icon-eye",
                        "label": "' . Yii::t('app/default', 'Скрыть показать') . '",
                        "action": function (obj) {
                            $node = tree.get_node($node);
                            categorySwitch($node);
                        }
                    }, 
                    "Add": {
                        "icon":"icon-add",
                        "label": "' . Yii::t('app/default', 'CREATE') . '",
                        "action": function (obj) {
                            $node = tree.get_node($node);
                            console.log($node);
                            window.location = "/admin/shop/category/index?parent_id="+$node.id.replace("node_", "");
                        }
                    }, 
                    "Edit": {
                        "icon":"icon-edit",
                        "label": "' . Yii::t('app/default', 'UPDATE') . '",
                        "action": function (obj) {
                            $node = tree.get_node($node);
                            window.location = "/admin/shop/category/index?id="+$node.id.replace("node_", "");
                        }
                    },  
                    "Rename": {
                        "icon":"icon-rename",
                        "label": "' . Yii::t('app/default', 'RENAME') . '",
                        "action": function (obj) {
                            console.log($node);
                            tree.edit($node);
                        }
                    },                         
                    "Remove": {
                        "icon":"icon-trashcan",
                        "label": "' . Yii::t('app/default', 'DELETE') . '",
                        "action": function (obj) {
                            if (confirm("' . Yii::t('app/default', 'DELETE_CONFIRM') . '\nТак же будут удалены все товары.")) {
                                tree.delete_node($node);
                            }
                        }
                    }
                };
      }')
            ]
        ]);
        ?>
    </div>
</div>
