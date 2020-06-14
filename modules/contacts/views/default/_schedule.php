<?php
use panix\engine\jui\DatetimePicker;
use yii\helpers\Html;

/**
 * @var core\modules\contacts\models\SettingsForm $model
 * @var panix\engine\bootstrap\ActiveForm $form
 */


$list = [0 => 'monday', 1 => 'tuesday', 2 => 'wednesday', 3 => 'thursday', 4 => 'friday', 5 => 'saturday', 6 => 'sunday']
?>
<?=
$form->field($model, 'enable_schedule')->checkbox();
?>

<?php
echo \panix\ext\multipleinput\MultipleInput::widget([
    'model' => $model,
    'attribute' => 'schedule',
    'max' => 7,
    'min' => 7,
    'allowEmptyList' => false,
    'enableGuessTitle' => true,
    //'addButtonPosition' => \panix\ext\multipleinput\MultipleInput::POS_HEADER, // show add button in the header
    'columns' => [
        [
            'name' => 'static', // can be ommited in case of static column
            'title' => $model::t('DAY'),
            'enableError' => false,
            'type' => \panix\ext\multipleinput\MultipleInputColumn::TYPE_STATIC,
            'value' => function ($data, $i) use ($model) {
                $list = $model::dayList();
                return Html::tag('span', $list[(int)$i['index']]);
            },
            'options' => ['class' => 'text-center'],
            'headerOptions' => [
                'style' => 'width: 70px;',
            ],

        ],
        /*[
            'type' => \unclead\multipleinput\MultipleInputColumn::TYPE_CHECKBOX_LIST,
            'name' => 'enable',
            'headerOptions' => [
                'style' => 'width: 80px;',
            ],
            'items' => [
                1 => 'Test 1',
                2 => 'Test 2',
                3 => 'Test 3',
                4 => 'Test 4'
            ],
            'options' => [
                // see checkboxList implementation in the BaseHtml helper for getting more detail
                'unselect' => 2
            ]
        ],*/
        [
            'name' => 'start_time',
            'type' => DatetimePicker::class,
            'title' => $model::t('START_TIME'),
            'enableError' => false,
            'options' => [
                'timeFormat' => 'HH:mm',
                'mode' => 'time',
                'class' => 'text-center',
                'options' => ['class'=>'form-control m-auto','autocomplete' => 'off', 'placeholder' => $model::t('DAY_OFF')]
            ],
            'columnOptions' => ['class' => 'text-center'],
            'headerOptions' => [
                'style' => 'width: 250px;',
            ],
        ],
        [
            'name' => 'end_time',
            'type' => DatetimePicker::class,
            'title' => $model::t('END_TIME'),
            'enableError' => false,
            'options' => [
                'timeFormat' => 'HH:mm',
                'mode' => 'time',
                'options' => ['class'=>'form-control m-auto','autocomplete' => 'off', 'placeholder' => $model::t('DAY_OFF')]
            ],
            'headerOptions' => [
                'style' => 'width: 250px;',
            ],
        ]
    ]
]);

/*
echo DatetimePicker::widget([
    'model' => $model,
    'attribute' => "{$day}_time_end",
    'mode' => 'time',
    'timeFormat'=>'hh:mm'
])*/
?>
