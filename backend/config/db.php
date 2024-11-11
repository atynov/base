<?php

$params = array_merge(
    require __DIR__ . '/constants.php',
    require __DIR__ . '/params.php'
);

return [
    'class' => \yii\db\Connection::class,
    'dsn' => 'pgsql:host=' . $params['db']['host'] . ';port=' . $params['db']['port'] . ';dbname=' . $params['db']['name'],
    'username' => $params['db']['user'],
    'password' => $params['db']['password'],
    'charset' => 'utf8',
    'enableSchemaCache' => !YII_DEBUG,
    'enableQueryCache' => true,
    'schemaCacheDuration' => 3600,
    'schemaCache' => 'cache',
];
