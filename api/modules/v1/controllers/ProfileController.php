<?php
namespace api\modules\v1\controllers;

use yii\rest\Controller;
use yii\filters\auth\HttpBearerAuth;
use Yii;

class ProfileController extends Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        /**This authenticate access to your controller if you are using token for access.
         * The access toen needs to be passed through the request header
         */
        $behaviors['authenticator'] = [
            'class'=>HttpBearerAuth::className()
        ];

        $behaviors['corsFilter'] = [
            'class'=>\yii\filters\Cors::className()
        ];

        return $behaviors;
    }
}