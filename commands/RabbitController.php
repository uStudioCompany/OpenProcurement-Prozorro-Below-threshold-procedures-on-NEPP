<?php


namespace app\commands;


use PhpAmqpLib\Connection\AMQPStreamConnection;
use yii;
use yii\console\Controller;



/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class RabbitController extends Controller
{

    public function actionMailQueue()
    {
        $connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
        $channel = $connection->channel();

        $channel->queue_declare('MailQueue', false, false, false, false);

        echo ' [*] Waiting for messages. To exit press CTRL+C', "\n";

        $callback = function($msg) {
            $new = yii\helpers\Json::decode($msg->body, true);

            echo $new['to'];
            $sended =  Yii::$app->mailer->compose()
                ->setFrom(Yii::$app->params['mail_sender_full'])
                ->setTo($new['to'])
                ->setSubject($new['subject'])
                ->setHtmlBody($new['body'])
                ->send();

            if($sended){
                echo ' - sended.';
            }
        };

        $channel->basic_consume('MailQueue', '', false, true, false, false, $callback);

        while(count($channel->callbacks)) {
            $channel->wait();
        }
    }

}