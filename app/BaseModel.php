<?php

namespace App;

use App\Traits\UuidModel;
use Illuminate\Database\Eloquent\Model;

abstract class BaseModel extends Model {
    use UuidModel;

    /**
     * @var $PARENT_MODELS
     *
     * An array of mappings for parent resources. Used to define which entities this entity can be accessed from, and
     * how to get from those parent entities to this entity. See usage in RestResourceController::getAllForParent().
     *
     * Format:
     * [
     *      [parent_resource] => [
     *          'class' => [Model class],
     *          'function' => [Relationship function],
     *      ],
     * ]
     *
     * Where [parent_resource] is the resource as it would appear in a route (eg 'clients' for the Client model),
     * [Model class] is the fully qualified class name of the parent model class, and [Relationship function] is the
     * function used to get child records from this entity via the parent entity.
     *
     * E.g. If a user belongsToMany clients, the $PARENT_MODELS property in the user model would be:
     *
     * [
     *      'clients' => [
     *          'class' => 'App\Client',
     *          'function' => 'users',
     *      ],
     * ]
     */
    public static $PARENT_MODELS = [];

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