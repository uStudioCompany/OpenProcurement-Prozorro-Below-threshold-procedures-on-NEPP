<?php
namespace app\components;

use Yii;
use yii\web\NotFoundHttpException;
use app\models\Companies;
use app\models\CashFlow;
use app\models\Tenders;

use app\components\HTender;
use app\components\SimpleTenderConvertIn;

/**
        // проверка доступности участия
        Yii::$app->finance->isMembershipAvailable( $tender_or_id, $lots )
        // потрпатить деньги с баланса
        Yii::$app->finance->spendSumm( $tender_or_id, $lots ) 
        // посчитать стоимость участия
        Yii::$app->finance->calculateTenderMembershipPrice( $tender_or_id, $lots, $splitLotPrices )
        // баланс компании 
        Yii::$app->finance->refreshBalance( $company_or_id, $justShow )
        // возврат
        Yii::$app->finance->refund( $tender_or_id, $lot_id )

        // Кусок кода - пример использования из modules/sellers/controllers/TenderController
        ... 
        $tenders = Tenders::getModelById($id);
        $tender = HTender::load(SimpleTenderConvertIn::getSimpleTender($id));
        $post = Yii::$app->request->post();
        
                $lots = []; 
                if(!empty($tender->lots)) 
                    array_push( $lots, $tender->lots[0]->id );
                echo Yii::$app->finance->refreshBalance(false, true);
                // print_r(Yii::$app->finance->isMembershipAvailable( $tender, $lots ));
                // print_r(Yii::$app->finance->calculateTenderMembershipPrice( $id, $lots, true ));
                // print_r(Yii::$app->finance->spendSumm( $tender, $lots ));
                // print_r(Yii::$app->finance->refund( $tender, $lots[0] )); 
                exit;

        $bidModel = Bids::getModel($id);
        ...

*/

/**
 * 
 */
class finance extends \Yii\base\Component
{
    /**
     * @var string
     */
    // public $fieldName;

    /**
     * Constructor.
     */
    // public function __construct()
    // {
    // }

    /**
     * Метод определяет может ли текущая авторизированная компания участвовать в тендере - достаточно ли бабок на балансе
     * @param $tender_or_id
     * @param $lots
     * @return array
     * @throws \Exception
     */
    public function isMembershipAvailable( $tender_or_id, $lots )
    {
        if( empty($tender_or_id) )
            throw new \Exception("expecting integer tender_id param", 1);

        $company_id = \Yii::$app->user->identity->company_id;
        $company = $this->getCompanyById( $company_id );
        // print_r($company); exit;
        return $this->isMembershipAvailableFor( $company, $tender_or_id, $lots );
    }

    /**
     * Метод определяет достаточно ли на переданном балансе бабок для участия в тендере
     * @param $company_or_id
     * @param $tender_or_id
     * @param $lots
     * @return array
     * @throws \Exception
     */
    protected function isMembershipAvailableFor( $company_or_id, $tender_or_id, $lots )
    {
        $result = array(
            'available' => false,
        );

        $company = $this->normalize_company($company_or_id);

        if( $company->status != 1 ) {
            $result['code'] = 'COMPANY_STATUS_FAIL';
            $result['error'] = 'Company not approved';
            return $result;
        }

        $tender = $this->normalize_tender($tender_or_id);
        if( empty($tender) ) {
            $result['code'] = 'TENDER_NOT_FOUND';
            $result['error'] = 'Tender not found';
            return $result;
        }
        
        $expected_price = $this->calculateTenderMembershipPrice( $tender, $lots );

        //echo '<pre>'; print_r($company->id); DIE();

        if( $company->balance < $expected_price ) {
            $result['code'] = 'NOT_ENOUGH_BALANCE';
            $result['error'] = ''. $expected_price; /** @TODO: Добавить перевод */
            return $result;
        }

        $result['available'] = true;

        return $result;
    }

    /**
     * Метод умеет вычислять стоимость участия в тендере
     * @param $tender_or_id
     * @param $lots
     * @param bool|false $splitLotPrices
     * @return array|int
     * @throws \Exception
     */
    public function calculateTenderMembershipPrice( $tender_or_id, $lots, $splitLotPrices=false )
    {
        // нормализация параметров
        // if(!empty($splitLotPrices) && empty($lots))
        //     $splitLotPrices = false;

        $tender = $this->normalize_tender($tender_or_id);
        
        $prices = [];
        // print_r($tender->lots); exit;
        if( empty($tender->lots) ){
            if( !empty($lots) )
                throw new \Exception("Tender without lots, but its passed", 1);

            $price = $this->_calcPrice( $tender->value->amount );
        } else {
            if( empty($lots) )
                throw new \Exception("Expected lots array", 1);

            $price = 0;
            foreach( $tender->lots as $lot ){
                
                if( !in_array($lot->id, $lots) )
                    continue;
                
                $lot_price = $this->_calcPrice( $lot->value->amount );
                // echo $lot->id,' ',$lot->value->amount,' ', $lot_price; exit;
                if( !empty($splitLotPrices) )
                    array_push($prices, array(
                        'lot_id' => $lot->id,
                        'price' => $lot_price
                    ));
                $price += $lot_price;
            }
        }

        if( empty($splitLotPrices) )
            return $price;
        
        return array(
            'prices'=>$prices,
            'total_price'=>$price
        );
    }


