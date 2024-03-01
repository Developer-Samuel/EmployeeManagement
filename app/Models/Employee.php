<?php

namespace App\Models;

use Nette;
use SimpleXMLElement;

class Employee
{
    use Nette\SmartObject;

    public function __construct()
    {

    }

    /* ID - AUTO INCREMENT (XML) */
    public function generateNewId($xml)
    {
        $maxId = 0;
        foreach ($xml->Employee as $employee) {
            $id = (int)$employee->ID;
            if ($id > $maxId) {
                $maxId = $id;
            }
        }
        return $maxId + 1;
    }

    /* FIND EMPLOYEE BY ID */
    public function getById($id)
    {
        $employees = $this->list();
        foreach ($employees as $employee) {
            if ($employee['id'] == $id) {
                return $employee;
            }
        }
        return null;
    }

    /* CHECK IF EXISTS IdentificationNumber */
    public function existsByIdentificationNumber($identificationNumber, $excludeId = null)
    {
        $xmlString = file_get_contents('files/xml/employees.xml');
        
        $xml = new SimpleXMLElement($xmlString);

        foreach ($xml->Employee as $employee) {
            $id = (int)$employee->ID;
            $currentIdentificationNumber = (string)$employee->IdentificationNumber;
    
            if ($excludeId !== null && $id == $excludeId) {
                continue;
            }
    
            if ($currentIdentificationNumber == $identificationNumber) {
                return true;
            }
        }
    
        return false;
    }    

    /* LIST OF EMPLOYEES */
    public function list()
    {
        $xmlString = file_get_contents('files/xml/employees.xml');
        
        $xml = new SimpleXMLElement($xmlString);
        $employees = [];

        foreach ($xml->Employee as $employee) {
            $employees[] = [
                'id' => (int)$employee->ID,
                'fname' => (string)$employee->FirstName,
                'lname' => (string)$employee->LastName,
                'gender' => (string)$employee->Gender,
                'age' => (int)$employee->Age,
                'identification_number' => (string)$employee->IdentificationNumber,
                'month_salary' => (float)$employee->MonthSalary,
                'hired_at' => (string)$employee->HiredAt,
            ];
        }

        return $employees;
    }

    /* CREATE NEW EMPLOYEE */
    public function create(array $data)
    {
        $xmlString = file_get_contents('files/xml/employees.xml');
        $xml = new SimpleXMLElement($xmlString);
    
        $newId = $this->generateNewId($xml);
        $employee = $xml->addChild('Employee');
        $employee->addChild('ID', $newId);
        $employee->addChild('FirstName', $data['fname']);
        $employee->addChild('LastName', $data['lname']);
        $employee->addChild('Gender', $data['gender']);
        $employee->addChild('Age', $data['age']);
        $employee->addChild('IdentificationNumber', $data['identification_number']);
        $employee->addChild('MonthSalary', $data['month_salary']);
        $employee->addChild('HiredAt', $data['hired_at']);
    
        $xml->asXML('files/xml/employees.xml');
    
        $xmlContent = file_get_contents('files/xml/employees.xml');
        $lastEmployeePattern = "/<Employee>\s*<ID>{$newId}<\/ID>/";
        $xmlContent = preg_replace($lastEmployeePattern, "\t$0", $xmlContent);
    
        $xmlContent = preg_replace('/<\/Employees>\s*$/', "\n</Employees>", $xmlContent);
    
        file_put_contents('files/xml/employees.xml', $xmlContent);
    }

    /* EDIT EMPLOYEE */
    public function edit($id, $data)
    {
        $xmlString = file_get_contents('files/xml/employees.xml');
        $xml = new SimpleXMLElement($xmlString);

        foreach ($xml->Employee as $employee) {
            if ((int)$employee->ID == $id) {
                $employee->FirstName = $data['fname'];
                $employee->LastName = $data['lname'];
                $employee->Gender = $data['gender'];
                $employee->Age = $data['age'];
                $employee->IdentificationNumber = $data['identification_number'];
                $employee->MonthSalary = $data['month_salary'];
                $employee->HiredAt = $data['hired_at'];
                break;
            }
        }

        $xml->asXML('files/xml/employees.xml');
    }

    /* DELETE EMPLOYEE */
    public function delete($id)
    {
        $xmlString = file_get_contents('files/xml/employees.xml');
        $xml = new SimpleXMLElement($xmlString);

        $index = 0;
        foreach ($xml->Employee as $employee) {
            if ((int)$employee->ID == $id) {
                unset($xml->Employee[$index]);
                break;
            }
            $index++;
        }

        $xml->asXML('files/xml/employees.xml');
    }
    
}