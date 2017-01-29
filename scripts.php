<?php

return [

    'install' => function ($app) {
        $util = $app['db']->getUtility();

        if ($util->tablesExist('@cors_path') === false) {
            $util->createTable('@cors_path', function($table) {
                $table->addColumn('id', 'integer', ['unsigned' => true, 'length' => 10, 'autoincrement' => true]);
                $table->addColumn('path', 'string', ['length' => 255]);
                $table->addColumn('allow_credentials', 'boolean', ['default' => false]);
                $table->addColumn('origin_regex', 'boolean', ['default' => false]);
                $table->addColumn('status', 'boolean', ['default' => false]);
                $table->addColumn('max_age', 'integer', ['unsigned' => true, 'default' => 0]);
                $table->addColumn('forced_allow_origin_value', 'string', ['length' => 255]);
                $table->addColumn('allow_origin', 'array', ['notnull' => false]);
                $table->addColumn('allow_headers', 'array', ['notnull' => false]);
                $table->addColumn('allow_methods', 'array', ['notnull' => false]);
                $table->addColumn('expose_headers', 'array', ['notnull' => false]);
                $table->addColumn('hosts', 'array', ['notnull' => false]);
                $table->setPrimaryKey(['id']);
            });
        }
    },

    'uninstall' => function ($app) {
        $util = $app['db']->getUtility();

        if ($util->tableExists('@cors_path')) {
            $util->dropTable('@cors_path');
        }
    }

];