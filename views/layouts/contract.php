<?php $this->beginPage() ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <title msg="user agreement"></title>
</head>
<body style="font-family: timesnewroman; text-align: justify">
<?php $this->beginBody() ?>
<div choice="ug_buyers,ug_sellers,ug_admin" var="agreement_admin">
    <div class="content">

        <?= $content ?>

        <?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
<style type="text/css">
    * {
        padding: 0;
        margin: 0;
    }

    body.example:before {
        content: 'ПРОЕКТ ДОГОВОРУ!';
        text-transform: uppercase;
        white-space: nowrap;
        position: fixed;
        top: 60%%;
        left: 20%%;
        right: 0;
        text-align: center;
        color: red;
        font-size: 70px;
        display: block;
        opacity: 0.3;
        transform: rotate(-60);
    }

    @page {
        margin: 80px 75px 80px 125px !important;
    }

    .content {
        text-align: justify;
        font-size: 16px;
        line-height: 1.2em;
    }

    .newPage {
        page-break-before: always;
    }

    .content p:before {
        content: ' ';
        display: inline-block;
        width: 40px;
        min-height: 1px;
    }

    .content p.full:before {
        display: none;
    }

    .c {
        text-align: center;
    }

    .tr {
        text-align: right;
    }

    .i {
        font-style: italic;
    }

    .b {
        font-weight: bold;
    }

    .u {
        text-decoration: underline;
    }

    .r {
        outline: 1px solid red;
    }

    .h1 {
        font-size: 20px;
        line-height: 1em;
    }

    .h2 {
        font-size: 18px;
        line-height: 1em;
    }

    .p {

    }

    .small {
        font-size: 10px;
    }

    .upper {
        text-transform: uppercase;
    }

    ul {
        padding-left: 50px !important;
        display: block;
    }

    .notend:after {
        content: ' ';
        display: inline-block;
        position: relative;
        top: -1px;
        height: 1px;
        width: 100%%;
    }

    .ml10 {
        margin-left: 10px;
    }

    .ml20 {
        margin-left: 20px;
    }

    .ml30 {
        margin-left: 30px;
    }

    .ml35p {
        margin-left: 27%%;
    }

    .ml40p {
        margin-left: 40%%;
    }

    table {
        width: 100%%;
    }

    table td {
        vertical-align: top;
    }

    /*.mdash {
        display:inline-block;
        width:20px;
        text-align: center;
    }*/
</style>




