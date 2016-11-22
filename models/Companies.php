<?php

namespace app\models;

use app\models\Complaints;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use app\validators\MfoLengthValidator;
use app\validators\IpnStructureValidator;
use app\validators\BankAccountLengthValidator;

/**
 * This is the model class for table "companies".
 *
 * @property integer $id
 * @property string $legalName
 * @property string $fin_license
 * @property string $customer_type
 * @property integer $registrationCountryName
 * @property integer $identifier
 * @property parent_identifier $parent_identifier
 * @property string $moneygetId
 * @property string $fio
 * @property string $userPosition
 * @property string $userDirectionDoc
 * @property integer $countryName
 * @property integer $region
 * @property string $locality
 * @property string $streetAddress
 * @property string $postalCode
 * @property string $preferLang
 * @property integer $status
 * @property integer $owner_id
 * @property string $passport_data
 * @property integer $LegalType
 *
 * @property Languages $preferLang0
 * @property User $owner
 * @property Countries $countryName0
 * @property Countries $registrationCountryName0
 * @property Regions $region0
 * @property Persons[] $persons
 * @property User[] $users
 * @property integer $is_seller
 * @property float $balance
 * @property integer $create_at
 * @property integer $update_at
 */
class Companies extends \yii\db\ActiveRecord
{

    const STATUS_NEW = 0;

    const STATUS_ACCEPTED = 1;

    const STATUS_BLOCKED = 2;

    const _NATURAL_PERSON = 3; //фізична особа

    private $contractFields = [
        'identifier', // едрпоу
        'legalName', //Назва юридичної особи
        'userPosition', // посада
        'fio', // ФИО
        'userDirectionDoc' // статут
    ];

    public $isDaughter;
    public $haveFinLicense;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'companies';
    }

