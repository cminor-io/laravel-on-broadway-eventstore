<?php

namespace CminorIO\LaravelOnBroadway\EventStore\LaravelStore;

use Illuminate\Database\Schema\Blueprint;

/**
 * Defines the table schema for the laravel event store.
 *
 * Class LaravelStoreSchema
 * @package CminorIO\LaravelOnBroadway\EventStore\LaravelStore
 */
class LaravelStoreSchema
{

    /**
     * Give me the tools and I will tell you what my schema is...
     *
     * @param Blueprint $table The blueprint for the database table.
     * @return Blueprint The designed database table schema.
     */
    public static function describeSchema(Blueprint $table)
    {
        $table->increments('id');
        $table->string('uuid', 36);
        $table->integer('playhead')->unsigned();
        $table->text('metadata');
        $table->text('payload');
        $table->string('recorded_on', 32);
        $table->text('type');
        $table->unique(['uuid', 'playhead']);

        return $table;
    }
}
