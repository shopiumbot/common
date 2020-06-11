<?php

use panix\ext\multipleinput\MultipleInput;

/**
 * @var core\modules\contacts\models\SettingsForm $model
 * @var panix\engine\bootstrap\ActiveForm $form
 */

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
    'max' => 5,
   // 'min' => 0,
    'allowEmptyList' => false,


    'columns' => [
        [
            'name' => 'address',
            'enableError' => true,
            'type' => \panix\ext\multipleinput\MultipleInputColumn::TYPE_TEXT_INPUT,
            'options' => [
                //'class' => 'input-lang',
                'placeholder' => 'Адрес',

            ],
        ],
    ]
]);