/*    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'create_at',
                'updatedAtAttribute' => 'update_at',
                'value' => new Expression('NOW()'),
            ],
        ];
    }*/

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['LegalType', 'registrationCountryName', 'identifier', 'region', 'streetAddress', 'postalCode', 'fio', 'locality', 'userPosition', 'userDirectionDoc', 'is_seller'], 'required'],
            ['countryName', 'required', 'message' => Yii::t('app', 'Необхідно заповнити') . ' "' . Yii::t('app', 'Схема реєстрації') . '"'],
            [['isDaughter'], 'required', 'except' => 'default'],
            [['customer_type'], 'required','whenClient' => 'function (attribute, value) { return $(attribute.input).is(":visible"); }'],
            [['LegalType', 'haveFinLicense', 'countryName','registrationCountryName', 'identifier', 'postalCode', 'region', 'status', 'isDaughter', 'parent_identifier', 'create_at', 'update_at'], 'integer'],
            [['haveFinLicense'], 'in', 'range'  => [1, 0]],
            ['passport_data', 'string', 'max' => 255],
            [
                'legalName',
                'required',
                'whenClient' => 'function (attribute, value) {
                    return $(attribute.input).is(":visible");
                }',
            ],
            [
                'legalName_en',
                'required',
                'whenClient' => 'function (attribute, value) {
                    return $(attribute.input).is(":visible");
                }',
            ],
            [
                ['haveFinLicense', 'payer_pdv'],
                'required',
                'when' => function () {
                    return false;
                },
                'whenClient' => 'function (attribute, value) {
                    return $(attribute.input).is(":visible");
                }',
            ],
            [
                'fin_license',
                'required',
                'when' => function ($model) {
                    return $model->haveFinLicense == 1;
                },
                'whenClient' => 'function (attribute, value) {
                    return $(attribute.input).is(":visible");
                }',
            ],
            [
                'fin_license',
                'string',
                'length' => [5, 15],
                'tooLong' => Yii::t('app', 'The maximum length of the "Financial license" should not exceed 15 digits'),
                'tooShort' => Yii::t('app', 'The minimum length of the "Financial license" should not be less than 5 digits'),
                'when' => function ($model) {
                    return $model->haveFinLicense == 1;
                },
                'whenClient' => 'function (attribute, value) {
                    return $(attribute.input).is(":visible");
                }',
            ],
            [['fin_license'], 'unique', 'targetAttribute' => ['fin_license', 'is_seller'], 'targetClass' => 'app\models\Companies', 'comboNotUnique' => Yii::t('app', 'The company with the financial license is already registered in the system'),
                'when' => function ($model) {
                    return $model->haveFinLicense == 1;
                },
                'whenClient' => 'function (attribute, value) {
                    return $(attribute.input).is(":visible");
                }',
            ],
            [['isDaughter'], 'in', 'range'  => [1, 0]],
            [['moneygetId', 'streetAddress'], 'string', 'max' => 100],
            [['postalCode'], 'string', 'length' => 5],
            [
                'identifier',
                'string',
                'length' => [8, 8],
                'tooLong' => Yii::t('app', 'EDRPOU length should be 8 digits'),
                'tooShort' => Yii::t('app', 'EDRPOU length should be 8 digits'),
            ],
            [
                'parent_identifier',
                'required',
                'when' => function ($model) {
                    return $model->isDaughter == 1;
                },
                'whenClient' => 'function (attribute, value) {
                    return $(attribute.input).is(":visible");
                }',
                'except' => 'default'
            ],
            [
                'parent_identifier',
                'string',
                'length' => [8, 8],
                'tooLong' => Yii::t('app', 'Length EDRPOU parent company should be 8 digits'),
                'tooShort' => Yii::t('app', 'Length EDRPOU parent company should be 8 digits'),
                'when' => function ($model) {
                    return $model->isDaughter == 1;
                },
                'whenClient' => 'function (attribute, value) {
                    return $(attribute.input).is(":visible");
                }',
                'except' => 'default'
            ],
            [['legalName', 'streetAddress', 'postalCode', 'fio', 'locality'], 'string', 'max' => 100],
            [['userDirectionDoc','userPosition'], 'string', 'max' => 500],
            [['identifier'], 'unique', 'targetAttribute' => ['identifier', 'is_seller'], 'targetClass' => 'app\models\Companies', 'comboNotUnique' => Yii::t('app', 'Company with such a code is already registered in the system')],
            ['parent_identifier', 'compare', 'compareAttribute' => 'identifier', 'operator' => '!=', 'message' => Yii::t('app', 'The "EDRPOU parent company" can not match the field "EDRPOU"'),
                'when' => function ($model) {
                    return $model->isDaughter == 1;
                },
                'whenClient' => 'function (attribute, value) {
                    return $(attribute.input).is(":visible");
                }',
            ],
            [['mfo', 'bank_account', 'bank_branch'], 'required', 'except' => ['default', 'updateCompanyAdmin']],
            [['mfo', 'bank_account', 'ipn_id'], 'integer', /*'except' => 'default'*/],
            [['payer_pdv'], 'required',
                'on' => 'changeBankAccount'
            ],
            [['payer_pdv'], 'integer', 'max' => 1, 'min' => 0, 'except' => 'default'],
            [['payer_pdv'], 'in', 'range'  => [1, 0], 'except' => 'default'],
            [
                'mfo',
                'is6NumbersOnly',
                'on' => ['default', 'changeBankAccount', 'updateCompany'],
                'when' => function ($model) {
                    if (mb_strlen($model->mfo) == 0){
                        return (int)User::checkAdmin() != 1;
                    }
                    return true;
                },
                'whenClient' => 'function (attribute, value) {
                    if($(attribute.input).val().length == 0){
                        return '. (int)User::checkAdmin() .' != 1;
                    }
                    return true;
                }',
