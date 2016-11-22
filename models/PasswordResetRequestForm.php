<?php
namespace app\models;

use app\models\User;
use app\models;
use yii\base\Model;
use Yii;

/**
 * Password reset request form
 */
class PasswordResetRequestForm extends Model
{
    public $username;
    public $verifyCode;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['username', 'filter', 'filter' => 'trim'],
            ['username', 'required'],
            ['username', 'email'],
            ['username', 'exist',
                'targetClass' => 'app\models\User',
                'filter' => 'status = '. User::STATUS_ACTIVE . ' OR status = '. User::STATUS_ADMIN,
                'message' => Yii::t('app','Пользователя с таким именем(email) не существует.')
            ],
            ['verifyCode', 'captcha'],
        ];
    }

    /**
     * Sends an email with a link, for resetting the password.
     *
     * @return boolean whether the email was send
     */
    public function sendEmail()
    {
        /* @var $user User */
        $user = User::find()
            ->where(['status' => User::STATUS_ACTIVE])
            ->orWhere(['status' => User::STATUS_ADMIN])
            ->andWhere(['username' => $this->username])
            ->one();

        if ($user) {
            if (!User::isPasswordResetTokenValid($user->password_reset_token)) {
                $user->generatePasswordResetToken();
            }
            if ($user->save(false)) {

                return Notifications::passwordReset($user, $this->username);
            }
        }

        return false;
    }
}