    private function _calcPrice( $summ )
    {
        $price_table = [
            20000 => 17,
            50000 => 119,
            200000 => 340,
            1000000 => 510,
            INF => 1700
        ];
        $price = 0;
        foreach( $price_table as $level => $level_price ){
            if(!$price)
                $price = $level_price;

            if( $summ >= $level )
                $price = $level_price;
            else 
                break;
            // echo '<br>',$summ,' ',$level.' '.$price;
        }
        return $price;
    }


    /**
     * Потраьтить деньги текущей компании на участие в тенедере с переданным id
     * @param $tender_or_id
     * @param $lots
     * @return array
     * @throws \Exception
     */
    public function spendSumm( $tender_or_id, $lots )
    {
        $company_id = \Yii::$app->user->identity->company_id;
        $company = $this->getCompanyById( $company_id );
        
        return $this->spendSummFrom( $company, $tender_or_id, $lots );
    }

    /**
     * Потраьтить деньги c переданного баланса на участие в тенедере с переданным id
     * @param $company_or_id
     * @param $tender_or_id
     * @param $lots
     * @param int $reason_id
     * @return array
     * @throws \Exception
     */
    protected function spendSummFrom( $company_or_id, $tender_or_id, $lots, $reason_id=1 )
    {
        $result = array(
            'provided' => false
        );

        // проверить наличие денах 
        $company = $this->normalize_company( $company_or_id );
        $tender = $this->normalize_tender($tender_or_id);
        $tenderProj = Tenders::findOne( ['tender_id'=>$tender->id] );

        $price = $this->calculateTenderMembershipPrice( $tender, $lots, true ); // true - split lot prices

        if ( $company->balance < $price['total_price'] ) {
            $result['error'] = 'Low balance';
            $result['code'] = 'LOW_BALANCE';
            return $result;
        }
            
        // внести записи о списании
        $ins = array();

        $criteria = ['tender_id'=>$tenderProj->id];
        if( !empty($lots) ){
            $criteria['lot_id']=[];
            foreach( $lots as $lot){
                $criteria['lot_id'][]=$lot;
            }
        }
        $exist = CashFlow::find()->where($criteria)->orderBy('id DESC')->all();
        $existArr = [];

        if( empty($lots) ){
            array_push($ins, array(
                'price'=>$price['total_price']
            ));
        } else {
            foreach( $price['prices'] as $lot ){

                // Проверить - есть ли уже списание по этому тендеру
                if(!empty($exist)){
                    foreach($exist as $cf){
                        if(
                            $cf->tender_id == $tenderProj->id && 
                            ( 
                                empty($lots) || in_array($cf->lot_id,$lots) 
                            )
                        ){
                            if( $cf->way == CashFlow::CASHFLOW_WAY_OUT )
                                $existArr[] = $cf->id;
                            break;
                        } 
                    }
                }

                array_push($ins, $lot);                
            }
        }
        
        if(!empty($existArr)){
            return [
                'provided'=>false,
                'error'=>"Cashflow Double usage: ".print_r($existArr,1)
            ];
        }

        for( $i=0; $i< count($ins); $i++ ){
            $cashFlow = new CashFlow();
            $cashFlow->way = CashFlow::CASHFLOW_WAY_OUT;
            $cashFlow->balance_id = $company->id;
            $cashFlow->tender_id = $tenderProj->id;
            $cashFlow->created_at = time();
            $cashFlow->payed_at = time();
            $cashFlow->cash_flow_reason_id = $reason_id;
            if( $ins[$i]['lot_id'] )
                $cashFlow->lot_id = $ins[$i]['lot_id'];
            $cashFlow->amount = $ins[$i]['price'];
                
            if(!$cashFlow->save()){
                throw new \Exception("Failed save cashFlow: ". print_r($cashFlow->errors,1), 1);
            }
        }

        // пересчет баланса
        $this->refreshBalance( $company );

        $result['provided'] = true;
        return $result;
    }

    public function refundMus( $tender_or_id, $lots, $company_id=null)
    {
        /** Если выполнение в консоли */
        if (!(is_a(Yii::$app, 'yii\console\Application') && $company_id )) {
            $company_id = \Yii::$app->user->identity->company_id;
        }

        if (is_array($lots)) {
            foreach ($lots AS $lot) {
                $rez = $this->refundFor($company_id, $tender_or_id, $lot);
            }
        } else {
            $rez = $this->refundFor($company_id, $tender_or_id, $lots);
        }

        return $rez;

    }

