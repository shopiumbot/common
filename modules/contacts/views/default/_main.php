<?php

use panix\ext\multipleinput\MultipleInput;

/**
 * @var core\modules\contacts\models\SettingsForm $model
 * @var panix\engine\bootstrap\ActiveForm $form
 */

//        'latitude' => 46.3974947,
//'longitude' => 30.7125803,
?>

<?=
$form->field($model, 'latitude')->hint('latitude');
?>
<?=
$form->field($model, 'longitude')->hint('longitude');
?>
<?=
$form->field($model, 'email')
    ->widget(\panix\ext\taginput\TagInput::class, ['placeholder' => 'E-mail'])
    ->hint('Введите E-mail и нажмите Enter');
?>
<?php

echo $form->field($model, 'phone')->widget(MultipleInput::class, [
    'max' => 5,
    'min' => 1,
    'allowEmptyList' => false,
    //'enableGuessTitle' => true,
    'sortable' => true,
    'addButtonPosition' => MultipleInput::POS_ROW, // show add button in the header
    'columns' => [
        [
            'name' => 'number',
            'type' => panix\ext\telinput\PhoneInput::class,
            'enableError' => false,
            'title' => $model::t('PHONE'),
            'headerOptions' => [
                'style' => 'width: 250px;',
            ],
            'options' => [
                'jsOptions' => [
                    'hiddenInput' => 'number'
                ],
            ]
        ],
        [
            'name' => 'name',
            'enableError' => false,
            'title' => 'Имя',
            'options' => [
                'placeholder' => 'Ваше Имя',
            ],
        ],
    ]
]);

echo $form->field($model, 'address')->widget(MultipleInput::class, [
    'max' => 1,
    'min' => 1,
    'allowEmptyList' => false,


    'columns' => [
        [
            'name' => 'address',
            'enableError' => true,
            'type' => \panix\ext\multipleinput\MultipleInputColumn::TYPE_TEXT_INPUT,
            'options' => [
                //'class' => 'input-lang',
                'placeholder' => $model::t('ADDRESS'),

            ],
        ],
    ]
]);


