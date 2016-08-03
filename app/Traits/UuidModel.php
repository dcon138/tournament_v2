<?php

namespace App\Traits;

use Rhumsaa\Uuid\Uuid;
use Illuminate\Database\Eloquent\ModelNotFoundException;

trait UuidModel {
    /**
     * @var string $FOREIGN_KEY_REGEX
     *
     * Defines the regular expression that will be applied to field names to decide if the field is a foreign key field.
     * If the field is defined as a foreign key field, it will have it's value auto-mapped from uuid to database id.
     */
    private static $FOREIGN_KEY_REGEX = '/.*_id$/';

    /**
     * Adapted From: http://humaan.com/using-uuids-with-eloquent-in-laravel/
     *
     * Binds creating/saving events to create UUIDs (and also prevent them from being overwritten).
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
                //TODO should this throw an exception instead of just setting it back to what it was?
                $model->uuid = $original_uuid;
            }


            //TODO only do the below if $model is_subclass_of BaseModel?? if not the 2 conventional/unconventional function calls wont work
            $modelAttributes = $model->attributesToArray();
            $unconventionalForeignKeys = $model->getUnconventionalForeignKeys();
            $conventionalNonForeignKeys = $model->getConventionalNonForeignKeys();
            var_dump($modelAttributes);
            var_dump($unconventionalForeignKeys);
            foreach ($modelAttributes as $field => $value) {

                //if the field is NOT in the ignore list
                if (empty($conventionalNonForeignKeys) || !in_array($field, $conventionalNonForeignKeys)) {

                    //if the field matches the foreign key field convention regex
                    if (!empty($unconventionalForeignKeys) && array_key_exists($field, $unconventionalForeignKeys)) {
                        //TODO look for database table with name $table. If not found, throw fatal error exception
                        $table = $unconventionalForeignKeys[$field];
                        echo 'Foreign Key to table ' . $table . ' is ' . $field . ' => ' . $value . "\n";
                    } else if (preg_match(self::$FOREIGN_KEY_REGEX, $field)) {
                        $table = str_plural(substr($field, 0, -3));
                        //TODO look for database table with name $table. If not found, throw fatal error exception
                        echo 'Foreign Key to table ' . $table . ' is ' . $field . ' => ' . $value . "\n";
                    }
                }
            }
            die('ccc');
        });
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
        if (!is_string($uuid) || (preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/', $uuid) !== 1)) {
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

        if (preg_match('/^([0-9]+|[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12})$/', $id_or_uuid) !== 1) {
            throw (new ModelNotFoundException)->setModel(get_class($this));
        }

        $search = $query->where(function ($query) use ($id_or_uuid) {
            $query->where('id', $id_or_uuid)
                ->orWhere('uuid', $id_or_uuid);
        });

        return $first ? $search->firstOrFail() : $search;
    }
}