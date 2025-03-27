<?php
/**
 * VietQR Bank Account Query
 *
 * @package VietQR
 */

namespace VietQR\Query;

use VietQR\Base\BaseQuery;

class VrBankAccount extends BaseQuery
{
    protected static $table_name = 'vr_bank_accounts';
    protected static $primary_key = 'id';

    protected static $field_mapping = [
        'id' => 'id',
        'bank_code' => 'bank_code',
        'bank_name' => 'bank_name',
        'account_number' => 'account_number',
        'account_name' => 'account_name',
        'branch' => 'branch',
        'is_selected' => 'is_selected',
        'created_at' => 'created_at',
        'updated_at' => 'updated_at',
    ];
}