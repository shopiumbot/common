<?php

namespace core\modules\viber\controllers;


use core\components\controllers\WebController;
use panix\engine\CMS;
use Viber\Api\Message\CarouselContent;
use Viber\Api\Message\Video;
use Viber\Client;
use Viber\Bot;
use Viber\Api\Sender;
use yii\base\Exception;
use yii\web\Response;
use Viber\Api\Message\Text;
use Viber\Api\Keyboard\Button;
use Viber\Api\Keyboard;
use Viber\Api\Message\Picture;

class WebhookController extends WebController
{

    public $apikey = '4bdd945189a7d222-cd57fa9101f96aea-4d63f32084a5d333';

    public function beforeAction($action)
    {
        if ($action->id == 'hook') {
            $this->enableCsrfValidation = false;

        }
        return parent::beforeAction($action);
    }

    public function actionHook()
    {

        \Yii::$app->response->format = Response::FORMAT_JSON;


// reply name
        $botSender = new Sender([
            'name' => 'Я бот!)',
            'avatar' => 'https://shopiumbot.com/favicon.ico',
        ]);








        try {
            $bot = new Bot(['token' => $this->apikey]);

            $bot->onText('|test|s', function ($event) use ($bot, $botSender) {
                $receiverId = $event->getSender()->getId();

                $buttons = [];
                for ($i = 0; $i <= 8; $i++) {
                    $buttons[] =
                        (new Button())
                            ->setColumns(1)
                            ->setActionType('reply')
                            ->setActionBody('k' . $i)
                            ->setText('k' . $i);
                }
                /*return (new Text())
                    ->setSender($receiverId)
                    ->setText("Hi, you can see some demo: send 'k1' or 'k2' etc.")
                    ->setKeyboard(
                        (new Keyboard())
                            ->setButtons($buttons)
                    );*/



                $bot->getClient()->sendMessage(
                    (new Text())
                        ->setSender($botSender)
                        ->setReceiver($receiverId)
                        ->setText('you press the button')->setKeyboard(
                            (new Keyboard())
                                ->setButtons($buttons)
                        )
                );

            });

            $bot
                // first interaction with bot - return "welcome message"
                ->onConversation(function ($event) use ($bot, $botSender) {
                    $buttons = [];
                    for ($i = 0; $i <= 8; $i++) {
                        $buttons[] =
                            (new Button())
                                ->setColumns(1)
                                ->setActionType('reply')
                                ->setActionBody('k' . $i)
                                ->setText('k' . $i);
                    }
                    return (new Text())
                        ->setSender($botSender)
                        ->setText("Hi, you can see some demo: send 'k1' or 'k2' etc.")
                        ->setKeyboard(
                            (new Keyboard())
                                ->setButtons($buttons)
                        );
                })
                // when user subscribe to PA
                ->onSubscribe(function ($event) use ($bot, $botSender) {
                    $this->getClient()->sendMessage(
                        (new Text())
                            ->setSender($botSender)
                            ->setText('Thanks for subscription!')
                    );
                })
                ->onText('|btn-click|s', function ($event) use ($bot, $botSender) {
                    // $log->info('click on button');
                    $receiverId = $event->getSender()->getId();
                    $bot->getClient()->sendMessage(
                        (new Text())
                            ->setSender($botSender)
                            ->setReceiver($receiverId)
                            ->setText('you press the button')
                    );
                })
                ->onText('|catalog|s', function ($event) use ($bot, $botSender) {
                    // $log->info('click on button');
                    $receiverId = $event->getSender()->getId();
                    $bot->getClient()->sendMessage(
                        (new Text())
                            ->setSender($botSender)
                            ->setReceiver($receiverId)
                            ->setText('А десь должен быть каталог ) 1')
                    );

                    $bot->getClient()->sendMessage(
                        (new Text())
                            ->setSender($botSender)
                            ->setReceiver($receiverId)
                            ->setText('А десь должен быть каталог ) 2')
                    );

                    $bot->getClient()->sendMessage(
                        (new Picture())
                            ->setSender($botSender)
                            ->setReceiver($receiverId)
                            ->setText('some media data')
                            ->setMedia('https://bot.shopiumbot.com/images/get-file/6412a68512-1.jpg')
                    );

                    $bot->getClient()->sendMessage(
                        (new Text())
                            ->setSender($botSender)
                            ->setReceiver($receiverId)
                            ->setText('А десь должен быть каталог ) 3')
                    );
                })
                ->onText('|k\d+|is', function ($event) use ($bot, $botSender) {
                    $caseNumber = (int)preg_replace('|[^0-9]|s', '', $event->getMessage()->getText());
                    $client = $bot->getClient();
                    $receiverId = $event->getSender()->getId();
                    switch ($caseNumber) {
                        case 0:
                            $client->sendMessage(
                                (new Text())
                                    ->setSender($botSender)
                                    ->setReceiver($receiverId)
                                    ->setText('Basic keyboard layout')
                                    ->setKeyboard(
                                        (new Keyboard())
                                            ->setButtons([
                                                (new Button())
                                                    ->setActionType('reply')
                                                    ->setActionBody('btn-click')
                                                    ->setText('Tap this button')
                                            ])
                                    )
                            );
                            break;
                        //
                        case 1:
                            $client->sendMessage(
                                (new Text())
                                    ->setSender($botSender)
                                    ->setReceiver($receiverId)
                                    ->setText('More buttons and styles')
                                    ->setKeyboard(
                                        (new Keyboard())
                                            ->setButtons([
                                                (new Button())
                                                    ->setBgColor('#8074d6')
                                                    ->setTextSize('small')
                                                    ->setTextHAlign('center')
                                                    ->setActionType('reply')
                                                    ->setActionBody('catalog')
                                                    ->setText('Каталог'),

                                                (new Button())
                                                    ->setBgColor('#2fa4e7')
                                                    ->setTextHAlign('center')
                                                    ->setActionType('reply')
                                                    ->setActionBody('btn-click')
                                                    ->setText('Button 2'),

                                                (new Button())
                                                    ->setBgColor('#555555')
                                                    ->setTextSize('large')
                                                    ->setTextHAlign('left')
                                                    ->setActionType('reply')
                                                    ->setActionBody('btn-click')
                                                    ->setText('Button 3'),
                                            ])
                                    )
                            );
                            break;
                        //
                        case 2:
                            $client->sendMessage(
                                (new \Viber\Api\Message\Contact())
                                    ->setSender($botSender)
                                    ->setReceiver($receiverId)
                                    ->setName('Novikov Bogdan')
                                    ->setPhoneNumber('+380000000000')
                            );
                            break;
                        //
                        case 3:
                            $client->sendMessage(
                                (new \Viber\Api\Message\Location())
                                    ->setSender($botSender)
                                    ->setReceiver($receiverId)
                                    ->setLat(48.486504)
                                    ->setLng(35.038910)
                            );
                            break;
                        //
                        case 4:
                            $client->sendMessage(
                                (new \Viber\Api\Message\Sticker())
                                    ->setSender($botSender)
                                    ->setReceiver($receiverId)
                                    ->setStickerId(114408)
                            );
                            break;
                        //
                        case 5:
                            $client->sendMessage(
                                (new \Viber\Api\Message\Url())
                                    ->setSender($botSender)
                                    ->setReceiver($receiverId)
                                    ->setMedia('https://shopiumbot.com')
                            );
                            break;
                        //
                        case 6:
                            $client->sendMessage(
                                (new Picture())
                                    ->setSender($botSender)
                                    ->setReceiver($receiverId)
                                    ->setText('some media data')
                                    ->setMedia('https://developers.viber.com/img/devlogo.png')
                            );
                            break;
                        //
                        case 7:
                            $client->sendMessage(
                                (new Video())
                                    ->setSender($botSender)
                                    ->setReceiver($receiverId)
                                    ->setSize(2 * 1024 * 1024)
                                    ->setMedia('http://techslides.com/demos/sample-videos/small.mp4')
                            );
                            break;
                        //
                        case 8:
                            $client->sendMessage(
                                (new CarouselContent())
                                    ->setSender($botSender)
                                    ->setReceiver($receiverId)
                                    ->setButtonsGroupColumns(6)
                                    ->setButtonsGroupRows(6)
                                    ->setBgColor('#FFFFFF')
                                    ->setButtons([
                                        (new Button())
                                            ->setColumns(6)
                                            ->setRows(3)
                                            ->setActionType('open-url')
                                            ->setActionBody('https://www.google.com')
                                            ->setImage('https://bot.shopiumbot.com/images/get-file/6412a68512-1.jpg'),

                                        (new Button())
                                            ->setColumns(6)
                                            ->setRows(3)
                                            ->setActionType('open-url')
                                            ->setActionBody('https://www.google.com')
                                            ->setImage('https://bot.shopiumbot.com/images/get-file/6412a68512-1.jpg'),


                                        (new Button())
                                            ->setColumns(6)
                                            ->setRows(3)
                                            ->setActionType('open-url')
                                            ->setActionBody('https://www.google.com')
                                            ->setImage('https://bot.shopiumbot.com/images/get-file/6412a68512-1.jpg'),


                                        (new Button())
                                            ->setColumns(6)
                                            ->setRows(6)
                                            ->setActionType('open-url')
                                            ->setActionBody('https://www.google.com')
                                            ->setImage('https://bot.shopiumbot.com/images/get-file/6412a68512-1.jpg'),


                                        (new Button())
                                            ->setColumns(6)
                                            ->setRows(3)
                                            ->setActionType('open-url')
                                            ->setActionBody('https://www.google.com')
                                            ->setImage('https://bot.shopiumbot.com/images/get-file/6412a68512-1.jpg'),


                                        (new Button())
                                            ->setColumns(6)
                                            ->setRows(6)
                                            ->setActionType('open-url')
                                            ->setActionBody('https://www.google.com')
                                            ->setImage('https://bot.shopiumbot.com/images/get-file/6412a68512-1.jpg'),

                                        (new Button())
                                            ->setColumns(6)
                                            ->setRows(6)
                                            ->setActionType('reply')
                                            ->setActionBody('https://www.google.com')
                                            ->setText('Buy')
                                            ->setTextSize("large")
                                            ->setTextVAlign("middle")
                                            ->setTextHAlign("middle")
                                            ->setImage('https://i7.pngflow.com/pngimage/273/159/png-game-buttons-game-buttons-ui-button-game-clipart-thumb.png')
                                    ])
                            );
                            break;
                    }
                })
                ->run();

            $request = file_get_contents("php://input");
            $input = json_decode($request, true);
            return $request;
        } catch (Exception $e) {
            // todo - log exceptions
        }


    }

    public function actionSet()
    {
        $webhookUrl = 'https://bot.shopiumbot.com/viber/hook'; // for exmaple https://my.com/bot.php
        \Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $client = new Client(['token' => $this->apikey]);
            $result = $client->setWebhook($webhookUrl);

            return $result->getData();


        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage() . "\n";
        }
    }
}
