<?php
$this->startSetup();

$this->run("TRUNCATE crops;");

$type = 'amaranth';
$file = 'amaranth_out.csv';
Elm::getModel('crop')->import($file, $type);

$type = 'asparagus';
$file = 'asparagus_out.csv';
Elm::getModel('crop')->import($file, $type);

$type = 'broccoli';
$file = 'broccoli_out.csv';
Elm::getModel('crop')->import($file, $type);

$type = 'parsley';
$file = 'herbs_parsley_out.csv';
Elm::getModel('crop')->import($file, $type);

$type = 'lettuce';
$file = 'lettuce_out.csv';
Elm::getModel('crop')->import($file, $type);

$this->endSetup();
