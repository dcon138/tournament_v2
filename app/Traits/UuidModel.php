<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;
use Rhumsaa\Uuid\Uuid;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\BaseModel;
use Illuminate\Database\QueryException;

trait UuidModel
{

    public static $UUID_REGEX = '^([0-9]+|[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12})$';

    /**
     * @var string $FOREIGN_KEY_REGEX
     *
     * Defines the regular expression that will be applied to field names to decide if the field is a foreign key field.
     * If the field is defined as a foreign key field, it will have it's value auto-mapped from uuid to database id.
     */
    protected static $FOREIGN_KEY_REGEX = '/.*_id$/';

    /**
     * Adapted From: http://humaan.com/using-uuids-with-eloquent-in-laravel/
     *
     * Binds creating/saving events to create UUIDs (and also prevent them from being overwritten).
     *
     * Custom functionality added to switch out uuid's and database id's for saving / returning in the response.
     *
     * @return void
     */
    public static function bootUuidModel()
    {
        static::creating(function ($model) {
            // Don't let people provide their own UUIDs, we will generate a proper one.
            $model->uuid = Uuid::uuid4()->toString();
        });

        static::saving(function ($model) {
            // What's that, trying to change the UUID huh?  Nope, not gonna happen.
            $original_uuid = $model->getOriginal('uuid');

            if ($original_uuid !== $model->uuid) {
                $model->uuid = $original_uuid;
            }

            //if the model being saved is a child of our base model, then let's substitute any foreign key values.
            //at this stage, they will be uuid's. For saving, we need them to be database id's.
            if (!static::convertUuidsToIds($model)) {
                //if we get to this point in execution, a programming error has occurred. prevent save and return false.
                return false;
            }
        });

        static::saved(function ($model) {
            //if the model that has been saved is a child of our base model, then let's substitute any foreign key values.
            //at this stage, they will be database id's. For returning to the front-end, we need them to be uuid's.
            if (!static::convertIdsToUuids($model)) {
                //if we get to this point in execution, a programming error has occurred. prevent save and return false.
                return false;
            }
        });
    }

    /**
     * Wrapper function which utilizes switchIdsAndUuids() to switch out database id's for uuid's for all foreign keys
     * on an instantiated model.
     *
     * @param $model - the model instance to operate on
     * @return bool - true on success, false otherwise.
     */
    protected static function convertIdsToUuids($model)
    {
        return self::switchIdsAndUuids($model, 'getUuidFromId');
    }

    /**
     * Wrapper function which utilizes switchIdsAndUuids() to switch out uuid's for database id's for all foreign keys
     * on an instantiated model.
     *
     * @param $model - the model instance to operate on
     * @return bool - true on success, false otherwise.
     */
    protected static function convertUuidsToIds($model)
    {
        return self::switchIdsAndUuids($model, 'getIdFromUuid');
    }

    /**
     * Function to process the fields for a model instance and switch out any foreign keys, swapping either id's for uuid's
     * or uuid's for database id's depending on the value provided as the $function parameter.
     *
     * Takes into account the $unconventionalForeignKeys and $conventionalNonForeignKeys from the BaseModel class.
     *
     * @param $model - the model instance to operate on. Should be a child of \App\BaseModel.
     * @param $function - the function to use to swap the values. At this stage, either getIdFromUuid or getUuidFromId.
     * @return bool - true on success, false otherwise.
     */
    protected static function switchIdsAndUuids($model, $function)
    {
        $success = true;
        if (is_subclass_of($model, BaseModel::class)) {
            $modelAttributes = $model->attributesToArray();
            $unconventionalForeignKeys = $model->getUnconventionalForeignKeys();
            $conventionalNonForeignKeys = $model->getConventionalNonForeignKeys();

            //loop through each attribute to be saved, and determine if it is a foreign key
            foreach ($modelAttributes as $field => $value) {
                if (empty($value)) {
                    //if no value in this field, there is no point converting it.
                    continue;
                }

                //if the field is NOT in the ignore list
                if (empty($conventionalNonForeignKeys) || !in_array($field, $conventionalNonForeignKeys)) {

                    //if many-to-many relationship data is included, column name will be prefixed by 'pivot_', so remove it temporarily.
                    $originalField = $field;
                    $field = preg_replace('/^pivot_/', '', $field);
                    if (!empty($unconventionalForeignKeys) && array_key_exists($field, $unconventionalForeignKeys)) {
                        //if the field is in the list of foreign keys that don't match convention
                        $table = $unconventionalForeignKeys[$field];
                    } else if (preg_match(self::$FOREIGN_KEY_REGEX, $field)) {
                        //otherwise if the field matches the foreign key field convention regex
                        $table = str_plural(substr($field, 0, -3));
                    } else {
                        //if we get here, nothing needs to be done for this field so skip to the next one.
                        continue;
                    }

                    //determine the database id from the uuid, and update the value of the field to be saved.
                    try {
                        $id = $model->{$function}($table, $value);
                    } catch (QueryException $e) {
                        $success = false;
                    }
                    if ($id === false) {
                        $success = false;
                    } else {
                        $model->{$originalField} = $id;
                    }
                }
            }
        }
        return $success;
    }

