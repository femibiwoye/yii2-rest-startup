<?php

namespace api\modules\v1\models;

use yii\db\ActiveRecord;

class User extends ActiveRecord implements \yii\web\IdentityInterface
{
    const ACTIVE_USER = 1;
    const INACTIVE_USER = 0;

    public function rules()
    {
        return [
            ['status','default','value'=>self::ACTIVE_USER],
            ['status','in','range'=>[self::ACTIVE_USER, self::INACTIVE_USER]]
        ];
    }

    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        //return isset(self::$users[$id]) ? new static(self::$users[$id]) : null;
        return static::findOne(['id'=>$id,'status'=>self::ACTIVE_USER]);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        if($user = static::findOne(['access_token'=>$token,'status'=>self::ACTIVE_USER])){
            // User is logged out after 60 minutes of not communicating with the server.
            $expires = strtotime("+60 minute",strtotime($user->token_expires));
            if($expires > time()) {
                //Expire time is updated
                $user->token_expires = date('Y-m-d H:i:s', strtotime('now'));
                $user->save();
                return $user;
            }else{
                //Token is removed if it is no longer valid
                $user->access_token = '';
                $user->save();
            }
        }
    }

    /**
     * Finds user by username or email
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::find()->where(['username'=>$username])
            ->orwhere(['email'=>$username])
            ->andWhere(['status'=>self::ACTIVE_USER])
            ->one();
    }

    /**
     * Encrypt password
     *
     * @param $password
     * @throws \yii\base\Exception
     */
    public function setPassword($password)
    {
        $this->password = \Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generate random string for authentication
     */
    public function generateAuthKey()
    {
        $this->auth_key = \Yii::$app->security->generateRandomString();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return \Yii::$app->security->validatePassword($password,$this->password);
    }

    /**
     * This is for basic Auth
     *
     * @param $password
     * @param $userPassword
     * @return bool
     */
    public function validatePasswordBasic($password,$userPassword)
    {
        return \Yii::$app->security->validatePassword($password,$userPassword);
    }
}
