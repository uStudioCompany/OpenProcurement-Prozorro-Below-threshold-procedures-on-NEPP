<?php

/* @var $this yii\web\View */

use yii\helpers\Url;

$this->title = 'Кабинет Закупщика';
?>
<div class="site-index">
	<div class="jumbotron land-jumbo1">
	    <div class="container">
	        <div><img class="img-responsive center-block" src="<?=Url::to('@web/img/prozorro-logo.png') ?>" alt="PROZORRO LOGO"></div>
	        <h1><?=$this->title ?></h1>
	    </div>
	</div>
	<section>
		<div class="head-descr">
			<h3><b>Lorem ipsum dolor sit amet elit.</b></h3>
			<p>
				Lorem ipsum dolor sit amet, consectetur adipisicing elit. Aliquid, suscipit, rerum quos facilis repellat architecto commodi officia atque nemo facere eum non illo voluptatem quae delectus odit vel itaque amet.
			</p>
		</div>		
	</section>

	<div class="jumbotron land-jumbo2">
	    <div class="container"></div>
	</div>
	<section>
		<div class="head-descr">
			<h3><b>Lorem ipsum dolor sit amet elit.</b></h3>
			<p>
				Lorem ipsum dolor sit amet, consectetur adipisicing elit. Aliquid, suscipit, rerum quos facilis repellat architecto commodi officia atque nemo facere eum non illo voluptatem quae delectus odit vel itaque amet.
			</p>
			<a class="btn btn-default" href="<?=Url::to('/register') ?>">Зарегистрироваться</a>
		</div>
	</section>
</div>