//                'except' => 'default'
            ],
            [
                'ipn_id',
                'required',
                'when' => function ($model) {
                    return $model->payer_pdv == 1;
                },
                'whenClient' => 'function (attribute, value) {
                    return $(attribute.input).is(":visible");
                }',
                'message' => Yii::t('app', 'For VAT payers "TIN" field is required'),
//                'except' => 'default'
            ],
            [
                'ipn_id',
                'string',
                'length' => [12, 12],
                'when' => function ($model) {
                    return $model->payer_pdv == 1;
                },
                'whenClient' => 'function (attribute, value) {
                    return $(attribute.input).is(":visible");
                }',
                'tooLong' => Yii::t('app', 'Length code IPN should be 12 digits'),
                'tooShort' => Yii::t('app', 'Length code IPN should be 12 digits'),
//                'except' => 'default'
            ],
            [
                'ipn_id', IpnStructureValidator::className(),
                'when' => function ($model) {
                    return $model->payer_pdv == 1;
                },
                'whenClient' => 'function (attribute, value) {
                    return $(attribute.input).is(":visible");
                }',
                'except' => 'default'
            ],
            [
                'bank_account',
                'string',
                'length' => [5, 14],
                'tooLong' => Yii::t('app', 'Length bank account should be from 5 to 14 digits'),
                'tooShort' => Yii::t('app', 'Length bank account should be from 5 to 14 digits'),
                'when' => function ($model) {
                    if (mb_strlen($model->bank_account) == 0){
                        return (int)User::checkAdmin() != 1;
                    }
                    return true;
                },
                'whenClient' => 'function (attribute, value) {
                    if($(attribute.input).val().length == 0){
                        return '. (int)User::checkAdmin() .' != 1;
                    }
                    return true;
                }',
//                'except' => 'default'
            ],
            [['bank_branch'], 'string', 'min' => 1, 'max' => 255/*, 'except' => 'default'*/],
