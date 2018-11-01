<?php
namespace api\modules\v1\controllers;


use api\modules\v1\models\LoginForm;
use api\modules\v1\models\User;
use api\modules\v1\models\UserSignup;
use yii\base\ErrorException;
use yii\web\MethodNotAllowedHttpException;
use yii\rest\Controller;
use Yii;

class OauthController extends Controller
{

    public function actionSignup()
    {
        $model = new UserSignup();
        if (!Yii::$app->request->post()) {
            throw new MethodNotAllowedHttpException('Not submitted');
        }
        if ($model->load(\Yii::$app->getRequest()->getBodyParams(), '')) {
            return $model->signup();
        } else {
            return "nothing returned";
        }

    }

    /**
     * @return array
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function actionLogin()
    {
        $model = new LoginForm();

        //This action throws an exception if method is not post
        if (!Yii::$app->request->post()) {
            throw new MethodNotAllowedHttpException('Not submitted');
        }

        /**
         * The code below logs you in and return token details once auth is valid
         *
         * You can customise your response when you login
         */
        if ($model->load(\Yii::$app->getRequest()->getBodyParams(), '') && $model->login()) {
            $user = User::findOne(['username' => $model->username]);
            $user->access_token = \Yii::$app->security->generateRandomString();
            $user->token_expires = date('Y-m-d H:i:s', time());
            $user->save();
            return ['token' => $user->access_token, 'expiry' => $user->token_expires];
        }
    }
}