<?php
/**
 * Created by PhpStorm.
 * User: femiibiwoye
 * Date: 13/07/2018
 * Time: 5:33 PM
 */

namespace api\modules\v1\models;


use yii\base\Model;

class UserSignup extends Model
{
    public $username;
    public $email;
    public $password;
    public $password_confirm;

    public function rules()
    {
        return [
            ['username', 'trim'],
            ['username', 'required'],
            ['username', 'string', 'min' => 4, 'max' => 100],
            ['username', 'unique', 'targetClass' => 'api\modules\v1\models\User', 'message' => 'Ops! username taken'],

            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 200],
            ['email', 'unique', 'targetClass' => 'api\modules\v1\models\User', 'message' => 'Ops! email taken'],

            ['password', 'string', 'min' => 6],
            ['password', 'required'],

            ['password_confirm', 'required'],
            ['password_confirm', 'compare', 'compareAttribute' => 'password', 'message' => 'Ops! password not the same']
        ];
    }

    public function signup()
    {
        if (!$this->validate()) {
            return $this;
        }

        $user = new User();
        $user->username = $this->username;
        $user->email = $this->email;
        $user->access_token = \Yii::$app->security->generateRandomString(32); //This generate access token
        $user->setPassword($this->password);
        $user->generateAuthKey();

        return ($user->save()) ? $user : null;
    }
}