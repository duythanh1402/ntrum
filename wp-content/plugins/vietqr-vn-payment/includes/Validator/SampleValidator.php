<?php
/**
 * Banner Management Validator class
 */

namespace VietQR\Validator;

use VietQR\Base\Validator;
use VietQR\Respect\Validation\Validator as v;

class SampleValidator extends Validator {
    public function __construct() {
        $this->add_rule('attachment_id', v::optional(v::arrayType()->each(v::intVal()))
            ->setName('Attachment IDs')
            ->setTemplate('Please upload image for all banners'));

        $this->add_rule('href', v::optional(v::arrayType()->each(v::url()))
            ->setName('Banner Links')
            ->setTemplate('Please enter valid URL for all banners'));
    }
}