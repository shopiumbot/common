<?php

namespace core\components\controllers;

use core\modules\user\components\WebUser;
use core\modules\user\models\User;
use Yii;
use yii\web\IdentityInterface;
use yii\web\Response;
use panix\engine\CMS;
use shopium\mod\admin\models\LoginForm;

/**
 * Class WebController
 * @package panix\engine\controllers
 */
class WebController extends CommonController
{

    public function actions()
    {
        return [
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }


    public function actionIndex()
    {

        if (Yii::$app->user->isGuest) {
            return $this->redirect(['/user/login']);
        } else {
            return $this->redirect(['/admin']);
        }
        $this->layout = "main";
        $this->view->title = Yii::t('yii', 'Home');
        return $this->render('index');
    }


    public function getAssetUrl()
    {
        $assetsPaths = Yii::$app->getAssetManager()->publish(Yii::getAlias("@theme/assets"));
        return $assetsPaths[1];
    }

    /**
     * @inheritdoc
     */
    public function init()
    {

        $user = Yii::$app->user;
        $config = Yii::$app->settings->get('app');
        $timeZone = $config->timezone;
        Yii::$app->timeZone = $timeZone;
        // Yii::setAlias('@theme', Yii::getAlias("@app/web/themes/{$config->theme}"));
        Yii::setAlias('@theme', Yii::$app->view->theme->basePath);

        parent::init();
    }

    public function actionNoJavascript()
    {
        //TODO Пересмотреть данное решение для моб где нету вообще JavaScript
        $this->layout = 'error';
        return $this->render('no-javascript', [
            'name' => '',
            'message' => Yii::t('app/default', 'NO_JAVASCRIPT')
        ]);
    }

    public function actionError()
    {
        /**
         * @var $handler \yii\web\ErrorHandler
         * @var $exception \yii\web\HttpException
         */
        $handler = Yii::$app->errorHandler;
        $exception = $handler->exception;

        if ($exception !== null) {
            $statusCode = $exception->statusCode;
            $name = $exception->getName();
            $message = $exception->getMessage();
            $this->layout = "@theme/views/layouts/error";

            $this->pageName = Yii::t('app/error', $statusCode);

            $this->view->title = $this->pageName;
            $this->breadcrumbs[] = $statusCode;
            return $this->render('error', [
                'exception' => $exception,
                'handler' => $handler,
                'statusCode' => $statusCode,
                'name' => $name,
                'message' => $message
            ]);
        }
    }

    public function actionPlaceholder2()
    {

        $request = Yii::$app->request;
        // Dimensions
        $getsize = ($request->get('size')) ? $request->get('size') : '100x100';
        $dimensions = explode('x', $getsize);

        if (empty($dimensions[0])) {
            $dimensions[0] = $dimensions[1];
        }
        if (empty($dimensions[1])) {
            $dimensions[1] = $dimensions[0];
        }

        header("Content-type: image/png");
        // Create image
        $image = imagecreate($dimensions[0], $dimensions[1]);

        // Colours
        $bg = ($request->get('bg')) ? $request->get('bg') : 'ccc';

        $bg = CMS::hex2rgb($bg);
        $opacityBg = ($request->get('bg')) ? 0 : 127;
        //$setbg = imagecolorallocate($image, $bg['r'], $bg['g'], $bg['b']);
        $setbg = imagecolorallocatealpha($image, $bg['r'], $bg['g'], $bg['b'], $opacityBg);

        $fg = ($request->get('fg')) ? $request->get('fg') : '999';
        $fg = CMS::hex2rgb($fg);
        $setfg = imagecolorallocate($image, $fg['r'], $fg['g'], $fg['b']);

        $text = ($request->get('text')) ? strip_tags($request->get('text')) : $getsize;
        $text = str_replace('+', ' ', $text);
        $padding = ($request->get('padding')) ? (int)$request->get('padding') : 0;

        $fontsize = $dimensions[0] / 2;


        if (strlen($text) == 4 && preg_match("/([A-Za-z]{1}[0-9]{3})$/i", $text)) {
            $text = '&#x' . $text . ';';
            $font = Yii::getAlias('@vendor/panix/engine/assets/assets/fonts') . DIRECTORY_SEPARATOR . 'Pixelion.ttf';
        } elseif ($text == 'PIXELION' || $text == 'pixelion') {
            $font = Yii::getAlias('@vendor/panix/engine/assets/assets/fonts') . DIRECTORY_SEPARATOR . 'Pixelion.ttf';
        } else {
            $font = Yii::getAlias('@vendor/panix/engine/assets/assets/fonts') . DIRECTORY_SEPARATOR . 'Exo2-Light.ttf';
        }

        $textBoundingBox = imagettfbbox($fontsize - $padding, 0, $font, $text);
        // decrease the default font size until it fits nicely within the image
        while (((($dimensions[0] - ($textBoundingBox[2] - $textBoundingBox[0])) < $padding) || (($dimensions[1] - ($textBoundingBox[1] - $textBoundingBox[7])) < $padding)) && ($fontsize - $padding > 1)) {
            $fontsize--;
            $textBoundingBox = imagettfbbox($fontsize - $padding, 0, $font, $text);
        }

        imagettftext($image, $fontsize - $padding, 0, ($dimensions[0] / 2) - (($textBoundingBox[2] - $textBoundingBox[0]) / 2), ($dimensions[1] / 2) - (($textBoundingBox[1] + $textBoundingBox[7]) / 2), $setfg, $font, $text);


        imagepng($image);
        imagedestroy($image);
        die;
    }
    private function generateInitials($uname): string
   {
       $parameter_length=2;
       $nameOrInitials = mb_strtoupper( trim( $uname ) );
       $names          = explode( ' ', $nameOrInitials );
       $initials       = $nameOrInitials;
       $assignedNames  = 0;

       if ( count( $names ) > 1 )
       {
           $initials = '';
           $start    = 0;

           for ( $i = 0; $i < $parameter_length; $i ++ )
           {
               $index = $i;

               if ( ( $index === ( $parameter_length - 1 ) && $index > 0 ) || ( $index > ( count( $names ) - 1 ) ) )
               {
                   $index = count( $names ) - 1;
               }

               if ( $assignedNames >= count( $names ) )
                 {
                     $start ++;
                 }

               $initials .= mb_substr( $names[ $index ], $start, 1 );
               $assignedNames ++;
           }
       }

       $initials = mb_substr( $initials, 0, $parameter_length );

       return $initials;
   }
    public function actionPlaceholder()
    {

        $request = Yii::$app->request;
        // Dimensions
        $getsize = ($request->get('size')) ? $request->get('size') : '100x100';
        $dimensions = explode('x', $getsize);

        if (empty($dimensions[0])) {
            $dimensions[0] = $dimensions[1];
        }
        if (empty($dimensions[1])) {
            $dimensions[1] = $dimensions[0];
        }

        header("Content-type: image/png");
        // Create image
        $image = imagecreate($dimensions[0], $dimensions[1]);
        $colors = [
            'fc0fc0',
            'b200ed',
            '0e4c92',
            '3bb143',
            '7c4700',
            'd30000',
            'fc6600',
            'ffd300'
        ];


        $rand = range(0, count($colors));
        // Colours
        //$bg = ($request->get('bg')) ? $request->get('bg') : 'ccc';
        $bg = $colors[array_rand($colors)];
        $bg = CMS::hex2rgb($bg);

        //$setbg = imagecolorallocate($image, $bg['r'], $bg['g'], $bg['b']);
        $setbg = imagecolorallocatealpha($image, $bg['r'], $bg['g'], $bg['b'], 0);


        $fg = CMS::hex2rgb('fff');
        $setfg = imagecolorallocate($image, $fg['r'], $fg['g'], $fg['b']);

        $text = ($request->get('text')) ? strip_tags($request->get('text')) : $getsize;
        $text = mb_strtoupper(trim(str_replace('+', ' ', $text)));
        $words = explode(' ', $text);
        foreach($words as $word){
        //    $text
        }
        $text = $this->generateInitials($text);
       // $text =  mb_strcut($text, 0,1);
        $padding = ($request->get('padding')) ? (int)$request->get('padding') : 0;

        $fontsize = $dimensions[0] / 2;


        // if (strlen($text) == 4 && preg_match("/([A-Za-z]{1}[0-9]{3})$/i", $text)) {
        //     $text = '&#x' . $text . ';';
        //    $font = Yii::getAlias('@vendor/panix/engine/assets/assets/fonts') . DIRECTORY_SEPARATOR . 'Pixelion.ttf';
        //} elseif ($text == 'PIXELION' || $text == 'pixelion') {
        //$font = Yii::getAlias('@vendor/panix/engine/assets/assets/fonts') . DIRECTORY_SEPARATOR . 'Pixelion.ttf';
        // } else {
       // $font = Yii::getAlias('@vendor/panix/engine/assets/assets/fonts') . DIRECTORY_SEPARATOR . 'Exo2-Light.ttf';
        //  }
        $font = Yii::getAlias('@app/components') . DIRECTORY_SEPARATOR . 'OpenSans-Regular.ttf';
        $textBoundingBox = imagettfbbox($fontsize - $padding, 0, $font, $text);
        // decrease the default font size until it fits nicely within the image
        while ((($textBoundingBox[2] < $padding) || ($textBoundingBox[1] < $padding)) && ($fontsize - $padding > 1)) {
            $fontsize--;
            $textBoundingBox = imagettfbbox($fontsize - $padding, 0, $font, $text);
        }

        imagettftext($image, $fontsize, 0, ($dimensions[0] / 2) - ($textBoundingBox[2] / 2), ($dimensions[1] / 2) - ($textBoundingBox[7] / 2), $setfg, $font, $text);

        imagepng($image);
        imagedestroy($image);
        die;
    }

    public function loginByToken($token)
    {
        /* @var $identity User */
        $class = $this->identityClass;
        $identity = $class::findIdentityByAccessToken($token);
        if ($identity && Yii::$app->user->login($identity)) {
            return $identity;
        }

        return null;
    }

    public function actionLogin()
    {

        if (!Yii::$app->user->isGuest)
            return $this->redirect(['/admin']);

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login(86400 * 30)) {
            return $this->goBack(['/admin']);
        }

        // render
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    public function actionFavicon()
    {
        $this->enableStatistic = false;
        $size = Yii::$app->request->get('size');
        $response = Yii::$app->response;
        /** @var \panix\engine\components\ImageHandler $img */
        $size_allow = [16, 32, 57, 60, 72, 76, 96, 114, 120, 144, 152, 180];
        $config = Yii::$app->settings->get('app');
        if ($size && isset($config->favicon)) {

            $response->format = Response::FORMAT_RAW;

            $path = Yii::getAlias('@uploads') . DIRECTORY_SEPARATOR . $config->favicon;
            if (file_exists($path)) {
                $pathInfo = pathinfo($path);
                if ($pathInfo['extension'] == 'ico') {
                    $response->headers->set('Content-Type', 'image/x-icon');
                    return file_get_contents($path);
                } else {
                    if (!in_array($size, $size_allow)) {
                        $this->error404();
                    }
                    //$response->headers->add('Content-Type', 'image/png');
                    $img = Yii::$app->img->load($path);
                    $img->resize($size, $size);
                    $img->show();
                    die;
                }
            }
        }
        $this->error404();

    }

}
