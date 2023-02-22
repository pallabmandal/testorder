<?php

namespace App\Exceptions;

class PaymentException extends BaseException {
    public function __construct($message = 'Unable to process payment. Please try another payment provider', $code = 402) {
        parent::__construct($message, $code);
    }
}