    public function refund( $tender_or_id, $lot_id, $company_id=null)
    {
        /** Если выполнение в консоли */
        if (is_a(Yii::$app, 'yii\console\Application') && $company_id ) {
            return $this->refundFor($company_id, $tender_or_id, $lot_id);
        } else {
            return $this->refundFor(\Yii::$app->user->identity->company_id, $tender_or_id, $lot_id);
        }
    }

    protected function refundFor( $company_or_id, $tender_or_id, $lot_id, $reason_id=2  )
    {
        $result = array('returned'=>false);

        $company = $this->normalize_company( $company_or_id );
        $tender = $this->normalize_tender( $tender_or_id );
        $tenderProj = Tenders::findOne( ['tender_id'=>$tender->id] );

        if(empty($tender->lots) && !empty($lot_id) )
            throw new \Exception("Passed lot id but tender without lots", 1);
            
        if(!empty($tender->lots) && empty($lot_id) )
            throw new \Exception("Expected lot id", 1);
        
        $criteria = ['balance_id'=>$company->id, 'tender_id'=>$tenderProj->id];
        if( $lot_id )
            $criteria['lot_id'] = $lot_id;
        
        $cashFlow = CashFlow::find()->where($criteria)->orderBy('id DESC')->one();

        if(!$cashFlow)
            return [ 'error'=>"CashFlow not found" ];

        if( $cashFlow->way == CashFlow::CASHFLOW_WAY_IN )
            return [ 'error'=>"DOUBLE refund prohibited" ];

        $refundCashFlow = new CashFlow();
        $refundCashFlow->way = CashFlow::CASHFLOW_WAY_IN;
        $refundCashFlow->balance_id = $cashFlow->balance_id;
        if(!empty($reason_id))
            $refundCashFlow->cash_flow_reason_id = $reason_id;
        $refundCashFlow->tender_id = $cashFlow->tender_id;
        $refundCashFlow->lot_id = $cashFlow->lot_id;
        $refundCashFlow->created_at = time();
        $refundCashFlow->payed_at = time();
        $refundCashFlow->amount = $cashFlow->amount;

        if(!$refundCashFlow->save()){
            throw new \Exception("Failed save refund: ".print_r( $refundCashFlow->errors,1 ), 1);
        }

        // пересчет баланса -------------------------------------------------------------------------------------------- Нужно? забыли? или спецом?
        $this->refreshBalance( $company );

        return [ 'returned'=>true ];
    }

    public function refreshBalance( $company_or_id, $justShow=false )
    {

        if(empty($company_or_id)) {
            if (!(is_a(Yii::$app, 'yii\console\Application') && $company_or_id )) {
                $company_or_id = \Yii::$app->user->identity->company_id;
            }
        }
        if (isset($company_or_id)) {
            $company = $this->normalize_company($company_or_id);

            $cashFlow = CashFlow::find()->where(['balance_id' => $company->id])->orderBy('id')->all();

            $balance = 0;
            for ($i = 0, $cnt = count($cashFlow); $i < $cnt; $i++) {
                if ($cashFlow[$i]->way == CashFlow::CASHFLOW_WAY_IN) {
                    $balance += $cashFlow[$i]->amount;
                    if ($company->status != Companies::STATUS_ACCEPTED) {
                        if ($balance >= 10) {
                            $balance -= 10;
                            $company->status = Companies::STATUS_ACCEPTED;
                        }
                    }
                } else {
                    $balance -= $cashFlow[$i]->amount;
                }
            }

            if ($justShow)
                return $balance;

            $company->balance = $balance;
            if (!$company->save()) {
                throw new \Exception("Failed save company balance: " . print_r($company->errors, 1), 1);
            }

            return $balance;
        }
    }

    protected function getCompanyById( $id )
    {
        return Companies::findOne( $id );
    }

    protected function getTenderById( $id )
    {
        $tender = HTender::load(SimpleTenderConvertIn::getSimpleTender($id));
        return $tender;
    }

    private function normalize_tender($tender_or_id){
        
        if(
            !is_numeric($tender_or_id) && get_class($tender_or_id) == 'app\models\tenderModels\Tender' 
        ){
            $tender = $tender_or_id;
        } else {
            $tender = $this->getTenderById( $tender_or_id ); 
        }
        return $tender;
    }

    private function normalize_company($company_or_id){
        if( is_numeric($company_or_id) ) {
            $company = $this->getCompanyById( $company_or_id ); 
        } else {
            $company = $company_or_id;
        }
        
        return $company;
    }

}