//            [['create_at', 'update_at'], 'safe'],
        ];
    }
    public function is6NumbersOnly($attribute)
    {
        if (!preg_match('/^[0-9]{6}$/', $this->$attribute)) {
            $this->addError($attribute,  Yii::t('app', 'Length code MFO should be 6 digits'));
        }
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'id'),
            'LegalType' => Yii::t('app', 'Property type'),
            'haveFinLicense' => Yii::t('app', 'My company has a financial license'),
            'fin_license' => Yii::t('app', 'Financial license'),
            'customer_type' => Yii::t('app', 'Customer Type'),
            'legalName' => Yii::t('app', 'Legal name'),
            'legalName_en' => Yii::t('app', 'Name in english'),
            'registrationCountryName' => Yii::t('app', 'Registration country name'),
            'identifier' => Yii::t('app', 'Identifier'),
            'isDaughter' => Yii::t('app', 'Your company is a subsidiary of another company?'),
            'parent_identifier' => Yii::t('app', 'Parent Identifier'),
            'mfo' => Yii::t('app', 'MFO'),
            'bank_account' => Yii::t('app', 'Bank account'),
            'bank_branch' => Yii::t('app', 'Bank branch'),
            'payer_pdv' => Yii::t('app', 'Payer of VAT'),
            'ipn_id' => Yii::t('app', 'IPN ID'),
            'moneygetId' => Yii::t('app', 'Розпорядник коштiв'),
            'fio' => Yii::t('app', 'Full name'),
            'userPosition' => Yii::t('app', 'Position'),
            'userDirectionDoc' => Yii::t('app', 'Direction document'),
            'countryName' => Yii::t('app', 'Country name'),
            'region' => Yii::t('app', 'Region'),
            'locality' => Yii::t('app', 'Locality'),
            'streetAddress' => Yii::t('app', 'streetAddress'),
            'postalCode' => Yii::t('app', 'Postal code'),
            'preferLang' => Yii::t('app', 'Вибрана мова'),
            'status' => Yii::t('app', 'Status'),
            'is_seller' => Yii::t('app', 'Реєстрацiя як'),
            'balance' => Yii::t('app', 'Balance'),
            'created_at' => Yii::t('app', 'Створено'),
            'update_at' => Yii::t('app', 'Змiнено'),
            'passport_data' => Yii::t('app', 'Паспортні дані'),
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['changeBankAccount'] = [
            'mfo', 'bank_account', 'bank_branch', 'payer_pdv', 'parent_identifier', 'isDaughter', 'ipn_id',
        ];
        $scenarios['updateCompany'] = array_merge($scenarios['default'], $scenarios['changeBankAccount']);
        $scenarios['updateCompanyAdmin'] = $scenarios['updateCompany'];
        return $scenarios;

    }

    public function afterFind()
    {
        parent::afterFind();
        $this->isDaughter = ($this->parent_identifier) ? 1 : 0;
        $this->haveFinLicense = ($this->fin_license) ? 1 : 0;
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord) {
                $this->create_at = $this->update_at = time();
            } else { //записываем изменения, если уже нажата кнопка "Договор" и договор создан в системе.
//                if (Contracts::findOne(['company_id'=>Yii::$app->user->identity->company_id])) {
                if (Contracts::findOne(['company_id'=>$this->id])) {
                    $values = $this->getDirtyAttributes();
                    if (!empty($values)) {
                        $changesModel = new CompanyChangesHistory();
                        $changes = [];
                        foreach ($values as $k => $value) {
                            if (in_array($k, $this->contractFields)) {
                                $changes[$k] = [$this->getOldAttribute($k) => $this->getAttribute($k)];
                            }
                        }
                        if (count($changes)) {
                            $changesModel->changes = Json::encode($changes);
                            $changesModel->save(false);
                        }

                        $this->update_at = time();
                    }
                }
                if ($this->identifier !== $this->oldAttributes['identifier']){
                    // Отменяем измененния ЄДРПОУ если юзер не админ
                    $this->identifier = (!(int)User::checkAdmin())? $this->oldAttributes['identifier'] : $this->identifier;
                    // Обруляем финансовую авторизацию если изменился ЄДРПОУ и юзер админ
                    $this->status = ((int)User::checkAdmin())? 0 : $this->oldAttributes['status'];
                }

                if ($this->countryName !== $this->oldAttributes['countryName']){
                    // Отменяем измененния поля "Країна реєстрацiї" если юзер не админ
                    $this->countryName = (!(int)User::checkAdmin())? $this->oldAttributes['countryName'] : $this->countryName;
                }

                if ($this->LegalType !== $this->oldAttributes['LegalType']){
                    // Отменяем измененния поля "Форма власностi" если юзер не админ
                    $this->LegalType = (!(int)User::checkAdmin())? $this->oldAttributes['LegalType'] : $this->LegalType;
                }

                // Обнуляем "ІПН" если радио баттон - "Платник ПДВ" отмечн как "Нет"
                $this->ipn_id = ($this->payer_pdv == 0)? NULL : $this->ipn_id;


                // Обнуляем "Код ЄДРПОУ головної компанії" если радио баттон - "Платник ПДВ" отмечн как "Нет"
                $this->parent_identifier = ($this->payer_pdv == 0)? NULL : $this->parent_identifier;

                // Обнуляем "Код ЄДРПОУ головної компанії" если выбран радио баттон - "Моя компанія сплачує ПДВ на підставі особистого свідотства платника ПДВ"
                $this->parent_identifier = ($this->isDaughter == 0)? NULL : $this->parent_identifier;
            }



            // Обнуляем поле "Фінансова ліцензія" если радио баттон - "Моя компанія має фінансову ліцензію" отмечен как "Нет"
            $this->fin_license = ($this->haveFinLicense == 0)? NULL : $this->fin_license;


            return true;
        }
        return false;
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPreferLang0()
    {
        return $this->hasOne(Languages::className(), ['id' => 'preferLang']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOwner()
    {
        return $this->hasOne(User::className(), ['id' => 'owner_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRelationCountryName()
    {
        return $this->hasOne(Countries::className(), ['id' => 'registrationCountryName']);
    }

    public function getCountryName()
    {
        return $this->hasOne(CountrySheme::className(), ['id' => 'countryName']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCountryNameSheme()
    {
        return $this->hasOne(CountrySheme::className(), ['id' => 'countryName']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRegion0()
    {
        return $this->hasOne(Regions::className(), ['id' => 'region']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPersons()
    {
        return $this->hasMany(Persons::className(), ['company_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::className(), ['company_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCompanyType()
    {
        return $this->hasOne(CompanyType::className(), ['id' => 'LegalType']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCompanyCustomerType()
    {
        return $this->hasOne(CompanyCustomerType::className(), ['id' => 'customer_type']);
    }

    public static function checkAllowedCompanyStatusToLogin($companyId)
    {
        $allowedArr = [
            self::STATUS_NEW,
            self::STATUS_ACCEPTED
        ];

        if (in_array(Companies::findOne($companyId)->status, $allowedArr)) {
            return true;
        } else {
            return false;
        }

    }

    public static function checkAllowedCompanyStatusToWork($companyId)
    {
        $allowedArr = [
            self::STATUS_ACCEPTED
        ];

        if (in_array(Companies::findOne($companyId)->status, $allowedArr)) {
            return true;
        } else {
            return false;
        }

    }

    public static function checkCompanyHasFinLicense($companyId)
    {
        $fin = Companies::findOne($companyId);
        if ($fin->fin_license != '') {
            return $fin->fin_license;
        } else {
            return false;
        }

    }

    public static function getCompanyByUserLogin($username)
    {

        $query = Companies::find()
            ->join('LEFT JOIN', 'user', 'companies.id = user.company_id')
            ->where([
                'user.status' => User::STATUS_ACTIVE,
                'user.username' => $username,
            ])
            ->one();

        return $query ? $query : false;

    }

    public static function getAllStatuses()
    {
        return [
            self::STATUS_NEW => Yii::t('app', 'Just register'),
            self::STATUS_ACCEPTED => Yii::t('app', 'Approved'),
            self::STATUS_BLOCKED => Yii::t('app', 'Blocked')
        ];
    }

    public function setStatus($status)
    {
        $this->status = $status;
        $this->update(false);
    }

    public static function getStatus($companyId)
    {
        return self::findOne($companyId)->status;
    }

    public static function getCompanyContractData($companyCurrentData,$dateFrom)
    {
        $companyCurrentData = ArrayHelper::toArray($companyCurrentData);
        $firstChanges = CompanyChangesHistory::find()
            ->where(['company_id'=>Yii::$app->user->identity->company_id])
            ->orderBy([
                'create_at' => SORT_ASC,
            ])
            ->one();
        if($firstChanges){
            $firstChanges = Json::decode($firstChanges->changes);
            foreach ($firstChanges as $k=> $v) {
                foreach ($v as $key=>$value) {
                    $companyCurrentData[$k] = $key;
                }
            }
            return $companyCurrentData;
        }else{
            return $companyCurrentData;
        }
    }

    public static function getSellerCompanyComplaints($tid){
        return Complaints::find()
            ->where(['company_id'=>Yii::$app->user->identity->company_id])
            ->where(['tid'=>$tid])
            ->all();
    }

    public static function getSellerCompanyBids($tid, $mode='model'){
        $res = Bids::find()
            ->where(['company_id' => Yii::$app->user->identity->company_id, 'tid' => $tid])
            ->all();

        if($mode == 'model') {
            return $res;
        }elseif ($mode == 'array'){
            return [$res[0]->bid_id];
        }
    }

    public static function checkCompanyIsSeller(){
        $res = Companies::findOne(['id'=>Yii::$app->user->identity->company_id]);
        return $res ? $res->is_seller : false;
    }

    public static function checkCompanyIsBuyer(){
        $res = Companies::findOne(['id'=>Yii::$app->user->identity->company_id]);
        if($res){
            return $res->is_seller == 0 ? true : false;
        }else{
            return false;
        }
    }

    public static function getCompanyBusinesType(){
        if(Yii::$app->session->get('businesType')){
            return Yii::$app->session->get('businesType');
        }

        if(isset(Yii::$app->user->identity)){
            $res = Companies::findOne(['id'=>Yii::$app->user->identity->company_id])->is_seller;

            if($res && $res == 1){
                Yii::$app->session->set('businesType','seller');
                return 'seller';
            }else{
                Yii::$app->session->set('businesType','buyer');
                return 'buyer';
            }

//            return $res->is_seller == 0 ? true : false;
        }else{
            Yii::$app->session->set('businesType','');
            return '';
        }

    }

    public static function checkCompanyIsTenderOwner($tid){
        $res= Tenders::findOne(['id'=>$tid, 'company_id'=>isset(Yii::$app->user->identity->company_id) ? Yii::$app->user->identity->company_id : 0]);
        return $res ? true : false;
    }
    public static function checkCompanyIsContractOwner($cid){
        $res= Contracting::findOne(['id'=>$cid, 'company_id'=>isset(Yii::$app->user->identity->company_id) ? Yii::$app->user->identity->company_id : 0]);
//        Yii::$app->VarDumper->dump($res, 10, true, true);
        return $res ? true : false;
    }
    public static function checkCompanyIsComplaintOwner($cid){
        $res= Complaints::findOne(['complaint_id'=>$cid, 'company_id'=>isset(Yii::$app->user->identity->company_id) ? Yii::$app->user->identity->company_id : 0]);
        return $res ? true : false;
    }

    public static function checkCompanyIsPlanOwner($pid){
        $res= Plans::findOne(['id'=>$pid, 'company_id'=>isset(Yii::$app->user->identity->company_id) ? Yii::$app->user->identity->company_id : 0]);
        return $res ? true : false;
    }

    public static function checkSellerIsTenderParticipant($tid){
        $res= Bids::findOne(['tid'=>$tid, 'company_id'=>isset(Yii::$app->user->identity->company_id) ? Yii::$app->user->identity->company_id : 0]);
        return $res ? true : false;
    }

    public function getContracts()
    {
        return $this->hasMany(Contracts::className(), ['company_id' => 'id']);
    }

    public function getPayerPdv(){
        return ($this->payer_pdv) ? Yii::t('app', 'Yes') : Yii::t('app', 'No');
    }

    public function getIpnId(){
        return ($this->ipn_id) ? $this->ipn_id : "-";
    }

    public function getMfoId(){
        return ($this->mfo) ? $this->mfo : "-";
    }

    public function getParentIdentifier(){
        return ($this->parent_identifier) ? $this->parent_identifier : "-";
    }

    public function getFinLicense(){
        return ($this->fin_license) ? $this->fin_license : "-";
    }

    public function getBankAccount(){
        return ($this->bank_account) ? $this->bank_account : "-";
    }

    public function getBankBranch(){
        return ($this->bank_branch) ? $this->bank_branch : "-";
    }

    public static function companyActiveControl(){
        if(!Yii::$app->user->isGuest && Companies::checkCompanyIsSeller()) {
            if (!Companies::checkAllowedCompanyStatusToWork(\Yii::$app->user->identity->company_id)) {
                Yii::$app->session->setFlash('errorCompanyActive', Yii::t('app', 'To get started with the site should authorize financially!'));
            }
        }
    }

    public function isNeedCheckPayerVAT()
    {
        if ($this->is_seller == 0) {
            return false;
        }
        if ($this->LegalType == self::_NATURAL_PERSON) {
            return false;
        }
        return true;
    }
}
