<?php

if (!empty($view->shipping)) {

    echo $view->shippingRenderer->render(
        'shipping.default', array(
            'order' => $view->order
        )
    );
}

if (!empty($view->shippingFields)) {

    echo $view->shippingFieldRenderer->render(
        'shippingfield.default', array(
            'order' => $view->order
        )
    );
}
