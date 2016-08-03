<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

abstract class BaseModel extends Model {
    /**
     * @var $unconventionalForeignKeys
     *
     * An array of foreign key field => database table combinations for which uuid's should be converted to database id's.
     *
     * Note that any fields that match the convention database_table_name_id need not be added, they will automatically be added.
     */
    protected $unconventionalForeignKeys;

    /**
     * @var $conventionalNonForeignKeys
     *
     * An array of field names of fields that should NOT have their values converted from uuid's to database id's, regardless
     * of the fact that the field name follows the convention database_table_name_id.
     *
     * Note that any fields that do not match the aforementioned convention need not be added, as they will automatically not
     * be included in the list of fields to convert.
     */
    protected $conventionalNonForeignKeys;

    public function getUnconventionalForeignKeys()
    {
        return $this->unconventionalForeignKeys;
    }

    public function getConventionalNonForeignKeys()
    {
        return $this->conventionalNonForeignKeys;
    }
}