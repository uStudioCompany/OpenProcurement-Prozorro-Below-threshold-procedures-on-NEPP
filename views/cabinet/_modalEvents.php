<?php
use yii\bootstrap\Modal;
use app\models\CabinetEvent;
if  (Yii::$app->params['cabinetNotifications']) {
    if (\app\models\Companies::checkCompanyIsBuyer()) {
        $user = 'buyer';
        $event = new CabinetEvent();
        $events = $event->getUserEvent();
    }
    if (\app\models\Companies::checkCompanyIsSeller()) {
        $user = 'seller';
        $event = new \app\models\CabinetEventSeller();
        $events = $event->getSellerEvents();
    }
}
if (isset($events) && !is_null($events)) {
        Modal::begin([
            'id' => 'events',
            'header' => '<h3>' . Yii::t('app', 'Events') . '</h3>',
            'toggleButton' => [
                'label' => Yii::t('app', 'Events'),
                'class' => 'btn btn-success',
                'id' => 'modalEvents',
            ],
            'closeButton' => [
                'label' => '<i class="glyphicon glyphicon-remove"></i>',
                'class' => 'btn btn-danger pull-right'
            ],
            'size' => 'modal-lg',
            'footer' => ($user == 'seller' ?  ('<button id="readAll" class="btn btn-primary">' . Yii::t('app', 'Read all') . '</button>') : ''),
        ]);
    echo $this->render('_events', ['data' => $events, 'user' => $user]);
    Modal::end();
}
?>