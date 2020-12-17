<?php

namespace core\components;

use Yii;
use yii\log\Dispatcher;

/**
 * Class DispatcherLog
 */
class DispatcherLog extends Dispatcher
{
    public $enableEmail = false;
    public $traceLevel = YII_DEBUG ? 3 : 0;
    public $flushInterval = 1000 * 10;

    /**
     * {@inheritdoc}
     */
    public function init()
    {

        $date = new \DateTime(date('Y-m-d', time()), new \DateTimeZone('Europe/Kiev'));
        $date = $date->format('Y-m-d');
        $logPath = '@runtime/logs/' . $date . '/' . Yii::$app->id;


        $this->targets[] = [
            'class' => 'panix\engine\log\FileTarget',
            'levels' => ['error', 'warning'],
            'categories' => ['yii\db\*'],
            'logFile' => $logPath . '/db_error.log',
        ];

            $this->targets[] = [
                'class' => 'panix\engine\log\FileTarget',
                'levels' => ['info'],
                'enabled' => true,
                'categories' => [
                    'yii\db\Command::execute'
                ],
                'logVars' => [],
                'logFile' => $logPath . '/db_execute.log',

                'except' => [
                    'yii\db\Connection::open',
                    'yii\web\Session::open',
                    'yii\web\Session::close',
                    'yii\web\Session::unfreeze',
                    'yii\web\Session::freeze',
                ],

            ];


            $this->targets[] = [
                'class' => 'panix\engine\log\FileTarget',
                'levels' => ['info'],
                'enabled' => true,
                'categories' => [
                    'yii\db\Command::query',
                ],
                'logVars' => [],
                'logFile' => $logPath . '/db_query.log',

                'except' => [
                    'yii\db\Connection::open',
                    'yii\web\Session::open',
                    'yii\web\Session::close',
                    'yii\web\Session::unfreeze',
                    'yii\web\Session::freeze',
                ],

            ];

        $this->targets[] = [
            'class' => 'panix\engine\log\FileTarget',
            'levels' => ['info'],
            'enabled' => YII_DEBUG,
            'logFile' => $logPath . '/info.log',
            //'logVars' => [],
            'except' => [
                'yii\db\Command::query',
                'yii\db\Command::execute',
                'yii\db\Connection::open',
                'yii\swiftmailer\Mailer::sendMessage',
                'yii\mail\BaseMailer::send',
                'yii\httpclient\StreamTransport::send',
                'yii\web\Session::open',
                'yii\web\Session::unfreeze',
                'yii\web\Session::freeze',
            ],
        ];
        $this->targets[] = [
            'class' => 'panix\engine\log\FileTarget',
            'levels' => ['info'],
            'logVars' => [],
            'logFile' => $logPath . '/mail.log',
            'categories' => [
                'yii\mail\BaseMailer::send',
            ],

        ];
        $this->targets[] = [
            'class' => 'panix\engine\log\FileTarget',
            'levels' => ['profile'],
            'enabled' => YII_DEBUG,
            'logFile' => $logPath . '/profile.log',
            'except' => [
                'yii\db\Command::query',
                'yii\db\Command::execute',
                'yii\db\Connection::open',
                'yii\httpclient\StreamTransport::send'
            ],
        ];
        $this->targets[] = [
            'class' => 'panix\engine\log\FileTarget',
            'levels' => ['info'],
            'enabled' => YII_DEBUG,
            'logFile' => $logPath . '/httpclient.log',
            'categories' => [
                'yii\httpclient\StreamTransport::send',
            ],
        ];


        /*[
            'class' => 'panix\engine\log\FileTarget',
            'levels' => ['trace'],
            'enabled' => false,
            'logFile' => $logPath . '/trace.log',
        ],*/

        $this->targets[] = [
            'class' => 'panix\engine\log\EmailTarget',
            'levels' => ['error', 'warning'],
            //'categories' => ['yii\base\*'],
            'enabled' => true,
            'except' => [
                'yii\web\HttpException:404',
                'yii\web\HttpException:403',
                'yii\web\HttpException:400',
                'yii\i18n\PhpMessageSource::loadMessages'
            ],
        ];
        $this->targets[] = [
            'class' => 'panix\engine\log\FileTarget',
            'levels' => ['error'],
            'logFile' => $logPath . '/error.log',
        ];
        $this->targets[] = [
            'class' => 'panix\engine\log\FileTarget',
            'levels' => ['warning'],
            'logFile' => $logPath . '/warning.log',
        ];

        parent::init();


    }
}