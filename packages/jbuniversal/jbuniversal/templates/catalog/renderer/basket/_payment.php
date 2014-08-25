<?php

if (!empty($view->payment)) {

    echo $view->paymentRenderer->render('payment.default', array(
        'order' => $view->order
    ));
}
