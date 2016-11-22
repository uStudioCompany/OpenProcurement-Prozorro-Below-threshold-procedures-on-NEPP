<?php

return [

    'site_url'=>'https://brizol.net',
    'adminEmail' => 'admin@example.com',
    'getContractingToken'=>'- 5 minutes',
    'sendNotifications'=>true,
    'isOn.rabbitMQ' => false,

    'cabinetNotifications' => true,
    'DS' => false,
    'deleteFile' => false,
    'logging' => [
        'document.log' => true,
        'web.log' => true,
        'cron.log' => true
    ],
    'publicTenderPoint'=>'https://public.api.openprocurement.org/api/',
    'publicTenderVersion'=>'0',
    'urlUnit' => 'http://standards.openprocurement.org/unit_codes/recommended/',
    'urlDkpp' => 'http://standards.openprocurement.org/classifiers/dkpp/', // [uk]ДК 016:2010 - Державний класифікатор продукції та послуг
    'urlKekv' => 'http://standards.openprocurement.org/classifiers/kekv/', // [uk]Коди економічної класифікації видатків бюджету
    'urlDk015' => 'http://localhost/marketplace/web/015.json', // [uk]ДК015:97 -Класифікація видів науково-технічної діяльності
    'urlDk003' => 'http://localhost/marketplace/web/003.json', // [uk]ДК003 -Класифікатор професій
    'urlDk018' => 'http://localhost/marketplace/web/018.json', // [uk]ДК018:2000 - Державний класифікатор будівель та споруд
    'urlCpv'  => 'http://standards.openprocurement.org/classifiers/cpv/',//ДК 021:2015 - національний класифікатор України, “Єдиний закупівельний словник" (UA) (Схема CPV, primaryClassification)
    'urlMap'  => 'http://standards.openprocurement.org/mapping/', // [cpv2dkpp,dkpp2cpv]
    'urlDocType' => 'http://standards.openprocurement.org/document_types/recommended/',
    'subDocType' => ['tender','award','contract'],
    //'urlDocTypTender'   => 'http://standards.openprocurement.org/document_types/recommended/tender_',
    //'urlDocTypAward'    => 'http://standards.openprocurement.org/document_types/recommended/award_',
    //'urlDocTypContract' => 'http://standards.openprocurement.org/document_types/recommended/contract_',
    'docTaskCount'=>50000,
    'document_types' => ['tender','lot','item',],

    'upload_dir' => dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR .'web'. DIRECTORY_SEPARATOR .'uploads'. DIRECTORY_SEPARATOR,
    'tender_update_interval' => 30,
    'mail_sender'=>'test@byustudio.in.ua',
    'mail_sender_name'=>'Prozorroy',
    'mail_sender_full'=>['test@byustudio.in.ua'=>'Prozorroy'],
    'user.passwordResetTokenExpire' => 3600,
    'cache_time'=>3600,
//    'schemeEDR'=>[
//        'UA-EDR' =>'Україна (UA-EDR)'
//    ],
    'column_lang_pref'=>['uk-UA'=>'','en-US'=>'_en','ru-RU'=>'_ru'],

    'allowed.tender.update.lots'=>['active.enquiries','draft'],
    'allowed.tender.update.status'=>['active','active.enquiries','active.tendering','draft','draft.stage2'],
    'allowed.tender.awards.status'=>['active','active.qualification','active.awarded'],
    'allowed.tender.cancelation.status'=>['active.enquiries','active.tendering','active.pre-qualification','active.pre-qualification.stand-still','active.auction', 'active.qualification','active.awarded'],
    'allowed.tender.limitedavards.status'=>['active'],
    'allowed.tender.euprocedure.status'=>['active','active.pre-qualification','active.pre-qualification.stand-still'],

    'allowed.tender.question.answer.status'=>['active.enquiries','active.tendering'],

    'actual.status.tender' => ['active.tendering', 'active.enquiries'],
    'archive.status.tender' => ['unsuccessful', 'complete', 'cancelled'],
    'current.status.tender' => ['active.pre-qualification', 'active.auction', 'active.qualification', 'active.awarded', 'active.pre-qualification.stand-still'],

    'tender.method'=>['open','limited','selective'],
    'tender.method.type'=>['belowThreshold','reporting','negotiation','negotiation.quick','aboveThresholdUA','aboveThresholdUA.defense','aboveThresholdEU','competitiveDialogueUA','competitiveDialogueEU','competitiveDialogueUA.stage2','competitiveDialogueEU.stage2'],
    '2stage.tender' => ['selective_competitiveDialogueUA.stage2', 'selective_competitiveDialogueEU.stage2'],
    'two_phase_commit_count'=>5,
    'uploadFiles' => [
        'disabledExt' => [
            'php'
        ],
    ],

    // Активация тендера
    // Переговорка, = active
    // Допороги,  = active.enquiries
    // Европейская, ук, Военка, Диалог = active.tendering

    'active_statuses' => [
        'open_belowThreshold'=>'active.enquiries',
        'limited_reporting'=>'active',
        'limited_negotiation'=>'active',
        'limited_negotiation.quick'=>'active',
        'open_aboveThresholdUA'=>'active.tendering',
        'open_aboveThresholdUA.defense'=>'active.tendering',
        'open_aboveThresholdEU'=>'active.tendering',
        'open_competitiveDialogueUA'=>'active.tendering',
        'open_competitiveDialogueEU'=>'active.tendering',
    ],

    'DK_LIBS'=>[
//        '000' => 'Не вибрано',
        'ДКПП_dkpp' =>'ДК 016:2010',
        'ДК003_dk003' =>'ДК 003:2010',
        'ДК015_dk015' =>'ДК 015:97',
        'ДК018_dk018' =>'ДК 018:2000',
    ],
    'Holidays'=> [
        "2015-05-01",
        "2015-05-04",
        "2015-05-11",
        "2015-06-01",
        "2015-06-29",
        "2015-08-24",
        "2015-10-14",
        "2016-01-01",
        "2016-01-07",
        "2016-01-08",
        "2016-03-07",
        "2016-03-08",
        "2016-05-02",
        "2016-05-03",
        "2016-05-09",
        "2016-06-20",
        "2016-06-27",
        "2016-06-28",
        "2016-08-24",
        "2016-10-14"
//            '*-12-25', '*-01-01', '2013-12-23'
    ],
    //конверты документов
    'technicalSpecifications'=>'documents',
    'qualificationDocuments'=>'documents',
    'commercialProposal'=>'financial_documents',
    'billOfQuantity'=>'financial_documents',
    'eligibilityDocuments'=>'eligibility_documents',


];
