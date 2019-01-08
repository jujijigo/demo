<?php
require './captcha.php';
$captcha = new Captcha();
$captcha->string()->show();