    /**
     * Wrapper function that utilizes getFieldFromField() to get the uuid of a row from $table where the id is $id
     *
     * @param $table
     * @param $id
     * @return string
     */
    protected static function getUuidFromId($table, $id)
    {
        return self::getFieldFromField($table, 'uuid', 'id', $id);
    }

    /**
     * Wrapper function that utilizes getFieldFromField() to get the id of a row from $table where the uuid is $uuid
     *
     * @param $table
     * @param $uuid
     * @return string
     */
    protected static function getIdFromUuid($table, $uuid)
    {
        return self::getFieldFromField($table, 'id', 'uuid', $uuid);
    }

    /**
     * Simple function designed to get one field from one row of a table where another field has a certain value.
     *
     * @param $table - the table to retrieve from
     * @param $getField - the field whose value to return
     * @param $fromField - the field on which the where clause will be applied
     * @param $fromFieldValue - the value that $fromField must have in the where clause
     * @return string - the value of $getField
     * @throws ModelNotFoundException - if the row is not found, or the field in that row is empty or not set.
     */
    protected static function getFieldFromField($table, $getField, $fromField, $fromFieldValue)
    {
        $result = DB::select("SELECT " . $getField . " FROM " . $table . " WHERE " . $fromField . " = ?", [$fromFieldValue]);

        if (empty($result[0]->{$getField})) {
            throw new ModelNotFoundException('Record not found in table ' . $table . ' with ' . $fromField . ' ' . $fromFieldValue);
        }

        return $result[0]->{$getField};
    }

    /**
     * SOURCE: http://humaan.com/using-uuids-with-eloquent-in-laravel/
     *
     * Scope a query to only include models matching the supplied UUID.
     * Returns the model by default, or supply a second flag `false` to get the Query Builder instance.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     *
     * @param  \Illuminate\Database\Schema\Builder $query The Query Builder instance.
     * @param  string                              $uuid  The UUID of the model.
     * @param  bool|true                           $first Returns the model by default, or set to `false` to chain for query builder.
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Builder
     */
    public function scopeUuid($query, $uuid, $first = true)
    {
        if (!is_string($uuid) || (preg_match('/' . self::$UUID_REGEX . '/', $uuid) !== 1)) {
            throw (new ModelNotFoundException)->setModel(get_class($this));
        }

        $search = $query->where('uuid', $uuid);

        return $first ? $search->firstOrFail() : $search;
    }

    /**
     * SOURCE: http://humaan.com/using-uuids-with-eloquent-in-laravel/
     *
     * Scope a query to only include models matching the supplied ID or UUID.
     * Returns the model by default, or supply a second flag `false` to get the Query Builder instance.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     *
     * @param  \Illuminate\Database\Schema\Builder $query The Query Builder instance.
     * @param  string                              $id_or_uuid  The id or UUID of the model.
     * @param  bool|true                           $first Returns the model by default, or set to `false` to chain for query builder.
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Builder
     */
    public function scopeIdOrUuId($query, $id_or_uuid, $first = true)
    {
        if (!is_string($id_or_uuid) && !is_numeric($id_or_uuid)) {
            throw (new ModelNotFoundException)->setModel(get_class($this));
        }

        if (preg_match('/' . self::$UUID_REGEX . '/', $id_or_uuid) !== 1) {
            throw (new ModelNotFoundException)->setModel(get_class($this));
        }

        $search = $query->where(function ($query) use ($id_or_uuid) {
            $query->where('id', $id_or_uuid)
                ->orWhere('uuid', $id_or_uuid);
        });

        return $first ? $search->firstOrFail() : $search;
    }

    /**
     * Create a collection of models from plain arrays.
     *
     * Overridden to also convert database id's to uuid's for foreign keys upon model hydration.
     *
     * @param  array  $items
     * @param  string|null  $connection
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function hydrate(array $items, $connection = null)
    {
        $instance = (new static)->setConnection($connection);

        $items = array_map(function ($item) use ($instance) {
            $model = $instance->newFromBuilder($item);
            static::convertIdsToUuids($model);
            return $model;
        }, $items);

        return $instance->newCollection($items);
    }
}