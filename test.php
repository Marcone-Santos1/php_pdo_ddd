<?php

use Alura\Pdo\Domain\Model\Student;
use Alura\Pdo\Infrastructure\Persistence\ConnectionCreator;
use Alura\Pdo\Infrastructure\Repository\PdoStudentRepository;

require_once 'vendor/autoload.php';

$connection = ConnectionCreator::createConnection();
$repository = new PdoStudentRepository($connection);

$connection->beginTransaction();
try {
//    $repository->save(new Student(
//        null,
//        'Lucas',
//        new DateTimeImmutable('2004-03-17')
//    ));
//    $repository->save(new Student(
//        null,
//        'Matheus',
//        new DateTimeImmutable('2004-03-17')
//    ));
//    $repository->save(new Student(
//        null,
//        'João',
//        new DateTimeImmutable('2004-03-17')
//    ));

    $connection->commit();
} catch (PDOException $e) {
    echo $e->getMessage();
    $connection->rollBack();
}


//var_dump($repository->allStudents());
var_dump($repository->studentsWithPhone());
