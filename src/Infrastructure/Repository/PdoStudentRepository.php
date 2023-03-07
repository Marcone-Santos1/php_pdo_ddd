<?php

namespace Alura\Pdo\Infrastructure\Repository;

use Alura\Pdo\Domain\Model\Phone;
use Alura\Pdo\Domain\Model\Student;
use Alura\Pdo\Domain\Repository\StudentRepository;
use PDO;
use PDOStatement;

class PdoStudentRepository implements StudentRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function allStudents(): array
    {
        $stmt = $this->connection->query("SELECT * FROM students");
        return $this->hydrateStudentList($stmt);
    }

    public function studentsBirthAt(\DateTimeImmutable $birthDate): array
    {
        $stmt = $this->connection->query("SELECT * FROM students WHERE birth_date = ?")->execute([$birthDate]);
        return $this->hydrateStudentList($stmt);
    }

    private function hydrateStudentList(PDOStatement $smt): array
    {
        $studentDataList = $smt->fetchAll(PDO::FETCH_ASSOC);
        $studentList = [];

        foreach ($studentDataList as $studentData) {
            $studentList[] = new Student(
                $studentData['id'],
                $studentData['name'],
                new \DateTimeImmutable($studentData['birth_date'])
            );
        }
        return $studentList;
    }

    public function save(Student $student): bool
    {
        return ($student->id() == null) ? $this->insert($student) : $this->update($student);
    }

    private function insert(Student $student): bool
    {
        $sqlInsert = "INSERT INTO students (name, birth_date) VALUES (?, ?);";

        $success = $this->connection->prepare($sqlInsert)->execute([
            $student->name(),
            $student->birthDate()->format('Y-m-d')
        ]);

        if ($success) {
            $student->defineId($this->connection->lastInsertId());
        }
        return $success;
    }

    private function update(Student $student): bool
    {
        $sqlInsert = "UPDATE students SET name = ?, birth_date = ? WHERE id = ?;";
        $stmt = $this->connection->prepare($sqlInsert);
        $stmt->bindValue(1, $student->name());
        $stmt->bindValue(2, $student->birthDate()->format('Y-m-d'));
        $stmt->bindValue(3, $student->id(), PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function remove(Student $student): bool
    {
        $sqlInsert = "DELETE FROM students WHERE id = ?;";
        $stmt = $this->connection->prepare($sqlInsert);
        $stmt->bindValue(1, $student->id(), PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function studentsWithPhone(): array
    {
        $sql = "SELECT students.id, students.name, 
        students.birth_date, phones.id AS phone_id, 
        phones.area_code, phones.number
        FROM students
        INNER JOIN phones on students.id = phones.student_id;";

        $stmt = $this->connection->query($sql);

        $studentList = [];

        foreach ($stmt->fetchAll() as $row) {
            if (!array_key_exists($row['id'], $studentList)) {
                $studentList[$row['id']] = new Student(
                    $row['id'],
                    $row['name'],
                    new \DateTimeImmutable($row['birth_date'])
                );
            }
            $phone = new Phone($row['phone_id'], $row['area_code'], $row['number']);
            $studentList[$row['id']]->addPhone($phone);
        }
        return $studentList;
    }
}