<?php

namespace app\modules\buyer\controllers;

use app\models\CompanyType;
use app\models\CountrySheme;
use app\models\Invite;
use app\models\Notifications;
use app\models\Persons;
use app\models\Regions;
use app\models\User;
use app\models\Companies;
use app\models\UserJoinRequests;
use yii\helpers\Html;
use yii\web\Controller;
use Yii;
use yii\base\Model;
use yii\helpers\VarDumper;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;

class RegisterController extends Controller
{
    public function actionIndex()
    {
        $company = new Companies();
        $persons = new Persons();
        $user = new User();

        if (Yii::$app->request->isAjax) {

            Yii::$app->response->format = Response::FORMAT_JSON;

            if (isset($_POST['User']['username']) && $_POST['User']['username'] != '') {
                $user->load(Yii::$app->request->post());
                $user->validate();
                return $user->errors;
            }

            if (isset($_POST['Companies']['identifier']) && $_POST['Companies']['identifier'] != '') {
                $company->load(Yii::$app->request->post());
                $company->validate();
                return $company->errors;
            }
        }

        if (($company->load(Yii::$app->request->post()) && $persons->load(Yii::$app->request->post()) && $user->load(Yii::$app->request->post())) && (Model::validateMultiple([$company, $persons, $user]))) {

            $transaction = Yii::$app->db->beginTransaction();

            try {
                $company->status = Companies::STATUS_NEW;
                $company->save(false);
                $persons->company_id = $company->id;
                $persons->save(false);
                $user->company_id = $company->id;
                $user->is_owner = 1;
                $user->save(false);
                $transaction->commit();

            } catch (\Exception $e) {

                $transaction->rollBack();
                return $this->render('/common/flash_fail');
            }

            //лист-підтвердження реєстрації
            Notifications::confirmRegistration($user);

            Yii::$app->session->setFlash('register_success', Yii::t('app', 'Вы успешно зарегистрировались. Проверьте почту и перейдите по ссылке для подтверждения регистрации.'));

            return $this->render('success', [
                'company' => $company,
                'persons' => $persons,
                'user' => $user
            ]);

        }

        return $this->render('index', [
            'company' => $company,
            'persons' => $persons,
            'user' => $user

        ]);
    }

    public function actionJoin()
    {
        $join = new UserJoinRequests();
        $session = Yii::$app->session;

        //если пришел пост со страницы регистрации
        if (!empty(Yii::$app->request->post())) {
            $post = Yii::$app->request->post();
            if (isset($post['UserJoinRequests']['_joinToIdentifier']) && $post['UserJoinRequests']['_joinToIdentifier'] != '') {
                $joinToIdentifier = $post['UserJoinRequests']['_joinToIdentifier'];
                $session->set('joinToIdentifier', $joinToIdentifier);
            } elseif (isset($session['joinToIdentifier'])) {
                $joinToIdentifier = $session->get('joinToIdentifier');
            }
        }

        if (!isset($joinToIdentifier)) {
            throw new \yii\web\NotFoundHttpException();
        }

        //валидатор отрабатывает только при сабмите со страницы присоединения
        if ($join->load(Yii::$app->request->post()) && !isset($post['UserJoinRequests']['_joinToIdentifier']) && $join->validate()) {
            $join->company_id = Companies::findOne(['identifier' => $session->get('joinToIdentifier')])->id;
            if ($join->save(false)) {
                //достаем email овнера компании
                $ownerEmail = User::findOne(['company_id' => $join->company_id, 'is_owner' => 1])->username;
                Yii::$app->session->setFlash('join_success', Yii::t('app', 'Запрос на присоединение успешно отправлен.'));
            } else {
                return $this->render('/common/flash_fail');
            }

            //отправляем письмо с просьбой о присоеденении овнеру компании
            if ($ownerEmail) {
                Notifications::joinRegistration($join, $ownerEmail);
            }

            return $this->render('success_join');
        }

        return $this->render('join', [
            'join' => $join,
        ]);

    }

    public function actionConfirm()
    {
        $user = User::find()->where([
            'activationcode' => Yii::$app->request->getQueryParam('activationcode'),
            'status' => User::STATUS_INACTIVE,
        ])->one();
        if (!empty($user)) {
            $user->status = User::STATUS_ACTIVE;
            $user->save(false);

            Yii::$app->session->setFlash('register_success', Yii::t('app', 'Вы успешно подтвердили свой email.'));

            Yii::$app->user->login($user);

            return $this->redirect(['/'.Companies::getCompanyBusinesType().'/cabinet/']);

        } else {
            echo 'Нет такого токена';
        }

    }

    public function actionJoinconfirm()
    {

        $join = UserJoinRequests::find()->where([
            'activationcode' => Yii::$app->request->getQueryParam('activationcode'),
        ])->one();

        if (!empty($join)) {
            $invate = new Invite();
            $invate->email = $join->username;
            $invate->fio = $join->fio;
            $invate->company_id = $join->company_id;

            if($invate->save(false)){
                $join->delete();
                Yii::$app->session->setFlash('invate_success', Yii::t('app', 'Вы успешно подтвердили запрос присоеденение пользователя.'));
                return $this->render('success_join');
            }

        } else {
            echo 'Нет такого токена';
        }
    }

    public function actionGetcountrysheme(){

        if (Yii::$app->request->isAjax) {

            $post = Yii::$app->request->post();
            $res = CountrySheme::find()->where(['country_id' => $post['id']])->all();

            if ($res) {
                $html = '';
                foreach ($res as $k => $v) {
                    $html .= '<option type_ids="'.$v['company_type_ids'].'" value="' . $v['id'] . '">' . $v['name'] . '</option>';
                }
                echo $html;
            }

        }
    }

    public function actionGetcountryregion(){

        if (Yii::$app->request->isAjax) {

            $post = Yii::$app->request->post();
            $res = Regions::find()->where(['country_id' => $post['id']])->all();

            if ($res) {
                $html = '';
                foreach ($res as $k => $v) {
                    $html .= '<option value="' . $v['id'] . '">' . $v['name'] . '</option>';
                }
                echo $html;
            }

        }
    }

    public function actionGetshemetype(){

        if (Yii::$app->request->isAjax) {

            $post = Yii::$app->request->post();

            $res = CompanyType::find()->where(['in','id',explode(',',$post['ids'])])->all();

            if ($res) {
                $html = '';
                foreach ($res as $k => $v) {
                    $html .= '<option code_length= "'.$v['code_length'].'" value="' . $v['id'] . '">' . $v['name'] . '</option>';
                }
                echo $html;
            }

        }
    }

}
